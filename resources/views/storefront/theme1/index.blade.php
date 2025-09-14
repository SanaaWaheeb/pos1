@extends('storefront.layout.theme1')
@section('page-title')
    {{ __('Home') }}
@endsection

@push('css-page')
@endpush

@php
$imgpath=\App\Models\Utility::get_file('uploads/');
$productImg = \App\Models\Utility::get_file('uploads/is_cover_image/');
$catimg = \App\Models\Utility::get_file('uploads/product_image/');
$default =\App\Models\Utility::get_file('uploads/theme1/header/logo4.png');
@endphp

@section('content')



@if ($store->door == "on" )
{{ $store->board_id }}





       <style>
            #overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: white;
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;  /*Set a high z-index to ensure it's on top of other elements */
    
            }
            svg {
              position: absolute;
              width: 240px;
              height: 120px;
              top: 0; right: 0;
              bottom: 0; left: 0; 
              margin: auto;
            }
    
            svg #plug,
            svg #socket {
              fill:var(--primary);
            }
    
            svg #loop-normal {
              fill: none;
              stroke: var(--primary);
              stroke-width: 12;
            }
    
            svg #loop-offset {
              display: none;
            }
    
            .credit {
              position: absolute;
              padding: 20px;
              bottom: 150px;
              width: 100%;
              text-align: center;
              color: #000;
              font: 800 150% "Open Sans", sans-serif;
              text-transform: uppercase;
              text-decoration: none;
              z-index: 10000;  /*Set a high z-index to ensure it's on top of other elements */
            }
    </style>

    <style>
        body {
          font-family: Arial, sans-serif;
        }
        .modal3 {
          display: none;
          position: fixed;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          padding: 20px;
          background-color: #fff;
          border: 1px solid #ccc;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          z-index: 10001;    
    
        }
        .overlay3 {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.5);
          z-index: 10000;
        }
        .close-btn3 {
          cursor: pointer;
          position: absolute;
          top: 10px;
          right: 10px;
          font-size: 18px;
        }
    </style>


    <!-- Modal -->
    <div id="myModal" class="modal3">
    <div style="width: 350px; top: 50%; left: 50%;">
      <img src="https://media.tenor.com/LGkgbxFuywEAAAAM/plug-in.gif" alt="Machine Issue Image" style="width: 350px;"></br></br>
      <h2>{{ __('Machine Issue') }}</h2>
      </br>
      <p>{{ __('We apologize for the inconvenience. There is a malfunction in the device. You can try the following steps:') }}</p>
      <ol>
        <li>{{ __('Step 1: Check the power socket to ensure it is connected to the device.') }}</li>
        <li>{{ __('Step 2: Inspect the power button if available.') }}</li>
        <li>{{ __('Step 3: Turn off the device, wait for a minute, and then turn it on again.') }}</li>
        <li>{{ __('Step 4: Retry scanning the barcode.') }}</li>
      </ol>
      </br>
      <p>{{ __('If the issue persists, please contact technical support.') }}</p>
      <p>{{ __('Thank you.') }}</p>
    </div>
    </div>

    <!-- Overlay -->
    <div id="overlay3" class="overlay3"></div> 

    <div id="overlay">
    <div id="ConnectionSVG">
        <!-- Content within the overlay (if needed) -->
        <svg id="preloader" width="240px" height="120px" viewBox="0 0 240 120" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
  
      <path id="loop-normal" class="st1" d="M120.5,60.5L146.48,87.02c14.64,14.64,38.39,14.65,53.03,0s14.64-38.39,0-53.03s-38.39-14.65-53.03,0L120.5,60.5
    L94.52,87.02c-14.64,14.64-38.39,14.64-53.03,0c-14.64-14.64-14.64-38.39,0-53.03c14.65-14.64,38.39-14.65,53.03,0z">
        <animate attributeName="stroke-dasharray" from="500, 50" to="450 50" begin="0s" dur="2s" repeatCount="indefinite" />
        <animate attributeName="stroke-dashoffset" from="-40" to="-540" begin="0s" dur="2s" repeatCount="indefinite" />
      </path>

      <path id="loop-offset" d="M146.48,87.02c14.64,14.64,38.39,14.65,53.03,0s14.64-38.39,0-53.03s-38.39-14.65-53.03,0L120.5,60.5L94.52,87.02c-14.64,14.64-38.39,14.64-53.03,0c-14.64-14.64-14.64-38.39,0-53.03c14.65-14.64,38.39-14.65,53.03,0L120.5,60.5L146.48,87.02z"></path>

      <path id="socket" d="M7.5,0c0,8.28-6.72,15-15,15l0-30C0.78-15,7.5-8.28,7.5,0z">
        <animateMotion
          dur="2s"
          repeatCount="indefinite"
          rotate="auto"
          keyTimes="0;1"
          keySplines="0.42, 0.0, 0.58, 1.0"
        >
          <mpath xlink:href="#loop-offset"/>
        </animateMotion>
      </path>
      
    <path id="plug" d="M0,9l15,0l0-5H0v-8.5l15,0l0-5H0V-15c-8.29,0-15,6.71-15,15c0,8.28,6.71,15,15,15V9z">
      <animateMotion
        dur="2s"
          rotate="auto"
          repeatCount="indefinite"
          keyTimes="0;1"    
          keySplines="0.42, 0, 0.58, 1"
      >
        <mpath xlink:href="#loop-normal"/>
      </animateMotion>
    </path>   
      
    </svg>
    </div>
    
    <div class="credit">
        {{ __('We are checking if the machine is powered on') }}
    </div>

    </div>    

<!--===============================================================================================-->
	<script src="https://ava.com.sa/connection/jquery-1.11.3.min.js"></script>
<!--===============================================================================================-->
	<script src="https://ava.com.sa/connection/mqttws31.js"></script>
<!--===============================================================================================-->
<!--===============================================================================================-->
    <script src="https://ava.com.sa/connection/creapp.js"></script>
<!--===============================================================================================-->


