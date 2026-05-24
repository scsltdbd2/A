<?php
function formatCurrency(?float $amount): string
{
    if ($amount === null || $amount === '') {
        return '';
    }
    $config = require __DIR__ . '/../../config/app.php';
    return $config['currency_symbol'] . number_format((float)$amount, 2);
}

function formatCurrencyInput(?float $amount): string
{
    if ($amount === null || $amount === '') {
        return '';
    }
    return number_format((float)$amount, 2, '.', '');
}

function parseCurrency(?string $value): ?float
{
    if ($value === null || $value === '') {
        return null;
    }
    $value = str_replace([',', ' ', '৳', '$'], '', $value);
    return (float)$value;
}