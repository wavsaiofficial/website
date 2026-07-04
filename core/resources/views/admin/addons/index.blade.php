@extends('admin.layouts.app')
@section('panel')
    <div class="row responsive-row">
        <div class="col-12">
            @if ($addons->isNotEmpty())
                <div class="marketplace__grid">
                    @foreach ($addons as $addon)
                        <div class="addon-card active">
                            <div class="addon-card__top">
                                <div class="addon-card__logo-wrapper addon-card__logo-wrapper--telegram">
                                    <img src="{{ addonAsset($addon->name, 'assets/images/logo.png') }}" alt="addon">
                                </div>
                                @if ($addon->status == Status::ADDON_INSTALLED)
                                    <div class="addon-card__status-badge addon-card__status-badge--enabled">
                                        <span class="addon-card__status-pulse"></span>
                                        @lang('Installed')
                                    </div>
                                @else
                                    <div class="addon-card__status-badge addon-card__status-badge--disabled">
                                        <span class="addon-card__status-pulse"></span>
                                        @lang('Uninstalled')
                                    </div>
                                @endif
                            </div>
                            <div class="addon-card__middle">
                                <h3 class="addon-card__name mb-2">{{ __($addon->name) }}</h3>
                                <p class="addon-card__title">{{ __($addon->title) }}</p>
                                <p class="addon-card__description">
                                    {{ __($addon->description) }}
                                </p>
                            </div>
                            <div class="addon-card__bottom">
                                <div class="addon-card__meta-info">
                                    <span class="addon-card__meta-label">@lang('Version')</span>
                                    <span class="addon-card__meta-value">v{{ __($addon->version) }}</span>
                                </div>
                                <div class="addon-card__price-info">
                                    <span class="addon-card__price-label">@lang('Status')</span>
                                    <span class="addon-card__price-value">
                                        <div class="d-flex justify-content-center">
                                            <div class="form-check form-switch form--switch pl-0 form-switch-success">
                                                <input class="form-check-input confirmationBtn" type="checkbox"
                                                    role="switch" @checked($addon->status)
                                                    data-action="{{ route('admin.addons.toggle', $addon->id) }}"
                                                    data-question="{{ $addon->status ? trans('Are you sure to uninstall this addon?') : trans('Are you sure to install this addon?') }}">
                                            </div>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="marketplace__empty">
                    <div class="marketplace__empty-icon">
                        <svg viewBox="0 0 100 100" class="marketplace__empty-svg">
                            <defs>
                                <linearGradient id="emptyGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="hsl(var(--primary))" />
                                    <stop offset="100%" stop-color="hsl(var(--primary) / 0.4)" />
                                </linearGradient>
                                <filter id="emptyGlow" x="-20%" y="-20%" width="140%" height="140%">
                                    <feGaussianBlur stdDeviation="6" result="blur" />
                                    <feComposite in="SourceGraphic" in2="blur" operator="over" />
                                </filter>
                            </defs>
                            <circle cx="50" cy="50" r="44" stroke="hsl(var(--border-color))"
                                stroke-width="1.2" stroke-dasharray="6 4" fill="none" />
                            <circle cx="50" cy="50" r="32" stroke="hsl(var(--border-color))" stroke-width="1"
                                stroke-dasharray="3 3" fill="none" />
                            <rect x="36" y="36" width="28" height="28" rx="8" fill="none"
                                stroke="url(#emptyGrad)" stroke-width="2.5" filter="url(#emptyGlow)" />
                            <path d="M46 43 L54 57 M54 43 L46 57" stroke="url(#emptyGrad)" stroke-width="2.5"
                                stroke-linecap="round" />
                            <circle cx="50" cy="50" r="3.5" fill="hsl(var(--bg-color))"
                                stroke="url(#emptyGrad)" stroke-width="2" />
                        </svg>
                    </div>
                    <h2 class="marketplace__empty-title">@lang('No Addons Activated')</h2>
                    <p class="marketplace__empty-description">
                        @lang("It looks like you don't have any active modules on your dashboard yet. Browse the official integration marketplace to synchronize services, automate workflows, and extend features.")
                    </p>
                    <button type="button" class="btn btn--primary btn-large install-btn">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0"
                                viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve"
                                class="">
                                <g>
                                    <path
                                        d="M385 307.9h-62.2V167.7c0-16.7-13.5-30.2-30.2-30.2h-73.2c-16.7 0-30.2 13.6-30.2 30.2v140.2H127c-8 0-15.1 4.5-18.3 11.9-3.3 7.3-2 15.6 3.3 21.6L233.4 478c5.7 6.4 13.9 10.1 22.6 10.1 8.6 0 16.8-3.7 22.6-10.1L400 341.3c5.3-6 6.6-14.3 3.3-21.6S393 307.9 385 307.9zm7.5 26.8L271.1 471.3c-8 9-22.1 9-30.2 0L119.5 334.7c-5.8-6.5-1.2-16.8 7.6-16.8h72.2V167.7c0-11.1 9-20.2 20.2-20.2h73.2c11.2 0 20.2 9 20.2 20.2v150.2H385c8.7 0 13.3 10.3 7.5 16.8zm13.7-310.8H105.8c-16.6 0-30.2 13.5-30.2 30.2v35.3c0 16.7 13.6 30.2 30.2 30.2h300.4c16.6 0 30.2-13.6 30.2-30.2V54.1c0-16.6-13.5-30.2-30.2-30.2zm20.2 65.5c0 11.2-9 20.2-20.2 20.2H105.8c-11.2 0-20.2-9-20.2-20.2V54.1c0-11.1 9-20.2 20.2-20.2h300.4c11.1 0 20.2 9 20.2 20.2z"
                                        fill="#6338ff" opacity="1" data-original="#000000" class=""></path>
                                </g>
                            </svg>
                        </span> @lang('Begin Your Addon Journey')
                    </button>
                </div>
            @endif
        </div>
    </div>

    <x-admin.ui.modal id="install-modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Install New Addon')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="{{ route('admin.addons.upload') }}" method="POST" enctype="multipart/form-data"
                class="upload-form no-submit-loader">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Purchase Code')</label>
                        <input type="text" name="purchase_code" class="form-control"
                            placeholder="xxxxxxxx - xxxx - xxxx - xxxx - xxxxxxxxxxxx " value="{{ old('purchase_code') }}"
                            required>
                        <small class="text-muted">@lang('You received after purchase from Codecanyon.')</small>
                    </div>
                    <div class="form-group">
                        <label>@lang('Envato Username')</label>
                        <input type="text" name="envato_username" class="form-control"
                            placeholder="@lang('Your Envato Username')" value="{{ old('envato_username') }}" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Addon ZIP File')</label>
                        <input type="file" name="addon_zip" class="form-control" accept=".zip" required>
                        <small class="text-muted d-block mt-2">@lang('You received a zip file after purchase from Codecanyon.')</small>
                    </div>
                    <div class="form-group">
                        <x-admin.ui.btn.modal />
                    </div>
                </div>

            </form>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>


    <x-confirmation-modal />
