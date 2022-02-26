<?php

declare(strict_types=1);

namespace Bank\CommissionTask\Service;

use Bank\CommissionTask\Models\Operation;
use Bank\CommissionTask\Models\User;
use Bank\CommissionTask\Utilities\Converter;

class Bank
{
    /**
     * @var array
     */
    private $users;

    /**
     * @var Converter
     */
    private $converter;

    private static $DEPOSIT_FEE = 0.0003;
    private static $BUSINESS_WITHDRAW_FEE = 0.005;
    private static $PRIVATE_WITHDRAW_FEE = 0.003;
    private static $FREE_WITDRAWS_QUOTA = 3;
    private static $WITDRAW_AMOUNT_QUOTA = 1000;
    private static $DEFAULT_CURRENCY = 'EUR';

    public function __construct()
    {
        $this->users = [];
        $this->converter = new Converter();
    }

    public function calculateFee(Operation $operation, User $user)
    {
        $this->updateUsers($user);
        $user = $this->getCurrentUser($user->id);
        if ($operation->isDeposit()) {
            return $this->calculateDepositFee($operation->amount);
        }

        return $this->calculateWithdrawFee($operation, $user);
    }

    private function calculateDepositFee(float $amount): float
    {
        return $this->roundFee($amount * self::$DEPOSIT_FEE);
    }

    /**
     *  @return float $roundedFee
     */
    private function calculateWithdrawFee(Operation $operation, User $user): float
    {
        return $user->isBusiness() ?
            $this->calculateBusinessWithdrawFee($operation->amount)
            : $this->calculatePrivateWithdrawFee($operation, $user);
    }

    /**
     *  @return float $roundedFee
     */
    private function calculateBusinessWithdrawFee(float $amount): float
    {
        return $this->roundFee($amount * self::$BUSINESS_WITHDRAW_FEE);
    }

    /**
     *  @param Operation $amoperationount
     *
     *  @return float $roundedFee
     */
    private function calculatePrivateWithdrawFee(Operation $operation, User $user): float
    {
        $isDefaultCurrency = $operation->currency === self::$DEFAULT_CURRENCY;
        $convertedAmount = !$isDefaultCurrency ?
            $this->converter->convert($operation->amount, $operation->currency) :
            $operation->amount;
        $user->updateWithdrawInfo($convertedAmount, $operation->date);

        if ($user->totalwithdrawsInWeek > self::$FREE_WITDRAWS_QUOTA) {
            return $this->roundFee($convertedAmount * self::$PRIVATE_WITHDRAW_FEE);
        }

        if ($user->withdrawnAmounInWeek > self::$WITDRAW_AMOUNT_QUOTA) {
            $overAmount = min($user->withdrawnAmounInWeek - self::$WITDRAW_AMOUNT_QUOTA, $convertedAmount);
            $convertedOverAmount = !$isDefaultCurrency ? $this->converter->convert($overAmount, $operation->currency, false) : $overAmount;

            return $this->roundFee($convertedOverAmount * self::$PRIVATE_WITHDRAW_FEE);
        }

        return 0;
    }

    /**
     *  @return float $roundeValue
     */
    private function roundFee(float $fee, int $precision = 2): float
    {
        return ceil($fee * pow(10, $precision)) / pow(10, $precision);
    }

    /**
     *  updates users state in memory.
     *
     *  @param User User
     */
    private function updateUsers(User $user): void
    {
        if (!isset($this->users[$user->id])) {
            $this->users[$user->id] = $user;
        }
    }

    /**
     *  @param User User
     */
    private function getCurrentUser($userId): User
    {
        return $this->users[$userId];
    }
}
