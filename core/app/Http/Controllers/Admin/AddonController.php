<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\InstalledAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use ZipArchive;

class AddonController extends Controller
{
    public function index()
    {
        $pageTitle = 'Addon Manager';
        $addons    = InstalledAddon::latest()->get();
        return view('admin.addons.index', compact('pageTitle', 'addons'));
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'addon_zip'       => 'required|file|mimes:zip',
            'purchase_code'   => 'required|string',
            'envato_username' => 'required|string',
        ]);

        if ($validator->fails()) {
            return apiResponse('Validation failed', "error", $validator->errors()->all());
        }

        $existsAddon = InstalledAddon::where('purchase_code', $request->purchase_code)->exists();

        if ($existsAddon) {
            return apiResponse('addon_already_installed', "error", ['This addon is already uploaded to your server.']);
        }

        //check server requirements
        try {
            $this->checkPHPAndServerRequirements();
        } catch (\Exception $e) {
            $message = $e->getMessage() ?? 'Server does not meet the requirements to install addons. Please contact your hosting provider.';
            return apiResponse('Validation failed', "error", [$message]);
        }

        $purchaseCode = trim($request->purchase_code);

        // Store ZIP temporarily
        $zipFile = $request->file('addon_zip');
        $zipPath = storage_path('app/temp/addon/' . $zipFile->getClientOriginalName());
        $zipFile->move(storage_path('app/temp/addon'), $zipFile->getClientOriginalName());

        try {

            //purchase code validation first

            $response = Http::post(
                "https://ovosolution.com/verify-purchase/addon_server/addon-verify",
                [
                    'purchase_code'   => $purchaseCode,
                    'envato_username' => $request->envato_username
                ],
            );

            $data       = $response->json();

            if ($response->failed()) {
                $message = isset($data['errors']) ? $data['errors'] : 'Something went to wrong, please try again later.';
                $message = is_array($message) ? $message : [$message];
                return apiResponse('error', 'error', $message);
            }

            if (isset($data['errors']) && !is_null($data['errors'])) {
                $message = isset($data['errors']) ? $data['errors'] : 'Something went to wrong, please try again later.';
                $message = is_array($message) ? $message : [$message];
                return apiResponse('error', 'error', $message);
            }


            ///Read addon.json from ZIP without extracting 
            $meta                       = $this->readAddonJson($zipPath);
            $destinationAfterExtractZip = $this->extractZip($zipPath, $meta['name']);

            //need upload database
            $newAddon                = new InstalledAddon();
            $newAddon->name          = $meta['name'];
            $newAddon->title         = $meta['title'];
            $newAddon->slug          = $meta['slug'];
            $newAddon->description   = $meta['description'];
            $newAddon->provider      = $meta['provider'];
            $newAddon->author        = $meta['author'];
            $newAddon->version       = $meta['version'] ?? '1.0';
            $newAddon->purchase_code = $purchaseCode;
            $newAddon->save();


            // Clear caches
            Cache::forget('installed_addons_active');
            @unlink($zipPath);

            //need upload addon database
            $databaseFile = $destinationAfterExtractZip . '/Source/database/version/' . $newAddon->version . '/database.sql';

            sleep(2);

            if (file_exists($databaseFile)) {
                $databaseStatement = file_get_contents($databaseFile);
                DB::unprepared($databaseStatement);
            }


            return apiResponse('success', "success", ["Addon installed successfully!"]);
        } catch (\Exception $e) {
            @unlink($zipPath);
            $message = $e->getMessage() ?? 'An error occurred during installation. Please try again.';
            return apiResponse('Installation failed', "error", ['Installation failed: ' . $message]);
        }
    }


    private function readAddonJson(string $zipPath): ?array
    {

        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new \Exception('Could not open ZIP file.');
        }

        $json = null;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (basename($name) === 'config.json') {
                $json = json_decode($zip->getFromIndex($i), true);
                break;
            }
        }

        $zip->close();

        if (!$json || !is_array($json) || !isset($json['name'], $json['title'], $json['slug'], $json['version'], $json['author'], $json['description'], $json['provider'])) {
            @unlink($zipPath);
            throw new \Exception('Invalid addon package. The config.json is not found.');
        }
        return $json;
    }

    private function extractZip(string $zipPath, string $name): string
    {

        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new \Exception('Could not open ZIP file.');
        }

        $destination = base_path('addons/' . $name);

        if (is_dir($destination)) {
            $this->deleteDirectory($destination);
        }

        $zip->extractTo($destination);
        $zip->close();

        return $destination;
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;

        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function checkPHPAndServerRequirements(): void
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('The PHP Zip extension is required to install addons. Please enable it in your php.ini or contact your hosting provider to enable it.');
        }

        if (!extension_loaded('fileinfo')) {
            throw new \Exception('The PHP Fileinfo extension is required to install addons. Please enable it in your php.ini or contact your hosting provider to enable it.');
        }

        if (!extension_loaded('curl')) {
            throw new \Exception('The PHP cURL extension is required to install addons. Please enable it in your php.ini or contact your hosting provider to enable it.');
        }

        if (!extension_loaded('openssl')) {
            throw new \Exception('The PHP OpenSSL extension is required to install addons. Please enable it in your php.ini or contact your hosting provider to enable it.');
        }

        if (!extension_loaded('json')) {
            throw new \Exception('The PHP JSON extension is required to install addons. Please enable it in your php.ini or contact your hosting provider to enable it.');
        }


        $tmpPath = storage_path('app/temp/addon');

        if (!is_dir($tmpPath)) {
            mkdir($tmpPath, 0755, true);
        }

        if (!is_writable($tmpPath)) {
            throw new \Exception('The storage/app/temp/addon directory is not writable. Please set the correct permissions (755) or contact your hosting provider.');
        }

        if (!is_writable(base_path('addons'))) {
            throw new \Exception('The addons directory is not writable. Please set the correct permissions (755) or contact your hosting provider.');
        }
    }

    public function toggle(int $id)
    {
        $addon = InstalledAddon::findOrFail($id);

        if ($addon->status == Status::ADDON_INSTALLED) {
            $addon->status = Status::ADDON_UNINSTALLED;
            $message       = "Addon uninstalled successfully!";
        } else {
            $addon->status = Status::ADDON_INSTALLED;
            $message       = "Addon installed successfully!";
        }

        Cache::forget('installed_addons_active');
        $addon->save();
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }
}
