<?php

declare(strict_types=1);

namespace Bank\CommissionTask\Models;

use Carbon\Carbon;

class User
{
    private static $VALID_TYPES = [
        'private',
        'business',
    ];

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    public $id;

    /**
     * @var float
     */
    public $withdrawnAmounInWeek;

    /**
     * @var int
     */
    public $totalwithdrawsInWeek;

    /**
     * @var Carbon
     */
    public $lastWithdrawDate;

    public function __construct(int $id, string $type)
    {
        if (!in_array($type, self::$VALID_TYPES, true)) {
            throw new \Exception('Invalid user type.');
        }
        $this->id = $id;
        $this->type = $type;
        $this->withdrawnAmounInWeek = 0;
        $this->totalwithdrawsInWeek = 0;
    }

    public function isBusiness(): bool
    {
        return $this->type === 'business';
    }

    /**
     * @param float $amount in EUR
     */
    public function updateWithdrawInfo(float $amount, Carbon $date): void
    {
        if (!$this->lastWithdrawDate || !$this->isSameWeek($date, $this->lastWithdrawDate)) {
            $this->withdrawnAmounInWeek = $amount;
            $this->totalwithdrawsInWeek = 1;
        } else {
            $this->withdrawnAmounInWeek += $amount;
            ++$this->totalwithdrawsInWeek;
        }
        $this->lastWithdrawDate = $date;
    }

    /**
     * check if witdraw is in same week as previus.
     *
     * @param Carbon curDate
     */
    private function isSameWeek(Carbon $curDate, Carbon $prevDate): bool
    {
        $startOfWeek = $prevDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $prevDate->copy()->endOfWeek(Carbon::SUNDAY);

        return $curDate >= $startOfWeek && $curDate <= $endOfWeek;
    }
}
