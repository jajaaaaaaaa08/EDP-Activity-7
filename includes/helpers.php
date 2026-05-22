<?php

function formatBookId(int $id): string
{
    return 'BOK-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
}

function formatTransactionId(int $id): string
{
    return 'TXN-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
}

function formatBorrowerId(int $id): string
{
    return 'BRW-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
}

/** Default due date when not stored in the database. */
function resolveDueDate(?string $dueDate, string $borrowDate, int $defaultDays = 7): string
{
    if (!empty($dueDate) && $dueDate !== '0000-00-00') {
        return $dueDate;
    }

    return date('Y-m-d', strtotime($borrowDate . " +{$defaultDays} days"));
}

function formatDisplayDate(?string $date, string $fallbackBorrowDate, int $defaultDays = 7): string
{
    if (!empty($date) && $date !== '0000-00-00') {
        return date('m/d/Y', strtotime($date));
    }

    return date('m/d/Y', strtotime($fallbackBorrowDate . " +{$defaultDays} days"));
}

function redirectTo(string $path): void
{
    header("Location: {$path}");
    exit;
}