<script>
    var rac_id = "{{ $store->board_id }}";

    $(document).ready(function(e) {
        client = new Paho.MQTT.Client(config2.mqtt_server2, config2.mqtt_websockets_port2, "web_" + parseInt(Math.random() * 100, 10));

        client.connect({
            useSSL: true,
            userName: config2.mqtt_user2,
            password: config2.mqtt_password2,
            onSuccess: function() {
                console.log("MQTT Connected");
                client.subscribe("/PING/" + rac_id);
                client.subscribe("/RESPONSE/" + rac_id);
                
                setTimeout(function() {
                    mqttSend("/PING/" + rac_id, "ping");
                }, 500); // Delay to ensure subscription is active
            },
            onFailure: function(e) {
                console.log("MQTT Connection Failed: ", e);
            }
        });

        client.onConnectionLost = function(responseObject) {
            if (responseObject.errorCode !== 0) {
                console.log("Connection Lost: " + responseObject.errorMessage);
                $("#machineStatus").text("NOT CONNECTED");

                setTimeout(function() { 
                    client.connect({
                        useSSL: true,
                        userName: config2.mqtt_user2,
                        password: config2.mqtt_password2,
                        onSuccess: function() {
                            console.log("Reconnected");
                            client.subscribe("/PING/" + rac_id);
                            setTimeout(function() {
                                mqttSend("/PING/" + rac_id, "ping");
                            }, 500);
                        }
                    }); 
                }, 1000);
            }
        };

        client.onMessageArrived = function(message) {
            console.log("MQTT Message Arrived: " + message.payloadString);

            if (!isNaN(message.payloadString)) {
                console.log("Received number: " + message.payloadString);
            } else {
                if (message.payloadString === 'connected') {
                    $("#machineStatus").text("CONNECTED");
                    document.getElementById('overlay').style.display = 'none';
                    
                } else {
                    $("#machineStatus").text("NOT CONNECTED");
                }
            }
        };

    });

    var mqttSend = function(topic, msg) {
        var message = new Paho.MQTT.Message(msg);
        message.destinationName = topic;
        client.send(message);
        console.log("Sent MQTT Message:", topic, msg);
    };
</script>



@endif

<div class="wrapper">
    {{-- @foreach ($pixelScript as $script)
        <?= $script; ?>
    @endforeach
    @foreach($getStoreThemeSetting as $ThemeSetting )
    @if (isset($ThemeSetting['section_name']) && $ThemeSetting['section_name'] == 'Home-Header' && $ThemeSetting['section_enable'] == 'on')
    @php
        $homepage_header_title_key = array_search('Title', array_column($ThemeSetting['inner-list'], 'field_name'));
        $homepage_header_title = $ThemeSetting['inner-list'][$homepage_header_title_key]['field_default_text'];

        $homepage_header_Sub_text_key = array_search('Sub text', array_column($ThemeSetting['inner-list'], 'field_name'));
        $homepage_header_Sub_text = $ThemeSetting['inner-list'][$homepage_header_Sub_text_key]['field_default_text'];

        $homepage_header_Button_key = array_search('Button', array_column($ThemeSetting['inner-list'], 'field_name'));
        $homepage_header_Button = $ThemeSetting['inner-list'][$homepage_header_Button_key]['field_default_text'];

        $homepage_header_background_Image_key = array_search('Background Image', array_column($ThemeSetting['inner-list'], 'field_name'));
        $homepage_header_background_Image = $ThemeSetting['inner-list'][$homepage_header_background_Image_key ]['field_default_text'];
    @endphp

    <section class="main-home-first-section" style="background-image:url({{ $imgpath. $homepage_header_background_Image}}) ">
        <div class="container">
            <div class="banner-content">
                <h1>{{ $homepage_header_title }}</h1>
                <p>{{ $homepage_header_Sub_text }}</p>
                <a href="#" class="btn" id="pro_scroll"> {{ $homepage_header_Button }}
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 492.004 492.004"  xml:space="preserve">
                        <g>
                            <g>
                                <path d="M382.678,226.804L163.73,7.86C158.666,2.792,151.906,0,144.698,0s-13.968,2.792-19.032,7.86l-16.124,16.12    c-10.492,10.504-10.492,27.576,0,38.064L293.398,245.9l-184.06,184.06c-5.064,5.068-7.86,11.824-7.86,19.028    c0,7.212,2.796,13.968,7.86,19.04l16.124,16.116c5.068,5.068,11.824,7.86,19.032,7.86s13.968-2.792,19.032-7.86L382.678,265    c5.076-5.084,7.864-11.872,7.848-19.088C390.542,238.668,387.754,231.884,382.678,226.804z"/>
                            </g>
                        </g>
                     </svg>
                </a>
            </div>
        </div>
    </section>
    @endif
    @endforeach --}}

{{-- @if($getStoreThemeSetting[1]['section_enable'] == 'on')
<section class="store-promotions padding-top padding-bottom">
    <div class="container">
        <div class="row">
            @foreach ($getStoreThemeSetting as $key => $storethemesetting)
                @if ($storethemesetting['section_name'] == 'Home-Promotions')
                    @if (isset($storethemesetting['homepage-promotions-font-icon']) || isset($storethemesetting['homepage-promotions-title']) || isset($storethemesetting['homepage-promotions-description']))
                        @for ($i = 0; $i < $storethemesetting['loop_number']; $i++)
                            <div class="col-lg-4 col-sm-4 col-12">
                                <div class="store-promotions-box">
                                {!! $storethemesetting['homepage-promotions-font-icon'][$i] !!}
                                    <h4>{{ $storethemesetting['homepage-promotions-title'][$i] }}</h4>
                                    <@if(isset($storethemesetting['homepage-promotions-description'][$i]))  
                                        <p>{{ $storethemesetting['homepage-promotions-description'][$i] }}</p>  
                                    @else  
                                        <p>Default promotion description</p>  
                                    @endif
                                </div>
                            </div>
                        @endfor
                    @else
                        @for ($i = 0; $i < $storethemesetting['loop_number']; $i++)
                            <div class="col-lg-4 col-sm-4 col-12">
                                <div class="store-promotions-box">
                                    {!! $storethemesetting['inner-list'][0]['field_default_text'] !!}
                                    <h4>{{ $storethemesetting['inner-list'][1]['field_default_text'] }}</h4>
                                    <@if(isset($storethemesetting['homepage-promotions-description'][$i]))  
                                        <p>{{ $storethemesetting['homepage-promotions-description'][$i] }}</p>  
                                    @else  
                                        <p>Default promotion description</p>  
                                    @endif
                                </div>
                            </div>
                        @endfor
                    @endif
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif --}}

