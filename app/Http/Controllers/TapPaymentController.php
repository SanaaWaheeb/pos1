<?php

namespace App\Http\Controllers;

use App\Package\Payment;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Product;
use App\Models\ProductCoupon;
use App\Models\ProductVariantOption;
use App\Models\PurchasedProducts;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class TapPaymentController extends Controller
{
    // public $secret_key;
    // public $is_enabled;

    // public function paymentConfig()
    // {
    //     if (\Auth::user()->type == 'company') {
    //         $creatorId = \Auth::user()->creatorId();
    //         $payment_setting = Utility::getCompanyPaymentSetting($creatorId);
    //     } else {
    //         $payment_setting = Utility::getAdminPaymentSetting();
    //     }

    //     $this->secret_key = isset($payment_setting['tap_secret_key']) ? $payment_setting['tap_secret_key'] : '';
    //     $this->is_enabled = isset($payment_setting['is_tap_enabled']) ? $payment_setting['is_tap_enabled'] : 'off';

    //     return $this;
    // }
    public function planPayWithTap(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $tap_secret_key = isset($payment_setting['tap_secret_key']) ? $payment_setting['tap_secret_key'] : '';
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan      = Plan::find($planID);
        $authuser  = \Auth::user();
        $coupon_id = '';
        if($plan)
        {
            $price = $plan->price;
            if (isset($request->coupon) && !empty($request->coupon)) {
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if(!empty($coupons))
                {
                    $usedCoupun     = $coupons->used_coupon();
                    $discount_value = ($plan->price / 100) * $coupons->discount;
                    $price          = $plan->price - $discount_value;

                    if($coupons->limit == $usedCoupun)
                    {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                } else{
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if($price <= 0)
            {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id);

                if ($assignPlan['is_success'] == true && !empty($plan)) {
                    if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                        try {
                            $authuser->cancel_subscription($authuser->id);
                        } catch (\Exception $exception) {
                            \Log::debug($exception->getMessage());
                        }
                    }
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $userCoupon = new UserCoupon();
                    $userCoupon->user = $authuser->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order = $orderID;
                    $userCoupon->save();

                    PlanOrder::create(
                        [
                            'order_id' => $orderID,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price == null ? 0 : $price,
                            'price_currency' => isset($payment_setting['currency']) && !empty($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',
                            'txn_id' => '',
                            'payment_type' => 'Tap',
                            'payment_status' => 'success',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $assignPlan = $authuser->assignPlan($plan->id);
                    return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));
                }
            }

            try {
                $TapPay = new Payment(['tap_secret_key'=> $tap_secret_key]);
                return $TapPay->charge([
                    'amount' => $price,
                    'currency' => $currency,
                    'threeDSecure' => 'true',
                    'description' => 'test description',
                    'statement_descriptor' => 'sample',
                    'customer' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    ],
                    'source' => [
                    'id' => 'src_card'
                    ],
                    'post' => [
                    'url' => null
                    ],
                    // 'merchant' => [
                    //    'id' => 'YOUR-MERCHANT-ID'  //Include this when you are going to live
                    // ],
                    'redirect' => [
                    'url' => route('plan.get.tap.status', [ $plan->id,
                    'amount' => $price,
                    'coupon' => $request->coupon,
                        ])
                    ]
                ],true);

            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e);
            }
        } else{
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }


    public function planGetTapStatus(Request $request, $plan_id)
    {
        $payment_setting = Utility::getAdminPaymentSetting();

        $plan = Plan::find($plan_id);
        $user = \Auth::user();
        $couponCode = $request->coupon;
        $getAmount = $request->amount;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }

        $order = new PlanOrder();
        $order->order_id = $orderID;
        $order->name = $user->name;
        $order->card_number = '';
        $order->card_exp_month = '';
        $order->card_exp_year = '';
        $order->plan_name = $plan->name;
        $order->plan_id = $plan->id;
        $order->price = $getAmount;
        $order->price_currency = isset($payment_setting['currency']) && !empty($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $order->payment_type = 'Tap';
        $order->payment_status = 'success';
        $order->txn_id = '';
        $order->receipt = '';
        $order->user_id = $user->id;
        $order->save();

        $assignPlan = $user->assignPlan($plan->id);
        $coupons = Coupon::find($request->coupon_id);
        if (!empty($request->coupon_id)) {
            if (!empty($coupons)) {
                $userCoupon = new UserCoupon();
                $userCoupon->user = $user->id;
                $userCoupon->coupon = $coupons->id;
                $userCoupon->order = $orderID;
                $userCoupon->save();
                $usedCoupun = $coupons->used_coupon();
                if ($coupons->limit <= $usedCoupun) {
                    $coupons->is_active = 0;
                    $coupons->save();
                }
            }
        }

        Utility::referralTransaction($plan);
        if ($assignPlan['is_success']) {
            return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
        } else {
            return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
        }
    }

    public function invoicePayWithTap(Request $request, $slug)
    {
        try {
            $cart = session()->get($slug);
            if(!empty($cart))
            {
                $products = $cart['products'];
            } else{
                return redirect()->back()->with('error', __('Please add to product into cart'));
            }
            \Log::info('TAP - Cart Contents:', ['cart' => $cart]);

         $customers = $cart['customer'] ?? [
    'name' => 'Guest',
    'email' => 'guest@example.com',
    'phone' => '0000000000',
    'id' => time(),
];


            $store = Store::where('slug', $slug)->first();
            $currency = $store->currency_code;
            $user = User::find($store->created_by);
            $storepaymentSetting = Utility::getPaymentSetting($store->id);
            $tap_secret_key = isset($storepaymentSetting['tap_secret_key']) ? $storepaymentSetting['tap_secret_key'] : '';

            $total_tax = $sub_total = $total = $sub_tax = 0;
            $product_name = [];
            $product_id = [];

            foreach ($products as $key => $product) {
                if ($product['variant_id'] != 0) {

                    $product_name[] = $product['product_name'];
                    $product_id[] = $product['id'];

                    foreach ($product['tax'] as $tax) {
                        $sub_tax = ($product['variant_price'] * $product['quantity'] * $tax['tax']) / 100;
                        $total_tax += $sub_tax;
                    }
                    $totalprice = $product['variant_price'] * $product['quantity'];
                    $total += $totalprice;
                } else {
                    $product_name[] = $product['product_name'];
                    $product_id[] = $product['id'];

                    foreach ($product['tax'] as $tax) {
                        $sub_tax = ($product['price'] * $product['quantity'] * $tax['tax']) / 100;
                        $total_tax += $sub_tax;
                    }
                    $totalprice = $product['price'] * $product['quantity'];
                    $total += $totalprice;
                }
            }
            if ($products) {
                $get_amount = $total + $total_tax;

                if (isset($cart['coupon'])) {
                    if ($cart['coupon']['coupon']['enable_flat'] == 'off') {

                        $discount_value = ($get_amount / 100) * $cart['coupon']['coupon']['discount'];
                        $get_amount = $get_amount - $discount_value;
                    } else {

                        $discount_value = $cart['coupon']['coupon']['flat_discount'];
                        $get_amount = $get_amount - $discount_value;
                    }
                }
                $price = $get_amount;
                if (isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping'])) {
                    $shipping = Shipping::find($cart['shipping']['shipping_id']);
                    if (!empty($shipping)) {
                        $get_amount = $get_amount + $shipping->price;
                    }
                }
                $coupon = (empty($cart['coupan'])) ? "0" : $cart['coupan'];
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                try {
                    $TapPay = new Payment(['tap_secret_key'=> $tap_secret_key]);

                    return $TapPay->charge([
                        'amount' => $get_amount,
                        'currency' => !empty($currency) ?  $currency : 'USD',
                        'threeDSecure' => 'true',
                        'description' => 'test description',
                        'statement_descriptor' => 'sample',
                        'customer' => [
                            'first_name' => $user->name,
                            'email' => $user->email,
                        ],
                        'source' => [
                            'id' => 'src_card'
                        ],
                        'post' => [
                            'url' => null
                        ],
                        // 'merchant' => [
                        //    'id' => 'YOUR-MERCHANT-ID'  //Include this when you are going to live
                        // ],
                        'redirect' => [
                            'url' => route('order.tap.status', ['slug' => $slug, 'amount' => $price, 'orderId' => $orderID, 'product_id' => $product_id])
                        ]
                    ],true);
                } catch (\Throwable $e) {
                    return redirect()->back()->with('error', __($e->getMessage()));
                }
            } else {
                return redirect()->back()->with('error', __('product is not found.'));
            }

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }

    public function invoiceGetTapStatus(Request $request)
    {
        $getAmount = $request->amount;
        $product_id = $request->product_id;
        $slug = $request->slug;
        $store = Store::where('slug', $slug)->first();

        $storepaymentSetting = Utility::getPaymentSetting($store->id);
        try{
            $cart = session()->get($slug);
            $products       = $cart['products'];
            $cust_details = $cart['customer'];
            if(isset($cart['coupon']['data_id']))
            {
                $coupon = ProductCoupon::where('id', $cart['coupon']['data_id'])->first();
            }
            else
            {
                $coupon = '';
            }
            if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
            {
                $shipping       = Shipping::find($cart['shipping']['shipping_id']);
                $shipping_name  = $shipping->name;
                $shipping_price = $shipping->price;
                $shipping_data  = json_encode(
                    [
                        'shipping_name' => $shipping_name,
                        'shipping_price' => $shipping_price,
                        'location_id' => $cart['shipping']['location_id'],
                    ]
                );
            }
            else
            {
                $shipping_data = '';
            }

            $customer               = Auth::guard('customers')->user();
            $order                  = new Order();
            $order->order_id        = $request->orderId;
            $order->name            = isset($cust_details['name']) ? $cust_details['name'] : '' ;
            $order->email           = isset($cust_details['email']) ? $cust_details['email'] : '';
            $order->card_number     = '';
            $order->card_exp_month  = '';
            $order->card_exp_year   = '';
            $order->status          = 'pending';
            $order->user_address_id = isset($cust_details['id']) ? $cust_details['id'] : '';
            $order->shipping_data   = $shipping_data;
            $order->product_id      = implode(',', $product_id);
            $order->price           = $getAmount;
            $order->coupon          = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
            $order->coupon_json     = json_encode($coupon);
            $order->discount_price  = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
            $order->product         = json_encode($products);
            $order->price_currency  = $store->currency_code;
            $order->txn_id          = isset($pay_id) ? $pay_id : '';
            $order->payment_type    = 'Tap';
            $order->payment_status  = 'approved';
            $order->receipt         = '';
            $order->user_id         = $store['id'];
            $order->customer_id     = isset($customer->id) ? $customer->id : '';
            $order->save();

            //webhook
            $module = 'New Order';
            $webhook =  Utility::webhook($module, $store->id);
            if ($webhook) {
                $parameter = json_encode($order);
                //
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status != true) {
                    $msg  = 'Webhook call failed.';
                }
            }


            if ((!empty(Auth::guard('customers')->user()) && $store->is_checkout_login_required == 'on') ){

                foreach($products as $product_id)
                {
                    $purchased_products = new PurchasedProducts();
                    $purchased_products->product_id  = $product_id['product_id'];
                    $purchased_products->customer_id = $customer->id;
                    $purchased_products->order_id   = $order->id;
                    $purchased_products->save();
                }
            }
            $order_email = $order->email;
            $owner=User::find($store->created_by);
            $owner_email=$owner->email;
            $order_id = Crypt::encrypt($order->id);
            // if(isset($store->mail_driver) && !empty($store->mail_driver))
            // {
                $dArr = [
                    'order_name' => $order->name,
                ];
                $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);
                $resp1=Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);
            // }
            if(isset($store->is_twilio_enabled) && $store->is_twilio_enabled=="on")
            {
                Utility::order_create_owner($order,$owner,$store);
                Utility::order_create_customer($order,$customer,$store);
            }
            $msg = redirect()->route(
                'store-complete.complete', [
                                            $store->slug,
                                            Crypt::encrypt($order->id),
                                        ]
            )->with('success', __('Transaction has been success'));

            session()->forget($slug);

            return $msg;

        }catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
