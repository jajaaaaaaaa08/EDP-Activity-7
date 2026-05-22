<?php

require_once __DIR__ . '/helpers.php';

function fetchReportPreview(PDO $conn, string $reportType): array
{
    switch ($reportType) {
        case 'Borrowed Books Report':
            $query = "SELECT id, borrower_name, book_title, borrow_date, due_date, status
                      FROM transactions
                      WHERE status = 'Borrowed'
                      ORDER BY id DESC";
            $data = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($data as &$row) {
                $row['display_date'] = formatDisplayDate($row['due_date'] ?? null, $row['borrow_date']);
            }
            unset($row);
            return $data;

        case 'Returned Books Report':
            $query = "SELECT id, borrower_name, book_title, borrow_date, return_date, status
                      FROM transactions
                      WHERE status = 'Returned'
                      ORDER BY id DESC";
            $data = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($data as &$row) {
                $row['display_date'] = formatDisplayDate($row['return_date'] ?? null, $row['borrow_date']);
            }
            unset($row);
            return $data;

        default:
            $query = "SELECT b.BookID, b.Title, b.Author, c.CategoryName, b.status
                      FROM books b
                      LEFT JOIN categories c ON b.CategoryID = c.CategoryID
                      ORDER BY b.BookID DESC";
            return $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
}

function buildExportPayload(PDO $conn, string $reportType): array
{
    switch ($reportType) {
        case 'Borrowed Books Report':
            $query = "SELECT id, borrower_name, book_title, borrow_date, due_date
                      FROM transactions WHERE status = 'Borrowed' ORDER BY id DESC";
            $txns = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

            $today = date('Y-m-d');
            $borrowedToday = 0;
            $overdue = 0;
            $rows = [];

            foreach ($txns as $t) {
                if ($t['borrow_date'] === $today) {
                    $borrowedToday++;
                }

                $dueDate = resolveDueDate($t['due_date'] ?? null, $t['borrow_date']);

                if ($dueDate < $today) {
                    $overdue++;
                }

                $rows[] = [
                    formatTransactionId((int) $t['id']),
                    $t['borrower_name'],
                    $t['book_title'],
                    date('m/d/Y', strtotime($t['borrow_date'])),
                    date('m/d/Y', strtotime($dueDate)),
                ];
            }

            return [
                'data' => [
                    'report_type' => 'Borrowed Books Report',
                    'headers' => ['Transaction ID', 'Borrower', 'Book Title', 'Borrow Date', 'Due Date'],
                    'rows' => $rows,
                    'chart_data' => [
                        'Borrowed Today' => $borrowedToday,
                        'Overdue Books' => $overdue,
                    ],
                ],
                'output_file' => 'Borrowed_Report.xlsx',
                'data_file' => 'data_brw_' . uniqid() . '.json',
            ];

        case 'Returned Books Report':
            $query = "SELECT id, borrower_name, book_title, return_date, status
                      FROM transactions WHERE status = 'Returned' ORDER BY id DESC";
            $books = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

            $returnedCount = count($books);
            $unreturnedCount = (int) $conn->query("SELECT COUNT(*) FROM transactions WHERE status = 'Borrowed'")->fetchColumn();

            $rows = [];
            foreach ($books as $b) {
                $rows[] = [
                    formatTransactionId((int) $b['id']),
                    $b['borrower_name'],
                    $b['book_title'],
                    date('m/d/Y', strtotime($b['return_date'])),
                    $b['status'],
                ];
            }

            return [
                'data' => [
                    'report_type' => 'Returned Books Report',
                    'headers' => ['Transaction ID', 'Borrower', 'Book Title', 'Return Date', 'Status'],
                    'rows' => $rows,
                    'chart_data' => [
                        'Returned Books' => $returnedCount,
                        'Unreturned Books' => $unreturnedCount,
                    ],
                ],
                'output_file' => 'Returned_Report.xlsx',
                'data_file' => 'data_ret_' . uniqid() . '.json',
            ];

        default:
            $query = "SELECT b.BookID, b.Title, b.Author, c.CategoryName, b.status
                      FROM books b
                      LEFT JOIN categories c ON b.CategoryID = c.CategoryID";
            $books = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

            $borrowed = (int) $conn->query("SELECT COUNT(*) FROM transactions WHERE status='Borrowed'")->fetchColumn();
            $available = count($books) - $borrowed;

            $rows = [];
            foreach ($books as $b) {
                $rows[] = [
                    formatBookId((int) $b['BookID']),
                    $b['Title'],
                    $b['Author'],
                    $b['CategoryName'] ?? 'General',
                    $b['status'],
                ];
            }

            return [
                'data' => [
                    'report_type' => 'Inventory Report',
                    'headers' => ['Book ID', 'Title', 'Author', 'Category', 'Status'],
                    'rows' => $rows,
                    'chart_data' => [
                        'Available Books' => $available,
                        'Borrowed Books' => $borrowed,
                    ],
                ],
                'output_file' => 'Inventory_Report.xlsx',
                'data_file' => 'data_inv_' . uniqid() . '.json',
            ];
    }
}
