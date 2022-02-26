<?php

declare(strict_types=1);

namespace Bank\CommissionTask\Utilities;

use GuzzleHttp\Client;

class Converter
{
    private static $VALID_CURRENCIES = [
        'USD',
        'JPY',
    ];

    private static $API_URI = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';

    /**
     * @var array
     */
    private $rates;

    public function __construct()
    {
        $this->setRates();
    }

    /**
     *  @param float amount
     *  @param string currency
     *  @param bool sell
     *
     *  @return float $convertedAmount
     */
    public function convert(float $amount, string $currency, bool $sell = true): float
    {
        if (!in_array($currency, self::$VALID_CURRENCIES, true)) {
            throw new \Exception('Invalid currrency.');
        }
        $rate = $this->rates[$currency];

        return $sell ? (float) ($amount / $rate) : (float) ($amount * $rate);
    }

    private function setRates(): void
    {
        $client = new Client(['timeout' => 10]);
        $response = $client->get(self::$API_URI);
        $body = $response->getBody()->getContents();
        $this->rates = json_decode($body, true)['rates'];
    }
}
