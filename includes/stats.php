<?php

function getLibraryStats(PDO $conn): array
{
    $totalBooks = (int) $conn->query('SELECT COUNT(*) FROM books')->fetchColumn();
    $borrowedBooks = (int) $conn->query("SELECT COUNT(*) FROM transactions WHERE status = 'Borrowed'")->fetchColumn();

    return [
        'total_books' => $totalBooks,
        'borrowed_books' => $borrowedBooks,
        'available_books' => $totalBooks - $borrowedBooks,
        'total_members' => (int) $conn->query('SELECT COUNT(*) FROM borrowers')->fetchColumn(),
    ];
}
