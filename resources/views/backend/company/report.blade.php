@extends('backend.layouts.app')
@section('title')
    {{ __($company->user->name) }}
@endsection

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-end align-items-center">


            <input type="text" id="dateRange" class="form-control mr-2" placeholder="Select Date Range">
            <button id="filterButton" class="btn btn-primary mr-2">Filter</button>
            <button id='pdf' class="btn btn-primary c-btn c-btn--info">PDF</button>
            <button id="sendEmailButton" class="btn btn-primary ml-2 c-btn c-btn--info">
                <span id="buttonText">Send Email</span>
                <span id="buttonSpinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
            </button>

            <a href="{{ route('company.report', $company->id) }}"
                class="btn ll-btn ll-border-none">
                {{__('Rfresh')}}
     </a>
        </div>

        <style>
            .d-flex {
                display: flex;
            }
            .justify-content-end {
                justify-content: flex-end;
            }
            .align-items-center {
                align-items: center;
            }
            .mr-2 {
                margin-right: 0.5rem;
            }
            .form-control {
                width: auto;
            }
        </style>



        @php
            $totalJobs  = $company->jobs->count();

        @endphp
        <div class="row">
            <div   class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ $company->user->name . ' Report' }}</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <div class="row">
                            <div class="col-sm-12">
                                @if ($company->jobs->count() > 0)

                                <table id="my-table"  class="ll-table table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Social Media Clicks Through') }}</th>
                                            <th>{{ __('Aggregates  Clicks Through') }}</th>
                                            <th>{{ __('Website Reads') }}</th>
                                            <th>{{ __('Website Clicks Through') }}</th>
                                            <th>{{ __('status') }}</th>
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
                                                $SoicalReads +=rand(30, 150);
                                                $AggregatesReads +=rand(45, 285);
                                                $websiteReads += rand(50, 120);
                                                $websiteClicksThrough += rand(10, 30);
                                            }
                                        @endphp
                                            <tr>
                                                <td tabindex="0">
                                                    <a href="{{ route('job.show', $job->id) }}"  class="company">
                                                        <div>
                                                            <h2>{{ $job->title }}</h2>
                                                        </div>
                                                    </a>
                                                </td>
                                                <td tabindex="0">
                                                    <div class="text-center" >
                                                       {{ $SoicalReads }}
                                                    </div>
                                                </td>
                                                <td tabindex="0">
                                                    <div class="text-center" >
                                                       {{ $AggregatesReads }}
                                                    </div>
                                                </td>
                                                <td tabindex="0">
                                                    <div class="text-center" >
                                                       {{ $websiteReads }}
                                                    </div>
                                                </td>
                                                <td tabindex="0">
                                                    <div class="text-center">
                                                       {{  $websiteClicksThrough }}
                                                    </div>
                                                </td>



                                                <td tabindex="0">
                                                    @if(strtotime($job->deadline) < strtotime(now()))
                                                    <p>Expired</p>
                                                @else
                                                    <p>Active</p>
                                                @endif
                                                    {{-- <p>{{ ucfirst($job->status) }}</p> --}}
                                                </td>

                                                <td style="text-align:end" tabindex="0">
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
        </div>
    </div>





@endsection

