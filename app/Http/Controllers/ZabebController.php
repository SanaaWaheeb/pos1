<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCoupon;
use App\Models\ProductVariantOption;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZabebController extends Controller
{
    public function PayWithZabeb(Request $request, $slug, $order_amount)
    {
        // Validate email and password presence
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Fetch store and cart
        $cart = session()->get($slug, ['products' => [], 'cart_item_count' => 1]);
        $store = Store::where('slug', $slug)->first();
        if (empty($cart)) 
        {
            return redirect()->back()->with('error', __('Please add to product into cart'));
        } 
        if (!$store) {
            return redirect()->back()->with('error', __('Store not found.'));
        }

        // Get payment settings
        $store_payment_setting = Auth::check()
            ? Utility::getPaymentSetting()
            : Utility::getPaymentSetting($store->id);
        $clientId = $store_payment_setting['zabeb_client_id'] ?? '';
        $secretKey = $store_payment_setting['zabeb_secret_key'] ?? '';

        // Get Coupon
        if(isset($cart['coupon']['data_id']))
        {
            $coupon = ProductCoupon::where('id', $cart['coupon']['data_id'])->first();
        }
        else
        {
            $coupon = '';
        }

        // Get Shipping
        $shipping_data = null;
        if (isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping'])) {
            $shipping = Shipping::find($cart['shipping']['shipping_id']);
            if (!empty($shipping)) {
                $shipping_name = $shipping->name;
                $shipping_price = $shipping->price;
    
                $shipping_data = json_encode(
                    [
                        'shipping_name' => $shipping_name,
                        'shipping_price' => $shipping_price,
                        'location_id' => $cart['shipping']['location_id'],
                    ]
                );
            } else {
                $shipping_data = '';
            }
        }

        // Get product IDs
        $products = $cart['products'];
        $product_ids = [];
        $product_names = [];
        foreach($products as $item) {
            $product_ids[] = $item['product_id'];
            $product_names[] = $item['product_name'];
        }
        $productId = count($product_ids) > 1? 0 : $product_ids[0]; // Default id in case there exist multiple products

        // Authenticate the user against Zabeb
        $loginResp = Http::post('https://checkout.zabeb.com/api/login', [
            'email' => $request->email,
            'password' => $request->password,
            'publicId' => $clientId, 
            'secretKey' => $secretKey,
            'mode' => 'live'
        ]);
        if (!$loginResp->successful() || !isset($loginResp['user'])) {
            $err = $loginResp->json('error') ?? 'Zabeb login failed';
            return redirect()->back()->with('error', $err);
        }
        $loginData = $loginResp->json();
        $user = $loginData['user'];
        $accessToken = $loginData['access_token'];

        // Store order in DB
        if (Utility::CustomerAuthCheck($store->slug)) {
            $customer = Auth::guard('customers')->user()->id;
        } else {
            $customer = 0;
        }

        $cust_details = $cart['customer'] ?? null;
        $customer               = Auth::guard('customers')->user();
        $order                  = new Order();
        $order->order_id        = 'xxxxx';
        $order->board           = $store->board_id;
        // theme3 , but theme1 and theme2 only phone number
        $order->name            = $user['name'] ?? 'Guest';
        $order->email           = $user['email'] ?? 'guest@example.com';
        $order->card_number     = '';
        $order->card_exp_month  = '';
        $order->card_exp_year   = '';
        $order->status          = 'pending';
        $order->user_address_id = $cust_details['id'] ?? null;
        $order->shipping_data   = $shipping_data;
        $order->product_id      = implode(',', $product_ids);
        $order->price           = $order_amount;
        $order->coupon          = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
        $order->coupon_json     = json_encode($coupon);
        $order->discount_price  = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
        $order->product         = json_encode($products);
        $order->price_currency  = $store->currency_code;
        $order->txn_id          = 'xxxxxx';
        $order->payment_type    = 'zabeb';
        $order->payment_status  = 'pending';
        $order->receipt         = '';
        $order->user_id         = $store['id'];
        $order->is_confirmed    = 0;
        $order->customer_id     = isset($customer->id) ? $customer->id : '';
        $order->check_in_date = $cust_details['check_in_date'] ?? null;
        $order->number_of_nights = $cust_details['number_of_nights'] ?? 1;
         
        $order->save();

        // Udpate order id
        $order_id = $order->id;
        $order->order_id        = "#" . $order_id;
        $order->update();

        // Prepare payment data to zabeb url
        $paymentData = [
            "order_id" => $order->id,
            "product_id" => $productId,
            "store_id" => $store->id,
            "product_name" => base64_encode(implode(', ', $product_names)),
            "store_name" => $store->name,
            "qty" => $cart['cart_item_count'],
            "amount" => $order_amount,
            "machine_name" => 'machine',
            "track_name" => 'track',
            "slug" => $slug,
            "publicId" => $clientId,
            "secretKey" => $secretKey,
            "zabebPaymentMode" => 'live',            
            "url" => route('zabeb.callback'),
            'isLoggedIn' => true,
            'userId'             => $user['id'],
            'accessToken'        => $accessToken,
            'userName'           => $user['name'],
            'totalCredit'        => $user['employee_details']['total_credit'] ?? 0,
            'dailyLimit'         => $user['employee_details']['daily_limit'] ?? 1,
            'isDailyLimitEnable' => $user['employee_details']['is_daily_limit_enable'] ?? 1,
        ];

        // Encode and redirect to Zabeb
        $json    = json_encode($paymentData, JSON_UNESCAPED_UNICODE);
        $encoded = rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
        $zabebUrl = "https://checkout.zabeb.com/transaction/{$encoded}";

        // 4) Redirect the user off to Zabeb
        return redirect()->away($zabebUrl);
    }

    /**
     * Clean zabeb callback url
     */
    public function zabebPaymentCallback(Request $request)
    {
        try {
            // Get the full url path
            $fullPath = $request->path();

            // Split path into segments
            $pathSegments = explode('/', $fullPath);

            // Extract slugh and order_id segments
            $slug = str_replace('callback', '', $pathSegments[1]);
            $orderId = $pathSegments[count($pathSegments) - 2] ?? null;
            
            //  Verify the order exists
            $order = Order::find($orderId);
            if (!$order) {
                return redirect()->with('error', 'Order not found');
            }

            // Reduce products quantity
            $store = Store::where('slug', $slug)->where('is_store_enabled', '1')->first();
            if($store->theme_dir != 'theme5') {
                $cart = session()->get($slug, ['products' => [], 'cart_item_count' => 1]);
                $products = $cart['products'];
                foreach ($products as $key => $product) {
                    if ($product['variant_id'] != 0) {
                        $new_qty = $product['originalvariantquantity'] - $product['quantity'];
                        $product_edit = ProductVariantOption::find($product['variant_id']);
                        $product_edit->quantity = $new_qty;
                        $product_edit->save();
                    } else {
                        $new_qty = $product['originalquantity'] - $product['quantity'];
                        $product_edit = Product::find($product['product_id']);
                        $product_edit->quantity = $new_qty;
                        $product_edit->save();
                    }
                }
            }

            // Redirect to the clean completion URL
            return redirect()
                ->route('store-complete.complete', [
                    $slug, 
                    Crypt::encrypt($orderId)
                ])
                ->with('success', 'Payment successful! Your order is confirmed.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('store', $slug)
                ->with('error', 'Invalid order reference');
        }
    
    }
}
