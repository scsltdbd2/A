<?php
function sanitizeString(?string $value): string
{
    if ($value === null || $value === '') {
        return '';
    }
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function sanitizeEmail(?string $value): string
{
    if ($value === null || $value === '') {
        return '';
    }
    return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
}

function sanitizePhone(?string $value): string
{
    if ($value === null || $value === '') {
        return '';
    }
    $value = preg_replace('/[^0-9]/', '', $value);
    return $value;
}

function sanitizeNumeric(?string $value): ?float
{
    if ($value === null || $value === '') {
        return null;
    }
    $value = str_replace([',', ' ', '৳', '$'], '', $value);
    return is_numeric($value) ? (float)$value : null;
}

function sanitizeInt(?string $value): ?int
{
    if ($value === null || $value === '') {
        return null;
    }
    return is_numeric($value) ? (int)$value : null;
}