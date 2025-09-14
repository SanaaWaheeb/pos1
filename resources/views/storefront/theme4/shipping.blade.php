@extends('storefront.layout.theme4')
@section('page-title')
    {{__('Shipping')}}
@endsection
@php
     $productImg = \App\Models\Utility::get_file('uploads/is_cover_image/');
@endphp
@section('content')
<div class="wrapper">
    <section class="cart-section padding-bottom padding-top">
        <div class="container">
            <div class="row align-items-center cart-head" style="margin-bottom: 50px">
                <div class="col-lg-3 col-md-12 col-12">
                    <div class="cart-title">
                        <h2>{{ __('Hotel Booking') }}</h2>
                    </div>
                </div>
                <div class="col-lg-9 col-md-12 col-12 justify-content-end">
                    <div class="cart-btns" style="pointer-events: none">
                        <a href="#">1 - {{ __('My Cart') }}</a>
                        <a href="#" class="active-btn">2 -{{ __('Customer') }}</a>
                        <a href="#">3 - {{ __('Payment') }}</a>
                    </div>
                </div>

            </div>
            {{ Form::model($cust_details, ['route' => ['store.customer', $store->slug], 'method' => 'POST']) }}
                <div class="row">
                    <!-- Booking Information -->
                    <div class="col-lg-8 col-12">
                        <div class="customer-info">
                            <h5>{{ __('Booking Information') }}</h5>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    {{ Form::label('date_range', __('Choose Dates'), ['class' => 'form-label']) }} <span style="color:red">*</span>
                                    <input type="hidden" id="check-in-date" name="check_in_date">
                                    <input type="hidden" id="check-out-date" name="check_out_date">
                                    <input type="hidden" id="number_of_nights" name="number_of_nights">
                                        @csrf
                                        @include('components.price-calendar', ['readOnly' => true])
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon Logic -->
                    <div class="col-lg-4 col-12">
                        <div class="coupon-form">
                            <div class="coupon-header">
                                <h4>{{__('Coupon')}}</h4>
                            </div>
                            <div class="coupon-body">
                                <div class="input-wrapper">
                                    <input type="text" id="stripe_coupon" name="coupon" class="coupon hidd_val" placeholder="{{ __('Enter Coupon Code') }}">
                                    <input type="hidden" name="coupon" class="hidden_coupon" value="">
                                </div>
                                <div class="btn-wrapper apply-stripe-btn-coupon">
                                    <button class="btn apply-coupon">{{ __('Apply') }}</button>
                                </div>
                            </div>
                        </div>                
                    </div>
                </div>

                <div class="row">
                    <!-- Customer Information -->
                    <div class="col-lg-8 col-12">
                        <div class="customer-info">
                            <h5>{{ __('Customer Information') }}</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    {{Form::label('name',__('First Name'),array("class"=>"form-control-label")) }} <span style="color:red">*</span>
                                    {{Form::text('name',old('name'),array('class'=>'form-control','placeholder'=>__('Enter Your First Name'),'required'=>'required'))}}
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    {{Form::label('last_name',__('Last Name'),array("class"=>"form-control-label")) }} <span style="color:red">*</span>
                                    {{Form::text('last_name',old('last_name'),array('class'=>'form-control','placeholder'=>__('Enter Your Last Name'),'required'=>'required'))}}
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    {{Form::label('phone',__('Phone'),array("class"=>"form-control-label")) }} <span style="color:red">*</span>
                                    {{Form::text('phone',old('phone'),array('class'=>'form-control','placeholder'=>'(+966) 560747785','required'=>'required'))}}
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    {{Form::label('email',__('Email'),array("class"=>"form-control-label")) }} <span style="color:red">*</span>
                                    {{Form::email('email',(Utility::CustomerAuthCheck($store->slug) ? Auth::guard('customers')->user()->email : ''),array('class'=>'form-control','placeholder'=>__('Enter Your Email Address'),'required'=>'required'))}}
                                </div>
                            </div>                            
                            @if(!empty($store_payment_setting['custom_field_title_1']))
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    {{Form::label('custom_field_title_1',$store_payment_setting['custom_field_title_1'],array("class"=>"form-control-label")) }} <span style="color:red">*</span>
                                    {{Form::text('custom_field_title_1',old('custom_field_title_1'),array('class'=>'form-control','placeholder'=>'Enter '.$store_payment_setting['custom_field_title_1'],'required'=>'required'))}}
                                </div>
                            </div>
                            @endif
                            @if(!empty($store_payment_setting['custom_field_title_2']))
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        {{Form::label('custom_field_title_2',$store_payment_setting['custom_field_title_2'],array("class"=>"form-control-label")) }} <span style="color:red">*</span>
                                        {{Form::text('custom_field_title_2',old('custom_field_title_2'),array('class'=>'form-control','placeholder'=>'Enter '.$store_payment_setting['custom_field_title_1'],'required'=>'required'))}}
                                    </div>
                                </div>
                            @endif
                            @if(!empty($store_payment_setting['custom_field_title_3']))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            {{Form::label('custom_field_title_3',$store_payment_setting['custom_field_title_3'],array("class"=>"form-control-label")) }} <span style="color:red">*</span>
                                            {{Form::text('custom_field_title_3',old('custom_field_title_3'),array('class'=>'form-control','placeholder'=>'Enter '.$store_payment_setting['custom_field_title_1'],'required'=>'required'))}}
                                        </div>
                                    </div>
                            @endif
                            
                            @if(!empty($store_payment_setting['custom_field_title_4']))
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        {{Form::label('custom_field_title_4',$store_payment_setting['custom_field_title_4'],array("class"=>"form-control-label")) }} <span style="color:red">*</span>
                                        {{Form::text('custom_field_title_4',old('custom_field_title_4'),array('class'=>'form-control','placeholder'=>'Enter '.$store_payment_setting['custom_field_title_1'],'required'=>'required'))}}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Booking Summary -->
                    <div class="col-lg-4 col-12">
                        <div class="mini-cart" id="card-summary" style="margin: 40px 0px">
                            <div class="mini-cart-header">
                                <h4>{{ __('Summary') }}</h4>
                            </div>
                            <div id="cart-body" class="mini-cart-has-item">
                                <div class="mini-cart-body">
                                    @if (!empty($products))
                                        @php
                                            $total = 0;
                                            $sub_tax = 0;
                                            $sub_total = 0;
                                        @endphp
                                        @foreach ($products as $product)
                                            @if (isset($product['variant_id']) && !empty($product['variant_id']))
                                                <div class="mini-cart-item" style="margin: 0; width: 100%">
                                                    <div class="mini-cart-details-status">
                                                        <span>{{$product['quantity']}} X </span>
                                                        <div data-label="Product" class="mini-cart-image">
                                                            <a href="">
                                                                <img src="{{$productImg .$product['image']}}" alt="img">
                                                            </a>
                                                        </div>
                                                        <div data-label="Name">
                                                            <a href="#">{{$product['product_name'].' - ( ' . $product['variant_name'] .' ) '}}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $total += $totalprice;
                                                @endphp
                                            @else
                                                <div class="mini-cart-item" style="margin: 0; width: 100%">
                                                    <div class="mini-cart-details-status">
                                                        <span>{{$product['quantity']}} X </span>
                                                        <div data-label="Product" class="mini-cart-image">
                                                            <a href="">
                                                                <img src="{{$productImg .$product['image']}}" alt="img">
                                                            </a>
                                                        </div>
                                                        <div data-label="Name">
                                                            <a href="#">{{$product['product_name']}}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <div class="mini-cart-footer">
                                    {{-- <div class="u-save d-flex justify-content-between">
                                        <div class="cpn-lbl">{{ __('Subtotal') }}</div>
                                        <div class="cpn-price">{{\App\Models\Utility::priceFormat( !empty($sub_total)?$sub_total:'0')}}</div>
                                    </div> --}}
                                    {{-- @if($store->enable_shipping == "on")
                                        <div class="u-save d-flex justify-content-between">
                                            <div class="cpn-lbl">{{__('Shipping Price')}} </div>
                                            <div class="cpn-price shipping_price" data-value=""></div>
                                        </div>
                                    @endif --}}
                                    @foreach($taxArr['tax'] as $k=>$tax)
                                        <div class="u-save d-flex justify-content-between">
                                            @php
                                                $rate = $taxArr['rate'][$k];
                                            @endphp
                                            <div class="cpn-lbl">{{$tax}}</div>
                                            <div class="cpn-price">{{\App\Models\Utility::priceFormat($rate)}}</div>
                                        </div>
                                    @endforeach
                                    <!-- Display service per night -->
                                    <div class="u-save d-flex justify-content-between">
                                        <div class="cpn-lbl">{{ __('Check-in Date') }}</div>
                                        <div id="check-in"></div>
                                    </div>
                                    <div class="u-save d-flex justify-content-between">
                                        <div class="cpn-lbl">{{ __('Check-out Date') }}</div>
                                        <div id="check-out"></div>
                                    </div>
                                    <div class="u-save d-flex justify-content-between">
                                        <div class="cpn-lbl">{{__('Number of Nights')}}</div>
                                        <div id="num-nights">{{__('Night')}}</div>
                                    </div>
                                    <div class="u-save d-flex justify-content-between">
                                        <div class="cpn-lbl">{{ __('Coupon') }}</div>
                                        <div class="cpn-price dicount_price">{{\App\Models\Utility::priceFormat(0)}}</div>
                                    </div>
                                    <div
                                        class="mini-cart-footer-total-row d-flex align-items-center justify-content-between">
                                        <div class="mini-total-lbl">
                                            {{__('Total')}}
                                        </div>
                                        <div class="mini-total-price final_total_price" id="total_value" data-value="666">
                                            <!-- <input type="hidden" class="product_total" value="{{$total}}"> -->
                                            <!-- <input type="hidden" class="total_pay_price" value="{{App\Models\Utility::priceFormat($total)}}"> -->
                                            <input type="hidden" name="total" id="total-booking-price" value="{{ $total }}">
                                            <span class="pro_total_price" data-value="{{ $total }}"> </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Proceed to checkout button -->
                    <div class="col-md-12 col-12"  style="margin-top: 80px">
                        <div class="pagination-btn d-flex align-items-center justify-content-center " style="width:100% ">
                            <button type="submit" class="next-btn btn">{{__('Proceed to Checkout')}} <i class="fas fa-shopping-basket"></i></button>
                        </div>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </section>
