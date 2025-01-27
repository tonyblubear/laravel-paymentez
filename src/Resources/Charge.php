<?php

namespace TonyStore\LaravelPaymentez\Resources;

use Illuminate\Http\Client\RequestException;
use TonyStore\LaravelPaymentez\Response\Response;
use TonyStore\LaravelPaymentez\Resources\Resource;
use TonyStore\LaravelPaymentez\Exceptions\ResponseException;
use TonyStore\LaravelPaymentez\Exceptions\PaymentezErrorException;


class Charge extends Resource
{
    const CREATE_ENDPOINT = 'create';
    const AUTHORIZE_ENDPOINT = 'authorize';
    const CAPTURE_ENDPOINT = 'capture';
    const VERIFY_ENDPOINT = 'verify';
    const REFUND_ENDPOINT = 'refund';

    const ENDPOINTS = [
        self::CREATE_ENDPOINT => "transaction/debit/",
        self::AUTHORIZE_ENDPOINT => "transaction/authorize/",
        self::CAPTURE_ENDPOINT => "transaction/capture/",
        self::VERIFY_ENDPOINT => "transaction/verify",
        self::REFUND_ENDPOINT => "transaction/refund/"
    ];

    /**
     * @param string $token
     * @param array $order
     * @param array $user
     * @return Response
     * @throws PaymentezErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws  \Illuminate\Http\Client\RequestException
     */
    public function create(string $token, array $order, array $user): Response
    {
        $card = [
            'token' => $token
        ];

        $this->getRequestor()->validateRequestParams([
            'dev_reference' => 'string',
            'amount' => 'numeric',
            'description' => 'string',
            'vat' => 'numeric'
        ], $order);

        $this->getRequestor()->validateRequestParams([
            'id' => 'string',
            'email' => 'string'
        ], $user);

        try {
            $response = $this->getRequestor()->post(self::ENDPOINTS[self::CREATE_ENDPOINT], [
                'user' => $user,
                'order' => $order,
                'card' => $card
            ]);
        } catch (RequestException $requestException) {
            ResponseException::launch($requestException);
        }

        if ($response->ok()) {
            $this->setData($response);
            return $this->getData();
        }

        throw new PaymentezErrorException("Error on create charge.");
    }

    /**
     * @param string $token
     * @param array $order
     * @param array $user
     * @return Response
     * @throws PaymentezErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws  \Illuminate\Http\Client\RequestException
     */
    public function authorize(
        string $token,
        array $order,
        array $user
    ): Response {
        $card = [
            'token' => $token
        ];

        $this->getRequestor()->validateRequestParams([
            'dev_reference' => 'string',
            'amount' => 'numeric',
            'description' => 'string',
            'vat' => 'numeric'
        ], $order);

        $this->getRequestor()->validateRequestParams([
            'id' => 'string',
            'email' => 'string'
        ], $user);

        try {
            $response = $this->getRequestor()->post(self::ENDPOINTS[self::AUTHORIZE_ENDPOINT], [
                'user' => $user,
                'order' => $order,
                'card' => $card
            ]);
        } catch (RequestException $requestException) {
            ResponseException::launch($requestException);
        }

        if ($response->ok()) {
            $this->setData($response);
            return $this->getData();
        }

        throw new PaymentezErrorException("Error on create charge authorization.");
    }

    /**
     * @param string $transactionId
     * @param float|null $amount
     * @return Response
     * @throws PaymentezErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws  \Illuminate\Http\Client\RequestException
     */
    public function capture(string $transactionId, float $amount = null): Response
    {
        $transaction = [
            'id' => (empty($transactionId) ? null : $transactionId)
        ];

        $this->getRequestor()->validateRequestParams([
            'id' => 'string'
        ], $transaction);

        if (!is_null($amount)) {
            $order = [
                'amount' => $amount
            ];

            $this->getRequestor()->validateRequestParams([
                'amount' => 'numeric'
            ], $order);
        }

        try {
            $request = [
                'transaction' => $transaction
            ];

            if (isset($order)) {
                $request['order'] = $order;
            }

            $response = $this->getRequestor()->post(self::ENDPOINTS[self::CAPTURE_ENDPOINT], $request);
        } catch (RequestException $requestException) {
            ResponseException::launch($requestException);
        }

        if ($response->ok()) {
            $this->setData($response);
            return $this->getData();
        }

        throw new PaymentezErrorException("Error on capture charge.");
    }

    /**
     * @param string $type
     * @param string $value
     * @param string $transactionId
     * @param array $user
     * @return Response
     * @throws PaymentezErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws  \Illuminate\Http\Client\RequestException
     */
    public function verify(
        string $type,
        string $value,
        string $transactionId,
        array $user,
        bool $more_info = null
    ): Response {
        $transaction = [
            'id' => (empty($transactionId) ? null : $transactionId)
        ];

        $this->getRequestor()->validateRequestParams([
            'id' => 'string'
        ], $transaction);

        $this->getRequestor()->validateRequestParams([
            'id' => 'string'
        ], $user);

        $request = [
            'user' => $user,
            'transaction' => $transaction,
            'type' => $type,
            'value' => strval($value)
        ];

        if ($more_info) {
            $request['more_info'] = $more_info;
        }

        try {
            $response = $this->getRequestor()->post(self::ENDPOINTS[self::VERIFY_ENDPOINT], $request);
        } catch (RequestException $requestException) {
            ResponseException::launch($requestException);
        }

        if ($response->ok()) {
            $this->setData($response);
            return $this->getData();
        }

        throw new PaymentezErrorException("Error on verify charge.");
    }

    /**
     * @param string $transactionId
     * @param float|null $amount
     * @return Response
     * @throws PaymentezErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws  \Illuminate\Http\Client\RequestException
     */
    public function refund(string $transactionId, float $amount = null, bool $more_info = null): Response
    {
        $transaction = [
            'id' => (empty($transactionId) ? null : $transactionId)
        ];

        $this->getRequestor()->validateRequestParams([
            'id' => 'string'
        ], $transaction);

        if (!is_null($amount)) {
            $order = [
                'amount' => $amount
            ];

            $this->getRequestor()->validateRequestParams([
                'amount' => 'numeric'
            ], $order);
        }

        try {
            $request = [
                'transaction' => $transaction,
                'more_info' => $more_info,
            ];

            if (isset($order)) {
                $request['order'] = $order;
            }

            $response = $this->getRequestor()->post(self::ENDPOINTS[self::REFUND_ENDPOINT], $request);
        } catch (RequestException $requestException) {
            ResponseException::launch($requestException);
        }

        if ($response->ok()) {
            $this->setData($response);
            return $this->getData();
        }

        throw new PaymentezErrorException("Error on refund charge.");
    }
}
