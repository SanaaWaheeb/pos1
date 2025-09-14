@extends('layouts.admin')
@php
    $logo=\App\Models\Utility::get_file('uploads/logo');
    $product_item=\App\Models\Utility::get_file('uploads/is_cover_image/');
    $company_favicon=Utility::getValByName('company_favicon');
    $SITE_RTL = Utility::getValByName('SITE_RTL');
    $setting = \App\Models\Utility::colorset();
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if(isset($setting['color_flag']) && $setting['color_flag'] == 'true')
    {
        $themeColor = 'custom-color';
    }
    else {
        $themeColor = $color;
    }
    $storesetting = Utility::StorageSettings();
@endphp
@section('page-title', __('Pos'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('Pos') }}</li>
@endsection
@section('content')
    <div class="mt-4 product-tab-wrp">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-4 pdp-section-title">
                    <h3 class="mb-3">Product Section</h3>
                </div>
            </div>
        </div>
        <div class="category-wrp mb-4">
            <div class="ms-0 row">
                <div class="button-list b-bottom catgory-pad category-tab-wrapper ps-0 col-lg-8 col-12" >
                    <div class="form-row m-0 gap-3" id="categories-listing">
                    </div>
                </div>
                <div class="col-lg-4 col-12 ps-0 search-main-form">
                    <div class="search-bar-left search-form-wrp d-flex">
                        <form class="search-input-wrp">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                                </div>
                                <input id="searchproduct" type="text" data-url="{{ route('search.products') }}" placeholder="{{ __('Search Product') }}" class="form-control pr-4 rounded-right">
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <?php $lastsegment = request()->segment(count(request()->segments())) ?>

        <div class="mt-2 row row-gap pdp-sop-card">
            <div class="col-lg-7">
                <div class="sop-card card h-100">

                    <div class="card-body pdp-card-inner py-3 px-2">
                        <div class="right-content">

                            <div class="product-body-nop pdp-body-nop">
                                <div class="form-row row m-1" id="product-listing">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 ps-lg-0 pe-lg-0">
                <div class="card m-0 h-100">
                    <div class="card-header p-3">
                        <div class="row align-items-center row-gap">
                            <div class="col-md-6">
                                <h3 class="mb-0">{{__('Billing Section')}}</h3>
                            </div>
                            <div class="col-md-6">
                                {{ Form::select('customer_id',$customers,'', array('class' => 'form-control select customer_select','id'=>'customer','required'=>'required')) }}
                                {{ Form::hidden('vc_name_hidden', '',['id' => 'vc_name_hidden']) }}
                                <input type="hidden" id="store_id" value="{{ \Auth::user()->current_store }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-body carttable cart-product-list carttable-scroll pdp-cart-body d-flex" id="carthtml">
                        @php $total = 0 @endphp
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-left">{{__('Name')}}</th>
                                    <th class="text-center">{{__('QTY')}}</th>
                                    <th>{{__('Tax')}}</th>
                                    <th class="text-center">{{__('Price')}}</th>
                                    <th class="text-center">{{__('Sub Total')}}</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody id="tbody">
                                @if(session($lastsegment) && !empty(session($lastsegment)) && count(session($lastsegment)) > 0)
                                    @foreach(session($lastsegment) as $id => $details)
                                        @php

                                            $product = \App\Models\Product::find($details['id']);
                                            $image_url = !empty($product->is_cover) ? $product->is_cover : 'default.jpg';
                                            if($details['variant_id'] <= 0){
                                                $total = $total + (float) $details['subtotal'];
                                            }else{
                                                $total = $total + (float) $details['variant_subtotal'];
                                            }
                                        @endphp
                                          @if(\Auth::user()->current_store == $product->store_id)
                                            @if($details['variant_id'] <= 0)
                                                <tr data-product-id="{{$id}}" id="product-id-{{$details['id']}}">
                                            @else
                                                <tr data-product-id="{{$id}}" id="product-variant-id-{{$details['variant_id']}}">
                                            @endif
                                                    <td class="cart-images">
                                                        <img alt="Image placeholder" src="{{ $product_item . $image_url }}" class="card-image avatar rounded-circle-sale border border-2 border-primary rounded">
                                                    {{-- <img alt="Image placeholder" src="{{ $product_item.$image_url }}" class="card-image avatar rounded-circle-sale shadow hover-shadow-lg"> --}}
                                                </td>
                                                @if($details['variant_id'] <= 0)
                                                    <td class="name">{{ $details['product_name'] }}</td>
                                                    <td>
                                                        <span class="quantity buttons_added">
                                                            <input type="button" value="-" class="minus">
                                                            <input type="number" step="1" min="1" max="" name="quantity"
                                                                title="{{ __('Quantity') }}" class="input-number"
                                                                data-url="{{ url('update-cart/') }}" data-id="{{ $id }}"
                                                                size="4" value="{{ $details['quantity'] }}">
                                                            <input type="button" value="+" class="plus">
                                                        </span>
                                                    </td>
                                                @else
                                                    <td class="name">{{ $details['product_name'] . '-' . $details['variant_name'] }}</td>
                                                    <td>
                                                        <span class="quantity buttons_added">
                                                            <input type="button" value="-" class="minus">
                                                            <input type="number" step="1" min="1" max="" name="quantity"
                                                                title="{{ __('Quantity') }}" class="input-number"
                                                                data-url="{{ url('update-cart/') }}" data-id="{{ $id }}"
                                                                size="4" value="{{ $details['quantity'] }}">
                                                            <input type="button" value="+" class="plus">
                                                        </span>
                                                    </td>
                                                @endif

                                                <td>
                                                    @if(!empty($product->product_tax))
                                                        @php
                                                            $taxes=\Utility::tax($product->product_tax);
                                                        @endphp
                                                        @foreach($taxes as $tax)
                                                            <span class="badge bg-primary">{{$tax->name .' ('.$tax->rate .'%)'}}</span> <br>
                                                        @endforeach
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                @if($details['variant_id'] <= 0)
                                                    {{--  <td class="price text-right">{{  \App\Models\Utility::priceFormat($details['price']) }}</td>  --}}
                                                    <td class="price text-right">{{ \App\Models\Utility::priceFormat($details['price']) }}</td>

                                                    <td class="col-sm-3 mt-2">
                                                        {{--  <span class="subtotal">{{  \App\Models\Utility::priceFormat($details['subtotal']) }}</span>  --}}
                                                        <span class="subtotal">{{ \App\Models\Utility::priceFormat($details['subtotal']) }}</span>
                                                    </td>
                                                @else
                                                    <td class="price text-right">{{ \App\Models\Utility::priceFormat($details['variant_price']) }}</td>
                                                    <td class="col-sm-3 mt-2">
                                                        <span class="subtotal">{{ \App\Models\Utility::priceFormat($details['variant_subtotal']) }}</span>
                                                    </td>
                                                @endif
                                                <td class="col-sm-2 mt-2 action-btn-wrapper">
                                                    <a href="#" class="bs-pass-para btn btn-sm btn-icon bg-danger text-white" data-confirm="{{ __('Are You Sure?') }}" data-text="{{__('This action can not be undone. Do you want to continue?')}}"
                                                    data-confirm-yes="delete-form-{{ $id }}" title="{{ __('Delete') }}" data-id="{{ $id }}" data-bs-placement="top"  data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                        <i class="ti ti-trash" title="{{ __('Delete') }}"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'delete', 'url' => ['remove-from-cart'],'id' => 'delete-form-'.$id]) !!}
                                                    <input type="hidden" name="session_key" value="{{ $lastsegment }}">
                                                    <input type="hidden" name="id" value="{{ $id }}">
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr class="text-center no-found">
                                        <td colspan="7">{{__('No Data Found.!')}}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>


                        {{-- <div class="total-section mt-3">
                            <div class="row align-items-center">
                                <div class="col-md-6 col-12">
                                    <div class="left-inner ">
                                    <div class="d-flex text-end justify-content-end align-items-center">
                                        {{ Form::number('discount',null, array('class' => ' form-control discount','required'=>'required','placeholder'=>__('Discount'))) }}
                                        {{ Form::hidden('discount_hidden', '',['id' => 'discount_hidden']) }}
                                    </div>
                                </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="right-inner mt-3">
                                        <div class="d-flex text-end justify-content-md-end  justify-content-flex-start">
                                            <h6 class="mb-0 text-dark" style=" color: black !important; ">{{__('Sub Total')}} :</h6>
                                            <h6 class="mb-0 text-dark subtotal_price" id="displaytotal" style=" color: black !important; ">{{  \App\Models\Utility::priceFormat($total) }}</h6>
                                        </div>

                                    <div class="d-flex align-items-center justify-content-md-end  justify-content-flex-start">
                                        <h6 class="" style=" color: black !important; ">{{__('Total')}} :</h6>
                                        <h6 class="totalamount"  style=" color: black !important; ">{{ \App\Models\Utility::priceFormat($total) }}</h6>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-3" id="btn-pur">
                                @can('Create Pos')
                                    <button type="button" class="btn btn-primary rounded"  data-ajax-popup="true" data-size="xl"
                                            data-align="centered" data-url="{{route('pos.create')}}" data-title="{{__('POS Invoice')}}"
                                            @if(session($lastsegment) && !empty(session($lastsegment)) && count(session($lastsegment)) > 0) @else disabled="disabled" @endif>
                                        {{ __('PAY') }}
                                    </button>
                                @endcan
                                <div class="tab-content btn-empty text-end">
                                    <a href="#" class="btn btn-danger bs-pass-para rounded m-0"  data-toggle="tooltip" data-original-title="{{ __('Empty Cart') }}"
                                        data-confirm="{{ __('Are You Sure?') }}" data-text="{{__('This action can not be undone. Do you want to continue?')}}"
                                        data-confirm-yes="delete-form-emptycart">{{ __('Empty Cart') }}
                                    </a>
                                    {!! Form::open(['method' => 'post', 'url' => ['empty-cart'],'id' => 'delete-form-emptycart']) !!}
                                    <input type="hidden" name="session_key" value="{{ $lastsegment }}" id="empty_cart">
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div> --}}


                        <div class="total-section pdp-discount mt-3">
                            <div class="row align-items-center">
                                <div class="col-xxl-6 col-xl-12 col-sm-12 col-12">
                                    <div class="left-inner d-flex">
                                            <span>{{__('Discount in our product')}}</span>
                                            <div class="d-flex text-end justify-content-end align-items-center">
                                                {{ Form::number('discount',null, array('class' => ' form-control discount','required'=>'required','placeholder'=>__('Discount'))) }}
                                                {{ Form::hidden('discount_hidden', '',['id' => 'discount_hidden']) }}
                                            </div>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-xl-12 col-sm-12 col-12">
                                        <div class="right-inner d-flex justify-content-between ">
                                            <div class="billing-price d-flex justify-content-between">
                                                <h6 class="mb-0 text-dark">{{ __('Sub Total') }} :</h6>
                                                <h6 class="mb-0 text-dark subtotal_price" id="displaytotal">
                                                    {{  \App\Models\Utility::priceFormat($total) }}
                                                </h6>
                                            </div>

                                            <div
                                                class="d-flex justify-content-between">
                                                <h6 class="mb-0">{{ __('Total') }} :</h6>
                                                <h6 class="totalamount mb-0">
                                                    {{ \App\Models\Utility::priceFormat($total) }}
                                                </h6>
                                            </div>
                                        </div>
                                        {{-- <div class="billing-price d-flex justify-content-between">
                                            <span class="mb-0 text-dark">{{ __('You are saving') }} :</span>
                                            <p class="mb-0 text-dark discount_price" id="discounttotal">
                                                {{ \App\Models\Utility::priceFormat($total) }}
                                            </p>
                                        </div> --}}

                                        <div class="d-flex align-items-center justify-content-between pt-3" id="btn-pur">
                                            <div class="tab-content btn-empty text-end">
                                                <a href="#" class="btn btn-danger bs-pass-para rounded m-0"  data-toggle="tooltip" data-original-title="{{ __('Empty Cart') }}"
                                                    data-confirm="{{ __('Are You Sure?') }}" data-text="{{__('This action can not be undone. Do you want to continue?')}}"
                                                    data-confirm-yes="delete-form-emptycart">{{ __('Empty Cart') }}
                                                </a>
                                                {!! Form::open(['method' => 'post', 'url' => ['empty-cart'],'id' => 'delete-form-emptycart']) !!}
                                                <input type="hidden" name="session_key" value="{{ $lastsegment }}" id="empty_cart">
                                                {!! Form::close() !!}
                                            </div>
                                            @can('Create Pos')
                                                <button type="button" class="btn btn-primary rounded"  data-ajax-popup="true" data-size="xl"
                                                        data-align="centered" data-url="{{route('pos.create')}}" data-title="{{__('POS Invoice')}}"
                                                        @if(session($lastsegment) && !empty(session($lastsegment)) && count(session($lastsegment)) > 0) @else disabled="disabled" @endif>
                                                    {{ __('PAY') }}
                                                </button>
                                            @endcan

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $( document ).ready(function() {

            $( "#vc_name_hidden" ).val($('.customer_select').val());
            $( "#discount_hidden").val($('.discount').val());

            $(function () {
                getProductCategories();

            });

            if ($('#searchproduct').length > 0) {
                var url = $('#searchproduct').data('url');
                var store_id = $( "#store_id" ).val();
                searchProducts(url,'','0',store_id);
            }


            {{--  $( '#warehouse' ).change(function() {
            var ware_id = $( "#warehouse" ).val();
                searchProducts(url,'','0',ware_id);
            });  --}}
            $( '.customer_select' ).change(function() {
                $( "#vc_name_hidden" ).val($(this).val());
            });



            $(document).on('click', '#clearinput', function (e) {
                var IDs = [];
                $(this).closest('div').find("input").each(function () {
                    IDs.push('#' + this.id);
                });
                $(IDs.toString()).val('');
            });


            $(document).on('keyup', 'input#searchproduct', function () {
                var url = $(this).data('url');
                var value = this.value;
                var cat = $('.cat-active').children().data('cat-id');
                var store_id = $( "#store_id" ).val();
                searchProducts(url, value,cat,store_id);
            });


            function searchProducts(url, value,cat_id,store_id = '0') {
                var session_key = $('#empty_cart').val();
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'search': value,
                        'cat_id': cat_id,
                        'store_id' : store_id,
                        'session_key': session_key
                    },
                    success: function (data) {
                        $('#product-listing').html(data);
                    }
                });
            }

            function getProductCategories() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('product.categories') }}',
                    success: function (data) {

                        $('#categories-listing').html(data);
                    }
                });
            }

            $(document).on('click', '.toacart', function () {

                var sum = 0
                $.ajax({
                    url: $(this).data('url'),

                    success: function (data) {

                        if (data.code == '200') {

                            $('#displaytotal').text(addCommas(data.product.subtotal));
                            $('.totalamount').text(addCommas(data.product.subtotal));

                            if ('carttotal' in data) {
                                $.each(data.carttotal, function (key, value) {
                                    // $('#product-id-' + value.id + ' .subtotal').text(addCommas(value.subtotal));
                                    // sum += value.subtotal;
                                    if(value.variant_id == 0){
                                        $('#product-id-' + value.id + ' .subtotal').text(addCommas(value.subtotal));
                                        sum += value.subtotal;
                                    }else{
                                        $('#product-variant-id-' + value.variant_id + ' .subtotal').text(addCommas(value.variant_subtotal));
                                        sum += value.variant_subtotal;
                                    }
                                });
                                $('#displaytotal').text(addCommas(sum));

                                $('.totalamount').text(addCommas(sum));

                        $('.discount').val('');
                            }

                            $('#tbody').append(data.carthtml);
                            $('.no-found').addClass('d-none');
                            $('.carttable #product-id-' + data.product.id + ' input[name="quantity"]').val(data.product.quantity);
                            $('#btn-pur button').removeAttr('disabled');
                            $('.btn-empty button').addClass('btn-clear-cart');

                            }
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        show_toastr('{{ __("Error") }}', data.error, 'error');
                    }
                });
            });

            $(document).on('change keyup', '#carthtml input[name="quantity"]', function (e) {

                e.preventDefault();
                var ele = $(this);
                var sum = 0;
                var quantity = ele.closest('span').find('input[name="quantity"]').val();
                var discount = $('.discount').val();
                var session_key = $('#empty_cart').val();
                if(quantity != null && quantity != 0){
                    $.ajax({
                        url: ele.data('url'),
                        method: "patch",
                        data: {
                            id: ele.attr("data-id"),
                            quantity: quantity,
                            discount:discount,
                            session_key: session_key
                        },
                        success: function (data) {

                            if (data.code == '200') {

                                if (quantity == 0) {
                                    ele.closest(".row").hide(250, function () {
                                        ele.closest(".row").remove();
                                    });
                                    if (ele.closest(".row").is(":last-child")) {
                                        $('#btn-pur button').attr('disabled', 'disabled');
                                        $('.btn-empty button').removeClass('btn-clear-cart');
                                    }
                                }

                                $.each(data.product, function (key, value) {
                                    // sum += value.subtotal;
                                    // $('#product-id-' + value.id + ' .subtotal').text(addCommas(value.subtotal));
                                    if(value.variant_id == 0){
                                        $('#product-id-' + value.id + ' .subtotal').text(addCommas(value.subtotal));
                                        sum += value.subtotal;
                                    }else{
                                        $('#product-variant-id-' + value.variant_id + ' .subtotal').text(addCommas(value.variant_subtotal));
                                        sum += value.variant_subtotal;
                                    }
                                });

                                $('#displaytotal').text(addCommas(sum));
                                if(discount <= sum){
                                    $('.totalamount').text(data.discount);
                                }
                                else{
                                    $('.totalamount').text(addCommas(0));
                                }
                            }
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('{{ __("Error") }}', data.error, 'error');
                        }
                    });
                }
            });

            $(document).on('click', '.remove-from-cart', function (e) {
                e.preventDefault();

                var ele = $(this);
                var sum = 0;

                if (confirm('{{ __("Are you sure?") }}')) {
                    ele.closest(".row").hide(250, function () {
                        ele.closest(".row").parent().parent().remove();
                    });
                    if (ele.closest(".row").is(":last-child")) {
                        $('#btn-pur button').attr('disabled', 'disabled');
                        $('.btn-empty button').removeClass('btn-clear-cart');
                    }
                    $.ajax({
                        url: ele.data('url'),
                        method: "DELETE",
                        data: {
                            id: ele.attr("data-id"),

                        },
                        success: function (data) {
                            if (data.code == '200') {

                                $.each(data.product, function (key, value) {
                                    sum += value.subtotal;
                                    $('#product-id-' + value.id + ' .subtotal').text(addCommas(value.subtotal));
                                });

                                $('#displaytotal').text(addCommas(sum));

                                show_toastr('success', data.success, 'success')
                            }
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('{{ __("Error") }}', data.error, 'error');
                        }
                    });
                }
            });

            $(document).on('click', '.btn-clear-cart', function (e) {
                e.preventDefault();

                if (confirm('{{ __("Remove all items from cart?") }}')) {

                    $.ajax({
                        url: $(this).data('url'),
                        data: {
                            session_key: session_key
                        },
                        success: function (data) {
                            location.reload();
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('{{ __("Error") }}', data.error, 'error');
                        }
                    });
                }
            });

            $(document).on('click', '.btn-done-payment', function (e) {
                e.preventDefault();
                var ele = $(this);

                $.ajax({
                    url: ele.data('url'),

                    method: 'GET',
                    data: {
                        vc_name: $('#vc_name_hidden').val(),
                        warehouse_name: $('#warehouse_name_hidden').val(),
                        discount : $('#discount_hidden').val(),
                    },
                    beforeSend: function () {
                        ele.remove();
                    },
                    success: function (data) {

                        if (data.code == 200) {
                            show_toastr('success', data.success, 'success')
                        }

                    },
                    error: function (data) {
                        data = data.responseJSON;
                        show_toastr('{{ __("Error") }}', data.error, 'error');
                    }

                });

            });

            $(document).on('click', '.category-select', function (e) {
                var cat = $(this).data('cat-id');
                var white = 'text-white';
                var dark = 'text-dark';
                $('.category-select').find('.tab-btns').removeClass('btn-primary')
                $(this).find('.tab-btns').addClass('btn-primary')
                $('.category-select').parent().removeClass('cat-active');
                $('.category-select').find('.card-title').removeClass('text-white').addClass('text-dark');
                $('.category-select').find('.card-title').parent().removeClass('text-white').addClass('text-dark');
                $(this).find('.card-title').removeClass('text-dark').addClass('text-white');
                $(this).find('.card-title').parent().removeClass('text-dark').addClass('text-white');
                $(this).parent().addClass('cat-active');
                var url = '{{ route('search.products') }}'
                var store_id=$('#store_id').val();
                searchProducts(url,'',cat,store_id);
            });

            $(document).on('change keyup', '.discount', function () {

                var discount = $('.discount').val();
                var total = $('#displaytotal').text();
                var maintotal = parseFloat(total.replace("$","").replace(",",""))
                if(discount <= maintotal){
                    $( "#discount_hidden" ).val(discount);
                }else{
                    $( "#discount_hidden" ).val(maintotal);
                }
                $.ajax({
                    url: "{{route('cartdiscount')}}",
                    method: 'POST',
                    data: {discount: discount,},
                    success: function (data)
                    {
                        if(discount <= maintotal){
                            $('.totalamount').text(data.total);
                        }else{
                            $('.totalamount').text(addCommas(0));
                        }
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        show_toastr('{{ __("Error") }}', data.error, 'error');
                    }
                });


                var price = {{$total}}
                var total_amount = price-discount;
                $('.totalamount').text(total_amount);


            });

        });


        // Product Variant script

        $(document).on('change', '.variant-selection', function() {
                var variants = [];
                $(".variant-selection").each(function(index, element) {
                    if (element.value != '' && element.value != undefined) {
                        var el_val = element.value;
                        variants.push(el_val);
                    }
                });
                if (variants.length > 0) {

                    $.ajax({
                        url: '{{ route('get.products.variant.quantity') }}',
                        data: {
                            "_token": $('meta[name="csrf-token"]').attr('content'),
                            variants: variants.join(' : '),
                            product_id: $('#product_id').val()
                        },

                        success: function(data) {
                            if (data.variant_id == 0) {
                                $('.variant_stock1').addClass('d-none');
                                $('.variation_price1').html("@lang('Please Select Variants')");
                                // $('#variant_qty').val('0');
                            } else {
                                var qty = "@lang('Price') : "  + data.price;
                                var amount = "@lang('QTY') : " + data.quantity;
                                $('.variation_price1').html(qty);
                                $('#variant_id').val(data.variant_id);
                                // $('#variant_qty').val(data.quantity);
                                $('.variant_qty').html(amount);
                                $('.variant_stock1').removeClass('d-none');
                                if (data.quantity != 0) {
                                    $('.variant_stock1').html("@lang('In Stock')");
                                    $(".variant_stock1").css({
                                        "backgroundColor": "#C2FFA5",
                                        "color": "#58A336"
                                    });
                                } else {
                                    $(".variant_qty").css({
                                        // "backgroundColor": "#FFA5A5",
                                        "color": "rgb(253 58 110)"
                                    });
                                    $('.variant_qty').html("@lang('Out Of Stock')");
                                }
                            }
                        }
                    });
                }
            });


            $(document).on('click', '.toacartvariant', function () {

            var sum = 0;
            var id = $(this).attr('data-id');
            var session_key = "{{ $lastsegment }}";
            var variants = [];
                $(".variant-selection").each(function(index, element) {
                    variants.push(element.value);
                });

                if (jQuery.inArray('0', variants) != -1) {
                    show_toastr('Error', "{{ __('Please select all option.') }}", 'error');
                    return false;
                }

                var variation_ids = $('#variant_id').val();

            $.ajax({
                    url: '{{ route('addToCartVariant', ['__product_id', 'session_key', 'variation_id']) }}'
                        .replace('__product_id', id).replace('session_key', session_key).replace('variation_id', variation_ids ?? 0),//$(this).data('url'),
                    data: {
                        "_token": "{{ csrf_token() }}",
                        variants: variants.join(' : '),
                    },
                success: function (data) {
                    if (data.code == '200') {

                        $('#displaytotal').text(addCommas(data.product.variant_subtotal));
                        $('.totalamount').text(addCommas(data.product.variant_subtotal));

                        if ('carttotal' in data) {
                            $.each(data.carttotal, function (key, value) {
                                    if(value.variant_id == 0){
                                        $('#product-id-' + value.id + ' .subtotal').text(addCommas(value.subtotal));
                                        sum += value.subtotal;
                                    }else{
                                        $('#product-variant-id-' + value.variant_id + ' .subtotal').text(addCommas(value.variant_subtotal));
                                        sum += value.variant_subtotal;
                                    }
                            });
                            $('#displaytotal').text(addCommas(sum));

                            $('.totalamount').text(addCommas(sum));

                        $('.discount').val('');
                        }
                        $('#tbody').append(data.carthtml);
                        $('.no-found').addClass('d-none');
                        $('.carttable #product-variant-id-' + data.product.variant_id + ' input[name="quantity"]').val(data.product.quantity);
                        $('#btn-pur button').removeAttr('disabled');
                        $('.btn-empty button').addClass('btn-clear-cart');

                        }
                },
                error: function (data) {
                    data = data.responseJSON;
                    show_toastr('{{ __("Error") }}', data.error, 'error');
                }
            });
        });

            $(document).on('click', '.add_to_cart_variant', function () {
                $('#commonModal').modal('hide');
            });

    </script>
    <script>
        const storeSetting = @json($storesetting);
        var site_currency_symbol_position = storeSetting['currency_symbol_position'];
        var site_currency_symbol_space = storeSetting['currency_symbol_space'];
        var site_currency_symbol = storeSetting['currency'];
        window.translations = {
            yes: "{{ __('Yes') }}",
            cancel: "{{ __('CANCEL') }}"
        };
    </script>
@endpush