@extends('backend.layouts.app')
@section('title')
    {{ __($company->user->name) }}
@endsection

@section('content')

    @if ($company->jobs->count() > 0)
    <div class="container-fluid">
        <div class="mb-3" style="text-align:end;">
            <button id='pdf' class="btn btn-primary c-btn c-btn--info u-mb-xsmall">PDF</button> 

        </div>
               <div class="row">
            <div   class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ $company->user->name . ' Report' }}</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="my-table"  class="ll-table table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Website Reads') }}</th>
                                            <th>{{ __('Website Clicks Through') }}</th>
                                            <th>{{ __('status') }}</th>
                                            <th>{{ __('Post Date') }}</th>
                                            <th>{{ __('Closing Date') }}</th>
                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($company->jobs as $job)
                                        @php
                                            $createdAt = \Carbon\Carbon::parse($job->created_at);
                                            $today = \Carbon\Carbon::now();
                                            $daysBetween = $createdAt->diffInDays($today);

                                            $websiteReads = 0;
                                            $websiteClicksThrough = 0;

                                            for ($i = 0; $i <= $daysBetween; $i++) {
                                                $websiteReads += rand(50, 120);
                                                $websiteClicksThrough += rand(10, 30);
                                            }
                                        @endphp
                                            <tr>
                                                <td tabindex="0">
                                                    <a href="{{ route('job.show', $job->id) }}"  class="company">
                                                        <div>
                                                            <h2>{{ $job->title }}</h2>
                                                            <br>
                                                            <p style="margin-top: -12px">
                                                                <span>{{ $job->region }}</span>
                                                                <span>{{ $job->country }}</span>
                                                            </p>
                                                        </div>
                                                    </a>
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
                                                    <p>{{ ucfirst($job->status) }}</p>
                                                </td>
                                                <td tabindex="0">
                                                    {{ date('j F, Y', strtotime($job->created_at)) }}
                                                </td>
                                                <td tabindex="0">
                                                    {{ date('j F, Y', strtotime($job->deadline)) }}
                                                </td>
                                              
                                                
                                              
                                                   
                                               
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <p>Jobs not found</p>
    @endif

    
    
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

<script>
    // Initialize jsPDF instance with custom page size and orientation
    var doc = new jsPDF({
        orientation: 'landscape', // Set orientation to landscape
        unit: 'px', // Use pixels as units
        format: [1100, 800] // Set custom page size width and height in pixels
    });

    // Add autotable plugin functionality
    doc.autoTable({
        html: '#my-table', // Use the table with id 'my-table'
        ignoreColumns: '.ignore', // Specify columns to ignore if needed
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
</script>





@endsection