@php
    $total = 0;
    $cart = session()->get($store->slug);
    // Check if the cart and products are available
    if (isset($cart['products'])) {
        foreach ($cart['products'] as $product) {
            // Get price and quantity for each product
            $price = $product['price'];
            $quantity = $product['quantity'];

            // Calculate the subtotal for the current product
            $subtotal = $price * $quantity;

            // Add the product subtotal to the total
            $total += $subtotal;
        }
    }
@endphp
<!-- Products -->

    @if ($products['Start shopping']->count() > 0)
    <section class="bestseller-section tabs-wrapper padding-bottom" id="pro_items">
        <div class="container">
            <div class="bestseller-title">
                <h2>{{ __('Products') }}</h2>
              <div class="tab-bar">
                        <ul class="cat-tab tabs" id="myTab">
                            @foreach ($categories as $key => $category)
                            <li class="tab-link {{ $key == 0 ? 'active' : '' }}" data-tab="tab-{!! preg_replace('/[^A-Za-z0-9\-]/', '_', $category) !!}">
                                <a href="##" >
                                    {{ __($category) }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
            </div>
            <div class="tabs-container">
                @foreach ($products as $key => $items)
                <div id="tab-{!! preg_replace('/[^A-Za-z0-9\-]/', '_', $key) !!}" class="tab-content {{ $key == 'Start shopping' ? 'active' : '' }}">
                        @if ($items->count() > 0)
                            <div class="row products-grid">
                                @foreach ($items as $product)
                                    <div class="col-lg">
                                        <div class="product-card">
                                            <div class="card-img">
                                                <a href="{{ route('store.product.product_view', [$store->slug, $product->id]) }}">
                                                    @if (!empty($product->is_cover))
                                                        <img alt="Image placeholder" src="{{ $productImg . $product->is_cover }}" >
                                                    @else
                                                        <img alt="Image placeholder" src="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}" >
                                                    @endif
                                                </a>
                                                  {{-- <div class="heart-icon">   
                                                    @if (Auth::guard('customers')->check())
                                                        @if (!empty($wishlist) && isset($wishlist[$product->id]['product_id']))
                                                            @if ($wishlist[$product->id]['product_id'] != $product->id)
                                                                <a
                                                                    class="heart-icon action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{ $product->id }}"
                                                                    data-id="{{ $product->id }}">
                                                                    <i class="far fa-heart"></i>
                                                                </a>
                                                            @else
                                                                <a class="heart-icon action-item wishlist-icon bg-light-gray wishlist_{{ $product->id }}"
                                                                     disabled>
                                                                    <i class="fas fa-heart"></i>
                                                                </a>
                                                            @endif
                                                        @else
                                                            <a
                                                                class="heart-icon action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{ $product->id }}"
                                                                data-id="{{ $product->id }}">
                                                                <i class="far fa-heart"></i>
                                                            </a>
                                                        @endif
                                                    @else
                                                        <a
                                                            class="heart-icon action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{ $product->id }}"
                                                            data-id="{{ $product->id }}">
                                                            <i class="far fa-heart"></i>
                                                        </a>
                                                    @endif
                                                 </div>  --}}
                                            </div>
                                            <div class="card-content">
                                                {{-- <div class="rating">
                                                    @if ($store->enable_rating == 'on')
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @php
                                                                $icon = 'fa-star';
                                                                $color = '';
                                                                $newVal1 = $i - 0.5;
                                                                if ($product->product_rating() < $i && $product->product_rating() >= $newVal1) {
                                                                    $icon = 'fa-star-half-alt';
                                                                }
                                                                if ($product->product_rating() >= $newVal1) {
                                                                    $color = 'text-warning';
                                                                }
                                                            @endphp
                                                            <i class="star fas {{ $icon . ' ' . $color }}"></i>
                                                        @endfor
                                                    @endif
                                                </div> --}}
                                                <h6>
                                                    <a href="{{ route('store.product.product_view', [$store->slug, $product->id]) }}">{{ $product->name }}</a>
                                                </h6>
                                            {{-- <p><span class="td-gray">{{ __('Category') }}:</span>{{ $product->product_category() }}</p> --}}

                                                <div class="last-btn">
                                                <div class="price">
                                                    <ins>
                                                        @if ($product->enable_product_variant == 'on')
                                                            {{ __('In variant') }}
                                                        @else
                                                            @if ($product->price == 0)
                                                                {{ __('Free') }}
                                                            @else
                                                                {{ \App\Models\Utility::priceFormat($product->price) }}
                                                            @endif
                                                        @endif
                                                    </ins>
                                                </div>

                                            @if ($product->enable_product_variant == 'on')
                                                <a href="{{ route('store.product.product_view', [$store->slug, $product->id]) }}" class="cart-btn">
                                                    <i class="fas fa-shopping-basket"></i>
                                                </a>
                                            @else
                                                @if ($product->price == 0)
                                                    {{-- free product: trigger the modal --}}
                                                    <a href="javascript:;"
                                                    data-id="{{ $product->id }}"
                                                    class="cart-btn open-free-modal"
                                                    data-chat-type="{{ $store->method }}"> <!-- sms or email -->
                                                    <i class="fas fa-shopping-basket"></i>
                                                    </a>
                                                @else
                                                    {{-- normal “add to cart” --}}
                                                    <a data-id="{{ $product->id }}" class="cart-btn add_to_cart">
                                                        <i class="fas fa-shopping-basket"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="row d-flex justify-content-center">
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                    <h6 class="no_record"><i class="fas fa-ban"></i>{{ __('No Record Found') }}</h6>
                                </div>
                            </div>
                        @endif
                    
                    
                    
                    <div>
                        <button id="machineStatus" style="display: none;">
                            NOT CONNECTED
                        </button>
                    </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

{{-- @if($getStoreThemeSetting[2]['section_enable'] == 'on')
    @foreach ($getStoreThemeSetting as $storethemesetting)
        @if (isset($storethemesetting['section_name']) && $storethemesetting['section_name'] == 'Home-Email-Subscriber' && $storethemesetting['section_enable'] == 'on')
            @php
            $emailsubs_img_key = array_search('Subscriber Background Image', array_column($storethemesetting['inner-list'], 'field_name'));
            $emailsubs_img = $storethemesetting['inner-list'][$emailsubs_img_key]['field_default_text'];

            $SubscriberTitle_key = array_search('Subscriber Title', array_column($storethemesetting['inner-list'], 'field_name'));
            $SubscriberTitle = $storethemesetting['inner-list'][$SubscriberTitle_key]['field_default_text'];

            $SubscriberDescription_key = array_search('Subscriber Description', array_column($storethemesetting['inner-list'], 'field_name'));
            $SubscriberDescription = $storethemesetting['inner-list'][$SubscriberDescription_key]['field_default_text'];

            $SubscribeButton_key = array_search('Subscribe Button Text', array_column($storethemesetting['inner-list'], 'field_name'));
            $SubscribeButton = $storethemesetting['inner-list'][$SubscribeButton_key]['field_default_text'];
            @endphp
            <section class="subcribe-section" style="background-image: url({{ $imgpath  . $emailsubs_img }});">
                <div class="container">
                    <div class="subcribe-inner">
                        <h2>{{ !empty($SubscriberTitle) ? $SubscriberTitle : 'Always on time' }}</h2>
                        <p>{{ !empty($SubscriberDescription) ? $SubscriberDescription : 'Subscription here' }}</p>
                        {{ Form::open(['route' => ['subscriptions.store_email', $store->id], 'method' => 'POST']) }}
                        <div class="input-box">
                            {{ Form::email('email', null, ['placeholder' => __('TYPE YOUR EMAIL ADDRESS...')]) }}
                            <button type="submit"> <span class="btn-inner--text">{{ $SubscribeButton }}</span> <i class="fas fa-paper-plane"></i></button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </section>
        @endif
    @endforeach
@endif --}}
@if (count($topRatedProducts) > 0)
    <section class="top-product padding-bottom">
        <div class="container">
            <div class="top-product-title">
                <h2>{{ __('Top rated products') }}</h2>
                <a href="{{ route('store.categorie.product', $store->slug) }}" class="showmore-btn">{{ __('Show more products') }}</a>
            </div>
            <div class="row product-row">
                @foreach ($topRatedProducts as $k => $topRatedProduct)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <div class="product-card">
                            <div class="card-img">
                                <a href="{{ route('store.product.product_view', [$store->slug, $topRatedProduct->product_id]) }}">
                                    @if (!empty($topRatedProduct->product->is_cover))
                                        <img alt="Image placeholder" src="{{$productImg . $topRatedProduct->product->is_cover }}">
                                    @else
                                        <img alt="Image placeholder" src="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}">
                                    @endif
                                </a>
                                 {{-- <div class="heart-icon"> 
                                    @if (Auth::guard('customers')->check())
                                        @if (!empty($wishlist) && isset($wishlist[$topRatedProduct->product->id]['product_id']))
                                            @if ($wishlist[$topRatedProduct->product->id]['product_id'] != $topRatedProduct->product->id)
                                                <a
                                                    class="heart-icon action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{ $topRatedProduct->product->id }}"
                                                    data-id="{{ $topRatedProduct->product->id }}">
                                                    <i class="far fa-heart"></i>
                                                </a>
                                            @else
                                                <a class="heart-icon action-item wishlist-icon bg-light-gray"
                                                    data-id="{{ $topRatedProduct->product->id }}" disabled>
                                                    <i class="fas fa-heart"></i>
                                                </a>
                                            @endif
                                        @else
                                            <a
                                                class="heart-icon action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{ $topRatedProduct->product->id }}"
                                                data-id="{{ $topRatedProduct->product->id }}">
                                                <i class="far fa-heart"></i>
                                            </a>
                                        @endif
                                    @else
                                        <a
                                            class="heart-icon action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{ $topRatedProduct->product->id }}"
                                            data-id="{{ $topRatedProduct->product->id }}">
                                            <i class="far fa-heart"></i>
                                        </a>
                                    @endif
                                 </div>  --}}
                            </div>
                            {{-- <div class="card-content">
                                <div class="rating">

                                    @if ($store->enable_rating == 'on')
                                        @for ($i = 1; $i <= 5; $i++)
                                            @php
                                                $icon = 'fa-star';
                                                $color = '';
                                                $newVal1 = $i - 0.5;
                                                if ($topRatedProduct->product->product_rating() < $i && $topRatedProduct->product->product_rating() >= $newVal1) {
                                                    $icon = 'fa-star-half-alt';
                                                }
                                                if ($topRatedProduct->product->product_rating() >= $newVal1) {

                                                    $color = 'text-warning';

                                                }
                                            @endphp

                                        <i class="star fas {{ $icon . ' ' . $color }}"></i>
                                        @endfor
                                    @endif
                                </div> --}}
                                <h6>
                                    <a href="#">{{ $topRatedProduct->product->name }}</a>
                                </h6>
                            <p><span class="td-gray">{{ __('Category') }}:</span> {{ $topRatedProduct->product->product_category() }}</p>

                                <div class="last-btn">
                                    <div class="price">
                                        <ins>
                                            @if ($topRatedProduct->product->enable_product_variant == 'on')
                                                {{ __('In variant') }}
                                            @else
                                                {{ \App\Models\Utility::priceFormat($topRatedProduct->product->price) }}
                                            @endif
                                        </ins>
                                    </div>
                                    @if ($topRatedProduct->product->enable_product_variant == 'on')
                                        <a href="{{ route('store.product.product_view', [$store->slug, $topRatedProduct->product->id]) }}" class="cart-btn"> <i class="fas fa-shopping-basket"></i></a>
                                    @else
                                        <a href="javascript:void(0)" class="cart-btn add_to_cart" data-id="{{ $topRatedProduct->product->id }}"> <i class="fas fa-shopping-basket"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
<div class="checkout-box" style="{{ $total == 0 ? 'display: none;' : '' }}">
    <div class="align-items-center justify-content-center">
        <div class="col-md-4 col-12">
            <div class="price-bar">
                <span>{{ __('Total value:') }}</span>
                <span id="displaytotal">{{\App\Models\Utility::priceFormat(price: !empty($total)?$total:0)}}</span>
            </div>
        </div>
            {{-- @if($store_settings['is_checkout_login_required'] == null || $store_settings['is_checkout_login_required'] == 'off' && !Auth::guard('customers')->user())
                <a href="#" class="checkout-btn modal-target checkout_btn" data-modal="Checkout" id="checkout-btn">
                    {{__('Proceed to checkout')}}
                    <i class="fas fa-shopping-basket"></i>
                </a>
            @else --}}
                <a href="{{ route('store-payment.payment', $store->slug) }}" class="checkout-btn">
                    {{__('Proceed to checkout')}}
                    <i class="fas fa-shopping-basket"></i>
                </a>
            {{-- @endif --}}
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- <style>
  :root {
    /* tweak these values as you like */
    --swal-font-base: 14px;         /* overall base font-size */
    --swal-title-size: 1rem;        /* the popup title */
    --swal-content-size: 0.875rem;   /* the subtitle/text */
    --swal-icon-size: 3.5rem;        /* the big X or checkmark */
    --swal-input-width: 180px;       /* your input field width */
    --swal-btn-padding: 0.5em 1.2em; /* button padding */
    --swal-btn-radius: 0.25em;       /* button corner radius */
    --swal-btn-font: 0.9rem;         /* button text size */
  }

  /* apply all the vars to your popup */
  .my-swal-popup {
    font-size: var(--swal-font-base);
    padding: 1.5em;
  }
  .my-swal-popup .swal2-title {
    font-size: var(--swal-title-size);
    margin-bottom: 0.25em;
    text-align: center;
  }
  .my-swal-popup .swal2-content {
    font-size: var(--swal-content-size);
    text-align: center;
    margin-bottom: 1em;
  }

  /* icon centering + size + color overrides via CSS variables */
  .my-swal-popup .swal2-icon {
    display: block;
    margin: 0 auto 1em;
    /* width: var(--swal-icon-size) !important;
    height: var(--swal-icon-size) !important;
    font-size: var(--swal-icon-size) !important; */
    /* You can override colors like this: */
    /* color: var(--your-icon-color, #f00); */
  }

  /* shrink the input field */
  .my-swal-input {
    width: var(--swal-input-width) !important;
    margin: 0 auto 1em;
    padding: 0.5em;
    font-size: 1em;
  }

  /* base button style */
  .my-swal-btn {
    padding: var(--swal-btn-padding);
    border-radius: var(--swal-btn-radius) !important;
    font-size: var(--swal-btn-font) !important;
    min-width: 80px;
    margin: 0 0.25em;
  }

  /* confirm/cancel variants—you can change these colors at will */
  .my-swal-confirm {
    background-color: #556ee6 !important;
    color: #fff !important;
  }
  .my-swal-cancel {
    background-color: #f46a6a !important;
    color: #fff !important;
  }

  /* center the action buttons */
  .my-swal-popup .swal2-actions {
    display: flex;
    justify-content: center;
    margin-top: 0.5em;
  }
</style> --}}