@endsection


@if ($addons->isNotEmpty())
    @push('breadcrumb-plugins')
        <button class="btn btn--primary install-btn" type="button">
            <i class="las la-plus"></i> @lang('Install New Addon')
        </button>
    @endpush
@endif

@push('script')
    <script>
        "use strict";
        (function($) {
            const $modal = $("#install-modal");
            $('.install-btn').on('click', function() {
                $modal.modal('show');
            });

            $(".upload-form").on('submit', function(e) {
                e.preventDefault();
                const $this = $(this);
                const $submitBtn = $this.find('button[type="submit"]');
                const oldHtml = $submitBtn.html();
                const formData = new FormData($this[0]);

                $.ajax({
                    url: $this.attr('action'),
                    method: $this.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend() {
                        $submitBtn.prop('disabled', true)
                            .addClass('disabled')
                            .text("@lang('Uploading')...");
                    },
                    success(response) {
                        notify(response.status, response.message);
                        if (response.status == 'success') {
                            $modal.modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error(e) {
                        const message = e.responseJSON?.message || '@lang('An unexpected error occurred. Please try again.')';
                        notify('error', message);
                    },
                    complete() {
                        $submitBtn.prop('disabled', false).removeClass('disabled').html(oldHtml);
                    }
                });
            });

        })(jQuery);
    </script>
@endpush




@push('style')
    <style>
        /* Grid Element */
        .marketplace__grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 2rem;
            align-items: stretch;
        }

        .addon-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: hsla(var(--addon-card-bg) / 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px dashed hsl(var(--border-color));
            border-radius: 20px;
            padding: 1.8rem;
            box-shadow: var(--addon-card-shadow);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s cubic-bezier(0.2, 0.8, 0.2, 1) box-shadow 0.3s cubic-bezier(0.2, 0.8, 0.2, 1),
                border-color 0.3s ease;
        }

        /* Subtle background accent glow */
        .addon-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, hsl(var(--primary) / 0.15), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* Premium Hover Lift Effect */
        .addon-card:hover,
        .addon-card.active {
            transform: translateY(-4px);
            box-shadow: var(--addon-card-shadow-hover);
            border-color: var(--addon-card-border-hover);
        }

        .addon-card:hover::before {
            opacity: 1;
        }

        /* Card Modifier: addon-card--disabled */
        .addon-card--disabled {
            opacity: 0.93;
        }

        .addon-card--disabled .addon-card__logo-wrapper {
            filter: grayscale(40%) opacity(75%);
        }

        .addon-card--disabled .addon-card__name {
            opacity: 0.85;
        }

        .addon-card--disabled .addon-card__description {
            opacity: 0.7;
        }

        /* Card Element: top */
        .addon-card__top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        /* Logo container element */
        .addon-card__logo-wrapper {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: hsl(var(--secondary));
            box-shadow: 0 4px 12px -3px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .addon-card__logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .addon-card__logo-wrapper::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 60%);
            pointer-events: none;
        }

        /* Brand Modifiers for Logo Wrapper */
        .addon-card__logo-wrapper--telegram {
            background-color: hsla(200, 80%, 50%, 0.08);
        }

        .addon-card__logo-wrapper--stripe {
            background-color: hsla(250, 100%, 65%, 0.08);
        }

        .addon-card__logo-wrapper--ai {
            background-color: hsla(320, 85%, 60%, 0.08);
        }

        .addon-card__logo-wrapper--seo {
            background-color: hsla(160, 84%, 39%, 0.08);
        }

        .addon-card__logo-wrapper--analytics {
            background-color: hsla(38, 92%, 50%, 0.08);
        }

        .addon-card__logo-wrapper--theme {
            background-color: hsla(190, 90%, 42%, 0.08);
        }

        /* Status Badge element */
        .addon-card__status-badge {
            font-family: var(--font-body);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.75px;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            line-height: 1;
        }

        /* Status Badge Modifier: addon-card__status-badge--enabled */
        .addon-card__status-badge--enabled {
            background-color: hsla(var(--success) / 0.08);
            color: hsl(var(--success));
            border: 1px solid hsla(var(--success) / 0.15);
            box-shadow: 0 0 12px hsla(var(--success) / 0.05);
        }

        .addon-card__status-pulse {
            width: 6px;
            height: 6px;
            background-color: hsl(var(--success));
            border-radius: 50%;
            display: inline-block;
            position: relative;
            animation: statusPulseGlow 2s infinite ease-in-out;
        }

        .addon-card__status-badge--disabled .addon-card__status-pulse {
            background-color: hsl(var(--danger));
            animation: statusPulseGlowDanger 2s infinite ease-in-out;
        }

        /* Pulse Keyframes */
        @keyframes statusPulseGlow {
            0% {
                transform: scale(0.9);
                box-shadow: 0 0 0 0 hsla(var(--success) / 0.7);
            }

            70% {
                transform: scale(1.1);
                box-shadow: 0 0 0 6px hsla(var(--success) / 0);
            }

            100% {
                transform: scale(0.9);
                box-shadow: 0 0 0 0 hsla(var(--success) / 0);
            }
        }

        /* Pulse Keyframes */
        @keyframes statusPulseGlowDanger {
            0% {
                transform: scale(0.9);
                box-shadow: 0 0 0 0 hsla(var(--danger) / 0.7);
            }

            70% {
                transform: scale(1.1);
                box-shadow: 0 0 0 6px hsla(var(--danger) / 0);
            }

            100% {
                transform: scale(0.9);
                box-shadow: 0 0 0 0 hsla(var(--danger) / 0);
            }
        }

        /* Status Badge Modifier: addon-card__status-badge--disabled */
        .addon-card__status-badge--disabled {
            background-color: hsla(var(--danger) / 0.08);
            color: hsl(var(--danger));
            border: 1px solid hsla(var(--danger) / 0.15);
            box-shadow: none;
        }

        /* Card Element: middle */
        .addon-card__middle {
            flex-grow: 1;
            margin-bottom: 2rem;
        }

        .addon-card__name {
            font-family: var(--font-heading);
            font-size: 1.15rem;
            font-weight: 600;
            color: hsl(var(--title-color));
            margin-bottom: 0.6rem;
            letter-spacing: -0.2px;
        }

        .addon-card__description {
            font-size: 0.85rem;
            color: hsl(var(--body-color));
            line-height: 1.55;
            display: -webkit-box;

        }

        /* Card Element: bottom (Single-row flex) */
        .addon-card__bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px dashed hsl(var(--border-color));
            padding-top: 1.4rem;
        }

        /* Card Elements: meta & price wrap */
        .addon-card__meta-info {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .addon-card__meta-label,
        .addon-card__price-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: hsl(var(--body-color));
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.85;
        }

        .addon-card__meta-value {
            font-family: var(--font-heading);
            font-size: 0.85rem;
            font-weight: 600;
            color: hsl(var(--title-color));
        }

        .addon-card__price-info {
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .addon-card__price-value {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            font-weight: 700;
            color: hsl(var(--primary));
            line-height: 1.1;
        }

        .addon-card__price-small {
            font-size: 0.75rem;
            font-weight: 500;
            color: hsl(var(--body-color));
        }


        /* Large Tablets & Desktop adjust scales */
        @media (max-width: 1200px) {
            .marketplace__title {
                font-size: 1.9rem;
            }
        }

        /* Small Tablets & iPads */
        @media (max-width: 992px) {
            .marketplace__grid {
                grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
                gap: 1.5rem;
            }
        }

        /* Mobile Devices */
        @media (max-width: 600px) {
            .marketplace__main {
                padding: 1.5rem 1.2rem;
            }

            .marketplace__header {
                flex-direction: column;
                gap: 1.5rem;
                align-items: stretch;
            }

            .marketplace__grid {
                grid-template-columns: 1fr;
                gap: 1.2rem;
            }

            .addon-card {
                padding: 1.4rem;
            }

            .marketplace__title {
                font-size: 1.6rem;
            }

            .marketplace__subtitle {
                font-size: 0.88rem;
            }
        }



        .marketplace__empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 6rem 2rem;
            background-color: hsla(var(--addon-card-bg) / 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 2px dashed hsl(var(--border-color));
            border-radius: var(--radius-card);
            max-width: 660px;
            margin: 1.5rem auto 0;
            box-shadow: var(--addon-card-shadow);
            transition: transform 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .marketplace__empty-icon {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, hsla(var(--primary) / 0.08), hsla(var(--primary) / 0.01));
            border: 1px solid hsla(var(--primary) / 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            position: relative;
            box-shadow: 0 8px 24px hsla(var(--primary) / 0.04);
        }

        .marketplace__empty-icon::after {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            border: 1px dashed hsla(var(--primary) / 0.25);
            animation: rotateDashedOutline 25s linear infinite;
        }

        @keyframes rotateDashedOutline {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .marketplace__empty-svg {
            width: 60px;
            height: 60px;
        }

        .marketplace__empty-title {
            font-family: var(--font-heading);
            font-size: 1.45rem;
            font-weight: 700;
            color: hsl(var(--title-color));
            margin-bottom: 0.8rem;
            letter-spacing: -0.5px;
        }

        .marketplace__empty-description {
            font-size: 0.88rem;
            color: hsl(var(--body-color));
            max-width: 460px;
            line-height: 1.6;
            margin-bottom: 2.2rem;
        }



        /* Mobile Devices */
        @media (max-width: 600px) {

            .marketplace__empty {
                padding: 4rem 1.5rem;
            }

            .marketplace__empty-title {
                font-size: 1.3rem;
            }
        }

        .addon-card__title {
            font-size: 13px;
            margin-bottom: 1rem;
            border-bottom: 2px dashed hsl(var(--border-color));
            padding-bottom: 2rem;
        }
    </style>
@endpush
