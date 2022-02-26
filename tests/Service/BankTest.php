<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Tests\Service;

use Bank\CommissionTask\Models\Operation;
use Bank\CommissionTask\Models\User;
use Bank\CommissionTask\Service\Bank;
use Bank\CommissionTask\Utilities\CSV;
use PHPUnit\Framework\TestCase;

class BankTest extends TestCase
{
    /**
     * @var Bank
     */
    private static $bank;

    public static function setUpBeforeClass(): void
    {   
        self::$bank = new Bank();
        
    }

    /**
     * @param array $data
     * @param string $expectation
     *
     * @dataProvider dataProviderForAddTesting
     */
    public function testAdd(array $data, float $expectation)
    {   
        $this->assertEquals(
            $expectation,
            self::$bank->calculateFee(
                new Operation(($data['date']), $data['operationType'], $data['amount'], $data['currency']),
                new User(($data['userId']), $data['userType'])
            )
        );
    }

    public function dataProviderForAddTesting(): array
    {   
        $lines = (new CSV('inputs/testData.csv'))->toArray();
        $data = [];
        $answers = [0.6, 3, 0, 0.06, 1.5, 0, 0.69, 0.3, 0.3, 3, 0, 0, 8607.4];
        foreach ($lines as $key => $line) {
            $data[] = [$line, $answers[$key]];
        }
        
        return $data;
    }
}
