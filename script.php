<?php
require 'vendor/autoload.php';

use Bank\CommissionTask\Models\Operation;
use Bank\CommissionTask\Models\User;
use Bank\CommissionTask\Service\Bank;
use Bank\CommissionTask\Utilities\CSV;
$fileName = isset($argc) && $argc >1 ? $argv[1] : 'testData';
$inputFile = "inputs/$fileName.csv";

$csv = new CSV($inputFile);
$operations = $csv->toArray();
$bank = new Bank();

foreach ($operations as  $data) {
    $fee = $bank->calculateFee(
        new Operation(($data['date']), $data['operationType'], $data['amount'], $data['currency']),
        new User(($data['userId']), $data['userType'])
    );
    echo $fee , PHP_EOL;
}