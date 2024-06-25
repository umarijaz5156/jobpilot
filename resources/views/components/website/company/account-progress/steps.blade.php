@props(['basic' => 'active', 'profile' => 'inactive', 'social_media' => 'inactive', 'contact' => 'inactive'])

<div class="tw-py-6 tw-max-w-[654px] tw-mx-auto">
    <ul class="step-wrap tw-list-none tw-relative tw-grid tw-grid-cols-4 tw-gap-0">
        <li class="post-step">
            <div class="tw-flex tw-flex-col tw-gap-3 tw-items-center">
                <p class="rounded-full tw-mb-0">
                    @if ($basic === 'active')
                        <x-svg.step-active />
                    @endif
                    @if ($basic === 'inactive')
                        <x-svg.step-inactive />
                    @endif
                    @if ($basic === 'complete')
                        <x-svg.step-complete />
                    @endif
                </p>
                <p class="sm:tw-body-small-600 tw-text-base tw-mb-0 tw-text-gray-900">
                     {{ __('basic') }}
                </p>
            </div>
        </li>
        <li class="post-step {{ $basic === 'complete' ? 'passed':'' }}">
            <div class="tw-flex tw-flex-col tw-gap-3 tw-items-center">
                <p class="tw-rounded-full tw-mb-0">
                    @if ($profile === 'active')
                        <x-svg.step-active />
                    @endif
                    @if ($profile === 'inactive')
                        <x-svg.step-inactive />
                    @endif
                    @if ($profile === 'complete')
                        <x-svg.step-complete />
                    @endif
                </p>
                <p class="sm:tw-body-small-600 tw-text-base tw-mb-0 tw-text-gray-900">
                    {{ __('profile') }}
                </p>
            </div>
        </li>
        <li class="post-step {{ $profile === 'complete' ? 'passed':'' }}">
            <div class="tw-flex tw-flex-col tw-gap-3 tw-items-center">
                <p class="rounded-full tw-mb-0">
                    @if ($social_media === 'active')
                        <x-svg.step-active />
                    @endif
                    @if ($social_media === 'inactive')
                        <x-svg.step-inactive />
                    @endif
                    @if ($social_media === 'complete')
                        <x-svg.step-complete />
                    @endif
                </p>
                <p class="sm:tw-body-small-600 tw-text-base tw-mb-0 tw-text-gray-900">
                    {{ __('social_media') }}
                </p>
            </div>
        </li>
        <li class="post-step {{ $social_media === 'complete' ? 'passed':'' }}">
            <div class="tw-flex tw-flex-col tw-gap-3 tw-items-center">
                <p class="tw-rounded-full tw-mb-0">
                    @if ($contact === 'active')
                        <x-svg.step-active />
                    @endif
                    @if ($contact === 'inactive')
                        <x-svg.step-inactive />
                    @endif
                    @if ($contact === 'complete')
                        <x-svg.step-complete />
                    @endif
                </p>
                <p class="sm:tw-body-small-600 tw-text-base tw-mb-0 tw-text-gray-900">
                    {{ __('contact') }}
                </p>
            </div>
        </li>
    </ul>
</div>

<style>
    .post-step {
        position: relative;
        z-index: 100;
        text-align: center;
    }

    .post-step::before {
        content: '';
        display: block;
        position: absolute;
        top: 11px;
        right: calc(50% + 12px);
        height: 3px;
        width: calc(100% - 24px);
        background: var(--gray-100);
        z-index: -1;
    }
    body[dir="rtl"] .post-step::before {
        left: calc(50% + 12px) !important;
        right: auto !important;
    }

    .post-step:first-child::before {
        display: none;
    }
    /* body[dir="rtl"] .post-step:first-child::before {
        display: block !important;
    }
    body[dir="rtl"] .post-step:last-child::before {
        display: none;
    } */

    .post-step.passed::before,
    .post-step.active::before {
        background: var(--primary-500);
    }
</style>
