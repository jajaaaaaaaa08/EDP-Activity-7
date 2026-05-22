<?php

require_once __DIR__ . '/helpers.php';

/**
 * Issue a book to a borrower. Preserves books.php behavior (no due_date column).
 */
function issueBook(PDO $conn, int $bookId, int $borrowerId, bool $setDueDate = false): void
{
    $borrowDate = date('Y-m-d');

    $stmtBook = $conn->prepare('SELECT Title FROM books WHERE BookID = ?');
    $stmtBook->execute([$bookId]);
    $book = $stmtBook->fetch(PDO::FETCH_ASSOC);

    $stmtBorrower = $conn->prepare('SELECT fullname FROM borrowers WHERE id = ?');
    $stmtBorrower->execute([$borrowerId]);
    $borrower = $stmtBorrower->fetch(PDO::FETCH_ASSOC);

    if ($setDueDate) {
        $dueDate = date('Y-m-d', strtotime('+7 days'));
        $stmt = $conn->prepare(
            "INSERT INTO transactions (book_id, borrower_id, book_title, borrower_name, borrow_date, due_date, status)
             VALUES (:bid, :brid, :title, :name, :date, :due, 'Borrowed')"
        );
        $stmt->execute([
            'bid' => $bookId,
            'brid' => $borrowerId,
            'title' => $book['Title'],
            'name' => $borrower['fullname'],
            'date' => $borrowDate,
            'due' => $dueDate,
        ]);
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO transactions (book_id, borrower_id, book_title, borrower_name, borrow_date, status)
             VALUES (:bid, :brid, :title, :name, :date, 'Borrowed')"
        );
        $stmt->execute([
            'bid' => $bookId,
            'brid' => $borrowerId,
            'title' => $book['Title'],
            'name' => $borrower['fullname'],
            'date' => $borrowDate,
        ]);
    }

    $update = $conn->prepare("UPDATE books SET status = 'Borrowed', borrower_id = ? WHERE BookID = ?");
    $update->execute([$borrowerId, $bookId]);
}

function returnBook(PDO $conn, int $transactionId): void
{
    $stmt = $conn->prepare('SELECT book_id FROM transactions WHERE id = ?');
    $stmt->execute([$transactionId]);
    $txn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$txn) {
        return;
    }

    $returnDate = date('Y-m-d');
    $bookId = (int) $txn['book_id'];

    $updateTxn = $conn->prepare("UPDATE transactions SET status = 'Returned', return_date = ? WHERE id = ?");
    $updateTxn->execute([$returnDate, $transactionId]);

    $updateBook = $conn->prepare("UPDATE books SET status = 'Available', borrower_id = NULL WHERE BookID = ?");
    $updateBook->execute([$bookId]);
}