{{-- @include('storefront.free_modal') --}}
{{-- @foreach ($getStoreThemeSetting as $storethemesetting)
    @if (isset($storethemesetting['section_name']) && $storethemesetting['section_name'] == 'Home-Categories' && $storethemesetting['section_enable'] == 'on' && !empty($pro_categories))
        @php
        $Titlekey = array_search('Title', array_column($storethemesetting['inner-list'], 'field_name'));
        $Title = $storethemesetting['inner-list'][$Titlekey]['field_default_text'];

        $Description_key = array_search('Description', array_column($storethemesetting['inner-list'], 'field_name'));
        $Description = $storethemesetting['inner-list'][$Description_key]['field_default_text'];
        @endphp

        <section class="category-section padding-bottom">
            <div class="container">
                <div class="category-title">
                    <div class="main-title">
                        <h2 class="h1">{{ !empty($Title) ? $Title : 'Categories' }}</h2>
                        <p>{{ !empty($Description) ? $Description : 'There is only that moment and the incredible certainty <br> that everything under the sun has been written by one hand only.' }}</p>
                    </div>
                </div>
                <div class="row">
                    @foreach ($pro_categories as $key => $pro_categorie)
                        @if ($product_count[$key] > 0)
                            <div class="col-lg-4 col-md-6 col-12" style=" padding-top: 15px; ">
                                <div class="category-card">
                                    <div class="category-card-inner">
                                        @if (!empty($pro_categorie->categorie_img))
                                            <img src="{{  $catimg . $pro_categorie->categorie_img }}" alt="Image placeholder">
                                        @else
                                            <img src="{{ asset(Storage::url('uploads/product_image/default.jpg')) }}" alt="Image placeholder">
                                        @endif
                                        <div class="category-text">
                                            <h3>{{ $pro_categorie->name }}</h3>
                                            <p>{{ __('Products') }}: {{ !empty($product_count[$key]) ? $product_count[$key] : '0' }}</p></p>
                                            <a href="{{ route('store.categorie.product', [$store->slug, $pro_categorie->name]) }}" class="showmore-btn">{{ __('Show more products') }} <i class="fas fa-shopping-basket"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endforeach --}}