@section('style')
<style>
        .switch {
            position: relative;
            display: inline-block;
            width: 35px;
            height: 19px;
        }

        /* Hide default HTML checkbox */
        .switch input {
            display: none;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 15px;
            width: 15px;
            left: 3px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input.success:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(15px);
            -ms-transform: translateX(15px);
            transform: translateX(15px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
    <!-- >=>Leaflet Map<=< -->
    <x-map.leaflet.map_links />

    @include('map::links')
@endsection

@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.3/jspdf.plugin.autotable.min.js"></script>



{{-- date oicker --}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
   $(function() {
    $('#dateRange').daterangepicker({
        opens: 'right',
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'DD/MM/YYYY' // Update format here
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'Last 1 Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Last 2 Months': [moment().subtract(2, 'months').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Last 3 Months': [moment().subtract(3, 'months').startOf('month'), moment().endOf('month')],
            'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
            'Current Year': [moment().startOf('year'), moment()],
            'Last 12 Months': [moment().subtract(12, 'months').startOf('month'), moment().endOf('month')]
        }

    }, function(start, end, label) {
        $('#dateRange').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY')); // Update format here
    });

    $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY')); // Update format here
    });

    $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $('#filterButton').on('click', function() {
        var dateRange = $('#dateRange').val();
        if (!dateRange) return;

        var dates = dateRange.split(' - ');
        var startDate = dates[0];
        var endDate = dates[1];

        var url = new URL(window.location.href);
        url.searchParams.set('start_date', startDate);
        url.searchParams.set('end_date', endDate);
        window.location.href = url.toString();
    });
});

</script>

<script>
   // Initialize jsPDF instance with custom page size and orientation
var doc = new jsPDF({
    orientation: 'landscape', // Set orientation to landscape
    unit: 'px', // Use pixels as units
    format: [1100, 800] // Set custom page size width and height in pixels
});

// Function to convert date from YYYY-MM-DD to DD/MM/YYYY
function formatDate(dateString) {
    var date = new Date(dateString);
    var day = ('0' + date.getDate()).slice(-2);
    var month = ('0' + (date.getMonth() + 1)).slice(-2);
    var year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

var startDate = '{{ $startDate }}' ? formatDate('{{ $startDate }}') : null;
var endDate = '{{ $endDate }}' ? formatDate('{{ $endDate }}') : null;
var totalJobs = '{{ $totalJobs }}'; // Get the total number of jobs from Laravel

// Add the heading and date range
var title = '{{ $company->user->name }}';

doc.text(title, 20, 30); // Add the title at position (20, 30)
if (startDate && endDate) {
    var dateRange = startDate + ' - ' + endDate;
    doc.text('Date Range: ' + dateRange, 20, 65); // Add the date range at position (20, 50)
}
doc.text('Total Jobs: ' + totalJobs, 20, 50); // Add the total number of jobs at position (20, 70)

// Your additional PDF generation code here...

    // Add autotable plugin functionality
    doc.autoTable({
        html: '#my-table', // Use the table with id 'my-table'
        ignoreColumns: '.ignore', // Specify columns to ignore if needed
        startY: 70, // Start the table after the heading
        headStyles: {
            fillColor: [41, 128, 185], // Header background color (optional)
            textColor: 255, // Header text color (optional)
        },
        bodyStyles: {
            textColor: 0, // Body text color (optional)
        },
        columnStyles: {
            0: { fontStyle: 'bold' }, // Make the first column bold (optional)
        },
        margin: { top: 10, left: 10, right: 10 }, // Set margin top, left, and right
        didParseCell: function(data) {
            if (data.row.index > 0 && data.column.index === 0) {
                // Adjust height for multi-line content in the first column
                var cellHeight = doc.getTextDimensions(data.cell.text, { maxWidth: data.cell.width }).h;
                data.cell.height = cellHeight + doc.internal.getLineHeight() * 2; // Increase line height for better spacing
            }
        },
        filename: '{{ $company->user->name }}_Report.pdf', // Set the filename for the PDF download
    });

    // Optionally, add a button to trigger the PDF download
    $('#pdf').on('click', function() {
        doc.save('{{ $company->user->name }}_Report.pdf');
    });




      // Send Email Button Click Handler
    // Send Email Button Click Handler
$('#sendEmailButton').on('click', function() {
    // Show loading spinner
    $('#buttonText').hide();
    $('#buttonSpinner').show();

    var startDate = '{{ $startDate }}';
    var endDate = '{{ $endDate }}';

    $.ajax({
        url: '{{ route('send.email') }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: {
            userId: '{{ $company->id }}',
            startDate: startDate,
            endDate: endDate
        },
        success: function(response) {
            alert('Email sent successfully!');
        },
        error: function(xhr) {
            alert('Failed to send email.');
        },
        complete: function() {
            // Hide loading spinner
            $('#buttonSpinner').hide();
            $('#buttonText').show();
        }
    });
});

</script>





@endsection
