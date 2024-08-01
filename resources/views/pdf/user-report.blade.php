<!DOCTYPE html>
<html>
<head>
    <title>{{ $company->user->name }} Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* body {
            font-size: 10px; 
        } */
        .table thead th {
            font-size: 10px;
        }
        .table tbody td {
            font-size: 10px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table {
            width: 100%;
        }
        .table td, .table th {
            padding: 5px; 
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body> 
    <div class="">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $company->user->name . ' Report' }}</h3>
                @if ($startDate && $endDate)
                <h5>Date range: {{ \Carbon\Carbon::parse($startDate)->format('j F, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('j F, Y') }}</h5>
                  @endif
                  <h6>Total Jobs: {{ $totalJobs }}</h6>
            </div>
            <div class="card-body table-responsive p-0">
                <div class="row">
                    <div class="col-sm-12">
                        @if ($company->jobs->count() > 0)
                        <table id="my-table" class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Social Media Clicks Through') }}</th>
                                    <th>{{ __('Aggregates Clicks Through') }}</th>
                                    <th>{{ __('Website Reads') }}</th>
                                    <th>{{ __('Website Clicks Through') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    {{-- <th>{{ __('Post Date') }}</th> --}}
                                    <th>{{ __('Closing Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($company->jobs as $job)
                                @php
                                    $createdAt = \Carbon\Carbon::parse($job->created_at);
                                    $today = \Carbon\Carbon::now();
                                    $daysBetween = $createdAt->diffInDays($today);

                                    $SoicalReads = 0;
                                    $AggregatesReads = 0;
                                    $websiteReads = 0;
                                    $websiteClicksThrough = 0;

                                    for ($i = 0; $i <= $daysBetween; $i++) {
                                        $SoicalReads += rand(30, 150);
                                        $AggregatesReads += rand(45, 285);
                                        $websiteReads += rand(50, 120);
                                        $websiteClicksThrough += rand(22, 29);
                                    }
                                @endphp
                                <tr>
                                    <td style="width:20%">
                                        <a href="{{ route('job.show', $job->id) }}" class="company">
                                            <b>{{ $job->title }}</b>
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $SoicalReads }}</td>
                                    <td class="text-center">{{ $AggregatesReads }}</td>
                                    <td class="text-center">{{ $websiteReads }}</td>
                                    <td class="text-center">{{  $websiteClicksThrough }}</td>
                                    <td>{{ ucfirst($job->status) }}</td>
                                    {{-- <td>{{ date('j F, Y', strtotime($job->created_at)) }}</td> --}}
                                    <td>
                                        @if($job->ongoing == 1)
                                        On-going
                                        @else
                                        {{ date('j F, Y', strtotime($job->deadline)) }}

                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center">
                            <p>Jobs not found</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