{{-- @if($getStoreThemeSetting[4]['section_enable'] == 'on')
<section class="testimonial-section padding-bottom">
    <div class="container">
        <div class="main-title">
            @foreach ($getStoreThemeSetting as $storethemesetting)
            @if (isset($storethemesetting['section_name']) && $storethemesetting['section_name'] == 'Home-Testimonial' && $storethemesetting['array_type'] == 'inner-list' && $storethemesetting['section_enable'] == 'on')
                @php
                    $Heading_key = array_search('Heading', array_column($storethemesetting['inner-list'], 'field_name'));
                    $Heading = $storethemesetting['inner-list'][$Heading_key]['field_default_text'];

                    $HeadingSubText_key = array_search('Heading Sub Text', array_column($storethemesetting['inner-list'], 'field_name'));
                    $HeadingSubText = $storethemesetting['inner-list'][$HeadingSubText_key]['field_default_text'];
                @endphp
                <h2 class="h1">{{ !empty($Heading) ? $Heading : 'Testimonials' }}</h2>
                <p>{{ !empty($HeadingSubText) ? $HeadingSubText : 'There is only that moment and the incredible certainty that <br> everything under the sun has been written by one hand only.' }}</p>
            @endif
            @endforeach
        </div>
        <div class="testimonial-slider">
            @foreach ($getStoreThemeSetting as $key => $storethemesetting)
                @if (isset($storethemesetting['section_name']) && $storethemesetting['section_name'] == 'Home-Testimonial' && $storethemesetting['array_type'] == 'multi-inner-list')
                    @if (isset($storethemesetting['homepage-testimonial-card-image']) || isset($storethemesetting['homepage-testimonial-card-title']) || isset($storethemesetting['homepage-testimonial-card-sub-text']) || isset($storethemesetting['homepage-testimonial-card-description']) || isset($storethemesetting['homepage-testimonial-card-enable']))
                        @for ($i = 0; $i < $storethemesetting['loop_number']; $i++)
                            @if($storethemesetting['homepage-testimonial-card-enable'][$i] == 'on')
                                <div class="testimonial-card">
                                    <div class="testimonial-card-inner">
                                        <@if(isset($storethemesetting['homepage-promotions-description'][$i])) 
                                                <p>{{ $storethemesetting['homepage-promotions-description'][$i] }}</p> 
                                            @else 
                                                <p>Default promotion description</p> 
                                            @endif
                                    <div class="testi-info">
                                        <div class="avtar-img">
                                            <img alt="" src="{{ $imgpath . (!empty($storethemesetting['homepage-testimonial-card-image'][$i]['field_prev_text']) ? $storethemesetting['homepage-testimonial-card-image'][$i]['field_prev_text'] : 'avatar.png') }}">
                                        </div>
                                        <div class="testi-content-bottom">
                                            <h5>{{ $storethemesetting['homepage-testimonial-card-title'][$i] }}</h5>
                                            <h6>{{ $storethemesetting['homepage-testimonial-card-sub-text'][$i] }}</h6>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            @endif
                        @endfor
                    @else
                        @for ($i = 0; $i < $storethemesetting['loop_number']; $i++)
                            <div class="testimonial-card">
                                <div class="testimonial-card-inner">
                                    <p>{{ $storethemesetting['inner-list'][4]['field_default_text'] }}</p>
                                <div class="testi-info">
                                    <div class="avtar-img">
                                        <img alt="" src="{{$imgpath . (!empty($storethemesetting['inner-list'][1]['field_default_text']) ? $storethemesetting['inner-list'][1]['field_default_text'] : 'avatar.png') }}">
                                    </div>
                                    <div class="testi-content-bottom">
                                        <h5>{{ $storethemesetting['inner-list'][2]['field_default_text'] }}</h5>
                                        <h6>{{ $storethemesetting['inner-list'][3]['field_default_text'] }}</h6>
                                    </div>
                                </div>
                                </div>
                            </div>
                        @endfor
                    @endif
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif --}}
    {{-- <section class="client-logo">
        <div class="container">
            <div class="client-logo-slider">

                @foreach ($getStoreThemeSetting as $key => $storethemesetting)
                    @if (isset($storethemesetting['section_name']) && $storethemesetting['section_name'] == 'Home-Brand-Logo' && $storethemesetting['section_enable'] == 'on')
                        @foreach ($storethemesetting['inner-list'] as $image)
                            @if (!empty($image['image_path']))
                                @foreach ($image['image_path'] as $img)
                                    <div class="client-logo-itm">
                                        <a href="#">
                                            <img src="{{$imgpath . (!empty($img) ? $img : 'storego-image.png') }}" alt="Footer logo">
                                        </a>
                                    </div>
                                @endforeach
                            @else
                            <div class="client-logo-itm">
                                <a href="#">
                                    <img src="{{$default }}" alt="Footer logo">
                                </a>
                            </div>
                            <div class="client-logo-itm">
                                <a href="#">
                                    <img src="{{$default }}" alt="Footer logo">
                                </a>
                            </div>
                            <div class="client-logo-itm">
                                <a href="#">
                                    <img src="{{$default }}" alt="Footer logo">
                                </a>
                            </div>
                            <div class="client-logo-itm">
                                <a href="#">
                                    <img src="{{$default }}" alt="Footer logo">
                                </a>
                            </div>
                            <div class="client-logo-itm">
                                <a href="#">
                                    <img src="{{$default }}" alt="Footer logo">
                                </a>
                            </div>
                            <div class="client-logo-itm">
                                <a href="#">
                                    <img src="{{$default }}" alt="Footer logo">
                                </a>
                            </div>
                            <div class="client-logo-itm">
                                <a href="#">
                                    <img src="{{$default }}" alt="Footer logo">
                                </a>
                            </div>
                            @endif
                        @endforeach
                    @endif
                @endforeach

            </div>
        </div>
    </section>
 </div>
@endsection --}}