</div>
@endsection
@push('script-page')
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    <script>
        function billing_data() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        }

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form'); // adjust selector if your form has ID or class

            form.addEventListener('submit', function(e) {
                const checkIn = document.getElementById('check-in-date').value;
                const checkOut = document.getElementById('check-out-date').value;
                const nights = document.getElementById('number_of_nights').value;

                if (!checkIn || !checkOut || !nights || nights <= 0) {
                    e.preventDefault(); // 🛑 Stop form submission
                    alert('Please select a valid date range first!');
                }
            });
        });

        // ------------- Handle Apply/Remove coupon -------------
        $(document).on('click', '.apply-coupon', function(e) {
            e.preventDefault();

            var ele = $(this);
            var coupon = ele.closest('.row').find('.coupon').val();
            var hidden_field = $('.hidden_coupon').val();
            var price = $('.pro_total_price').attr('data-value');
            // var shipping_price = $('#card-summary .shipping_price').attr('data-value');
            if (coupon == hidden_field && coupon != "" && e.originalEvent) {
                show_toastr('Error', 'Coupon Already Used', 'error');
            } else {
                const x =  {{ $store->id }};
                if (coupon != '') {
                    $.ajax({
                        url: '{{ route('apply.productcoupon') }}',
                        datType: 'json',
                        data: {
                            price: price,
                            // shipping_price: shipping_price,
                            store_id: {{ $store->id }},
                            coupon: coupon
                        },
                        success: function(data) {
                            $('#stripe_coupon, #paypal_coupon').val(coupon);
                            if (data.is_success) {
                                $('.hidden_coupon').val(coupon);
                                $('.hidden_coupon').attr(data);

                                // update coupon price in summary
                                $('.dicount_price').html(data.discount_price);
                                const couponTotal = data.discount_price?.replace('-$', '');
                                $('.dicount_price').attr('data-value', couponTotal);

                                var html = '';
                                html +=
                                    '<span class="text-sm font-weight-bold s-p-total pro_total_price" data-value="' +
                                    data.final_price_data_value + '">' + data.final_price + '</span>'
                                $('.final_total_price').find('.pro_total_price').replaceWith(html);

                                // Update hidden input field
                                $('#total-booking-price').val(data.final_price);

                                if (e.originalEvent) show_toastr('Success', data.message, 'success');
                            } else {
                                show_toastr('Error', data.message, 'error');
                            }
                        }
                    })
                } else {

                    $.ajax({
                        url: '{{ route('apply.removecoupn') }}',
                        datType: 'json',
                        data: {
                            price: "price",
                            shipping_price: "shipping_price",
                            slug: {{ $store->id }},
                            coupon: "coupon"
                        },
                        success: function(data) {}
                    });
                    var hidd_cou = $('.hidd_val').val();

                    if (hidd_cou == "") {
                        var total_pa_val = $(".total_pay_price").val();
                        $(".final_total_price").html(total_pa_val);
                        // $(".dicount_price").html(0.00);

                    }
                    show_toastr('Error', '{{ __('Invalid Coupon Code.') }}', 'error');
                }
            }

        });
    </script>
@endpush
