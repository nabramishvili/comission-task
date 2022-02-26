<?php

declare(strict_types=1);

namespace Bank\CommissionTask\Models;

use Carbon\Carbon;

class Operation
{
    private static $VALID_OPERATION_TYPES = [
        'deposit',
        'withdraw',
    ];

    private static $VALID_CURRENCIES = [
        'EUR',
        'USD',
        'JPY',
    ];

    /**
     * @var string
     */
    public $currency;

    /**
     * @var Carbon
     */
    public $date;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var string
     */
    private $type;

    public function __construct(Carbon $date, string $type, float $amount, string $currency)
    {
        if (!in_array($type, self::$VALID_OPERATION_TYPES, true)) {
            throw new \Exception('Invalid operation type.');
        }
        if (!in_array($currency, self::$VALID_CURRENCIES, true)) {
            throw new \Exception('Invalid currency');
        }
        $this->date = $date;
        $this->type = $type;
        $this->currency = $currency;
        $this->amount = $amount;
    }

    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }
}
