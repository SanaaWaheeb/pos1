<?php

declare(strict_types=1);

namespace alalm3i\EdfaPay;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class PaymentUrlGenerator
{
    private const API_URL = 'https://api.edfapay.com/payment/initiate';

    public const ACTION = 'SALE';

    public $currency = 'SAR';

    public $merchant_key = '';

    public $merchant_password = '';

    public $order_id = '';

    public $order_amount = '';

    public $order_description = '';

    public $payer_first_name = '';

    private $payer_last_name = '';

    private $payer_email = '';

    private $payer_mobile = '';

    private $payer_ip_address = '';

    private $hash = '';

    private $term_url_3ds = '';

    public array $payload = [];

    public function __construct()
    {
        $this->merchant_key = Config::get('edfapay.merchant_key');
        $this->merchant_password = Config::get('edfapay.merchant_password');
        $this->term_url_3ds = Config::get('edfapay.return_url');
    }

    public function setOrderId(string $order_id)
    {
        $this->order_id = $order_id;

        return $this;

    }

    public function setOrderAmount(string $order_amount)
    {
        $this->order_amount = $order_amount;

        return $this;
    }

    public function setOrderDescription(string $order_description)
    {
        $this->order_description = $order_description;

        return $this;
    }

    public function setPayerFirstName(string $payer_first_name)
    {
        $this->payer_first_name = $payer_first_name;

        return $this;
    }

    public function setPayerLastName(string $payer_last_name)
    {
        $this->payer_last_name = $payer_last_name;

        return $this;
    }

    public function setPayerEmail(string $payer_email)
    {
        $this->payer_email = $payer_email;

        return $this;
    }

    public function setPayerMobile(string $payer_mobile)
    {
        $this->payer_mobile = $payer_mobile;

        return $this;
    }

    public function setPayerIpAddress(string $payer_ip_address)
    {
        $this->payer_ip_address = $payer_ip_address;

        return $this;
    }

    public function setTermUrl3ds(string $term_url_3ds)
    {
        $this->term_url_3ds = $term_url_3ds;

        return $this;
    }

    private function generateHash(): string
    {
        if (! $this->validateHashInput()) {
            return 'Hash input is not correct or not complete.';
        }

        $input = strtoupper(
            $this->order_id.
            $this->order_amount.
            $this->currency.
            $this->order_description.
            $this->merchant_password
        );

        return sha1(md5($input));
    }

    private function setHash(): void
    {
        $this->hash = $this->generateHash();
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    private function validateHashInput(): bool
    {
        if (
            empty($this->order_id) ||
            empty($this->order_amount) ||
            empty($this->currency) ||
            empty($this->order_description) ||
            empty($this->merchant_password)
        ) {
            return false;
        }

        return true;
    }

    private function validatePayload(): void
    {

        // Refactor and add validate rules for each value.
        foreach ($this->payload as $key => $value) {
            if ($value == '') {
                throw new \Exception('Missing payload input: '.$key);
            }
        }
    }

    public function preparePayload()
    {

        $this->setHash();

        $this->payload = [
            'action' => self::ACTION,
            'edfa_merchant_id' => $this->merchant_key,
            'order_id' => $this->order_id,
            'order_amount' => $this->order_amount,
            'order_currency' => $this->currency,
            'order_description' => $this->order_description,
            'payer_first_name' => $this->payer_first_name,
            'payer_last_name' => $this->payer_last_name,
            'payer_email' => $this->payer_email,
            'payer_phone' => $this->payer_mobile,
            'payer_ip' => $this->payer_ip_address,
            'term_url_3ds' => $this->term_url_3ds,
            'hash' => $this->hash,
            'req_token' => 'N',
            'recurring_init' => 'N',
            'payer_country' => 'SA',
            'payer_city' => 'Riyadh',
            'payer_zip' => '12221',
        ];

        $this->validatePayload();
    }

    /**
     * @throws \Exception
     */
    public function generate(): string
    {
        $this->preparePayload();
        $response = Http::asForm()->post(self::API_URL, $this->payload);

        $response->throw();

        return $response->json('redirect_url');
    }
}
