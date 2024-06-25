<div class="modal fade cadidate-modal" id="candidate-profile-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog  modal-wrapper">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col-md-8">
                        <div class="candidate-profile mb-4 mb-md-0">
                            <div class="candidate-profile-img">
                                <img id="candidate_image"
                                    src="{{ asset('frontend') }}/assets/images/all-img/candidate-profile.png"
                                    alt="profile">
                            </div>
                            <div class="candidate-profile-info">
                                <h2 class="name"></h2>
                                <h4 class="designation"></h4>
                                <h6 class="availablity d-none">{{ __('i_am_available') }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-md-flex align-items-center justify-content-md-end">
                        <div class="d-flex gap-3 flex-column flex-md-row">
                            <button
                            class="btn hover:!tw-border-black tw-inline-flex tw-gap-2 tw-py-2.5 tw-px-4 !tw-border-[2px] !tw-border-[#0066CC] tw-items-center tw-text-base tw-text-[#0066CC]"
                            type="button" data-bs-toggle="modal" data-bs-target="#messageModal" data-bs-dismiss="modal">
                            <svg width="24" height="24" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M5.83333 7.08333H10M5.83333 10H12.5M8.06979 15H13.5C14.9001 15 15.6002 15 16.135 14.7275C16.6054 14.4878 16.9878 14.1054 17.2275 13.635C17.5 13.1002 17.5 12.4001 17.5 11V6.5C17.5 5.09987 17.5 4.3998 17.2275 3.86502C16.9878 3.39462 16.6054 3.01217 16.135 2.77248C15.6002 2.5 14.9001 2.5 13.5 2.5H6.5C5.09987 2.5 4.3998 2.5 3.86502 2.77248C3.39462 3.01217 3.01217 3.39462 2.77248 3.86502C2.5 4.3998 2.5 5.09987 2.5 6.5V16.9463C2.5 17.3903 2.5 17.6123 2.59102 17.7263C2.67019 17.8255 2.79022 17.8832 2.91712 17.8831C3.06302 17.8829 3.23639 17.7442 3.58313 17.4668L5.57101 15.8765C5.9771 15.5517 6.18014 15.3892 6.40624 15.2737C6.60683 15.1712 6.82036 15.0963 7.04101 15.051C7.28972 15 7.54975 15 8.06979 15Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <span>{{ __('message') }}</span>
                        </button>

                            <div class="rt-mb-md-10">
                                {{-- Not bookmark  --}}
                                <button id="bookmakCandidate"
                                    class="d-none bg-primary-50 text-primary-500 plain-button icon-48 btn-star-white hover:bg-primary-500 hover:text-primary-50">
                                    <svg width="16" height="20" viewBox="0 0 16 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15 19L8 14L1 19V3C1 2.46957 1.21071 1.96086 1.58579 1.58579C1.96086 1.21071 2.46957 1 3 1H13C13.5304 1 14.0391 1.21071 14.4142 1.58579C14.7893 1.96086 15 2.46957 15 3V19Z"
                                            stroke="{{ $setting->frontend_primary_color }}" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>

                                {{-- Already bookmarked  --}}
                                <button id="removeBookmakCandidate"
                                    class="d-none bg-primary-500 text-primary-500 plain-button icon-48 btn-star-white hover:bg-primary-500 hover:text-primary-50">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19 21L12 16L5 21V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H17C17.5304 3 18.0391 3.21071 18.4142 3.58579C18.7893 3.96086 19 4.46957 19 5V21Z"
                                            fill="#fff" stroke="#fff"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <ul class="nav nav-tabs tw-gap-x-6 !tw-border-b !tw-border-[#E3E5E6]" id="myTab"
                            role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="ll-nav-link nav-link" id="profile-tab" data-bs-toggle="tab" ata-bs-target="#profile" type="button" role="tab" aria-controls="profile" ria-selected="false"> {{ __('about_me') }} </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="tw-py-6">
                                    <h4 class="tw-text-lg tw-font-medium tw-leading-[100%] tw-text-[#18191C]"> {{ __('biography') }} </h4>
                                    <p class="tw-text-base tw-text-[#5E6670]" id="candidate-bio"> </p>
                                    <h4 class="tw-text-lg tw-font-medium tw-leading-[100%] tw-text-[#18191C] tw-mb-4"> {{ __('experience') }} </h4>
                                    <ul class="tw-p-0 tw-m-0 tw-list-none ll-experience-list" id="experience-list">
                                        <!-- The experience items will be dynamically added here -->
                                    </ul>
                                    <h4 class="tw-text-lg tw-font-medium tw-leading-[100%] tw-text-[#18191C] tw-mb-4"> {{ __('education') }} </h4>
                                    <ul class="tw-p-0 tw-m-0 tw-list-none ll-experience-list" id="education-list">
                                        <!-- The education items will be dynamically added here -->
                                    </ul>
                                </div>
                                <div class="devider"></div>
                                <div class="social-links">
                                    <h2 class="title"> {{ __('follow_me_social_media') }} </h2>
                                    <div class="social-media">
                                        <ul>
                                            <li>
                                                <a href="#" id="facebook"
                                                    class="bg-primary-50 text-primary-500 plain-button tw-inline-flex tw-justify-center tw-items-center tw-rounded !tw-w-10 !tw-h-10 hover:bg-primary-500 hover:text-primary-50">
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M11.6666 20H7.70126V10.1414H5V6.9316H7.70116V4.64762C7.70116 1.9411 8.89588 0 12.8505 0C13.6869 0 15 0.168134 15 0.168134V3.14858H13.6208C12.2155 3.14858 11.6668 3.5749 11.6668 4.75352V6.9316H14.9474L14.6553 10.1414H11.6667L11.6666 20Z"
                                                            fill="var(--primary-500)"></path>
                                                    </svg>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" id="twitter"
                                                    class="bg-primary-50 text-primary-500 plain-button tw-inline-flex tw-justify-center tw-items-center tw-rounded !tw-w-10 !tw-h-10 hover:bg-primary-500 hover:text-primary-50">
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M6.25472 18.0025C13.7602 18.0025 17.8646 11.8202 17.8646 6.45913C17.8646 6.28355 17.861 6.10878 17.8531 5.93474C18.6517 5.36017 19.3409 4.64876 19.8885 3.83387C19.1573 4.15706 18.3706 4.37457 17.5452 4.47265C18.3877 3.97039 19.0344 3.17581 19.3396 2.22867C18.5386 2.70111 17.6623 3.03416 16.7485 3.21345C16.0039 2.42486 14.9438 1.93164 13.77 1.93164C11.5166 1.93164 9.68928 3.74853 9.68928 5.98813C9.68928 6.30653 9.72514 6.61614 9.79515 6.91321C6.40381 6.74354 3.3966 5.12914 1.38403 2.67436C1.02154 3.2935 0.830883 3.99717 0.831516 4.71351C0.831516 6.12108 1.55187 7.36364 2.64735 8.0906C1.99936 8.07093 1.36559 7.8969 0.799339 7.58316C0.798732 7.6002 0.798732 7.61679 0.798732 7.63499C0.798732 9.59978 2.20524 11.2403 4.07233 11.612C3.72166 11.7069 3.35981 11.7549 2.99637 11.7547C2.7339 11.7547 2.4781 11.729 2.22949 11.6816C2.74898 13.2936 4.25532 14.4666 6.04129 14.4994C4.64469 15.5878 2.88541 16.236 0.973322 16.236C0.648053 16.2363 0.323049 16.2176 0 16.1798C1.80589 17.3307 3.95021 18.0022 6.25492 18.0022"
                                                            fill="var(--primary-500)"></path>
                                                    </svg>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" id="youtube"
                                                    class="bg-primary-50 text-primary-500 plain-button tw-inline-flex tw-justify-center tw-items-center tw-rounded !tw-w-10 !tw-h-10 hover:bg-primary-500 hover:text-primary-50">
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M0 2.66051C0 2.01699 0.225232 1.48611 0.675676 1.06784C1.12612 0.649561 1.71172 0.44043 2.43243 0.44043C3.14029 0.44043 3.71299 0.646337 4.15058 1.05819C4.60102 1.4829 4.82625 2.0363 4.82625 2.71842C4.82625 3.33618 4.60747 3.85097 4.16988 4.26282C3.71944 4.68753 3.12741 4.89989 2.39382 4.89989H2.37452C1.66666 4.89989 1.09396 4.68753 0.656371 4.26282C0.218784 3.83811 0 3.304 0 2.66051ZM0.250965 19.5524V6.65664H4.53668V19.5524H0.250965ZM6.9112 19.5524H11.1969V12.3516C11.1969 11.9012 11.2484 11.5537 11.3514 11.3092C11.5315 10.8716 11.805 10.5015 12.1718 10.1991C12.5386 9.89666 12.9987 9.74545 13.5521 9.74545C14.9936 9.74545 15.7143 10.7171 15.7143 12.6605V19.5524H20V12.1586C20 10.2538 19.5496 8.80915 18.6486 7.8246C17.7477 6.84004 16.5573 6.34776 15.0772 6.34776C13.417 6.34776 12.1236 7.06205 11.1969 8.49062V8.52923H11.1776L11.1969 8.49062V6.65664H6.9112C6.93693 7.06848 6.94981 8.34904 6.94981 10.4983C6.94981 12.6476 6.93693 15.6656 6.9112 19.5524Z"
                                                            fill="var(--primary-500)"></path>
                                                    </svg>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" id="linkedin"
                                                    class="bg-primary-50 text-primary-500 plain-button tw-inline-flex tw-justify-center tw-items-center tw-rounded !tw-w-10 !tw-h-10 hover:bg-primary-500 hover:text-primary-50">
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M10 20C15.5228 20 20 15.5228 20 10C20 4.47715 15.5228 0 10 0C4.47715 0 0 4.47715 0 10C0 15.5228 4.47715 20 10 20Z"
                                                            fill="var(--primary-500)"></path>
                                                        <path class="path2"
                                                            d="M16.6703 10.0004C16.6723 10.1374 16.6553 10.2742 16.6193 10.4064C16.5833 10.5387 16.5288 10.6654 16.4573 10.7824C16.386 10.8994 16.2985 11.0059 16.1975 11.0987C16.0965 11.1914 15.983 11.2694 15.8603 11.3304C15.863 11.3669 15.8653 11.4037 15.8665 11.4404C15.8695 11.5137 15.8695 11.5871 15.8665 11.6604C15.8653 11.6972 15.863 11.7339 15.8603 11.7704C15.8603 14.0104 13.2502 15.8305 10.0302 15.8305C6.81018 15.8305 4.20016 14.0104 4.20016 11.7704C4.19741 11.7339 4.19516 11.6972 4.19391 11.6604C4.1909 11.5871 4.1909 11.5137 4.19391 11.4404C4.19516 11.4037 4.19741 11.3669 4.20016 11.3304C4.00784 11.242 3.83704 11.1129 3.69955 10.952C3.56207 10.791 3.46119 10.6022 3.4039 10.3984C3.34671 10.1946 3.33452 9.98072 3.3682 9.77169C3.40187 9.56267 3.48059 9.36347 3.5989 9.18789C3.71711 9.01235 3.8721 8.86463 4.05311 8.75497C4.23413 8.64532 4.43683 8.57636 4.64716 8.55289C4.85752 8.52931 5.07049 8.55176 5.27131 8.61867C5.47213 8.68558 5.656 8.79536 5.81017 8.94039C6.09638 8.7464 6.39625 8.57336 6.70743 8.42264C7.01868 8.27214 7.34044 8.14413 7.67019 8.04013C7.99994 7.93613 8.33694 7.85638 8.6782 7.80113C9.01945 7.74588 9.36445 7.71563 9.71021 7.71038L10.4502 4.24034C10.4585 4.20009 10.4747 4.16209 10.498 4.12809C10.521 4.09434 10.5507 4.06534 10.585 4.04309C10.6195 4.02084 10.658 4.00559 10.6985 3.99809C10.7387 3.99084 10.7802 3.99159 10.8202 4.00034L13.2702 4.49035C13.5175 4.06559 14.0365 3.88359 14.4948 4.06059C14.953 4.23759 15.215 4.72135 15.1125 5.20185C15.0103 5.68236 14.5738 6.01736 14.0832 5.99211C13.5925 5.96686 13.1927 5.58886 13.1402 5.10035L11.0002 4.65035L10.3502 7.77038C10.6917 7.77763 11.0325 7.80963 11.3695 7.86588C12.0443 7.97838 12.6994 8.1877 13.3145 8.48739C13.6217 8.63689 13.9177 8.80833 14.2002 9.00039C14.4034 8.80534 14.6587 8.67328 14.9353 8.62014C15.2118 8.56681 15.4979 8.59469 15.759 8.70039C16.0201 8.80604 16.2451 8.98499 16.4068 9.21564C16.5685 9.44632 16.66 9.71887 16.6703 10.0004V10.0004ZM6.74618 11.3832C6.79668 11.5044 6.87018 11.6147 6.96318 11.7074C7.05593 11.8004 7.16618 11.8739 7.28744 11.9244C7.4088 11.9746 7.53885 12.0004 7.67019 12.0004C8.07469 12.0004 8.4392 11.7567 8.5942 11.3832C8.74895 11.0094 8.6632 10.5794 8.37719 10.2934C8.09119 10.0074 7.66119 9.92165 7.28744 10.0764C6.91393 10.2314 6.67018 10.5959 6.67018 11.0004C6.67018 11.1317 6.69593 11.2617 6.74618 11.3832V11.3832ZM12.4737 13.7837C12.525 13.7337 12.5542 13.6654 12.5552 13.5939C12.5562 13.5222 12.5285 13.4532 12.4785 13.4019C12.4285 13.3507 12.3602 13.3214 12.2887 13.3204C12.217 13.3194 12.148 13.3472 12.0902 13.3904C11.9399 13.4983 11.7806 13.5932 11.6142 13.6742C11.4478 13.7552 11.2749 13.822 11.0972 13.8739C10.9197 13.9257 10.738 13.9624 10.5542 13.9837C10.3702 14.0049 10.185 14.0104 10.0002 14.0004C9.81571 14.0087 9.63071 14.0012 9.44746 13.9782C9.26416 13.9554 9.08316 13.9169 8.90645 13.8632C8.72945 13.8097 8.5577 13.7412 8.39245 13.6587C8.22719 13.5762 8.06919 13.4797 7.92019 13.3704C7.86844 13.3279 7.80269 13.3062 7.73569 13.3094C7.66869 13.3127 7.60544 13.3409 7.55794 13.3882C7.51069 13.4357 7.48244 13.4989 7.47919 13.5659C7.47594 13.6329 7.49769 13.6987 7.54019 13.7504C7.71594 13.8829 7.90269 13.9999 8.09844 14.1004C8.29419 14.2009 8.49845 14.2842 8.70845 14.3497C8.91845 14.4152 9.1337 14.4624 9.35195 14.4912C9.57021 14.5197 9.79046 14.5294 10.0102 14.5204C10.23 14.5294 10.4502 14.5197 10.6685 14.4912C11.1056 14.4337 11.5296 14.3015 11.922 14.1004C12.1177 13.9999 12.3045 13.8829 12.4802 13.7504L12.4737 13.7837ZM12.2902 12.0804C12.4257 12.0819 12.5602 12.0557 12.6855 12.0037C12.8107 11.9514 12.924 11.8744 13.0185 11.7772C13.113 11.6799 13.1865 11.5644 13.235 11.4377C13.2835 11.3111 13.3057 11.1759 13.3002 11.0404C13.3002 10.6359 13.0565 10.2714 12.683 10.1164C12.3092 9.96165 11.8792 10.0474 11.5932 10.3334C11.3072 10.6194 11.2215 11.0494 11.3762 11.4232C11.5312 11.7967 11.8957 12.0404 12.3002 12.0404L12.2902 12.0804Z"
                                                            fill="white"></path>
                                                    </svg>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" id="pinterest"
                                                    class="bg-primary-50 text-primary-500 plain-button tw-inline-flex tw-justify-center tw-items-center tw-rounded !tw-w-10 !tw-h-10 hover:bg-primary-500 hover:text-primary-50">
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M6.6672 10C6.6672 8.15912 8.15912 6.6664 10 6.6664C11.8409 6.6664 13.3336 8.15912 13.3336 10C13.3336 11.8409 11.8409 13.3336 10 13.3336C8.15912 13.3336 6.6672 11.8409 6.6672 10ZM4.86512 10C4.86512 12.836 7.164 15.1349 10 15.1349C12.836 15.1349 15.1349 12.836 15.1349 10C15.1349 7.164 12.836 4.86512 10 4.86512C7.164 4.86512 4.86512 7.164 4.86512 10ZM14.1382 4.66152C14.1381 4.89886 14.2083 5.13089 14.3401 5.32829C14.4719 5.52568 14.6593 5.67956 14.8785 5.77047C15.0977 5.86138 15.339 5.88524 15.5718 5.83904C15.8046 5.79283 16.0185 5.67862 16.1863 5.51087C16.3542 5.34311 16.4686 5.12934 16.515 4.89658C16.5614 4.66382 16.5377 4.42253 16.447 4.20322C16.3563 3.98392 16.2025 3.79644 16.0052 3.6645C15.808 3.53257 15.576 3.4621 15.3386 3.462H15.3382C15.02 3.46215 14.715 3.58856 14.49 3.81347C14.265 4.03837 14.1384 4.34339 14.1382 4.66152ZM5.96 18.1398C4.98504 18.0954 4.45512 17.933 4.10296 17.7958C3.63608 17.614 3.30296 17.3975 2.95272 17.0478C2.60248 16.698 2.38568 16.3652 2.20472 15.8983C2.06744 15.5463 1.90504 15.0162 1.86072 14.0413C1.81224 12.9872 1.80256 12.6706 1.80256 10.0001C1.80256 7.3296 1.81304 7.01384 1.86072 5.95888C1.90512 4.98392 2.06872 4.45488 2.20472 4.10184C2.38648 3.63496 2.60296 3.30184 2.95272 2.9516C3.30248 2.60136 3.63528 2.38456 4.10296 2.2036C4.45496 2.06632 4.98504 1.90392 5.96 1.8596C7.01408 1.81112 7.33072 1.80144 10 1.80144C12.6693 1.80144 12.9862 1.81192 14.0412 1.8596C15.0162 1.904 15.5452 2.0676 15.8982 2.2036C16.3651 2.38456 16.6982 2.60184 17.0485 2.9516C17.3987 3.30136 17.6147 3.63496 17.7965 4.10184C17.9338 4.45384 18.0962 4.98392 18.1405 5.95888C18.189 7.01384 18.1986 7.3296 18.1986 10.0001C18.1986 12.6706 18.189 12.9863 18.1405 14.0413C18.0961 15.0162 17.9329 15.5462 17.7965 15.8983C17.6147 16.3652 17.3982 16.6983 17.0485 17.0478C16.6987 17.3972 16.3651 17.614 15.8982 17.7958C15.5462 17.933 15.0162 18.0954 14.0412 18.1398C12.9871 18.1882 12.6705 18.1979 10 18.1979C7.32952 18.1979 7.01376 18.1882 5.96 18.1398ZM5.8772 0.06056C4.81264 0.10904 4.0852 0.27784 3.44992 0.52504C2.792 0.78032 2.23504 1.1228 1.67848 1.67848C1.12192 2.23416 0.78032 2.792 0.52504 3.44992C0.27784 4.0856 0.10904 4.81264 0.06056 5.8772C0.01128 6.94344 0 7.28432 0 10C0 12.7157 0.01128 13.0566 0.06056 14.1228C0.10904 15.1874 0.27784 15.9144 0.52504 16.5501C0.78032 17.2076 1.122 17.7661 1.67848 18.3215C2.23496 18.877 2.792 19.219 3.44992 19.475C4.0864 19.7222 4.81264 19.891 5.8772 19.9394C6.944 19.9879 7.28432 20 10 20C12.7157 20 13.0566 19.9887 14.1228 19.9394C15.1874 19.891 15.9144 19.7222 16.5501 19.475C17.2076 19.219 17.765 18.8772 18.3215 18.3215C18.8781 17.7658 19.219 17.2076 19.475 16.5501C19.7222 15.9144 19.8918 15.1874 19.9394 14.1228C19.9879 13.0558 19.9992 12.7157 19.9992 10C19.9992 7.28432 19.9879 6.94344 19.9394 5.8772C19.891 4.81256 19.7222 4.0852 19.475 3.44992C19.219 2.7924 18.8772 2.23504 18.3215 1.67848C17.7658 1.12192 17.2076 0.78032 16.5509 0.52504C15.9144 0.27784 15.1874 0.10824 14.1236 0.06056C13.0574 0.01208 12.7165 0 10.0008 0C7.28512 0 6.944 0.01128 5.8772 0.06056Z"
                                                            fill="var(--primary-500)"></path>
                                                        <path
                                                            d="M6.6672 10C6.6672 8.15912 8.15912 6.6664 10 6.6664C11.8409 6.6664 13.3336 8.15912 13.3336 10C13.3336 11.8409 11.8409 13.3336 10 13.3336C8.15912 13.3336 6.6672 11.8409 6.6672 10ZM4.86512 10C4.86512 12.836 7.164 15.1349 10 15.1349C12.836 15.1349 15.1349 12.836 15.1349 10C15.1349 7.164 12.836 4.86512 10 4.86512C7.164 4.86512 4.86512 7.164 4.86512 10ZM14.1382 4.66152C14.1381 4.89886 14.2083 5.13089 14.3401 5.32829C14.4719 5.52568 14.6593 5.67956 14.8785 5.77047C15.0977 5.86138 15.339 5.88524 15.5718 5.83904C15.8046 5.79283 16.0185 5.67862 16.1863 5.51087C16.3542 5.34311 16.4686 5.12934 16.515 4.89658C16.5614 4.66382 16.5377 4.42253 16.447 4.20322C16.3563 3.98392 16.2025 3.79644 16.0052 3.6645C15.808 3.53257 15.576 3.4621 15.3386 3.462H15.3382C15.02 3.46215 14.715 3.58856 14.49 3.81347C14.265 4.03837 14.1384 4.34339 14.1382 4.66152ZM5.96 18.1398C4.98504 18.0954 4.45512 17.933 4.10296 17.7958C3.63608 17.614 3.30296 17.3975 2.95272 17.0478C2.60248 16.698 2.38568 16.3652 2.20472 15.8983C2.06744 15.5463 1.90504 15.0162 1.86072 14.0413C1.81224 12.9872 1.80256 12.6706 1.80256 10.0001C1.80256 7.3296 1.81304 7.01384 1.86072 5.95888C1.90512 4.98392 2.06872 4.45488 2.20472 4.10184C2.38648 3.63496 2.60296 3.30184 2.95272 2.9516C3.30248 2.60136 3.63528 2.38456 4.10296 2.2036C4.45496 2.06632 4.98504 1.90392 5.96 1.8596C7.01408 1.81112 7.33072 1.80144 10 1.80144C12.6693 1.80144 12.9862 1.81192 14.0412 1.8596C15.0162 1.904 15.5452 2.0676 15.8982 2.2036C16.3651 2.38456 16.6982 2.60184 17.0485 2.9516C17.3987 3.30136 17.6147 3.63496 17.7965 4.10184C17.9338 4.45384 18.0962 4.98392 18.1405 5.95888C18.189 7.01384 18.1986 7.3296 18.1986 10.0001C18.1986 12.6706 18.189 12.9863 18.1405 14.0413C18.0961 15.0162 17.9329 15.5462 17.7965 15.8983C17.6147 16.3652 17.3982 16.6983 17.0485 17.0478C16.6987 17.3972 16.3651 17.614 15.8982 17.7958C15.5462 17.933 15.0162 18.0954 14.0412 18.1398C12.9871 18.1882 12.6705 18.1979 10 18.1979C7.32952 18.1979 7.01376 18.1882 5.96 18.1398ZM5.8772 0.06056C4.81264 0.10904 4.0852 0.27784 3.44992 0.52504C2.792 0.78032 2.23504 1.1228 1.67848 1.67848C1.12192 2.23416 0.78032 2.792 0.52504 3.44992C0.27784 4.0856 0.10904 4.81264 0.06056 5.8772C0.01128 6.94344 0 7.28432 0 10C0 12.7157 0.01128 13.0566 0.06056 14.1228C0.10904 15.1874 0.27784 15.9144 0.52504 16.5501C0.78032 17.2076 1.122 17.7661 1.67848 18.3215C2.23496 18.877 2.792 19.219 3.44992 19.475C4.0864 19.7222 4.81264 19.891 5.8772 19.9394C6.944 19.9879 7.28432 20 10 20C12.7157 20 13.0566 19.9887 14.1228 19.9394C15.1874 19.891 15.9144 19.7222 16.5501 19.475C17.2076 19.219 17.765 18.8772 18.3215 18.3215C18.8781 17.7658 19.219 17.2076 19.475 16.5501C19.7222 15.9144 19.8918 15.1874 19.9394 14.1228C19.9879 13.0558 19.9992 12.7157 19.9992 10C19.9992 7.28432 19.9879 6.94344 19.9394 5.8772C19.891 4.81256 19.7222 4.0852 19.475 3.44992C19.219 2.7924 18.8772 2.23504 18.3215 1.67848C17.7658 1.12192 17.2076 0.78032 16.5509 0.52504C15.9144 0.27784 15.1874 0.10824 14.1236 0.06056C13.0574 0.01208 12.7165 0 10.0008 0C7.28512 0 6.944 0.01128 5.8772 0.06056Z"
                                                            fill="var(--primary-500)"></path>
                                                    </svg>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" id="github"
                                                    class="bg-primary-50 text-primary-500 plain-button tw-inline-flex tw-justify-center tw-items-center tw-rounded !tw-w-10 !tw-h-10 hover:bg-primary-500 hover:text-primary-50">
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M10.4433 17.041L6.34002 16.9644C5.01143 16.9377 3.67958 16.991 2.3771 16.7145C0.395646 16.3015 0.25528 14.2762 0.108384 12.5774C-0.0940053 10.189 -0.015661 7.75739 0.366267 5.38902C0.581714 4.05994 1.43044 3.26716 2.74271 3.18055C7.17243 2.86743 11.6315 2.90407 16.0514 3.05064C16.5182 3.06396 16.9883 3.13725 17.4486 3.22052C19.7206 3.62691 19.7761 5.92199 19.923 7.85399C20.0699 9.80597 20.0078 11.7679 19.7271 13.7066C19.5019 15.3122 19.071 16.6579 17.2527 16.7878C14.9742 16.9577 12.7479 17.0943 10.4629 17.051C10.4629 17.041 10.4498 17.041 10.4433 17.041ZM8.03095 12.9771C9.74799 11.9711 11.4324 10.9818 13.1396 9.98251C11.4193 8.97654 9.7382 7.98723 8.03095 6.98792V12.9771Z"
                                                            fill="var(--primary-500)"></path>
                                                    </svg>
                                                </a>
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="col-lg-4">
                        <div class="sidebar-widget">
                            <div class="row">
                                <div class="col-md-12">
                                    <h2 class="title">{{ __('skills') }}</h2>
                                    <div class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-6" id="candidate_skills"></div>
                                </div>
                                <div class="col-md-12">
                                    <h2 class="title">{{ __('languages') }}</h2>
                                    <div class="tw-flex tw-flex-wrap tw-gap-2" id="candidate_languages"></div>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar-widget">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="icon-box">
                                        <div class="icon-img">
                                            <x-svg.birth-date-icon />
                                        </div>
                                        <h3 class="sub-title text-uppercase">{{ __('date_of_birth') }}</h3>
                                        <h2 class="title" id="candidate_birth_date">N/A</h2>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="icon-box">
                                        <div class="icon-img">
                                            <x-svg.fold-icon />
                                        </div>
                                        <h3 class="sub-title">{{ __('country') }}</h3>
                                        <h2 class="title" id="candidate_nationality">N/A</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="icon-box">
                                        <div class="icon-img">
                                            <x-svg.board-icon />
                                        </div>
                                        <h3 class="sub-title">{{ __('marital_status') }}</h3>
                                        <h2 class="title" id="candidate_marital_status">N/A
                                        </h2>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="icon-box">
                                        <div class="icon-img">
                                            <x-svg.gender />
                                        </div>
                                        <h3 class="sub-title">{{ __('gender') }}</h3>
                                        <h2 class="title" id="candidate_gender">N/A</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="icon-box">
                                        <div class="icon-img">
                                            <i class="ph-suitcase-simple f-size-24 text-primary-500"></i>
                                        </div>
                                        <h3 class="sub-title">{{ __('experience') }}</h3>
                                        <h2 class="title" id="candidate_experience">N/A</h2>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="icon-box">
                                        <div class="icon-img">
                                            <x-svg.education-cap-icon />
                                        </div>
                                        <h3 class="sub-title">{{ __('education') }}</h3>
                                        <h2 class="title" id="candidate_education">N/A</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar-widget">
                            <div class="contact">
                                <h2 class="title">{{ __('contact_information') }}</h2>
                                <div class="d-none" id="contact_whatsapp_part">
                                    <a target="_blank" href="https://wa.me" class="whatsapp-button"
                                        id="contact_whatsapp">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M11.4182 0.0138947C5.07986 0.315691 0.077995 5.59432 0.0972232 11.9399C0.103078 13.8726 0.569664 15.6968 1.39275 17.3086L0.129022 23.4429C0.0606609 23.7747 0.359933 24.0652 0.689568 23.9872L6.70043 22.5631C8.24472 23.3323 9.97927 23.7766 11.815 23.8046C18.2935 23.9036 23.6953 18.7596 23.8972 12.2836C24.1137 5.34172 18.3844 -0.317864 11.4182 0.0138947ZM18.5905 18.4934C16.8302 20.2537 14.4896 21.2231 12.0002 21.2231C10.5425 21.2231 9.14695 20.8961 7.85212 20.2511L7.01503 19.8341L3.32975 20.7072L4.10548 16.9415L3.69308 16.134C3.02089 14.8175 2.68006 13.394 2.68006 11.903C2.68006 9.4135 3.64951 7.07306 5.40984 5.31267C7.15445 3.56807 9.53312 2.58284 12.0004 2.58284C14.4898 2.5829 16.8302 3.55234 18.5905 5.31261C20.3509 7.07294 21.3203 9.41339 21.3204 11.9028C21.3203 14.3701 20.3351 16.7488 18.5905 18.4934Z"
                                                fill="#00C236"></path>
                                            <path
                                                d="M17.777 14.4844L15.4714 13.8224C15.1683 13.7353 14.8419 13.8213 14.621 14.0464L14.0572 14.6208C13.8194 14.863 13.4587 14.9409 13.144 14.8136C12.0534 14.3722 9.75907 12.3323 9.17316 11.3119C9.00412 11.0176 9.03207 10.6496 9.23962 10.381L9.73187 9.74418C9.92472 9.49467 9.96542 9.15958 9.83788 8.87116L8.86786 6.67724C8.63551 6.15176 7.96402 5.9988 7.52532 6.36982C6.88184 6.91407 6.11833 7.74111 6.02552 8.65735C5.86188 10.2728 6.55467 12.3091 9.17448 14.7543C12.2011 17.5791 14.6247 17.9523 16.2028 17.57C17.0978 17.3532 17.8131 16.4841 18.2646 15.7725C18.5723 15.2873 18.3293 14.643 17.777 14.4844Z"
                                                fill="#00C236"></path>
                                        </svg>
                                        <span style="margin-left: 5px;">WhatsApp</span>
                                    </a>
                                    <div class="devider"></div>
                                </div>
                                <div class="contact-icon-box">
                                    <div class="icon-img">
                                        <x-svg.globe-icon />
                                    </div>
                                    <div class="info">
                                        <h3 class="subtitle">{{ __('website') }}</h3>
                                        <a target="_blank" class="title" id="candidate_website">N/A</a>
                                    </div>
                                </div>
                                <div class="devider"></div>
                                <div class="contact-icon-box">
                                    <div class="icon-img">
                                        <x-svg.location2-icon />
                                    </div>
                                    <div class="info">
                                        <h3 class="subtitle">{{ __('location') }}</h3>
                                        <h2 class="title" id="candidate_location">N/A</h2>
                                    </div>
                                </div>
                                <div class="devider"></div>
                                <div class="contact-icon-box">
                                    <div class="icon-img">
                                        <x-svg.telephone-icon />
                                    </div>
                                    <div class="info">
                                        <h3 class="subtitle">{{ __('phone') }}</h3>
                                        <h2 class="title" id="candidate_phone">N/A</h2>
                                        <h3 class="subtitle">{{ __('secondary_phone') }}</h3>
                                        <h2 class="title" id="candidate_seconday_phone">N/A
                                        </h2>
                                    </div>
                                </div>
                                <div class="devider"></div>
                                <div class="contact-icon-box">
                                    <div class="icon-img">
                                        <x-svg.envelope-icon width="32" height="32" />
                                    </div>
                                    <div class="info">
                                        <h3 class="subtitle">{{ __('email_address') }}</h3>
                                        <h2 class="title" id="contact_info_email">N/A</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-wrapper !tw-max-w-[648px]">
        <div class="modal-content tw-w-[648px]">
            <div class="chat-top tw-rounded-tl-xl tw-rounded-tr-xl tw-p-6 tw-bg-[#F1F2F4]">
                <h2 class="tw-text-base tw-font-semibold tw-text-[#18191C] tw-mt-0 tw-mb-3">
                    {{ __('message_candidate') }}</h2>
                <div class="tw-flex tw-gap-3 tw-mb-3 tw-items-center">
                    <img class="tw-w-[48px] tw-h-[48px] tw-rounded-full tw-object-cover" src="{{ asset('frontend') }}/assets/images/all-img/candidate-profile.png"
                        alt="candidate img" id="message_candidate_image">
                    <div>
                        <h3 class="tw-mt-0 tw-mb-1 tw-font-medium tw-text-sm tw-text-[#18191C]" id="message_candidate_name">
                            N/A
                        </h3>
                        <p class="tw-mb-0 tw-text-sm tw-text-[#5E6670]" id="message_candidate_position">
                            No Position
                        </p>
                    </div>
                </div>
            </div>
            <div class="chat-bottom tw-p-6 tw-bg-white tw-rounded-bl-xl tw-rounded-br-xl">
                <form action="{{ route('company.message.candidate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="candidate_id" id="message_candidate_id">
                    <input type="hidden" name="request_type" value="non_api">

                    <div class="form-group">
                        <label for="chat"
                            class="tw-text-sm tw-text-[#18191C] tw-mb-2">{{ __('your_message') }}</label>
                        <textarea required id="chat" placeholder="{{ __('write_your_message') }}"
                        rows="5" name="message"></textarea>
                        <span class="invalid-feedback" role="alert"></span>
                    </div>
                    <div class="tw-mt-6 tw-flex tw-justify-between tw-items-center">
                        <button type="button" class="btn-close" data-dismiss="modal"></button>
                        <button type="button"
                            class="btn tw-py-[11px] tw-px-[23px] tw-text-base tw-capitalize tw-font-semibold tw-text-[#474C54] tw-border tw-border-[#E4E5E8]">{{ __('cancel') }}</button>
                        <button type="submit"
                            class="btn tw-py-[11px] tw-text-base tw-capitalize tw-font-semibold tw-text-white tw-bg-[#0066CC] hover:tw-bg-transparent tw-border-black tw-px-[23px]">
                            <span>{{ __('send_message') }}</span>
                        </button>
                    </div>
                </form>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
    </div>
</div>
