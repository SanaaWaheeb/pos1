<?php

namespace alalm3i\EdfaPay;

class EdfaPay
{
    public static function paymentURL(array $input = []): PaymentUrlGenerator
    {
        return (new PaymentUrlGenerator())
            ->setOrderId($input['order_id'])
            ->setOrderAmount($input['order_amount'])
            ->setOrderDescription($input['order_description'])
            ->setPayerFirstName($input['payer_first_name'])
            ->setPayerLastName($input['payer_last_name'])
            ->setPayerEmail($input['payer_email'])
            ->setPayerMobile($input['payer_mobile'])
            ->setPayerIpAddress($input['payer_ip_address'])
            ->setTermUrl3ds($input['redirect_url']);

    }
}
