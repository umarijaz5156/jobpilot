@extends('frontend.layouts.app')

@section('title')
    {{ __('pending_edited_jobs') }}
@endsection
@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.company.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right">
                        <div class="dashboard-right-header rt-mb-32 mt-5">
                            <div class="left-text m-0">
                                <h3 class="f-size-18 lh-1 m-0">
                                    {{ __('pending_edited_jobs') }}
                                    <span class="text-gray-400">({{ $myJobs->total() }})</span>
                                </h3>
                            </div>
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>
                        <div class="db-job-card-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>{{ __('job') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('applications') }}</th>
                                        <th>{{ __('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($myJobs->count() > 0)
                                        @foreach ($myJobs as $job)
                                            <tr>
                                                <td>
                                                    <div class="iconbox-content">
                                                        <div class="post-info2">
                                                            <div class="post-main-title">
                                                                <a href="{{ route('website.job.details', $job->slug) }}"
                                                                    class="text-gray-900 f-size-16  ft-wt-5">
                                                                    {{ $job->title }}
                                                                </a>
                                                            </div>
                                                            <div class="body-font-4 text-gray-600 pt-2">
                                                                <span class="info-tools rt-mr-8">
                                                                    {{ ucfirst($job->job_type->name) }}
                                                                </span>
                                                                <span class="info-tools">
                                                                    {{ $job->days_remaining }}
                                                                    {{ __('remaining') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($job->status == 'active')
                                                        <div class="text-success-500 ft-wt-5 d-flex align-items-center">
                                                            <i class="ph-check-circle f-size-18 mt-1 rt-mr-4"></i>
                                                            {{ __('active') }}
                                                        </div>
                                                    @elseif ($job->status == 'pending')
                                                        <div class="text-primary-500 ft-wt-5 d-flex align-items-center">
                                                            <i class="ph-hourglass f-size-18 mt-1 rt-mr-4"></i>
                                                            {{ __('pending') }}
                                                        </div>
                                                    @else
                                                        <div class="text-danger-500 ft-wt-5 d-flex align-items-center">
                                                            <i class="ph-x-circle f-size-18 mt-1 rt-mr-4"></i>
                                                            {{ __('job_expire') }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ph-users f-size-20 rt-mr-4"></i>
                                                        {{ $job->applied_jobs_count }} {{ __('applications') }}
                                                    </div>

                                                </td>
                                                <td>
                                                    <div class="db-job-btn-wrap d-flex justify-content-end">
                                                        <a href="{{ route('company.job.application', ['job' => $job->id]) }}"
                                                            class="btn bg-gray-50 text-primary-500 rt-mr-8">
                                                            <span class="button-text">
                                                                {{ __('view_applications') }}
                                                            </span>
                                                        </a>
                                                        <button type="button" class="btn btn-icon" id="dropdownMenuButton5"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M12 13.125C12.6213 13.125 13.125 12.6213 13.125 12C13.125 11.3787 12.6213 10.875 12 10.875C11.3787 10.875 10.875 11.3787 10.875 12C10.875 12.6213 11.3787 13.125 12 13.125Z"
                                                                    fill="#767F8C" stroke="#767F8C" />
                                                                <path
                                                                    d="M12 6.65039C12.6213 6.65039 13.125 6.14671 13.125 5.52539C13.125 4.90407 12.6213 4.40039 12 4.40039C11.3787 4.40039 10.875 4.90407 10.875 5.52539C10.875 6.14671 11.3787 6.65039 12 6.65039Z"
                                                                    fill="#767F8C" stroke="#767F8C" />
                                                                <path
                                                                    d="M12 19.6094C12.6213 19.6094 13.125 19.1057 13.125 18.4844C13.125 17.8631 12.6213 17.3594 12 17.3594C11.3787 17.3594 10.875 17.8631 10.875 18.4844C10.875 19.1057 11.3787 19.6094 12 19.6094Z"
                                                                    fill="#767F8C" stroke="#767F8C" />
                                                            </svg>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end company-dashboard-dropdown"
                                                            aria-labelledby="dropdownMenuButton5">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('website.job.details', $job->slug) }}">
                                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M10 3.54102C3.75 3.54102 1.25 9.99996 1.25 9.99996C1.25 9.99996 3.75 16.4577 10 16.4577C16.25 16.4577 18.75 9.99996 18.75 9.99996C18.75 9.99996 16.25 3.54102 10 3.54102Z"
                                                                            stroke="var(--primary-500)" stroke-width="1.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                        <path
                                                                            d="M10 13.125C11.7259 13.125 13.125 11.7259 13.125 10C13.125 8.27411 11.7259 6.875 10 6.875C8.27411 6.875 6.875 8.27411 6.875 10C6.875 11.7259 8.27411 13.125 10 13.125Z"
                                                                            stroke="var(--primary-500)" stroke-width="1.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                    {{ __('view_details') }}
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ route('company.job.edit', $job->slug) }}"
                                                                    class="dropdown-item">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                                        height="20" fill="#000000" viewBox="0 0 256 256">
                                                                        <rect width="256" height="256"
                                                                            fill="none"></rect>
                                                                        <path
                                                                            d="M92.7,216H48a8,8,0,0,1-8-8V163.3a7.9,7.9,0,0,1,2.3-5.6l120-120a8,8,0,0,1,11.4,0l44.6,44.6a8,8,0,0,1,0,11.4l-120,120A7.9,7.9,0,0,1,92.7,216Z"
                                                                            fill="none" stroke="#000000"
                                                                            stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="16"></path>
                                                                        <line x1="136" y1="64"
                                                                            x2="192" y2="120" fill="none"
                                                                            stroke="#000000" stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="16">
                                                                        </line>
                                                                    </svg>
                                                                    {{ __('edit') }}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <x-website.not-found />
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="rt-pt-30">
                            @if ($myJobs->total() > $myJobs->count())
                                <nav>
                                    {{ $myJobs->links('vendor.pagination.frontend') }}
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboard-footer text-center body-font-4 text-gray-500">
                <x-website.footer-copyright />
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('.delete').on('click', function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `{{ __('are_you_sure_want_to_delete_this_item') }}`,
                    text: "{{ __('if_you_delete_this') }}, {{ __('it_will_be_gone_forever') }}",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>

    <script>
        $('#status-filter').on('change', function() {
            this.submit();
        })
    </script>
@endsection
