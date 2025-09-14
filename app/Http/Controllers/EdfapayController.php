<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCoupon;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EdfapayController extends Controller
{
    public function payWithEdfapay(Request $request, $slug)
    {
        // Fetch store and cart
        $cart = session()->get($slug, ['products' => [], 'cart_item_count' => 0]);
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
        $edfaPayPassword = $store_payment_setting['edfapay_password'];
        $edfaPayMerchantKey = $store_payment_setting['edfapay_merchant_key'];

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

        // Fetch IP address
        $ipAddress = '';
        $ipResponse = file_get_contents('https://api.ipify.org?format=json');
        if ($ipResponse !== false) {
            $ipData = json_decode($ipResponse, true);
            $ipAddress = $ipData['ip'] ?? '';
        }

        // Get product IDs
        $product_ids = [];
        $products = $cart['products'];
        foreach($products as $item) {
            $product_ids[] = $item['product_id'];
        }

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
        // $order->board           = $store->board_id;
        // theme3 , but theme1 and theme2 only phone number::
        $order->name            = $cust_details['name'] ?? 'Guest';
        $order->email           =  $cust_details['email'] ?? 'guest@example.com';

        $order->card_number     = '';
        $order->card_exp_month  = '';
        $order->card_exp_year   = '';
        $order->status          = 'pending';
        $order->user_address_id = $cust_details['id'] ?? null;
        $order->shipping_data   = $shipping_data;
        $order->product_id      = implode(',', $product_ids);
        $order->price           = $request->order_amount;
        $order->coupon          = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
        $order->coupon_json     = json_encode($coupon);
        $order->discount_price  = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
        $order->product         = json_encode($products);
        $order->price_currency  = $store->currency_code;
        $order->txn_id          = 'xxxxxx';
        $order->payment_type    = 'edfapay';
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
        $encoded_order_id = base64_encode($order_id);
        $order->order_id        = "#" . $order_id;
        $order->update();

        // Security purpose
        $orderCurrency = "SAR";
        $payerCountry = "SA";
        $orderDescription = 'Hi From AVA';
        $to_md5 = $order_id . $request->order_amount . $orderCurrency . $orderDescription . $edfaPayPassword;
        $md5_hash = md5(strtoupper($to_md5));
        $hash = sha1($md5_hash);

        // Prepare payment data to edfapay url
        $paymentData = [
            "action" => "SALE",
            "edfa_merchant_id" => $edfaPayMerchantKey,
            'order_id' => $order_id,
            "order_amount" => $request->order_amount,
            "order_currency" => $orderCurrency,
            "order_description" => $orderDescription,
            "req_token" => "N",
            "payer_first_name" => "payerfirstname",
            "payer_last_name" => "payerlastname",
            "payer_address" => "payeraddress",
            "payer_country" => $payerCountry,
            "payer_city" => "payercity",
            "payer_zip" => "12221",
            "payer_email" => "edfapayPayer@mailinator.com",
            "payer_phone" => "966565555555",
            'payer_ip' => $ipAddress,
            "term_url_3ds" => route('edfapay.callback', [
                'slug' => $slug, 
                'order_id' => $encoded_order_id,
            ]),
            "auth" => "N",
            "recurring_init" => "N",
            "hash" => $hash,
        ];

        // Send request to payment gateway
        $ch = curl_init('https://api.edfapay.com/payment/initiate');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paymentData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response !== false) {
            $responseData = json_decode($response, true);
            if (isset($responseData['redirect_url'])) {
                return redirect()->away($responseData['redirect_url']);
            } else {
                return response()->json(['error' => __('Failed to initiate payment')], 400);
            }
        } else {
            return response()->json(['error' => __('Payment gateway not reachable')], 500);
        }
    }

    /**
     * Forward user to waiting page until checking payment status
     */
    public function edfaPayPaymentCallback(Request $request) 
    { 
        $slug = $request->slug;
        $order_id = $request->order_id;
    
        // Redirect to the waiting page
        return view('layouts.waiting', compact('slug', 'order_id'));
    }

    /**
     * Checking payment status
     */
    public function checkOrderStatus(Request $request) {
        $order_id = $request->order_id;
        $decOrderId = base64_decode($order_id);

        $order = Order::find(id: $decOrderId);
        if (isset($order)) {
            $status = $order->payment_status;

            return response()->json([
                'status' => $status
            ]);
        }

        return response()->json([
            'status' => 'pending',
        ]);
    }
}
