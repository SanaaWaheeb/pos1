<?php

namespace App\Http\Controllers;

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
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthorizeNetController extends Controller
{

    public function planPayWithAuthorizeNet(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan      = Plan::find($planID);
        $authuser  = \Auth::user();
        $coupon_id = '';

        if($plan)
        {
            $net          = $plan->price;
            $price        = intval($net);
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
                            'payment_type' => 'Authorizenet',
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
                $data = [
                    'id' =>  $plan->id,
                    'amount' =>  $price,
                    'coupon' =>  $request->coupon,
                ];
                $data  =    json_encode($data);

                return view('AuthorizeNet.request', compact('plan', 'price', 'data', 'currency'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e);
            }
        } else{
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetAuthorizeNetStatus(Request $request)
    {
        $input          = $request->all();
        $admin_settings = Utility::getAdminPaymentSetting();
        $data           = json_decode($input['data'], true);
        $amount         =  $data['amount'];
        $plan           = Plan::find($data['id']);
        $authuser       = Auth::user();
        $orderID        = strtoupper(str_replace('.', '', uniqid('', true)));
        $admin_currancy = isset($admin_settings['currency']) ? $admin_settings['currency'] : 'USD';
        try {
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($admin_settings['authorizenet_merchant_login_id']);
            $merchantAuthentication->setTransactionKey($admin_settings['authorizenet_merchant_transaction_key']);
            $refId                  = 'ref' . time();
            // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($input['cardNumber']);
            $creditCard->setExpirationDate($input['year'] . '-' . $input['month']);
            $creditCard->setCardCode($input['cvv']);

            $paymentOne             = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);
            // Create a TransactionRequestType object and add the previous objects to it
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($amount);
            $transactionRequestType->setPayment($paymentOne);
            // Assemble the complete transaction request
            $requestNet             = new AnetAPI\CreateTransactionRequest();
            $requestNet->setMerchantAuthentication($merchantAuthentication);
            $requestNet->setRefId($refId);
            $requestNet->setTransactionRequest($transactionRequestType);
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', __('something Went wrong!'));
        }
        $controller = new AnetController\CreateTransactionController($requestNet);
        if (!empty($admin_settings['authorizenet_mode']) && $admin_settings['authorizenet_mode'] == 'live') {

            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION); // change SANDBOX to PRODUCTION in live mode

        } else {

            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX); // change SANDBOX to PRODUCTION in live mode
        }

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $order = new PlanOrder();
                    $order->order_id = $orderID;
                    $order->name = $authuser->name;
                    $order->card_number = '';
                    $order->card_exp_month = '';
                    $order->card_exp_year = '';
                    $order->plan_name = $plan->name;
                    $order->plan_id = $plan->id;
                    $order->price = $amount;
                    $order->price_currency = $admin_currancy;
                    $order->txn_id = $orderID;
                    $order->payment_type = 'Authorizenet';
                    $order->payment_status = 'success';
                    $order->receipt = '';
                    $order->user_id = $authuser->id;
                    $order->save();

                    if (isset($data['coupon']) && $data['coupon']) {
                        $coupons = Coupon::where('code', strtoupper($data['coupon']))->where('is_active', '1')->first();
                        if (!empty($coupons)) {
                            $userCoupon = new UserCoupon();
                            $userCoupon->user = $authuser->id;
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
                    $assignPlan         = $authuser->assignPlan($plan->id);

                    Utility::referralTransaction($plan);
                    if ($assignPlan['is_success']) {
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                    } else {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                    if ($tresponse->getErrors() != null) {
                        return redirect()->route('plans.index')->with('error', __('Transaction Failed!'));
                    }
                }
            } else {
                $tresponse      = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return redirect()->route('plans.index')->with('error', __('Transaction Failed!'));
                } else {
                    return redirect()->route('plans.index')->with('error', __('No reponse returned!'));
                }
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('No reponse returned!'));
        }
    }

    public function invoicePayWithAuthorizeNet(Request $request, $slug)
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
            $merchant_login_id = $payment_setting['authorizenet_merchant_login_id'];
            $merchant_transaction_key = $payment_setting['authorizenet_merchant_transaction_key'];

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
                    $get_amount = round($get_amount);
                    $price = round($price);
                    $data = [
                        'authuser'  =>  $authuser,
                        'slug'      => $slug,
                        'get_amount'=> $price,
                        'orderId'   => $orderID,
                        'product_id'=> $product_id,
                    ];

                    $data  =    json_encode($data);

                    return view('AuthorizeNet.invoice', compact('get_amount', 'authuser', 'data', 'currency'));
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

    public function getInvoicePaymentStatus(Request $request)
    {
        $input      = $request->all();
        $data       = json_decode($input['data'], true);
        $amount  =   $data['get_amount'];
        $product_id = $data['product_id'];
        $slug       = $data['slug'];
        $store = Store::where('slug', $slug)->first();
        $user = User::where('id', $store->created_by)->first();

        $storepaymentSetting = Utility::getPaymentSetting($store->id);

        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        try {
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($storepaymentSetting['authorizenet_merchant_login_id']);
            $merchantAuthentication->setTransactionKey($storepaymentSetting['authorizenet_merchant_transaction_key']);
            $refId                  = 'ref' . time();
            // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($input['cardNumber']);
            $creditCard->setExpirationDate($input['year'] . '-' . $input['month']);
            $creditCard->setCardCode($input['cvv']);

            $paymentOne             = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);
            // Create a TransactionRequestType object and add the previous objects to it
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($amount);
            $transactionRequestType->setPayment($paymentOne);
            // Assemble the complete transaction request
            $requestNet             = new AnetAPI\CreateTransactionRequest();
            $requestNet->setMerchantAuthentication($merchantAuthentication);
            $requestNet->setRefId($refId);
            $requestNet->setTransactionRequest($transactionRequestType);


            $controller = new AnetController\CreateTransactionController($requestNet);
            if (!empty($storepaymentSetting['authorizenet_mode']) && $storepaymentSetting['authorizenet_mode'] == 'live') {

                $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION); // change SANDBOX to PRODUCTION in live mode

            } else {

                $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX); // change SANDBOX to PRODUCTION in live mode
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {

                    $cart = session()->get($slug);
                    $products       = $cart['products'];
                    $cust_details = $cart['customer'];
                    if(isset($cart['coupon']['data_id']))
                    {
                        $coupon = ProductCoupon::where('id', $cart['coupon']['data_id'])->first();
                    } else{
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
                    $order->order_id        = $data['orderId'];
                    $order->name            = isset($cust_details['name']) ? $cust_details['name'] : '' ;
                    $order->email           = isset($cust_details['email']) ? $cust_details['email'] : '';
                    $order->card_number     = '';
                    $order->card_exp_month  = '';
                    $order->card_exp_year   = '';
                    $order->status          = 'pending';
                    $order->user_address_id = isset($cust_details['id']) ? $cust_details['id'] : '';
                    $order->shipping_data   = $shipping_data;
                    $order->product_id      = implode(',', $product_id);
                    $order->price           = $amount;
                    $order->coupon          = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
                    $order->coupon_json     = json_encode($coupon);
                    $order->discount_price  = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                    $order->product         = json_encode($products);
                    $order->price_currency  = $store->currency_code;
                    $order->txn_id          = isset($pay_id) ? $pay_id : '';
                    $order->payment_type    = 'Authorizenet';
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
                }
            } else {
                $tresponse      = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return redirect()->route('store.slug',$slug)->with('error', __('Transaction Unsuccesfull'));
                } else {
                    return redirect()->route('store.slug',$slug)->with('error', __('No reponse returned!'));
                }
            }
        } else {
            return redirect()->route('store.slug',$slug)->with('error', __('No reponse returned!'));
        }
    }

}
