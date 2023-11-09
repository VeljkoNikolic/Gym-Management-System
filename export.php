<?php

require_once 'config.php';
require 'vendor/autoload.php'; // Include the PhpSpreadsheet autoloader

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;

if (isset($_GET['what'])) {

    if ($_GET['what'] == 'members') {
        $sql = 'SELECT * FROM members';
        $csv_cols = [
            "member_id",
            "first_name",
            "last_name",
            "email",
            "phone_number",
            "photo_path",
            "trainer_id",
            "training_plan_id",
            "access_card_pdf_path",
            "created_at"
        ];
    } else if ($_GET['what'] == 'trainers') {
        $sql = 'SELECT * FROM trainers';
        $csv_cols = [
            "trainer_id",
            "first_name",
            "last_name",
            "email",
            "phone_number",
            "created_at"
        ];
    } else {
        die();
    }

    $run = $conn->query($sql);
    $results = $run->fetch_all(MYSQLI_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set column widths and center-align content
    $colIndex = 1;
    foreach ($csv_cols as $col) {
        $sheet->getColumnDimensionByColumn($colIndex)->setWidth(20); // Set the width as desired
        $colIndex++;
    }

    // Add headers
    $colIndex = 1;
    foreach ($csv_cols as $col) {
        $sheet->setCellValueByColumnAndRow($colIndex, 1, $col);

        // Apply center alignment and bold font to the header cell
        $headerCell = $sheet->getStyleByColumnAndRow($colIndex, 1);
        $headerCell->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $headerCell->getFont()->setBold(true);

        $colIndex++;
    }

    // Add data
    $rowIndex = 2;
    foreach ($results as $result) {
        $colIndex = 1;
        foreach ($result as $value) {
            $cell = $sheet->getCellByColumnAndRow($colIndex, $rowIndex);
            $cell->setValue($value);

            // Apply center alignment to the cell
            $sheet->getStyle($cell->getCoordinate())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $colIndex++;
        }
        $rowIndex++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = $_GET['what'] . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename=' . $filename);
    $writer->save('php://output');
}
?>
