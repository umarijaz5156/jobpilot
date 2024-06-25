@extends('frontend.layouts.app')

@section('title', __('messenger'))

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div id="companyChatBox">
                <chat-box :users="{{ $users }}" :auth="{{ auth()->user() }}"
                    :jobs="{{ $jobs }}"/>
            </div>
        </div>
        <div class="dashboard-footer text-center body-font-4 text-gray-500">
            <x-website.footer-copyright />
        </div>
    </div>
@endsection

@section('css')
    <style>
        .chat-box-card {
            border: 1px solid #E4E5E8;
            height: 964px;
            overflow: hidden;
        }

        .select-job+.select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 48px;
        }

        .custom-checkbox label {
            position: relative;
            padding-left: 28px;
        }
        .custom-checkbox label::before {
            content: '';
            display: inline-flex;
            justify-content: center;
            align-items: center;
            background: white;
            width: 20px;
            height: 20px;
            border-radius: 3px;
            border: 1px solid #9DC1EB;
            background: var(--gray-00, #FFF);
            position: absolute;
            left: 0px;
            top: 50%;
            transform: translateY(-50%)
        }
        .custom-checkbox:has(input:checked) label::before {
            content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12' fill='none'%3E%3Cpath d='M10 3.00006L4.5 8.50006L2 6.00006' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background: #0A65CC;
        }
        .chat-box__detail-top {
            border-bottom: 1px solid #E4E5E8;
        }
        .chat-box__detail-bottom {
            border-top: 1px solid #E4E5E8;
        }
        .message-form.active button {
            background: var(--primary-500);
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .day-devider {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 28px;
            margin-bottom: 12px;
        }
        .day-devider::after {
            content: '';
            display: block;
            width: 100%;
            border: 1px dashed #E3E4E6;
        }
        .day-devider span{
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 4px 10px;
            border-radius: 6px;
            border: 2px solid #FFF;
            background: #F3F4F5;
            color: #2F3033;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: 20px;
        }
    </style>
@endsection
