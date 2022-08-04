<?php

declare(strict_types = 1);

// Your Code

function getTransactionFiles(string $dirPath){
    $files = [];
    foreach (scandir($dirPath) as $file){
         if(is_dir($file)){
             continue;
         }

         $files[] = $dirPath . $file;
    }
    return $files;
}

function getTransactions(string $fileName , ?callable $trasnactionHandler = null) : array
{
    if(! file_exists($fileName) ){
        trigger_error("file $fileName is not exist" , E_USER_ERROR);
    }

    $file = fopen($fileName , 'r');

    fgetcsv($file);

    $trasnactions = [];

    while ( ($trasnaction = fgetcsv($file) ) !== false){

        $trasnactions[] = $trasnactionHandler($trasnaction);
    }
    return $trasnactions;
}

function extractTransactions(array $trasnactionsRow) : array
{
    [$date , $checkNumber , $description ,$amount] = $trasnactionsRow;
    $amount = (float) str_replace(['$', ','] , '' , $amount);
    return [
        'date'          => $date,
        'checkNumber'   => $checkNumber,
        'description'   => $description,
        'amount'        => $amount
    ];
}

function calculateTotals(array $transactions): array
{
    $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

    foreach ($transactions as $transaction) {
        $totals['netTotal'] += $transaction['amount'];

        if ($transaction['amount'] >= 0) {
            $totals['totalIncome'] += $transaction['amount'];
        } else {
            $totals['totalExpense'] += $transaction['amount'];
        }
    }

    return $totals;
}