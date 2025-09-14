
@php
    $color = !empty($settings['color']) ? $settings['color'] : 'theme-3';

if(isset($settings['color_flag']) && $settings['color_flag'] == 'true')
{
    $themeColor = 'custom-color';
}
else {
    $themeColor = $color;
}

$cust_darklayout = Utility::getValByName('cust_darklayout');
if($cust_darklayout == ''){
    $cust_darklayout = 'off';
}

if(!empty(session()->get('lang')))
{
    $currantLang = session()->get('lang');
}else{
    $currantLang = 'en';
}
$data = DB::table('settings');
    $data = $data
        ->where('created_by', '>', 1)
        ->where('store_id', $store->id)
        ->where('name', 'SITE_RTL')
        ->first();
    if(!isset($data)){
        $data = (object)[
            "name"=> "SITE_RTL",
            "value"=> "off"
            ];
    }
if($currantLang == 'ar' || $currantLang == 'he'){
    $data->value = 'on';
}
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ empty($data) ? '' : ($data->value == 'on' ? 'rtl' : '' )}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
    </title>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/libs/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/libs/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/libs/@fancyapps/fancybox/dist/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/libs/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
    @if (isset($data) && $data->value == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
        <style>
            [dir="rtl"] .invoice-preview .invoice-header:not(.border-0) h5:after, [dir="rtl"] .invoice-preview .invoice-header:not(.border-0) .h5:after, [dir="rtl"] .invoice-preview .invoice-header:not(.border-0) .h5:after {
                /* right: -25px;224px;
                left: auto; */
                right: 10px; 
                top: 17px;
                border-radius: 3px 0 0 3px;
            }
            
            .invoice-preview .invoice-header:not(.border-0) h5:after, .invoice-preview .invoice-header:not(.border-0) .h5:after {
                content: "";
                height: 30px;
                width: 3px;
                background: #51459d;
                position: absolute;
                left: -25px;
                top: -5px;
                border-radius: 0 3px 3px 0;
            }
        </style>
    @endif
    @if (isset($cust_darklayout) && $cust_darklayout == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="main-style-link">
        <link rel="stylesheet" href="{{ asset('custom/css/custom-dark.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
        <style>
            .invoice-preview .invoice-header:not(.border-0) h5:after, [dir="rtl"] .invoice-preview .invoice-header:not(.border-0) .h5:after, [dir="rtl"] .invoice-preview .invoice-header:not(.border-0) .h5:after {
                /* right: -25px;224px;
                left: auto; */
                left: 10px; 
                top: 17px;
                border-radius: 3px 0 0 3px;
            }
            .invoice-preview .invoice-header:not(.border-0) h5:after, .invoice-preview .invoice-header:not(.border-0) .h5:after {
                content: "";
                height: 30px;
                width: 3px;
                background: #51459d;
                position: absolute;
                left: 0px;
                top: 18px;
                border-radius: 0 3px 3px 0;
            }
        </style>
    @endif
    <style>
        :root {
            --color-customColor: <?= $color ?>;    
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">

    <style type="text/css">
        body.custom-color .invoice-preview .invoice-header h5:after, body.custom-color .invoice-preview .invoice-header .h5:after, body.custom-color .invoice-preview .invoice-header .h5:after {
            background: var(--color-customColor);
        }

        body.theme-1 .invoice-preview .invoice-header h5:after, body.theme-1 .invoice-preview .invoice-header .h5:after, body.theme-1 .invoice-preview .invoice-header .h5:after {
            background: #0CAF60;
        }
        body.theme-2 .invoice-preview .invoice-header h5:after, body.theme-2 .invoice-preview .invoice-header .h5:after, body.theme-2 .invoice-preview .invoice-header .h5:after {
            background: #584ED2;
        }
        body.theme-3 .invoice-preview .invoice-header h5:after, body.theme-3 .invoice-preview .invoice-header .h5:after, body.theme-3 .invoice-preview .invoice-header .h5:after {
            background: #6FD943;
        }
        body.theme-4 .invoice-preview .invoice-header h5:after, body.theme-4 .invoice-preview .invoice-header .h5:after, body.theme-4 .invoice-preview .invoice-header .h5:after {
            background: #145388;
        }
        body.theme-5 .invoice-preview .invoice-header h5:after, body.theme-5 .invoice-preview .invoice-header .h5:after, body.theme-5 .invoice-preview .invoice-header .h5:after {
            background: #B9406B;
        }
        body.theme-6 .invoice-preview .invoice-header h5:after, body.theme-6 .invoice-preview .invoice-header .h5:after, body.theme-6 .invoice-preview .invoice-header .h5:after {
            background: #008ECC;
        }
        body.theme-7 .invoice-preview .invoice-header h5:after, body.theme-7 .invoice-preview .invoice-header .h5:after, body.theme-7 .invoice-preview .invoice-header .h5:after {
            background: #922C88;
        }
        body.theme-8 .invoice-preview .invoice-header h5:after, body.theme-8 .invoice-preview .invoice-header .h5:after, body.theme-8 .invoice-preview .invoice-header .h5:after {
            background: #C0A145;
        }
        body.theme-9 .invoice-preview .invoice-header h5:after, body.theme-9 .invoice-preview .invoice-header .h5:after, body.theme-9 .invoice-preview .invoice-header .h5:after {
            background: #48494B;
        }
        body.theme-10 .invoice-preview .invoice-header h5:after, body.theme-10 .invoice-preview .invoice-header .h5:after, body.theme-10 .invoice-preview .invoice-header .h5:after {
            background: #0C7785;
        }
        .invoice-preview {
            box-shadow: 0 6px 30px rgb(182 186 203 / 30%);
            margin-bottom: 24px;
            transition: box-shadow 0.2s ease-in-out;
        }
        .invoice-preview {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background-clip: border-box;
        }

        .invoice-preview .invoice-header {
            border-bottom: 1px solid #f1f1f1;
        }
        .invoice-header:first-child {
            border-radius: calc(10px - 0px) calc(10px - 0px) 0 0;
        }

        .invoice-header {
            padding: 25px 25px;
            margin-bottom: 0;
        }

        :root {
            /* --theme-color: #ff8d8d; */
            --theme-color: {{ $color }};
            --white: #ffffff;
            --black: #000000;
        }

        body {
            font-family: 'Lato', sans-serif;
        }

        p,
        li,
        ul,
        ol {
            margin: 0;
            padding: 0;
            list-style: none;
            line-height: 1.5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr th {
            padding: 0.75rem;
            text-align: left;
        }

        table tr td {
            padding: 0.75rem;
            text-align: left;
        }

        table th small {
            display: block;
            font-size: 12px;
        }

        .invoice-preview-main {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            background: #ffff;
            box-shadow: 0 0 10px #ddd;
        }

        .invoice-logo {
            max-width: 200px;
            width: 100%;
        }

        .invoice-header table td {
            padding: 15px 30px;
        }

        .text-right {
            text-align: right;
        }

        .no-space tr td {
            padding: 0;
            white-space: nowrap;
        }

        .vertical-align-top td {
            vertical-align: top;
        }

        .view-qrcode {
            max-width: 139px;
            height: 139px;
            width: 100%;
            margin-left: auto;
            margin-top: 15px;
            background: var(--white);
            padding: 13px;
            /* padding: 9px; */
            border-radius: 10px;
        }

        .view-qrcode img {
            width: 100%;
            height: 100%;
        }

        .invoice-body {
            padding: 0px 25px 0;
        }

        table.add-border tr {
            /* border-top: 1px solid var(--theme-color); */
            border-top: 1px solid #000000;
        }

        tfoot tr:first-of-type {
            /* border-bottom: 1px solid var(--theme-color); */
            border-bottom: 1px solid #000000;
        }

        .total-table tr:first-of-type td {
            padding-top: 0;
        }

        .total-table tr:first-of-type {
            border-top: 0;
        }

        .sub-total {
            padding-right: 0;
            padding-left: 0;
        }

        .border-0 {
            border: none !important;
        }

        .invoice-summary td,
        .invoice-summary th {
            font-size: 13px;
            font-weight: 600;
        }

        .invoice-summary th {
            font-size: 15px;
            font-weight: 600;
        }

        .total-table td:last-of-type {
            width: 146px;
        }

        .invoice-footer {
            padding: 15px 20px;
        }

        .itm-description td {
            padding-top: 0;
        }

        html[dir="rtl"] table tr td,
        html[dir="rtl"] table tr th {
            text-align: right;
        }

        html[dir="rtl"] .text-right {
            text-align: left;
        }

        html[dir="rtl"] .view-qrcode {
            margin-left: 0;
            margin-right: auto;
        }

        p:not(:last-of-type) {
            margin-bottom: 15px;
        }

        .invoice-summary p {
            margin-bottom: 0;
        }
        .wid-75 {
            width: 75px;
        }
    </style>
</head>

<body class="{{ $themeColor }}">
    <div class="invoice-preview-main" id="boxes">
        <div class="row">
            <!-- [ sample-page ] start -->
            <div class="col-sm-12">
                <div class="row" id="printableArea">
                    <div class="col-md-7" style="flex: 0 0 auto; width: 58.33333%;">
                        <div class="invoice-preview">
                            <div class="invoice-header justify-content-between">
                                <h5 class="mb-0">{{ __('Order') }} {{ $order->order_id }}</h5>
                            </div>
                            <div class="invoice-body">
                                <div class="">{{-- table-responsive --}}
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Item') }}</th>
                                                <th>{{ __('Quantity') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Total') }}</th>
                                                {{-- <th>{{ __('Action') }}</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $sub_tax = 0;
                                                $total = 0;
                                                $order_id = trim($order->order_id,'#'); 
                                            @endphp
                                            @foreach ($order_products as $key => $product)
                                                @if (isset($product->variant_id) && $product->variant_id != 0)
                                                    <tr>
                                                        <td class="total">
                                                            <span class="h6 text-sm" style="line-height: 0.8;">
                                                                @if (isset($product->product_name))
                                                                    <a
                                                                        href="{{ route('product.show', $product->id) }}">{{ $product->product_name . ' - ( ' . $product->variant_name . ' )' }}</a>
                                                                @else
                                                                    <a href="{{ route('product.show', $product->id) }}">
                                                                        {{ $product->name }}
                                                                    </a>
                                                                @endif
                                                            {{-- </span> --}}
                                                                </br>
                                                            @if (!empty($product->tax))
                                                                @php
                                                                    $total_tax = 0;
                                                                @endphp
                                                                @foreach ($product->tax as $tax)
                                                                    @php
                                                                        $sub_tax = ($product->variant_price * $product->quantity * $tax->tax) / 100;
                                                                        $total_tax += $sub_tax;
                                                                    @endphp
                                                                    {{ $tax->tax_name . ' ' . $tax->tax . '%' . ' (' . $sub_tax . ')' }}
                                                                @endforeach
                                                            @else
                                                                @php
                                                                    $total_tax = 0;
                                                                @endphp
                                                            @endif
                                                        </span>
                                                        </td>
                                                        <td>
                                                            {{ $product->quantity }}
                                                        </td>
                                                        <td>
                                                            {{ App\Models\Utility::priceFormat($product->variant_price) }}
                                                        </td>
                                                        <td>
                                                            {{ App\Models\Utility::priceFormat($product->variant_price * $product->quantity + $total_tax) }}
                                                        </td>
                                                        {{-- <td>
                                                            @can('Delete Orders')
                                                                <div class="action-btn bg-light-secondary ms-2">
                                                                    {!! Form::open(['method' => 'DELETE',
                                                                    'route' => ['delete.order_item', $product->id , $product->variant_id,$order_id,$key],]) !!}
                                                                    <a class="show_confirm align-items-center btn btn-sm d-inline-flex" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                                    <span><i class="ti ti-trash"></i></span>
                                                                    </a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @endcan
                                                        </td> --}}
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td class="total">
                                                            <span class="h6 text-sm" style="line-height: 0.8;">
    
                                                                @if (isset($product->product_name))
                                                                    <a href="{{ route('product.show', $product->id) }}">{{ $product->product_name }}
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('product.show', $product->id) }}">
                                                                        {{ $product->name }}
                                                                    </a>
                                                                @endif
                                                            {{-- </span> --}}
                                                                </br>
                                                            @if (!empty($product->tax))
                                                                @php
                                                                    $total_tax = 0;
                                                                @endphp
                                                                @foreach ($product->tax as $tax)
                                                                    @php
                                                                        $sub_tax = ($product->price * $product->quantity * $tax->tax) / 100;
                                                                        $total_tax += $sub_tax;
                                                                    @endphp
                                                                    {{ $tax->tax_name . ' ' . $tax->tax . '%' . ' (' . $sub_tax . ')' }}
                                                                @endforeach
                                                            @else
                                                                @php
                                                                    $total_tax = 0;
                                                                @endphp
                                                            @endif
                                                        </span>
                                                        </td>
                                                        <td>
                                                            {{ $product->quantity }}
                                                        </td>
                                                        <td>
                                                            {{ App\Models\Utility::priceFormat($product->price) }}
                                                        </td>
                                                        <td>
                                                            {{ App\Models\Utility::priceFormat($product->price * $product->quantity + $total_tax) }}
                                                        </td>
                                                        {{-- <td>
                                                            @can('Delete Orders')
                                                                <div class="action-btn ms-2">
                                                                    {!! Form::open(['method' => 'DELETE',
                                                                    'route' => ['delete.order_item', $product->id , $product->variant_id,$order_id,$key],]) !!}
                                                                    <a class="show_confirm align-items-center btn btn-sm d-inline-flex" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                                    <span ><i class="ti ti-trash"></i></span>
                                                                    </a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @endcan
                                                        </td> --}}
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- <div class="col-md-6 col-sm-12 col-lg-6 "  style="flex: 0 0 auto; width: 50%;">
                                <div class="invoice-preview">
                                    <div class="invoice-header justify-content-between">
                                        <h5 class="">{{ __('Shipping Information') }}</h5>
                                    </div>
                                    <div class="invoice-body pt-0">
                                        <address class="mb-0 text-sm">
                                            <dl class="row mt-4 align-items-center">
                                                <dt class="col-sm-4 h6 text-sm">{{ __('Name') }}</dt>
                                                <dd class="col-sm-8 text-sm me-0"> {{ !empty($user_details->name) ? $user_details->name : '' }}</dd>
                                                <dt class="col-sm-4 h6 text-sm">{{ __('Company') }}</dt>
                                                <dd class="col-sm-8 text-sm me-0"> {{ !empty($user_details->shipping_address) ? $user_details->shipping_address  : '' }}</dd>
                                                <dt class="col-sm-4 h6 text-sm">{{ __('City') }}</dt>
                                                <dd class="col-sm-8 text-sm me-0">{{ !empty($user_details->shipping_city) ? $user_details->shipping_city : '' }}</dd>
                                                <dt class="col-sm-4 h6 text-sm">{{ __('Country') }}</dt>
                                                <dd class="col-sm-8 text-sm me-0"> {{ !empty($user_details->shipping_country) ? $user_details->shipping_country : '' }}</dd>
                                                <dt class="col-sm-4 h6 text-sm">{{ __('Postal Code') }}</dt>
                                                <dd class="col-sm-8 text-sm me-0">{{ !empty($user_details->shipping_postalcode) ? $user_details->shipping_postalcode : '' }}</dd>
                                                <dt class="col-sm-4 h6 text-sm">{{ __('Phone') }}</dt>
                                                <dd class="col-sm-8 text-sm me-0">
                                                    @if(!empty($user_details->phone))
                                                    <a href="{{ $url = 'https://api.whatsapp.com/send?phone=' . str_replace(' ', '', $user_details->phone) . '&text=Hi' }}"
                                                        target="_blank">
                                                        {{ !empty($user_details->phone) ? $user_details->phone : '' }}
                                                    </a>
                                                    @endif
                                                </dd>
                                                @if (!empty($location_data && $shipping_data))
                                                    <dt class="col-sm-4 h6 text-sm">{{ __('Location') }}</dt>
                                                    <dd class="col-sm-8 text-sm me-0">{{ $location_data->name }}</dd>
                                                    <dt class="col-sm-4 h6 text-sm">{{ __('Shipping Method') }}</dt>
                                                    <dd class="col-sm-8 text-sm me-0">{{ $shipping_data->shipping_name }}</dd>
                                                @endif
                                            </dl>
                                        </address>
                                    </div>
                                </div>
                            </div> --}}
    
                            <div class="col-md-6 col-sm-12 col-lg-6 " style="flex: 0 0 auto; width: 50%;">
                                <div class="invoice-preview">
                                    <div class="invoice-header justify-content-between">
                                        <h5 class="">{{ __('Shipping Information') }}</h5>
                                    </div>
                                    <div class="invoice-body pt-0">
                                        <dl class="row mt-4 align-items-center">
                                            <dt class="col-sm-4 h6 text-sm">{{ __('Name') }}</dt>
                                            <dd class="col-sm-8 text-sm me-0"> {{ !empty($user_details->name) ? $user_details->name : '' }}</dd>
                                            <dt class="col-sm-4 h6 text-sm">{{ __('Company') }}</dt>
                                            <dd class="col-sm-8 text-sm me-0"> {{ !empty($user_details->billing_address) ? $user_details->billing_address  : '' }}</dd>
                                            <dt class="col-sm-4 h6 text-sm">{{ __('City') }}</dt>
                                            <dd class="col-sm-8 text-sm me-0">{{ !empty($user_details->billing_city) ? $user_details->billing_city : '' }}</dd>
                                            <dt class="col-sm-4 h6 text-sm">{{ __('Country') }}</dt>
                                            <dd class="col-sm-8 text-sm me-0"> {{ !empty($user_details->billing_country) ? $user_details->billing_country : '' }}</dd>
                                            <dt class="col-sm-4 h6 text-sm">{{ __('Postal Code') }}</dt>
                                            <dd class="col-sm-8 text-sm me-0">{{ !empty($user_details->billing_postalcode) ? $user_details->billing_postalcode : '' }}</dd>
                                            <dt class="col-sm-4 h6 text-sm">{{ __('Phone') }}</dt>
                                            <dd class="col-sm-8 text-sm me-0">
                                                @if(!empty($user_details->phone))
                                                <a href="{{ $url = 'https://api.whatsapp.com/send?phone=' . str_replace(' ', '', $user_details->phone) . '&text=Hi' }}"
                                                    target="_blank">
                                                    {{ $user_details->phone }}
                                                </a>
                                                @endif
                                            </dd>
                                            @if (!empty($location_data && $shipping_data))
                                                <dt class="col-sm-4 h6 text-sm">{{ __('Location') }}</dt>
                                                <dd class="col-sm-8 text-sm me-0">{{ $location_data->name }}</dd>
                                                <dt class="col-sm-4 h6 text-sm">{{ __('Shipping Method') }}</dt>
                                                <dd class="col-sm-8 text-sm me-0">{{ $shipping_data->shipping_name }}</dd>
                                            @endif
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="flex: 0 0 auto; width: 41.66667%;">
                        <div class="invoice-preview">
                            <div class="invoice-header">
                                <h5 class="mb-0">{{ __('Order') }} {{ $order->order_id }}</h5>
                            </div>
                            <div class="invoice-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ __('Sub Total') }} :</td>
                                                <td>{{ App\Models\Utility::priceFormat($sub_total) }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Estimated Tax') }} :</td>
                                                <td>{{ App\Models\Utility::priceFormat($total_taxs) }}</td>
                                            </tr>
                                            @if (!empty($discount_price))
                                                <tr>
                                                    <td>{{ __('Apply Coupon') }} :</td>
                                                    <td>
                                                        @if($order->payment_type == 'POS')
                                                            {{ App\Models\Utility::priceFormat($discount_price) }}
                                                        @else
                                                            {{ $discount_price }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                            @if (!empty($shipping_data))
                                                @if (!empty($discount_value))
                                                    <tr>
                                                        <td>{{ __('Shipping Price') }} :</td>
                                                        <td>{{ App\Models\Utility::priceFormat($shipping_data->shipping_price) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('Grand Total') }} :</td>
                                                        <td>{{ App\Models\Utility::priceFormat($grand_total + $shipping_data->shipping_price - $discount_value) }}
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td>{{ __('Shipping Price') }} :</td>
                                                        <td>{{ App\Models\Utility::priceFormat($shipping_data->shipping_price) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ __('Grand Total') }} :</td>
                                                        <td>{{ App\Models\Utility::priceFormat($sub_total + $shipping_data->shipping_price + $total_taxs) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @elseif(!empty($discount_value))
                                                <tr>
                                                    <td>{{ __('Grand  Total') }} :</td>
                                                    <td>{{ App\Models\Utility::priceFormat($grand_total - $discount_value) }}
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>{{ __('Grand  Total') }} :</td>
                                                    <td>
                                                        @if($order->payment_type == 'POS')
                                                            @php
                                                                $discount = !empty($discount_price) ? $discount_price : 0;
                                                            @endphp
                                                            {{ App\Models\Utility::priceFormat($grand_total - $discount) }}
                                                        @else
                                                            {{ App\Models\Utility::priceFormat($grand_total) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                            <td>{{ __('Payment Type') }} :</td>
                                            <td>{{ $order['payment_type'] }}</td>
                                            {{-- @if (!empty($store_payment_setting['custom_field_title_1']) && !empty($user_details->custom_field_title_1))
                                            <tr>
                                                <td>{{ $store_payment_setting['custom_field_title_1'] }} :</td>
                                                <td>{{ $user_details->custom_field_title_1 }}</td>
                                            </tr>
                                            @endif
                                            @if (!empty($store_payment_setting['custom_field_title_2']) && !empty($user_details->custom_field_title_2))
                                            <tr>
                                                <td> {{ $store_payment_setting['custom_field_title_2'] }} :</td>
                                                <td> {{ $user_details->custom_field_title_2 }}</td>
                                            </tr>
                                            @endif
                                            @if (!empty($store_payment_setting['custom_field_title_3']) && !empty($user_details->custom_field_title_3))
                                            <tr>
                                                <td> {{ $store_payment_setting['custom_field_title_3'] }} :</td>
                                                <td> {{ $user_details->custom_field_title_3 }}</td>
                                            </tr>
                                            @endif
                                            @if (!empty($store_payment_setting['custom_field_title_4']) && !empty($user_details->custom_field_title_4))
                                            <tr>
                                                <td>{{ $store_payment_setting['custom_field_title_4'] }} :</td>
                                                <td> {{ $user_details->custom_field_title_4 }}</td>
                                            </tr>
                                            @endif --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @if((!empty($store_payment_setting['custom_field_title_1']) && !empty($user_details->custom_field_title_1)) || 
                            (!empty($store_payment_setting['custom_field_title_2']) && !empty($user_details->custom_field_title_2)) || 
                            (!empty($store_payment_setting['custom_field_title_3']) && !empty($user_details->custom_field_title_3)) || 
                            (!empty($store_payment_setting['custom_field_title_4']) && !empty($user_details->custom_field_title_4)))
                            <div class="invoice-preview">
                                <div class="invoice-header">
                                    <h5 class="">{{ __('Extra Information') }}</h5>
                                </div>
                                <div class="invoice-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (!empty($store_payment_setting['custom_field_title_1']) && !empty($user_details->custom_field_title_1))
                                                <tr>
                                                    <td>{{ $store_payment_setting['custom_field_title_1'] }} :</td>
                                                    <td>{{ $user_details->custom_field_title_1 }}</td>
                                                </tr>
                                                @endif
                                                @if (!empty($store_payment_setting['custom_field_title_2']) && !empty($user_details->custom_field_title_2))
                                                <tr>
                                                    <td> {{ $store_payment_setting['custom_field_title_2'] }} :</td>
                                                    <td> {{ $user_details->custom_field_title_2 }}</td>
                                                </tr>
                                                @endif
                                                @if (!empty($store_payment_setting['custom_field_title_3']) && !empty($user_details->custom_field_title_3))
                                                <tr>
                                                    <td> {{ $store_payment_setting['custom_field_title_3'] }} :</td>
                                                    <td> {{ $user_details->custom_field_title_3 }}</td>
                                                </tr>
                                                @endif
                                                @if (!empty($store_payment_setting['custom_field_title_4']) && !empty($user_details->custom_field_title_4))
                                                <tr>
                                                    <td>{{ $store_payment_setting['custom_field_title_4'] }} :</td>
                                                    <td> {{ $user_details->custom_field_title_4 }}</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
    
            </div>
            <!-- [ sample-page ] end -->
        </div>
    </div>
    {{-- @dd("asd"); --}}
    @if (!isset($preview))
        @include('orders.script');
    @endif
</body>

</html>
