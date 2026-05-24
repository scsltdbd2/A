<?php
function emptyToNull($value)
{
    if ($value === '' || $value === null) {
        return null;
    }
    return $value;
}

function nullToEmpty($value): string
{
    if ($value === null || $value === '') {
        return '';
    }
    return $value;
}

function isEmpty($value): bool
{
    return $value === null || $value === '';
}

function isNotEmpty($value): bool
{
    return !isEmpty($value);
}

function emptyToZero($value): float
{
    if ($value === null || $value === '' || $value === 0) {
        return 0.0;
    }
    return (float)$value;
}

function nullIfZero($value)
{
    if ($value === null || $value === '' || $value === 0 || $value === 0.0) {
        return null;
    }
    return $value;
}