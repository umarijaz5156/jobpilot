<div class="card">
    <div class="card-header">
        <h3 class="card-title line-height-36">
            {{ $title }}
        </h3>
    </div>
    <div class="card-body table-responsive p-0">
        <div class="row">
            <div class="col-sm-12">
                <table class="ll-table table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>{{ __('job') }}</th>
                            <th>{{ __('category') }}/{{ __('role') }}</th>
                            <th>{{ __('salary') }}</th>
                            <th>{{ __('deadline') }}</th>
                            <th style="text-align: right">{{ __('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($jobs->count() > 0)
                            @foreach ($jobs as $job)
                                <tr>
                                    <td tabindex="0">
                                        <a href="{{ route('job.show', $job->id) }}" class="company">
                                            <img src="{{ asset($job->company->logo_url) }}" alt="image">
                                            <div>
                                                <h2>{{ $job->title }}</h2>
                                                <p>
                                                    <span>{{ $job->company ? $job->company->user->name : '' }}</span>
                                                    <span>·</span>
                                                    <span>{{ $job->job_type->name }}</span>
                                                    @if ($job->is_remote)
                                                        <span>·</span>
                                                        <span>{{ __('remote') }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </a>
                                    </td>
                                    <td tabindex="0">
                                        <div class="category">
                                            <x-svg.table-layer />
                                            <div>
                                                <h3>{{ $job->category->name ?? '' }}</h3>
                                                <p>{{ $job->role->name ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td tabindex="0">
                                        <div class="category">
                                            <x-svg.table-money />
                                            <div>
                                                @if ($job->salary_mode == 'range')
                                                    <h3>{{ getFormattedNumber($job->min_salary) }} -
                                                        {{ getFormattedNumber($job->max_salary) }}
                                                        {{ currentCurrencyCode() }}</h3>
                                                @else
                                                    <h3>{{ $job->custom_salary }}</h3>
                                                @endif
                                                <p>{{ $job->salary_type->name }} </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td tabindex="0">
                                        {{ date('j F, Y', strtotime($job->deadline)) }}
                                    </td>
                                    <td style="text-align: right">
                                        <a data-toggle="tooltip" data-placement="top" title="{{ __('details') }}"
                                            href="{{ route('job.show', $job->id) }}" style="margin-right: 0px"
                                            class="btn ll-btn ll-border-none">{{ __('view_details') }}
                                            <x-svg.table-btn-arrow />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">{{ __('no_data_found') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