@push('script-page')
    <script>
        $(".add_to_cart").click(function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            // var total = "{{ $total }}";
            var displayTotal = $('#displaytotal').text(); // Get the text content
            var variants = [];
            $(".variant-selection").each(function(index, element) {
                variants.push(element.value);
            });

            if (jQuery.inArray('', variants) != -1) {
                show_toastr('Error', "{{ __('Please select all option.') }}", 'error');
                return false;
            }
            var variation_ids = $('#variant_id').val();

            $.ajax({
                url: '{{ route('user.addToCart', ['__product_id', $store->slug, 'variation_id']) }}'
                    .replace(
                        '__product_id', id).replace('variation_id', variation_ids ?? 0),
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    variants: variants.join(' : '),
                },
                success: function(response) {
                    if (response.status == "Success") {
                        const cartItemsCountElements = document.getElementById('cart-item-count');
                        if (cartItemsCountElements) {
                            cartItemsCountElements.textContent = `(${response.item_count})`
                        }
                        const total = parseFloat(displayTotal) + parseFloat(response.price);
                        $('#displaytotal').text(addCommas(total));

                        // Show/hide checkout box based on total
                        if (total > 0) {
                            $('.checkout-box').fadeIn(); // Show if total > 0
                        } else {
                            $('.checkout-box').fadeOut(); // Hide if total is 0
                        }

                        const checkoutBtn = document.querySelector('.checkout-btn');
                        if (checkoutBtn) {
                            let url = new URL(checkoutBtn.href, window.location.origin);
                            let segments = url.pathname.split('/'); 
                            segments[segments.length - 1] = total; // Replace the last segment with the updated total
                            url.pathname = segments.join('/');
                            checkoutBtn.href = url.toString(); 
                        }
                        show_toastr('Success', response.success, 'success');
                        $("#shoping_counts").html(response.item_count);
                    } else {
                        show_toastr('Error', response.error, 'error');
                    }
                },
                error: function(result) {
                    console.log('error');
                }
            });
        });

        $(document).on('click', '.add_to_wishlist', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                type: "POST",
                url: '{{ route('store.addtowishlist', [$store->slug, '__product_id']) }}'.replace(
                    '__product_id', id),
                data: {
                    "_token": "{{ csrf_token() }}",
                },

                success: function(response) {
                    if (response.status == "Success") {
                        show_toastr('Success', response.message, 'success');
                        $('.wishlist_' + response.id).removeClass('add_to_wishlist');
                        $('.wishlist_' + response.id).html('<i class="fas fa-heart"></i>');
                        $('.wishlist_count').html(response.count);
                    } else {
                        show_toastr('Error', response.error, 'error');
                    }
                },
                error: function(result) {}
            });
        });

        
        $(".productTab").click(function(e) {
            e.preventDefault();
            $('.productTab').removeClass('active')

        });

        $("#pro_scroll").click(function() {
            $('html, body').animate({
                scrollTop: $("#pro_items").offset().top
            }, 1000);
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
<style>
  :root {
    /* Font settings */
    --swal-font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --swal-font-base: 14px;
    --swal-title-size: 1.2rem;
    --swal-content-size: 0.875rem;
    --swal-text-color: #333333;
    
    /* Icon settings */
    --swal-icon-size: 3.5rem;
    --swal-success-color: #28a745;
    --swal-error-color: #dc3545;
    --swal-warning-color: #ffc107;
    --swal-info-color: #17a2b8;
    --swal-question-color: #556ee6;
    
    /* Input settings */
    --swal-input-width: 220px;
    --swal-input-bg: #ffffff;
    --swal-input-border: #ced4da;
    --swal-input-radius: 4px;
    
    /* Button settings */
    --swal-btn-font: 0.9rem;
    --swal-btn-padding: 0.5em 1.5em;
    --swal-btn-radius: 4px;
    --swal-btn-font-weight: 500;
    --swal-btn-letter-spacing: 0.5px;
    --swal-btn-text-transform: none;
    
    /* Confirm button */
    --swal-confirm-bg: #556ee6;
    --swal-confirm-text: #ffffff;
    --swal-confirm-hover-bg: #485ec4;
    --swal-confirm-active-bg: #3a4fa3;
    --swal-confirm-border: none;
    
    /* Cancel button */
    --swal-cancel-bg:var(--swal-text-color);
    --swal-cancel-text: #ffffff;
    --swal-cancel-hover-bg: #ffffff;
    --swal-cancel-active-bg: #ffffff;
    --swal-cancel-border: none;
    
    /* Popup settings */
    --swal-popup-bg: #ffffff;
    --swal-popup-radius: 8px;
    --swal-popup-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  /* Apply base styles to the popup */
  .my-swal-popup {
    font-family: var(--swal-font-family);
    font-size: var(--swal-font-base);
    color: var(--swal-text-color);
    background-color: var(--swal-popup-bg);
    border-radius: var(--swal-popup-radius);
    box-shadow: var(--swal-popup-shadow);
    padding: 1.75em;
    width: auto;
    max-width: 500px;
     --swal2-html-container-padding: 20px;
     
  }
  .my-swal-popup .swal2-icon.swal2-error {
  border: 0.25em solid var(--theme-color)  !important;
  color: var(--theme-color)  !important;
}
.my-swal-popup .swal2-icon.swal2-error .swal2-x-mark-line {
  background-color: var(--theme-color) !important;
}

  /* Title styling */
  .my-swal-popup .swal2-title {
    font-size: var(--swal-title-size);
    font-weight: 600;
    margin-bottom: 0.5em;
    color: var(--swal-text-color);
    line-height: 1.4;
  }

  /* Content text styling */
  .my-swal-popup .swal2-content {
    font-size: var(--swal-content-size);
    text-align: center;
    margin-bottom: 1.25em;
    color: var(--swal-text-color);
    line-height: 1.5;
  }

  /* Icon styling - with color overrides */
  .my-swal-popup .swal2-icon {
    /* width: var(--swal-icon-size) !important;
    height: var(--swal-icon-size) !important; */
    margin: 0 auto 1em;
    border-width: 0.25em;
  }
  .my-swal-popup .swal2-success [class^=swal2-success-line] {
    background-color: var(--swal-success-color);
  }
  .my-swal-popup .swal2-success .swal2-success-ring {
    border-color: rgba(40, 167, 69, 0.3);
  }
  .my-swal-popup .swal2-error [class^=swal2-x-mark-line] {
    background-color: var(--theme-color);
  }
  .my-swal-popup .swal2-warning {
    color: var(--theme-color);
    border-color: var(--theme-color);
  }
  .my-swal-popup .swal2-info {
    color: var(--theme-color);
    border-color: var(--theme-color);
  }
  .my-swal-popup .swal2-question {
    color: var(--theme-color);
    border-color: var(--theme-color);
  }

  /* Input field styling */
  .my-swal-input {
    width: var(--swal-input-width) !important;
    margin: 0 auto 1.25em;
    padding: 0.625em;
    font-size: 1em;
    background-color: var(--swal-input-bg);
    border: 1px solid var(--swal-input-border);
    border-radius: var(--swal-input-radius);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }
  .my-swal-input:focus {
    border-color: var(--swal-confirm-bg);
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(85, 110, 230, 0.25);
  }

  /* Base button styles */
  .my-swal-btn {
    font-family: var(--swal-font-family);
    font-size: var(--swal-btn-font) !important;
    font-weight: var(--swal-btn-font-weight);
    letter-spacing: var(--swal-btn-letter-spacing);
    text-transform: var(--swal-btn-text-transform);
    padding: var(--swal-btn-padding);
    border-radius: var(--swal-btn-radius) !important;
    min-width: 100px;
    margin: 0 0.5em;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    border: var(--swal-confirm-border);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  .my-swal-btn:focus {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(85, 110, 230, 0.5);
  }

  /* Confirm button styles */
  .my-swal-confirm {
    background-color:var(--theme-color); !important;
    color: var(--swal-confirm-text) !important;
  }
  .my-swal-confirm:hover {
    background-color: var(--theme-color) !important;
  }
  .my-swal-confirm:active {
    background-color: var(--theme-color) !important;
  }

  /* Cancel button styles */
  .my-swal-cancel {
   background-color: transparent !important;
  border: 2px solid var(--theme-color) !important;
  color: var(--theme-color) !important;
  }
  .my-swal-cancel:hover {
    background-color: var(--swal-cancel-hover-bg) !important;
  }
  .my-swal-cancel:active {
    background-color: var(--swal-cancel-active-bg) !important;
  }

  /* Action buttons container */
  .my-swal-popup .swal2-actions {
    display: flex;
    justify-content: center;
    margin-top: 1em;
    gap: 0.75em;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.open-free-modal').forEach(btn => {
    btn.addEventListener('click', async e => {
      const id = btn.dataset.id; // product id
      const chatType = btn.dataset.chatType; // "sms" or "email"
      const isSms = chatType === 'sms';

      // 1) ASK FOR PHONE or EMAIL
      const { value: destination, isConfirmed } = await Swal.fire({
        title: isSms ? 'Enter your mobile number' : 'Enter your email',
        icon: 'question',
        input: isSms ? 'tel' : 'email',
        inputPlaceholder: isSms ? '+1234567890' : 'you@example.com',
        showCancelButton: true,
        confirmButtonText: 'Send Code',
        cancelButtonText: 'Cancel',
        customClass: {
          popup: 'my-swal-popup',
          confirmButton: 'my-swal-btn my-swal-confirm',
          cancelButton: 'my-swal-btn my-swal-cancel',
          input: 'my-swal-input'
        },
        buttonsStyling: false
      });

      if (!isConfirmed || !destination) return;

      // Show loading state
      Swal.fire({
        title: 'Sending code...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // Stub: call your real SMS/email API here
      await sendOtp(destination);
      Swal.close();

      // 2) ASK FOR OTP
      const { value: otp, isConfirmed: otpOk } = await Swal.fire({
        title: 'Enter verification code',
        html: `We sent a verification code to <strong>${destination}</strong>`,
        icon: 'info',
        input: 'text',
        inputPlaceholder: '123456',
        showCancelButton: true,
        confirmButtonText: 'Verify',
        cancelButtonText: 'Cancel',
        customClass: {
          popup: 'my-swal-popup',
          confirmButton: 'my-swal-btn my-swal-confirm',
          cancelButton: 'my-swal-btn my-swal-cancel',
          input: 'my-swal-input'
        },
        buttonsStyling: false,
        preConfirm: code => {
          if (!/^\d+$/.test(code)) {
            Swal.showValidationMessage('Please enter a valid numeric code');
          }
          return code;
        }
      });

      if (!otpOk) return;

      // Show verifying state
      Swal.fire({
        title: 'Verifying...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // Stub: verify it on your side
      const result = await verifyOtp(otp, destination, id);
      Swal.close();

      if (result?.valid && result?.eligible) {
        Swal.fire({
          title: 'Success!',
          text: 'Your code has been verified successfully.',
          icon: 'success',
          confirmButtonText: 'Done!',
          customClass: {
            popup: 'my-swal-popup',
            confirmButton: 'my-swal-btn my-swal-confirm'
          },
          buttonsStyling: false
        });
        // …then add to cart / grant free product…
      } else {
        Swal.fire({
          title: 'Error',
          text: result?.message,
          icon: 'error',
          confirmButtonText: 'OK',
          customClass: {
            popup: 'my-swal-popup',
            confirmButton: 'my-swal-btn my-swal-confirm'
          },
          buttonsStyling: false
        });
      }
    });
  });
});

// === STUB FUNCTIONS ===
async function sendOtp(destination) {
    const chatType = destination.includes('@') ? 'email' : 'sms';

    // Show loading indicator before sending
    Swal.fire({
        title: 'Sending code...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Return a promise so the next step waits for it
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "{{ route('otp.send') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                destination, 
                type: chatType
            },
            success: function(response) {
                Swal.close(); // Close loading when done
                resolve(response); // Resolve promise for continuation
            },
            error: function (err) {
                Swal.close();
                console.error('Error sending OTP:', err.responseText);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to send verification code. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'my-swal-popup',
                        confirmButton: 'my-swal-btn my-swal-confirm'
                    },
                    buttonsStyling: false
                });
                reject(err);
            }
        });
    });
}

async function verifyOtp(code, destination, productId) {
    // Show loading while verifying
    Swal.fire({
        title: 'Verifying...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    return new Promise((resolve, reject) => {
        $.ajax({
            url: "{{ route('otp.verify') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                code,
                destination,
                productId
            },
            success: function(response) {
                Swal.close();
                resolve(response); // { valid, eligible, message }
            },
            error: function(err) {
                Swal.close();
                console.error('Error verifying OTP:', err.responseText);
                resolve({
                    valid: false,
                    message: 'Verification failed. Please try again.'
                });
                reject(err);
            }
        });
    });
}
</script>



@endpush
