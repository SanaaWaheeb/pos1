<?php

namespace alalm3i\EdfaPay;

use alalm3i\EdfaPay\Enum\CardType;
use Illuminate\Support\Facades\Log;

abstract class EdfaPayNotifications
{
    public array $responseArray = [];

    public CardType $method;

    /*
     *  $response : is the request content - $requests->getContent()
     */
    public function __construct($response)
    {
        $this->responseArray = $this->toArray($response);
        $this->guessPaymentMethod();
    }

    public function guessPaymentMethod(): void
    {
        if (isset($this->responseArray['order_id'])) {
            $this->method = CardType::CARD;
        } elseif (isset($this->responseArray['order_number'])) {
            $this->method = CardType::APPLEPAY;
        } else {
            Log::error('unknown payment type: ');
        }
    }

    public function isCard(): bool
    {
        return $this->method == CardType::CARD;
    }
    //    public function isApplePay(): bool
    //    {
    //        return $this->method == CardType::APPLEPAY;
    //    }

    public function isSale(): bool
    {
        if ($this->isCard()) {
            return $this->responseArray['action'] == 'SALE';
        } else {
            return $this->responseArray['type'] == 'sale';
        }
    }

    public function isSuccess(): bool
    {
        if ($this->isCard()) {
            return $this->responseArray['result'] == 'SUCCESS';
        } else {
            return $this->responseArray['status'] == 'success';
        }
    }

    public function isDeclined(): bool
    {
        return $this->responseArray['result'] == 'DECLINED';
    }

    public function isRedirect(): bool
    {
        return $this->responseArray['result'] == 'REDIRECT';
    }

    public function getStatus(): string
    {
        if ($this->isCard()) {
            return $this->responseArray['status'];
        } else {
            return $this->responseArray['order_status'];
        }
    }

    public function getOrderId(): string
    {
        if ($this->isCard()) {
            return $this->responseArray['order_id'];
        } else {
            return $this->responseArray['order_number'];
        }
    }

    public function getTransactionId(): string
    {
        if ($this->isCard()) {
            return $this->responseArray['trans_id'];
        } else {
            return $this->responseArray['id'];
        }
    }

    public function getTransactinoDate(): string
    {
        return $this->responseArray['trans_date'];
    }

    public function getAmount(): string
    {
        if ($this->isCard()) {
            return $this->responseArray['amount'];
        } else {
            return $this->responseArray['order_amount'];
        }
    }

    public function getType(): string
    {
        if ($this->isCard()) {
            return $this->responseArray['action'];
        } else {
            return $this->responseArray['type'];
        }
    }

    public function getJsonResponse(): string
    {
        return json_encode($this->responseArray);
    }

    private function toArray($response): array
    {
        $result = [];
        $items = explode('&', $response);

        foreach ($items as $item) {
            $single = explode('=', $item);
            $result[$single[0]] = trim(urldecode($single[1]));
        }

        return $result;
    }
}
