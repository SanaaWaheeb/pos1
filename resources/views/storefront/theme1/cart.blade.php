@extends('storefront.layout.theme1')
@section('page-title')
    {{ __('Cart') }}
@endsection
@php
$cart = session()->get($store->slug);
$imgpath=\App\Models\Utility::get_file('uploads/is_cover_image/');
@endphp
@section('content')
    @php
        $cart = session()->get($store->slug);
        $productImg = \App\Models\Utility::get_file('uploads/is_cover_image/'); 
    @endphp
    @if (!empty($cart['products']) || ($cart['products'] = []))
        <div class="wrapper" style="flex: 1; display: flex; justify-content: center; align-items: center">
            <section class="cart-section">
                <div class="container d-flex justify-content-center">
                    <div class="row ">
                        <div class="col-lg-3 col-md-12 col-12">
                            <div class="cart-title">
                                <h2>{{ __('My Cart') }}</h2>
                            </div>
                        </div>
                        {{-- <div class="col-lg-9 col-md-12 col-12 justify-content-end">
                            <div class="cart-btns">
                                <a href="{{ route('store.cart', $store->slug) }}" class="active-btn">1 - {{ __('My Cart') }}</a>
                                <a href="{{ route('user-address.useraddress', $store->slug) }}">2 -{{ __('Customer') }}</a>
                                <a href="{{ route('store-payment.payment', $store->slug) }}">3 - {{ __('Payment') }}</a>
                            </div>
                        </div> --}}
                        
                    </div>
                    <div class="row justify-content-center" id="cart-items-list">
                        <table class="cart-tble"> 
                            {{-- <thead>
                            <tr>
                                <th scope="col">{{ __('Product') }}</th>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Price') }}</th>
                                <th scope="col">{{ __('quantity') }}</th>
                                <th scope="col">{{ __('Tax') }}</th> 
                                <th scope="col">{{ __('Total') }}</th>
                            </tr>
                            </thead> --}}

                            <div id="cart-body" class="mini-cart-has-item cart-tble">
                                @if(!empty($products))
                                    @php
                                        $total = 0;
                                        $sub_tax = 0;
                                        $sub_total= 0;
                                    @endphp
                                    @foreach($products['products'] as $key => $product)
                                        @if ($product['variant_id'] != 0)
                                            <div class="mini-cart-body" data-id="{{$key}}" id="product-variant-id-{{ $product['variant_id'] }}">
                                                <div class="mini-cart-item">
                                                    <div class="mini-cart-image" data-label="Product">
                                                        <a href="">
                                                            <img src="{{$productImg .$product['image']}}" alt="img">
                                                        </a>
                                                    </div>
                                                    <div class="mini-cart-details-cart">
                                                        <p data-label="Name" class="mini-cart-title">
                                                            <a class="text-dark c-list-title mb-0 cart_word_break">{{$product['product_name'] .' - '. $product['variant_name']}}</a>
                                                        </p>

                                                        <div class="pvarprice d-flex align-items-center justify-content-between">
                                                            <div data-label="quantity" class="price">
                                                                <div class="qty-spinner" data-id="{{$key}}">
                                                                    <button type="button" class="quantity-decrement qty-minus product_qty">
                                                                        <svg width="12" height="2" viewBox="0 0 12 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M0 0.251343V1.74871H12V0.251343H0Z" fill="#61AFB3"></path>
                                                                        </svg>
                                                                    </button>
                                                                    <input type="number" class="quantity product_qty_input bx-cart-qty" data-cke-saved-name="quantity" name="quantity" data-id="{{$product['product_id']}}" value="{{$product['quantity']}}" id="product_qty">
                                                                    <button type="button" class="quantity-increment qty-plus product_qty">
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M6.74868 5.25132V0H5.25132V5.25132H0V6.74868H5.25132V12H6.74868V6.74868H12V5.25132H6.74868Z" fill="#61AFB3"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <a class="remove_item">
                                                                {{\App\Models\Utility::priceFormat($totalprice)}}
                                                            </a>
                                                        </div>

                                                        <div data-label="Tax"> 
                                                            @php
                                                                $total_tax=0;
                                                            @endphp
                                                            @if(!empty($product['tax']))
                                                            @foreach($product['tax'] as $k => $tax)
                                                                @php
                                                                    $sub_tax = ($product['variant_price']* $product['quantity'] * $tax['tax']) / 100;
                                                                    $total_tax += $sub_tax;
                                                                @endphp
                                                                <p class="t-gray p-title mb-0 variant_tax_{{ $k }}">
                                                                    {{$tax['tax_name'].' '.$tax['tax'].'%'.' ('.$sub_tax.')'}}
                                                                </p>
                                                            @endforeach
                                                            @else
                                                                -
                                                            @endif
                                                        </div> 

                                                        <td data-label="Total">
                                                            @php
                                                                $totalprice = $product['variant_price'] * $product['quantity'] + $total_tax;
                                                                $total += $totalprice;
                                                            @endphp
                                                            <p class="pt-price t-black15 subtotal">
                                                                {{\App\Models\Utility::priceFormat($totalprice)}}
                                                            </p>
                                                            <a href="#" class="action-item mr-2 remove-btn" data-toggle="tooltip" data-original-title="{{__('Move to trash')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-product-cart-{{$key}}').submit();"><i class="fas fa-times"></i></a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['delete.cart_item',[$store->slug,$product['product_id'],$product['variant_id']]],'id'=>'delete-product-cart-'.$key]) !!}
                                                            {!! Form::close() !!}
                                                        </td>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mini-cart-item" ata-id="{{$key}}" id="product-id-{{ $product['product_id'] }}">
                                                <div class="mini-cart-details-cart">
                                                    <div data-label="Product" class="mini-cart-image">
                                                        <a href="">
                                                            <img src="{{$productImg .$product['image']}}" alt="img">
                                                        </a>
                                                    </div>

                                                    <div class="cart-item-decr">
                                                        <div data-label="Name">
                                                            <a class="text-dark c-list-title mb-0 cart_word_break">{{$product['product_name']}}</a>
                                                        </div>

                                                        <div style="display: flex; align-items: center; justify-content: space-between">
                                                            <div data-label="Total">
                                                                @php
                                                                    $total_tax=0;
                                                                @endphp
                                                                @if(!empty($product['tax']))
                                                                @foreach($product['tax'] as $k => $tax)
                                                                    @php
                                                                    
                                                                        $sub_tax = ($product['price']* $product['quantity'] * $tax['tax']) / 100;
                                                                        $total_tax += $sub_tax;
                                                                    @endphp
                                                                @endforeach
                                                                @endif
                                                                @php
                                                                    $totalprice = $product['price'] * $product['quantity'] + $total_tax;
                                                                    $total += $totalprice;
                                                                @endphp
                                                                <span class="subtotal">{{\App\Models\Utility::priceFormat($totalprice)}}</span>
                                                            </div>

                                                            <div data-label="quantity" style="flex: 1; margin: 0 0.6rem">
                                                                <div class="qty-spinner" data-id="{{$key}}">
                                                                    <button type="button" class="quantity-decrement qty-minus product_qty">
                                                                        <svg width="12" height="2" viewBox="0 0 12 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M0 0.251343V1.74871H12V0.251343H0Z" fill="#61AFB3"></path>
                                                                        </svg>
                                                                    </button>
                                                                    <input type="number" class="quantity product_qty_input bx-cart-qty" data-cke-saved-name="quantity" name="quantity" data-id="{{$product['product_id']}}" value="{{$product['quantity']}}" id="product_qty">
                                                                    <button type="button" class="quantity-increment qty-plus product_qty">
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M6.74868 5.25132V0H5.25132V5.25132H0V6.74868H5.25132V12H6.74868V6.74868H12V5.25132H6.74868Z" fill="#61AFB3"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
    
                                                            <div>
                                                                <a href="#" class="action-item mr-2 remove-btn" data-toggle="tooltip" data-original-title="{{__('Move to trash')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-product-cart-{{$key}}').submit();"><i class="fa-solid fa-trash-can"></i></a>
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['delete.cart_item',[$store->slug,$product['product_id'],$product['variant_id']]],'id'=>'delete-product-cart-'.$key]) !!}
                                                                {!! Form::close() !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                               
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            <div class="coupon-form">
                            <div class="coupon-body">
                                <form action="">
                                    <div class="input-wrapper" style="gap: 10px">
                                        <input type="text" id="stripe_coupon" name="coupon" class="coupon hidd_val" placeholder="{{ __('Enter Coupon Code') }}">
                                        <input type="hidden" name="coupon" class="hidden_coupon" value="">
                                        <div class="btn-wrapper apply-stripe-btn-coupon">
                                            <button type="submit" class="btn apply-coupon">{{ __('Apply') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            </div>

                        </table>
                    </div>
                    <div class="checkout-box">
                        <div class="align-items-center justify-content-center">
                            <div class="col-md-4 col-12">

                                <div class="price-bar">
                                    <span>{{ __('Coupon') }}</span>
                                    <span id="coupon-value">{{ \App\Models\Utility::priceFormat(0) }}</span>
                                  </div>
                                  <div class="price-bar">
                                    <span>{{ __('Total value:') }}</span>
                                    <span id="displaytotal" data-original="{{ \App\Models\Utility::priceFormat($total ?? 0) }}">
                                      {{ \App\Models\Utility::priceFormat($total ?? 0) }}
                                    </span>
                                  </div>
                                  
                            </div>
                                @if($store_settings['is_checkout_login_required'] == null || $store_settings['is_checkout_login_required'] == 'off' && !Auth::guard('customers')->user())
                                    <a href="{{ route('store-payment.payment', $store->slug) }}" class="checkout-btn modal-target checkout_btn" data-modal="Checkout" id="checkout-btn">
                                        {{__('Proceed to checkout')}}
                                        <i class="fas fa-shopping-basket"></i>
                                    </a>
                                @else
                                    <a href="{{ route('store-payment.payment', $store->slug) }}" class="checkout-btn">
                                        {{__('Proceed to checkout')}}
                                        <i class="fas fa-shopping-basket"></i>
                                    </a>
                                @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @else
        <div class="wrapper" style="flex: 1; display: flex; justify-content: center; align-items: center;">
            <section class="empty-cart-section">
                {{-- <div class="bg-image bg-image-2">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1506.3 578.7">
                        <path
                            d="M 147.269 295.566 C 147.914 293.9 149.399 292.705 151.164 292.431 L 167.694 289.863 C 169.459 289.588 171.236 290.277 172.356 291.668 L 182.845 304.699 C 183.965 306.091 184.258 307.974 183.613 309.64 L 177.572 325.239 C 176.927 326.905 175.442 328.1 173.677 328.375 L 157.147 330.943 C 155.382 331.217 153.605 330.529 152.485 329.137 L 141.996 316.106 C 140.876 314.714 140.583 312.831 141.228 311.165 L 147.269 295.566 Z"
                            fill="#510fa8"></path>
                        <path
                            d="M 92.927 474.881 C 93.309 473.896 94.187 473.19 95.23 473.028 L 105.002 471.51 C 106.045 471.347 107.096 471.754 107.758 472.577 L 113.959 480.28 C 114.621 481.103 114.794 482.216 114.413 483.201 L 110.841 492.423 C 110.46 493.408 109.582 494.114 108.539 494.277 L 98.767 495.795 C 97.723 495.957 96.673 495.55 96.011 494.727 L 89.81 487.024 C 89.148 486.201 88.975 485.088 89.356 484.103 L 92.927 474.881 Z"
                            fill="#36b37e"></path>
                        <path
                            d="M 34.176 36.897 C 34.821 35.231 36.306 34.036 38.071 33.762 L 54.601 31.194 C 56.366 30.919 58.143 31.608 59.263 32.999 L 69.752 46.03 C 70.872 47.422 71.165 49.305 70.52 50.971 L 64.479 66.57 C 63.834 68.236 62.349 69.431 60.584 69.706 L 44.054 72.274 C 42.289 72.548 40.512 71.86 39.392 70.468 L 28.903 57.437 C 27.783 56.045 27.49 54.162 28.135 52.496 L 34.176 36.897 Z"
                            fill="#00b8d9"></path>
                        <path
                            d="M 975.636 9.762 C 976.101 8.561 977.171 7.7 978.443 7.502 L 990.354 5.652 C 991.626 5.454 992.907 5.95 993.714 6.953 L 1001.272 16.343 C 1002.079 17.346 1002.29 18.703 1001.826 19.903 L 997.472 31.144 C 997.008 32.344 995.938 33.205 994.666 33.403 L 982.754 35.254 C 981.483 35.451 980.202 34.956 979.395 33.953 L 971.837 24.563 C 971.03 23.559 970.818 22.203 971.283 21.002 L 975.636 9.762 Z"
                            fill="#2684ff"></path>
                        <path
                            d="M 1417.759 409.863 C 1418.404 408.197 1419.889 407.002 1421.654 406.728 L 1438.184 404.16 C 1439.949 403.885 1441.726 404.574 1442.846 405.965 L 1453.335 418.996 C 1454.455 420.388 1454.748 422.271 1454.103 423.937 L 1448.062 439.536 C 1447.417 441.202 1445.932 442.397 1444.167 442.672 L 1427.637 445.24 C 1425.872 445.514 1424.095 444.826 1422.975 443.434 L 1412.486 430.403 C 1411.366 429.011 1411.073 427.128 1411.718 425.462 L 1417.759 409.863 Z"
                            fill="#3c4858"></path>
                        <path
                            d="M 1313.903 202.809 C 1314.266 201.873 1315.1 201.201 1316.092 201.047 L 1325.381 199.604 C 1326.373 199.449 1327.372 199.837 1328.001 200.618 L 1333.895 207.941 C 1334.525 208.723 1334.689 209.782 1334.327 210.718 L 1330.932 219.484 C 1330.57 220.42 1329.735 221.092 1328.743 221.246 L 1319.454 222.689 C 1318.462 222.843 1317.464 222.457 1316.834 221.674 L 1310.94 214.351 C 1310.31 213.569 1310.146 212.511 1310.508 211.575 L 1313.903 202.809 Z"
                            fill="#ffab00"></path>
                        <path
                            d="M 1084.395 506.137 C 1084.908 504.812 1086.09 503.861 1087.494 503.643 L 1100.645 501.6 C 1102.049 501.381 1103.463 501.929 1104.354 503.036 L 1112.699 513.403 C 1113.59 514.51 1113.823 516.009 1113.31 517.334 L 1108.504 529.744 C 1107.99 531.07 1106.809 532.02 1105.405 532.239 L 1092.254 534.282 C 1090.85 534.5 1089.436 533.953 1088.545 532.845 L 1080.2 522.478 C 1079.309 521.371 1079.076 519.873 1079.589 518.547 L 1084.395 506.137 Z"
                            fill="#ff5630"></path>
                    </svg>
                </div> --}}
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 col-md-12 col-12">
                            <div class="empty-cart">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 524.67001 418.27099" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M442.17403,400.47713c2.06599,.12871,3.20692-2.43846,1.64338-3.93389l-.1557-.61814c.0204-.04935,.04089-.09868,.06153-.14794,1.23211-2.94003,4.62545-4.33201,7.57137-3.11404,9.3142,3.85087-.51966,12.69986,.21957,18.68773,.25911,2.06671,8.35473,2.18034,7.89681,4.2086,4.30482-9.41207,6.56835-19.68889,6.56478-30.02306-.0009-2.59653-.14392-5.19297-.43543-7.78281-.23975-2.11845-.56985-4.22389-.9969-6.30999-2.30966-11.27639-7.30614-22.01572-14.51084-30.98461-3.46263-1.89129-1.35074-4.85018-3.09586-8.39544-.62694-1.27868-6.21792-1.30745-6.45092-2.70892,.25019,.03272,3.86434-3.721,2.6705-5.5767-.78555-1.22107-.54106-2.7763,.4681-3.82017,.09887-.1023,.19234-.2103,.27796-.32648,2.98093-4.04443,7.09003-3.33985,9.23695,2.15406,4.58304,2.31107,4.62871,6.14647,1.81834,9.83619-1.78798,2.34745-2.03264,5.52304-3.60129,8.03604,.16157,.20664,.32958,.40684,.49112,.61348,2.96237,3.79686,5.52482,7.87809,7.68524,12.16592-.61182-4.76599,.28705-10.50831,1.8215-14.21023,1.75933-4.24835,5.06953-7.8221,7.96883-11.49665,3.46275-4.38865,10.5104-2.39707,11.12286,3.15951,.00588,.05337,.0116,.10665,.01724,.16003-.42884,.24212-.84895,.49935-1.25929,.77094-2.33882,1.54808-1.52824,5.17442,1.24391,5.60071l.06278,.00965c-.1545,1.54372-5.63258,6.40679-6.01957,7.91187,3.70514,14.30838,.93282,16.19755-10.46624,16.43703l-.59825,.8522c1.08024,3.1058,1.94956,6.2861,2.60235,9.50743,.61462,2.99021,1.042,6.01282,1.28197,9.04845,.29847,3.82994,.27396,7.6795-.04769,11.50325l.01933-.13563c.81879-4.21143,3.1039-8.1457,6.42284-10.87249,4.94421-4.06436,11.93091-5.56281,17.2652-8.83022,2.56778-1.57285,5.85959,.45742,5.41241,3.4352l-.02177,.14262c-.79432,.32315-1.56922,.69808-2.31831,1.11814-.42921,.24237-.84965,.49978-1.26032,.77165-2.33916,1.54848-1.52796,5.1753,1.24498,5.60009l.06281,.00962c.0452,.00645,.08399,.01291,.12913,.0194-1.3617,3.23628-14.33553,7.80147-16.71149,10.39229-2.31031,12.49793-1.17531,12.12596-11.81075,8.49032h-.00647c-1.16085,5.06419-2.8578,10.01225-5.03931,14.72794l-18.0202,.00623c-.06471-.2002-.12288-.40688-.18109-.60712,1.66645,.10285,3.34571,.00534,4.98619-.29875-1.33757-1.64013-2.67509-3.29317-4.01266-4.93324-.0323-.03228-.05819-.06459-.08402-.09686l-.02036-.02517-1.85532-6.10724c.14644-1.35476,.38536-2.69828,.70958-4.02051l.00049-.00037v.00006Z" fill="#f2f2f2"/><path d="M251.74443,416.41995l-159.45702,.05516c-8.01074,.00277-15.18649-4.13684-19.19426-11.07295-2.00413-3.46855-3.00642-7.27337-3.00774-11.07904-.00132-3.80518,.99882-7.61119,3.00007-11.08063l79.67786-145.04905c4.00346-6.93937,11.17634-11.08346,19.18708-11.08623s15.18649,4.13635,19.19475,11.07295l79.78843,145.01196c1.99876,3.45927,2.99861,7.25971,2.99894,11.06293-.00015,3.80322-.99981,7.60874-3.00154,11.07868-4.00297,6.93889-11.17585,11.08346-19.1866,11.08623h.00002Zm-179.65463-22.09948c-.00026,3.46387,17.5953-4.00023,19.41817-.8451,3.64623,6.31026-6.50934,21.00225,.77874,20.99973,0,0,173.27016-3.828,176.91202-10.14078,1.82069-3.15639,2.73062-6.61911,2.72942-10.08102s-.91353-6.92351-2.73688-10.07864l-79.78845-145.01196c-3.63597-6.2922-10.16364-10.05801-17.45172-10.05549-7.28467,.00252-99.86105,161.74891-99.86132,165.21326h.00002Z" fill="#3f3d56"/><path d="M253.51521,414.41934c-1.99862,4.00069,91.61376,2.7712,92.72051,2.77082,0,0,26.3119-.58131,26.86492-1.53993,.27648-.47931,.41466-1.00515,.41448-1.53084s-.13873-1.05137-.4156-1.5305l-12.11626-22.02074c-.55215-.9555-1.54341-1.52736-2.65013-1.52698-.04608,.00002-.11394,.04093-.20168,.11933-16.16328,14.44385-36.0828,23.99237-57.75707,24.31469-23.047,.34272-46.73217,.6899-46.85919,.94416h.00002Z" fill="#3f3d56"/><path d="M0,417.0814c.00023,.66003,.53044,1.18982,1.19047,1.18959l522.28995-.18066c.65997-.00023,1.18982-.53038,1.18959-1.19041-.00023-.65997-.53044-1.18982-1.19041-1.18959l-522.28995,.18066c-.66003,.00023-1.18988,.53044-1.18965,1.19041Z" fill="#ccc"/><g><polygon points="403.87773 411.30541 394.83299 411.30765 390.51828 376.42194 403.86746 376.41823 403.87773 411.30541" fill="#a0616a"/><path d="M372.88925,411.11096h0c-.28145,.4744-.42951,2.00528-.42932,2.55685h0c.00059,1.6954,1.37542,3.06935,3.07089,3.06876l28.01047-.00969c1.15659-.0004,2.09391-.93832,2.09351-2.09496l-.0004-1.16617s1.38444-3.50542-1.46987-7.82444c0,0-3.54448,3.38386-8.84444-1.91244l-1.56336-2.82981-11.30656,8.27506-6.26838,.77377c-1.3714,.16927-2.58738-.02532-3.29241,1.16307h-.00012Z" fill="#2f2e41"/></g><g><polygon points="369.19708 381.46752 360.62951 384.36642 345.36911 352.70019 358.01396 348.42131 369.19708 381.46752" fill="#a0616a"/><path d="M339.77867,391.20807h0c-.11468,.53956,.23535,2.03724,.41217,2.5597h0c.54355,1.60589,2.28601,2.46714,3.89196,1.92358l26.53193-8.98019c1.09554-.3708,1.68312-1.55951,1.31228-2.65512l-.37389-1.10461s.18882-3.76416-3.8984-6.94155c0,0-2.27401,4.34084-8.99107,1.02096l-2.38732-2.18005-8.0607,11.46034-5.69037,2.74062c-1.24494,.59958-2.45919,.80471-2.74651,2.15628l-.00009,.00003Z" fill="#2f2e41"/></g><polygon points="355.22599 179.09003 337.68255 237.19081 316.97199 289.45207 349.76053 364.08569 361.91802 358.67731 347.37062 290.11706 377.87822 243.59436 392.11437 388.82623 404.27349 388.14652 420.76309 212.84336 423.11421 174.67566 355.22599 179.09003" fill="#2f2e41"/><path d="M408.20979,50.38513l-25.66813,4.73752-2.02258,11.48454-15.53439,7.43609-8.07702,84.4428s-13.50433,17.56819-2.69414,22.96862l68.9007-6.77904s3.37327-12.4903-.00473-13.67529-3.38832-31.01955-3.38832-31.01955l15.51573-61.36599-22.97048-8.09829-4.05664-10.13141Z" fill="#e6e6e6"/><circle cx="393.06995" cy="29.43589" r="20.7276" fill="#a0616a"/><path d="M406.79605,32.51227c-1.65401,.9055-.08471,5.22869-1.11848,5.60245-1.19492,.43202-5.55162-4.52715-5.60554-10.08176-.01633-1.68115,.37051-2.48839-.00155-4.48164-.48668-2.6073-1.98045-5.68253-3.36317-5.60089-.81965,.04839-1.62013,1.20723-1.67986,2.2414-.0837,1.44941,1.32419,2.01989,1.12139,2.80064-.38627,1.4871-6.29829,2.5138-8.40308,.00291-1.65013-1.9685-.38463-5.4247-1.12236-5.60167-.54378-.13045-1.12759,1.77241-3.35988,3.9226-.84103,.8101-2.09186,2.0149-2.80044,1.68159-1.02397-.48167-.28003-3.87624-.56157-3.92125-.23314-.03727-.37947,2.3487-1.67908,4.48223-1.66968,2.74103-5.11398,4.85374-6.72112,3.92377-.98466-.56978-1.06245-2.15606-1.12158-3.36084-.1245-2.53816-3.81308-8.37644-2.85882-11.09166,1.94221-5.5263,5.2701-1.63078,6.7752-3.47504,2.03756-2.49669,3.86572-1.15434,7.28133-3.92396,3.33668-2.70564,3.36577-5.42504,5.60011-5.604,2.01221-.16116,2.7278,1.98528,5.04242,1.67887,1.96079-.25957,2.38749-1.92436,3.92087-1.68198,1.40389,.2219,1.49646,1.68849,3.36201,2.23966,1.19101,.35188,1.45267-.15743,2.80103-.00097,2.80559,.32556,4.69002,2.88083,5.043,3.35949,1.36081,1.84526,.83282,2.62341,2.24236,4.48086,1.21555,1.6018,3.90913,.9216,4.92189,1.72162,3.30928,2.61409,4.73124,7.43497,4.75994,12.92962,.0201,3.8431-.48103,5.78482-1.59913,10.63564-.84901,3.6834-4.78175,8.44334-6.94987,10.5659-.58199,.56975-2.21351,2.167-3.36066,1.68178-1.08704-.45979-1.11995-2.52764-1.12139-2.80064-.0049-.93901,.24521-2.1009,2.23907-5.04263,2.09442-3.09006,2.92645-3.44271,2.79948-4.48262-.22445-1.83818-3.16424-3.52123-4.48263-2.79948h.00012Z" fill="#2f2e41"/><g><path d="M334.49457,225.75979c-.64513,5.40264,1.61895,10.11513,5.05699,10.52567s6.7481-3.63632,7.39324-9.03898c.28242-2.36492,.00723-4.59754-.6797-6.39504l2.44642-22.92684-10.7834-.91891-1.26717,22.69891c-1.09088,1.58522-1.88401,3.69026-2.16639,6.05519h.00003Z" fill="#a0616a"/><path d="M365.49095,73.49769s-5.58664,.07946-9.28568,7.30383c-1.96565,3.83897-12.48211,75.62767-12.48211,75.62767l-9.68742,58.80847,16.50214,.08313,12.859-55.69302,10.81723-32.30088-8.72315-53.82921Z" fill="#e6e6e6"/></g><path d="M411.92961,217.13028c-1.58023,5.20651-.17535,10.24237,3.13786,11.24797s7.28015-2.39987,8.86035-7.60638c.69172-2.27906,.81133-4.5254,.44941-6.41533l6.4191-22.14543-10.45641-2.79098-5.21815,22.12729c-1.35133,1.36996-2.50044,3.30383-3.19216,5.58289v-.00003Z" fill="#a0616a"/><path d="M423.41564,69.63219s8.10413-6.08248,14.86262,3.37246c6.75846,9.45495,9.48742,87.1388,9.48742,87.1388l-18.89796,47.96845-14.18755-4.72373,13.49008-58.7749-23.65861-44.57614,18.90403-30.40494h-.00003Z" fill="#e6e6e6"/><g><circle cx="218.47958" cy="311.43145" r="73" fill="var(--theme-color)"/><g><circle cx="218.49274" cy="349.47878" r="6.70264" fill="#fff"/><path d="M218.4641,266.68145c-.6157,4.0912,.48747,4.94727,.49357,22.58508,.0061,17.63782,3.66579,41.28574-.47148,41.28717s-5.65047-48.80276-20.01681-53.12218c-16.89088-5.07846,21.98785-23.99409,19.99471-10.75007Z" fill="#fff"/></g></g><path d="M120.50313,348.70219l-6.59532,13.93527c7.97328-5.71745,20.42715-10.64598,30.3376-13.28567-9.23403-4.46037-20.53313-11.65189-27.2835-18.77194l3.7934,14.72434c-44.47393-9.0585-76.41138-43.17661-76.4249-82.25924l-1.60648-.5527c.01412,40.82281,31.50971,76.96121,77.7792,86.20995Z" fill="#3f3d56"/></svg>
                                <h4>{{__('Your cart is empty')}}</h4>
                                <p>
                                    {{__('Your cart is currently empty. Return to our shop and check out the latest offers. We have some great items that are waiting for you')}}.
                                </p>
                                {{-- <a href="{{route('store.slug',$store->slug)}}" class="cart-btn">
                                    <i class="fas fa-angle-left"></i>
                                    {{__('Return to shop')}}
                                </a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endif
@endsection

@push('script-page')
<script>


     $(document).on('click', '.product_qty', function (e) {
        e.preventDefault();
        var currEle = $(this);
        var product_id = $(this).siblings(".bx-cart-qty").attr('data-id');
        var arrkey = $(this).parents('div').attr('data-id');
        var sum = 0;
        setTimeout(function () {
            if (currEle.hasClass('qty-minus') == true) {
                qty_id = currEle.next().val()
            } else {
                qty_id = currEle.prev().val();
            }
            
            if (qty_id == 0 || qty_id == '' || qty_id < 0) {
                location.reload();
                return false;
            }

            $.ajax({
                url: '{{route('user-product_qty.product_qty',['__product_id',$store->slug,'arrkeys'])}}'.replace('__product_id', product_id).replace('arrkeys', arrkey),
                type: "post",
                headers: {
                    'x-csrf-token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "product_qty": qty_id,
                },
                success: function (response) {
                    if (response.status == "Error") {
                        show_toastr('Error', response.error, 'error');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                        // Update the cart item count dynamically
                        const cartItemsCountElements = document.getElementById('cart-item-count');
                        if (cartItemsCountElements) {
                            console.log("cart items: ", response.cart_items_count)
                            cartItemsCountElements.textContent = `(${response.cart_items_count})`;
                        }

                        $.each(response.product, function (key, value) {
                            if(value.variant_id != 0){
                                sum += value.variant_subtotal;
                                if(value.tax.length > 0){
                                    $.each(value.tax ,function(key, tv){
                                        var subtax = value.variant_price * value.quantity * tv.tax /100;
                                        $('#product-variant-id-' + value.variant_id + ' .variant_tax_'+key).text(tv.tax_name + ' ' + tv.tax + '% ' + '(' + subtax + ')');
                                    });
                                }
                                $('#product-variant-id-' + value.variant_id + ' .subtotal').text(addCommas(value.variant_subtotal));
                            }
                            else{
                                sum += value.subtotal;
                                if(value.tax.length > 0){
                                    $.each(value.tax ,function(key, tv){
                                        var subtax = value.price * value.quantity * tv.tax /100;
                                        $('#product-id-' + value.id + ' .tax_'+key).text(tv.tax_name + ' ' + tv.tax + '% ' + '(' + subtax + ')');
                                    });
                                }
                                $('#product-id-' + value.id + ' .subtotal').text(addCommas(value.subtotal));
                            }
                        });
                        // If a coupon is already applied, re-apply it with updated cart total
                        let appliedCoupon = $('input.hidden_coupon').val().trim();

                        if (appliedCoupon) {
                            $.ajax({
                                url: '{{ route("apply.productcoupon") }}',
                                dataType: 'json',
                                data: {
                                    price: sum,
                                    store_id: {{ $store->id }},
                                    coupon: appliedCoupon
                                },
                                success: function(data) {
                                    if (data.is_success) {
                                        $('#coupon-value').html(data.discount_price);
                                        $('#displaytotal')
                                            .html(data.final_price)
                                            .attr('data-original', data.final_price); // update the stored formatted value too
                                    } else {
                                        $('#coupon-value').text('{{ \App\Models\Utility::priceFormat(0) }}');
                                        $('#displaytotal').text(addCommas(sum));
                                    }
                                },
                                error: function() {
                                    $('#coupon-value').text('{{ \App\Models\Utility::priceFormat(0) }}');
                                    $('#displaytotal').text(addCommas(sum));
                                }
                            });
                        } else {
                            $('#coupon-value').text('{{ \App\Models\Utility::priceFormat(0) }}');
                            $('#displaytotal').text(addCommas(sum));
                        }

                        const checkoutBtn = document.querySelector('.checkout-btn');
                        if (checkoutBtn) {
                            let url = new URL(checkoutBtn.href, window.location.origin);
                            let segments = url.pathname.split('/'); 
                            segments[segments.length - 1] = sum; // Replace the last segment with the updated total
                            url.pathname = segments.join('/');
                            checkoutBtn.href = url.toString(); 
                        }
                    }
                },
                error: function (result) {
                   
                }
            });
        }, 100);
    })

    // $(document).on('click', '.checkout-btn', function (e) {
    //     e.preventDefault();

    //     let totalAmount = $('#displaytotal').text().trim().replace(/[^\d.-]/g, '');
    //     let checkoutUrl = '{{ route('payment.checkout', ['slug' => $store->slug, 'order_amount' => '__total__']) }}';
    //     checkoutUrl = checkoutUrl.replace('__total__', totalAmount);
    //     // console.log("url: ", checkoutUrl);

    //     $.ajax({
    //         url: checkoutUrl,
    //         type: 'GET',
    //         headers: {
    //             'x-csrf-token': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         success: function (response) {
    //             if (response.redirect_url) {
    //                 // Redirect to the payment gateway
    //                 window.location.href = response.redirect_url;
    //             } else if (response.error) {
    //                 show_toastr('Error', response.error, 'error');
    //             }
    //         },
    //         error: function (xhr) {
    //             let errorMessage = 'Something went wrong. Please try again.';
    //             if (xhr.responseJSON && xhr.responseJSON.error) {
    //                 errorMessage = xhr.responseJSON.error;
    //             }
    //             show_toastr('Error', errorMessage, 'error');
    //         }
    //     });
    // });


    $(".product_qty_input").on('blur', function (e) {
        e.preventDefault();

        var product_id = $(this).attr('data-id');
        var arrkey = $(this).parents('div').attr('data-id');
        var qty_id = $(this).val();
        

        setTimeout(function () {
            if (qty_id == 0 || qty_id == '' || qty_id < 0) {
                location.reload();
                return false;
            }

            $.ajax({
                url: '{{route('user-product_qty.product_qty',['__product_id',$store->slug,'arrkeys'])}}'.replace('__product_id', product_id).replace('arrkeys', arrkey),
                type: "post",
                headers: {
                    'x-csrf-token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "product_qty": qty_id,
                },
                success: function (response) {
                    if (response.status == "Error") {
                        show_toastr('Error', response.error, 'error');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                        location.reload(); // then reload the page.(3)
                    }
                },
                error: function (result) {
                    // console.log('error12');
                }
            });
        }, 500);
    });

    function qtyChange(product_id, arrkey, qty_id) {

    }

    $(document).on('click', '.qty-plus', function () {
          $(this).prev().val(+$(this).prev().val() );
        
    });

    $(document).on('click', '.qty-minus', function () {
        if ($(this).next().val() > 1) $(this).next().val(+$(this).next().val() );
    });

    // ------------- Handle Apply/Remove coupon -------------
    $(document).on('click', '.apply-coupon', function(e) {
        e.preventDefault();

        const $form = $(this).closest('.coupon-form');
        const coupon = $form.find('input.coupon').val().trim();
        const lastCode = $form.find('input.hidden_coupon').val().trim();

        // Grab raw cart-summary price (data-value must be numeric)
        const rawPrice = parseFloat($('.product_total').data('value')) || 0;

        // Grab original total from your price-bar span (already formatted)
        const originalDisplay = $('#displaytotal').data('original');
        
        // Extract numeric value from original display
        const originalValue = parseFloat(
            originalDisplay.toString()
            .replace(/[^0-9.-]+/g, '')
        ) || 0;

        // Decide which to apply—never apply on zero!
        const priceToApply = rawPrice > 0 ? rawPrice : originalValue;

        // Prevent re-using same coupon
        if (coupon && coupon === lastCode) {
            return show_toastr('Error', '{{ __("Coupon Already Used") }}', 'error');
        }

        if (coupon) {
            $.ajax({
                url: '{{ route("apply.productcoupon") }}',
                dataType: 'json',
                data: {
                    price: priceToApply,
                    store_id: {{ $store->id }},
                    coupon: coupon
                },
                success: function(data) {
                    console.log("DD: ", data);
                    if (data.is_success) {
                        $('.hidden_coupon').val(coupon);
                        $('.hidden_coupon').attr(data);

                        // update coupon price in summary
                        $('#coupon-value').text(data.discount_price);
                        $('#displaytotal').text(data.final_price);

                        show_toastr('Success', data.message, 'success');
                    } else {
                        show_toastr('Error', data.message, 'error');
                    }
                },
                error: function() {
                    show_toastr('Error', 'Server error applying coupon', 'error');
                }
            });
        }
        // CLEAR / INVALID
        else {
            $form.find('input.hidden_coupon').val('');
            $('#coupon-value').text('{{ \App\Models\Utility::priceFormat(0) }}');
            $('#displaytotal').text(originalDisplay); // Use the original formatted display
            show_toastr('Error', '{{ __("Invalid Coupon Code.") }}', 'error');
        }
    });

    
</script>
<script>
    var site_currency_symbol_position = '{{ $store->currency_symbol_position }}';
    var site_currency_symbol_space = '{{ $store->currency_symbol_space }}'
    var site_currency_symbol = '{{ $store->currency }}';
     window.translations = {
        yes: "{{ __('Yes') }}",
        cancel: "{{ __('CANCEL') }}"
    };


</script>

@endpush