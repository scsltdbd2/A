<?php
function generateId(string $prefix): string
{
    $config = require __DIR__ . '/../../config/app.php';
    $timestamp = date('YmdHis');
    $random = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    return $prefix . '_' . $timestamp . $random;
}

function generatePersonId(): string
{
    return generateId('person');
}

function generateAccountId(): string
{
    return generateId('account');
}

function generateTransactionId(): string
{
    return generateId('transaction');
}

function generateAssetId(): string
{
    return generateId('asset');
}

function generateAssetShareId(): string
{
    return generateId('share');
}

function generateInstallmentId(): string
{
    return generateId('installment');
}

function generateSpecialInstallmentId(): string
{
    return generateId('special');
}

function generateMaterialId(): string
{
    return generateId('material');
}

function generateLabourId(): string
{
    return generateId('labour');
}

function generateTransportId(): string
{
    return generateId('transport');
}

function generateUserId(): string
{
    return generateId('user');
}

function generateAIPendingActionId(): string
{
    return generateId('ai_action');
}