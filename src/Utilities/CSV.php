<?php

declare(strict_types=1);

namespace Bank\CommissionTask\Utilities;

use Carbon\Carbon;

class CSV
{
    private $file;

    /**
     *  @param string $file filename
     */
    public function __construct(string $file)
    {
        $this->file = fopen($file, 'r');
    }

    /**
     *  @param float amount
     *  @param string currency
     *  @param bool sell
     *
     *  @return array $convertedAmount
     */
    public function toArray(): array
    {
        $data = [];
        while (($line = fgetcsv($this->file)) !== false) {
            $data[] = [
                'date' => Carbon::parse($line[0]),
                'userId' => (int) $line[1],
                'userType' => (string) $line[2],
                'operationType' => (string) $line[3],
                'amount' => (float) $line[4],
                'currency' => (string) $line[5],
            ];
        }
        fclose($this->file);

        return $data;
    }
}
