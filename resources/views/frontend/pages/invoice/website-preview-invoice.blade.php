<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Invoice</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        p,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0px;
        }

        body {
            color: #666;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.6em;
            background-color: #f5f6fa;
        }

        .ll-invoice-wrapper {
            min-width: 1520px;
            width: 1520px;
            margin: 0px auto;
            position: relative;
        }

        .ll-invoice-in {
            padding: 0px 50px 30px;
            width: 900px;
            margin: 0px auto;
            background: white;
            overflow-x: hidden;
            min-height: 1092px;
            position: relative;
        }

        .ll-invoice-head {
            position: relative;
            height: 110px;
            margin-bottom: 15px;
        }

        .ll-invoice-head .ll-invoice-logo {
            float: left;
            width: 30%;
            padding: 35px 0px;
        }

        .ll-invoice-head .ll-invoice-title {
            float: left;
            width: 70%;
            font-size: 50px;
            line-height: 1em;
            color: white;
            text-transform: uppercase;
            text-align: right;
            position: relative;
            z-index: 99;
            padding: 30px 0px;
        }

        .ll-invoice-head .ll-invoice-bg-shape {
            position: absolute;
            height: 100%;
            width: 80%;
            -webkit-transform: skewX(35deg);
            transform: skewX(35deg);
            top: 0px;
            right: -140px;
            overflow: hidden;
            background-color: {{ $setting->main_color }};
        }

        .ll-invoice-info {
            position: relative;
            height: 30px;
            margin-bottom: 25px;
            vertical-align: middle;
        }

        .ll-invoice-info .ll-invoice-info_left {
            width: 45%;
            float: left;
            padding: 4px 0px;
            vertical-align: middle;
        }

        .ll-invoice-info_right {
            float: left;
            width: 55%;
            display: table;
            table-layout: fixed;
            color: white;
            position: relative;
            z-index: 999;
            padding: 4px 0px;
            vertical-align: middle;
        }

        .ll-invoice-info_right div {
            display: table-cell;
            text-align: right;
            padding: 0px;
            color: white !important;
            vertical-align: middle;
        }

        .ll-invoice-info_right div span,
        .ll-invoice-info_right div b {
            vertical-align: middle;
        }

        .ll-invoice-info .ll-invoice-info_shape {
            margin-right: 0;
            border-radius: 0;
            -webkit-transform: skewX(35deg);
            transform: skewX(35deg);
            position: absolute;
            height: 30px;
            width: 70%;
            right: -105px;
            overflow: hidden;
            border: none;
            background-color: {{ $setting->main_color }};
        }

        .ll-invoice-pay-to {
            display: table;
            table-layout: fixed;
            width: 100%;
        }

        .ll-invoice-pay-to div {
            display: table-cell;
        }

        .ll-invoice-pay-to .right-side {
            text-align: right;
        }

        .ll-invoice-main-table table {
            width: 100%;
            caption-side: bottom;
            border-collapse: collapse;
            vertical-align: middle;
        }

        .ll-invoice-main-table table th {
            padding: 10px 15px;
            text-align: left;
            color: white;
            vertical-align: middle;
        }

        .ll-invoice-main-table table th,
        .ll-invoice-main-table table td,
        .ll-invoice-footer-table .right-side table td {
            padding-bottom: 12px;
        }

        .ll-invoice-main-table table td {
            padding: 10px 15px;
            line-height: 1.55em;
            text-align: left;
            border-bottom: 1px solid #dbdfea;
            vertical-align: middle;
        }

        .ll-invoice-main-table table thead {
            vertical-align: middle;
        }

        .ll-invoice-main-table table thead tr {
            background-color: {{ $setting->main_color }};
            vertical-align: middle;
        }

        .ll-invoice-main-table table tbody {
            vertical-align: middle;
        }

        .ll-invoice-footer-table {
            display: table;
            table-layout: fixed;
            width: 100%;
            margin-bottom: 30px;
        }

        .ll-invoice-footer-table .left-side {
            width: 58%;
            padding: 10px 15px;
            display: table-cell;
        }

        .ll-invoice-footer-table .right-side {
            display: table-cell;
            width: 40%;
        }

        .ll-invoice-footer-table .right-side table {
            width: 100%;
            caption-side: bottom;
            border-collapse: collapse;
        }

        .ll-invoice-footer-table .right-side table tr {
            vertical-align: middle;
        }

        .ll-invoice-footer-table .right-side table td {
            padding: 10px 15px;
            line-height: 1.55em;
            vertical-align: middle;
        }

        .w-15 {
            width: 25%;
        }

        .w-33 {
            width: 33.33%;
        }

        .w-16 {
            width: 16.66%;
        }

        .w-8 {
            width: 8.33%;
        }

        .text-right {
            text-align: right !important;
        }

        .ll-invoice-download {
            position: absolute;
            top: 0;
            right: 130px;
            box-shadow: -2px 0 24px -2px rgba(43, 55, 72, 0.05);
            padding: 6px;
            border-radius: 6px;
            background-color: #fff;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        .ll-invoice-download button {
            background-color: rgba(52, 199, 89, 0.1);
            color: #34c759;
            border-radius: 0 0 5px 5px;
            border: none;
            display: inline-flex;
            gap: 4px;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="ll-invoice-wrapper" id="download-section">
        <div class="ll-invoice-in">
            <div class="ll-invoice-head">
                <div class="ll-invoice-logo">
                    <img src="{{ $setting->dark_logo_url }}" alt="logo">
                </div>
                <div class="ll-invoice-title">
                    Invoice
                </div>
                <div class="ll-invoice-bg-shape"></div>
            </div>
            <div class="ll-invoice-info">
                <div class="ll-invoice-info_left"></div>
                <div class="ll-invoice-info_right">
                    <div><span class="span-1">Invoice No:</span> <b>#{{ $transaction->order_id }}</b></div>
                    <div style="width:40%;"><span class="span-1">Date:</span> <b>{{ formatTime($transaction->created_at, 'M d, Y') }}</b></div>
                </div>
                <div class="ll-invoice-info_shape"></div>
            </div>
            <div class="ll-invoice-pay-to">
                <div class="left-side">
                    <p style="margin-bottom: 2px; color: #111;">
                        <b>Invoice From:</b>
                    </p>
                    <p style="margin-bottom: 15px;">
                        {{ config('app.name') }} <br>
                        {{ $setting->email }} <br>
                        <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
                    </p>
                </div>
                <div class="right-side">
                    <p style="margin-bottom: 2px; color: #111;">
                        <b>Invoice To:</b>
                    </p>
                    @php
                        $company_info = $transaction->company->user;
                        $company = $transaction->company;
                        $amount = $transaction->currency_symbol.''.$transaction->amount;
                    @endphp

                    <p style="margin-bottom: 15px;">
                        @if ($company_info->name)
                            {{ $transaction->company->user->name }} <br>
                        @endif
                        @if ($company_info->contactInfo->address)
                            {{ $transaction->company->user->contactInfo->address }} <br>
                        @endif
                        @if ($company_info->contactInfo->phone)
                            {{ $company_info->contactInfo->phone }} <br>
                        @endif
                        @if ($company_info->email)
                            {{ $company_info->email }} <br>
                        @endif
                        @if ($company->website)
                            <a href="{{ $company->website }}">{{ $company->website }}</a>
                        @endif
                    </p>
                </div>
            </div>
            <div class="ll-invoice-table-wrap">
                <div class="ll-invoice-main-table">
                    <table>
                        <thead>
                            <tr class="">
                                <th class="w-25">Plan</th>
                                @if ($transaction->payment_type != 'per_job_based')
                                <th class="w-32">Benefits</th>
                                @endif
                                <th class="w-16">Price</th>
                                <th class="w-8">Qty</th>
                                <th class="w-8 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="w-25">
                                    {{ $transaction->payment_type == 'per_job_based' ? __('pay_per_job'):$transaction->plan->label }}
                                </td>
                                @if ($transaction->payment_type != 'per_job_based')
                                <td class="w-32">
                                    <span>Job Limit : {{ $transaction->plan->job_limit }}</span> <br>
                                    <span>Featured Job Limit : {{ $transaction->plan->featured_job_limit }}</span> <br>
                                    <span>Highlight Job Limit : {{ $transaction->plan->highlight_job_limit }}</span> <br>
                                    <span>Candidate CV View Limit : {{ $transaction->plan->candidate_cv_view_limitation == 'limited' ? $transaction->plan->candidate_cv_view_limit: __('unlimited') }}</span>
                                    <br>
                                </td>
                                @endif
                                <td class="w-16">{{ $amount }}</td>
                                <td class="w-8">1</td>
                                <td class="w-8 text-right">{{ $amount }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="ll-invoice-footer-table">
                    <div class="left-side">
                        <p style="margin-bottom: 2px;"><b>Payment info:</b></p>
                        <p>Payment Method - {{ ucfirst($transaction->payment_provider) }}
                        <p>Payment Type - {{ str_replace('_', ' ', ucfirst($transaction->payment_type)) }}
                        <br>Payment Status: {{ ucfirst($transaction->payment_status) }}
                        <br>Amount:  {{ $amount }}
                        </p>
                    </div>
                    <div class="right-side">
                        <table>
                            <tbody>
                                <tr style="background: #f5f6fa;">
                                    <td style="color: #111; font-weight: 700;border-top: 1px solid #dbdfea;">Subtoal</td>
                                    <td class="text-right" style="color: #111; font-weight: 700;border-top: 1px solid #dbdfea;">{{ $amount }}</td>
                                </tr>
                                <tr style="background-color: {{ $setting->main_color }}; color: white;">
                                    <td style="font-size: 16px; font-weight: 700;">Grand Total </td>
                                    <td style="font-size: 16px; font-weight: 700;" class="text-right">
                                        {{ $amount }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div style="position: absolute; bottom: 30px; left:50px; right: 50px; text-align: center;">
                <hr style="background: #dbdfea; margin-bottom: 15px;">
                <p style="margin-bottom: 2px"><b style="color:#111">Terms &amp; Conditions:</b></p>
                <a href="/terms-condition" target="_blank" rel="noopener noreferrer">View Terms & Condition</a>
            </div>
        </div>
        <div class="ll-invoice-download">
            <form action="{{ route('company.transaction.invoice.download', $transaction->id) }}"
                method="POST">
                @csrf
                <button type="submit">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 17L12 21M12 21L16 17M12 21V12M20 16.7428C21.2215 15.734 22 14.2079 22 12.5C22 9.46243 19.5376 7 16.5 7C16.2815 7 16.0771 6.886 15.9661 6.69774C14.6621 4.48484 12.2544 3 9.5 3C5.35786 3 2 6.35786 2 10.5C2 12.5661 2.83545 14.4371 4.18695 15.7935" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    <span>Download</span>
                </button>
            </form>
        </div>
    </div>
</body>

</html>
