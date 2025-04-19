<?php

namespace App\Services\PaymentService;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

class YooMoneyService implements IPayment
{
    public function __construct(protected Client $client){}

    public function sendMoney(int $to, int $amount, string $label): array
    {
        $prepareResponse = $this->requestPayment($to, $amount, $label);

        if ($prepareResponse['status'] === 'success') {
            $processResponse = $this->processPayment($prepareResponse['request_id']);
        }
    }

    protected function requestPayment(int $to, int $amount, string $label): array
    {
        $response = $this->client->request(
            'POST',
            sprintf('%s/%s', config('yoomoney.base_url'), 'request-payment'),
            [
                "header" => [
                    sprintf('Authorization: Bearer %s', config('yoomoney.bearer_token')),
                    "Content-Type: application/x-www-form-urlencoded",
                    "Content-Length: 234",
                ],
                'form_params' => [
                    'pattern_id' => 'p2p',
                    'to' => $to,
                    'identifier_type' => 'phone',
                    'amount_due' => $amount,
                    'label' => $label,
                    'message' => sprintf('Вывод %s', now()->format('Y-m-d')),
                ],
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function processPayment(string $requestId): array
    {
        $response = $this->client->request(
            'POST',
            sprintf('%s/%s', config('yoomoney.base_url'), 'process-payment'),
            [
                "header" => [
                    sprintf('Authorization: Bearer %s', config('yoomoney.bearer_token')),
                    "Content-Type: application/x-www-form-urlencoded",
                    "Content-Length: 234",
                ],
                'form_params' => [
                    'request_id' => $requestId,
                ],
            ]
        );
        return json_decode($response->getBody()->getContents(), true);
    }
}
