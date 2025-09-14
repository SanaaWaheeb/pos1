<?php

namespace App\Http\Controllers;

use App\Khalti\Khalti;
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

class KhaltiPaymentController extends Controller
{
    public function planPayWithKhalti(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan      = Plan::find($planID);
        $authuser  = \Auth::user();
        $coupon_id = '';

        if($plan)
        {
            $price = $plan->price;
            if (isset($request->coupon_code) && !empty($request->coupon_code)) {
                $coupons = Coupon::where('code', strtoupper($request->coupon_code))->where('is_active', '1')->first();
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
                            'payment_type' => 'Khalti',
                            'payment_status' => 'success',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $assignPlan = $authuser->assignPlan($plan->id);

                    return $price;
                    // return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));
                }
            }

            try {
                $secret     = !empty($payment_setting['khalti_secret_key']) ? $payment_setting['khalti_secret_key'] : '';
                $amount     = $price;

                return $amount;
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e);
                // return redirect()->back()->with('error', $e);
            }
        } else{
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetKhaltiStatus(Request $request)
    {
        $planID     = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $orderID    = strtoupper(str_replace('.', '', uniqid('', true)));

        $admin_settings = Utility::getAdminPaymentSetting();
        $secret     = !empty($admin_settings['khalti_secret_key']) ? $admin_settings['khalti_secret_key'] : '';
        $currency = isset($admin_settings['currency']) ? $admin_settings['currency'] : 'USD';
        $plan       = Plan::find($planID);
        $user       = Auth::user();
        if ($plan) {
            $price = $plan->price;
            if ($request->coupon_code) {
                $coupons = Coupon::where('code', strtoupper($request->coupon_code))->where('is_active', '1')->first();
                if(!empty($coupons))
                {
                    $usedCoupun     = $coupons->used_coupon();
                    $discount_value = ($plan->price / 100) * $coupons->discount;
                    $price          = $plan->price - $discount_value;

                    if($coupons->limit == $usedCoupun)
                    {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }

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
                } else{
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            $payload    = $request->payload;
            $token      = $payload['token'];
            $amount     = $payload['amount'];
            $khalti     = new Khalti();
            $response   = $khalti->verifyPayment($secret, $token, $amount);

            try {
                if ($response['status_code'] == '200') {
                    $order = new PlanOrder();
                    $order->order_id = $orderID;
                    $order->name = $user->name;
                    $order->card_number = '';
                    $order->card_exp_month = '';
                    $order->card_exp_year = '';
                    $order->plan_name = $plan->name;
                    $order->plan_id = $plan->id;
                    $order->price = $price;
                    $order->price_currency = $currency;
                    $order->txn_id = $orderID;
                    $order->payment_type = 'Khalti';
                    $order->payment_status = 'success';
                    $order->receipt = '';
                    $order->user_id = $user->id;
                    $order->save();

                    $assignPlan = $user->assignPlan($plan->id);

                    Utility::referralTransaction($plan);
                    if ($assignPlan['is_success']) {
                        return $response;
                    } else {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                } else {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
                }
            } catch (\Exception $e) {
                return response()->json('failed');
            }
        } else {
            return response()->json('deleted');
        }
    }

    public function invoicePayWithKhalti(Request $request, $slug)
    {
        try {
            $cart     = session()->get($slug);
            if(!empty($cart))
            {
                $products = $cart['products'];
                $cust_details = $cart['customer'];
            } else{
                return redirect()->back()->with('error', __('Please add to product into cart'));
            }
            $store = Store::where('slug', $slug)->first();
            $currency = isset($store->currency_code) ? $store->currency_code : 'USD';
            $authuser = User::find($store->created_by);
            $payment_setting = Utility::getPaymentSetting($store->id);
            $secret     = !empty($payment_setting['khalti_secret_key']) ? $payment_setting['khalti_secret_key'] : '';

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
                if (isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping'])) {
                    $shipping = Shipping::find($cart['shipping']['shipping_id']);
                    if (!empty($shipping)) {
                        $get_amount = $get_amount + $shipping->price;
                    }
                }
                $coupon = (empty($cart['coupan'])) ? "0" : $cart['coupan'];
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                try {
                    $get_amount = round($get_amount);

                    return $get_amount;
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }

    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->first();
        $products = $request['product'];
        $order_id = $request['order_id'];
        $cart = session()->get($slug);
        $cust_details = $cart['customer'];

        if (!empty($request->coupon_id)) {
            $coupon = ProductCoupon::where('id', $request->coupon_id)->first();
        } else {
            $coupon = '';
        }

        $product_name = [];
        $product_id = [];
        $tax_name = [];
        $totalprice = 0;

        $storepaymentSetting = Utility::getPaymentSetting($store->id);

        $payload    = $request->payload;
        $secret     = !empty($storepaymentSetting['khalti_secret_key']) ? $storepaymentSetting['khalti_secret_key'] : '';
        $currency   = isset($storepaymentSetting['currency']) ? $storepaymentSetting['currency'] : 'USD';
        $token      = $payload['token'];
        $amount     = $payload['amount'];
        $khalti     = new Khalti();
        $response   = $khalti->verifyPayment($secret, $token, $amount);

        if ($response['status_code'] == '200') {
            foreach ($products as $key => $product) {
                if ($product['variant_id'] == 0) {
                    $new_qty = $product['originalquantity'] - $product['quantity'];
                    $product_edit = Product::find($product['product_id']);
                    $product_edit->quantity = $new_qty;
                    $product_edit->save();

                    $tax_price = 0;
                    if (!empty($product['tax'])) {
                        foreach ($product['tax'] as $key => $taxs) {
                            $tax_price += $product['price'] * $product['quantity'] * $taxs['tax'] / 100;

                        }
                    }
                    $totalprice += $product['price'] * $product['quantity'];
                    $product_name[] = $product['product_name'];
                    $product_id[] = $product['id'];
                } elseif ($product['variant_id'] != 0) {
                    $new_qty = $product['originalvariantquantity'] - $product['quantity'];
                    $product_variant = ProductVariantOption::find($product['variant_id']);
                    $product_variant->quantity = $new_qty;
                    $product_variant->save();

                    $tax_price = 0;
                    if (!empty($product['tax'])) {
                        foreach ($product['tax'] as $key => $taxs) {
                            $tax_price += $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;

                        }
                    }
                    $totalprice += $product['variant_price'] * $product['quantity'];
                    $product_name[] = $product['product_name'];
                    $product_id[] = $product['id'];
                }
            }

            $price = $totalprice + $tax_price;
            if (isset($cart['coupon'])) {
                if ($cart['coupon']['coupon']['enable_flat'] == 'off') {
                    $discount_value = ($price / 100) * $cart['coupon']['coupon']['discount'];
                    $price = $price - $discount_value;
                } else {
                    $discount_value = $cart['coupon']['coupon']['flat_discount'];
                    $price = $price - $discount_value;
                }
            }

            if (isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping'])) {
                $shipping = Shipping::find($cart['shipping']['shipping_id']);
                if (!empty($shipping)) {
                    // $price = $price + $shipping->price;
                    $shipping_name = $shipping->name;
                    $shipping_price = $shipping->price;
                    $shipping_data = json_encode(
                        [
                            'shipping_name' => $shipping_name,
                            'shipping_price' => $shipping_price,
                            'location_id' => $cart['shipping']['location_id'],
                        ]
                    );
                }
            } else {
                $shipping_data = '';
            }

            if ($product) {
                if (Utility::CustomerAuthCheck($store->slug)) {
                    $customer = Auth::guard('customers')->user()->id;
                } else {
                    $customer = 0;
                }
                $customer = Auth::guard('customers')->user();
                $order = new Order();
                $order->order_id = $order_id;
                $order->name            = isset($cust_details['name']) ? $cust_details['name'] : '' ;
                $order->email           = isset($cust_details['email']) ? $cust_details['email'] : '' ;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->status = 'pending';
                $order->user_address_id =  !empty($cust_details['id']) ? $cust_details['id'] : '';
                $order->shipping_data = !empty($shipping_data) ? $shipping_data : '';
                $order->coupon = $request->coupon_id;
                $order->coupon_json = json_encode($coupon);
                $order->discount_price = $request->dicount_price;
                $order->product_id = implode(',', $product_id);
                $order->price = $price;
                $order->product = json_encode($products);
                $order->price_currency = $store->currency_code;
                $order->txn_id = '';
                $order->payment_type = 'Khalti';
                $order->payment_status = 'approved';
                $order->receipt = '';
                $order->user_id = $store['id'];
                $order->customer_id = isset($customer->id) ? $customer->id : '';
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


                if ((!empty(Auth::guard('customers')->user()) && $store->is_checkout_login_required == 'on')) {
                    foreach ($products as $product_id) {
                        $purchased_products = new PurchasedProducts();
                        $purchased_products->product_id = $product_id['product_id'];
                        $purchased_products->customer_id = $customer->id;
                        $purchased_products->order_id = $order->id;
                        $purchased_products->save();
                    }
                }

                $msg = response()->json(
                    [
                        'status' => 'success',
                        'success' => __('Your Order Successfully Added'),
                        'order_id' => Crypt::encrypt($order->id),
                    ]
                );

                if (!empty(session()->get($slug)['wishlist'])) {

                    $wishlist = session()->get($slug)['wishlist'];
                    session()->forget($slug);
                    $session_wishlist['wishlist'] = $wishlist;
                    session()->put($slug, $session_wishlist);
                }


                $order_email = $order->email;
                $owner = User::find($store->created_by);

                $owner_email = $owner->email;
                $order_id = Crypt::encrypt($order->id);
                // if (isset($store->mail_driver) && !empty($store->mail_driver)) {
                    $dArr = [
                        'order_name' => $order->name,
                        'order_status' => $order->status,
                    ];
                    $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);
                    $resp1 = Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);
                // }
                if (isset($store->is_twilio_enabled) && $store->is_twilio_enabled == "on") {
                    Utility::order_create_owner($order, $owner, $store);
                    Utility::order_create_customer($order, $customer, $store);
                }
                return $msg;
            } else {
                return response()->json(
                    [
                        'status' => 'error',
                        'success' => __('Failed'),
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'success' => __('No reponse returned!'),
                ]
            );
        }
    }

}
