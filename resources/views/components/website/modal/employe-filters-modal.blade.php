<div class="modal fade" id="companyFiltersModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div
        class="modal-dialog  modal-wrapper tw-mx-0 md:tw-max-w-[352px] tw-w-[90%] tw-my-0 tw-absolute tw-top-0 tw-bootom-0 tw-left-0">
        <div class="modal-content tw-rounded-none tw-relative tw-min-h-screen tw-max-h-screen">
            <div class="tw-h-screen tw-overflow-x-hidden tw-overflow-y-auto tw-pb-24">
                <div class="tw-p-5">
                    <div class="tw-flex tw-justify-between items-center">
                        <h2 class="tw-text-[#18191C] tw-text-xl tw-font-medium tw-mb-0">Filter</h2>
                        <button type="button" class="tw-p-0 tw-border-0 tw-bg-transparent" data-bs-dismiss="modal"
                            aria-label="Close">
                            <x-svg.close-icon />
                        </button>
                    </div>
                </div>
                <div class="tw-px-5">
                    <h2 class="tw-text-[#0A65CC] tw-text-sm tw-font-medium">Industry</h2>
                    <ul class="tw-flex tw-flex-col tw-list-none tw-p-0 tw-m-0">
                        <li
                            class="tw-px-3 tw-py-2 tw-bg-transparent cursor-pointer tw-rounded-[4px] hover:tw-bg-[#E7F0FA] hover:tw-text-[#0A65CC] tw-text-[#18191C] tw-text-sm tw-font-medium">
                            All Category</li>
                        <li
                            class="tw-px-3 tw-py-2 tw-bg-transparent cursor-pointer tw-rounded-[4px] hover:tw-bg-[#E7F0FA] hover:tw-text-[#0A65CC] tw-text-[#18191C] tw-text-sm tw-font-medium">
                            Developments</li>
                        <li
                            class="tw-px-3 tw-py-2 tw-bg-transparent cursor-pointer tw-rounded-[4px] hover:tw-bg-[#E7F0FA] hover:tw-text-[#0A65CC] tw-text-[#18191C] tw-text-sm tw-font-medium">
                            Business</li>
                        <li
                            class="tw-px-3 tw-py-2 tw-bg-transparent cursor-pointer tw-rounded-[4px] hover:tw-bg-[#E7F0FA] hover:tw-text-[#0A65CC] tw-text-[#18191C] tw-text-sm tw-font-medium">
                            Finance & Accounting</li>
                        <li
                            class="tw-px-3 tw-py-2 tw-bg-transparent cursor-pointer tw-rounded-[4px] hover:tw-bg-[#E7F0FA] hover:tw-text-[#0A65CC] tw-text-[#18191C] tw-text-sm tw-font-medium">
                            IT & Software</li>
                        <li
                            class="tw-px-3 tw-py-2 tw-bg-transparent cursor-pointer tw-rounded-[4px] hover:tw-bg-[#E7F0FA] hover:tw-text-[#0A65CC] tw-text-[#18191C] tw-text-sm tw-font-medium">
                            Office Productivity</li>
                        <li
                            class="tw-px-3 tw-py-2 tw-bg-transparent cursor-pointer tw-rounded-[4px] hover:tw-bg-[#E7F0FA] hover:tw-text-[#0A65CC] tw-text-[#18191C] tw-text-sm tw-font-medium">
                            Personal Development</li>
                        <li
                            class="tw-px-3 tw-py-2 tw-bg-transparent cursor-pointer tw-rounded-[4px] hover:tw-bg-[#E7F0FA] hover:tw-text-[#0A65CC] tw-text-[#18191C] tw-text-sm tw-font-medium">
                            Design</li>
                        <li
                            class="tw-px-3 tw-py-2 tw-bg-transparent cursor-pointer tw-rounded-[4px] hover:tw-bg-[#E7F0FA] hover:tw-text-[#0A65CC] tw-text-[#18191C] tw-text-sm tw-font-medium">
                            Marketing/li>
                        <li
                            class="tw-px-3 tw-py-2 tw-bg-transparent cursor-pointer tw-rounded-[4px] hover:tw-bg-[#E7F0FA] hover:tw-text-[#0A65CC] tw-text-[#18191C] tw-text-sm tw-font-medium">
                            Photography & Video</li>
                    </ul>
                </div>
                <hr class="tw-bg-[#E4E5E8] tw-m-0">
                <div class="tw-p-5">
                    <h2 class="tw-text-sm tw-text-[#0A65CC] tw-mb-4 tw-font-medium">Job Type</h2>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="checkbox" id="fullTime" class="tw-scale-125" name="">
                        <label for="fullTime" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">Full Time</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="checkbox" id="partTime" class="tw-scale-125" name="">
                        <label for="partTime" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">Part Time</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="checkbox" id="internship" class="tw-scale-125" name="">
                        <label for="internship" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">Internship</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="checkbox" id="temporary" class="tw-scale-125" name="">
                        <label for="temporary" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">Temporary</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="checkbox" id="contractBase" class="tw-scale-125" name="">
                        <label for="contractBase" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">Contract Base</label>
                    </div>
                </div>
                <hr class="tw-bg-[#E4E5E8] tw-m-0">
                <div class="tw-p-5">
                    <h2 class="tw-text-sm tw-text-[#0A65CC] tw-mb-4 tw-font-medium">Team Szie</h2>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="radio" id="allTeam" class="tw-scale-125" name="jobType">
                        <label for="allTeam" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">All Team Size</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="radio" id="1Members" class="tw-scale-125" name="jobType">
                        <label for="1Members" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">1-10 members</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="radio" id="10Members" class="tw-scale-125" name="jobType">
                        <label for="10Members" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">11 to 20 members</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="radio" id="20Members" class="tw-scale-125" name="jobType">
                        <label for="20Members" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">21-50 Members</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="radio" id="50Members" class="tw-scale-125" name="jobType">
                        <label for="50Members" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">51 to 100 members</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="radio" id="100Members" class="tw-scale-125" name="jobType">
                        <label for="100Members" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">101 to 200 members</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="radio" id="200Members" class="tw-scale-125" name="jobType">
                        <label for="200Members" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">201-500 Members</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input type="radio" id="500Members" class="tw-scale-125" name="jobType">
                        <label for="500Members" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">500+ Members</label>
                    </div>
                </div>
            </div>
            <div
                class="tw-absolute tw-bottom-0 tw-left-0 tw-right-0 tw-p-5 tw-bg-white tw-z-50 tw-flex tw-justify-end tw-items-center tw-mt-3">
                <div>
                    <button type="submit" class="btn btn-primary tw-inline-block">{{ __('apply_filter') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
