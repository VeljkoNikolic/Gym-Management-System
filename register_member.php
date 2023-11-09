<?php

    require_once 'config.php';
    require_once 'fpdf/fpdf.php';

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $sql = "INSERT INTO members 
                (first_name, last_name, email, phone_number, photo_path, trainer_id, training_plan_id, access_card_pdf_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $photo_path = $_POST['photo_path'];
        $training_plan_id = $_POST['training_plan_id'];
        $access_card_pdf_path = '';
        $trainer_id = 0;

        $run = $conn->prepare($sql);
        $run->bind_param("sssssiis", $first_name, $last_name, $email, $phone_number,$photo_path, $trainer_id, $training_plan_id, $access_card_pdf_path);
        $run->execute();

        $member_id = $conn->insert_id;

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(40, 10, 'Access Card');
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Member ID: ' . $member_id);
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Name: ' . $first_name . " " . $last_name);
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Email: ' . $email);
        $pdf->Ln();

        $filename = 'Access Cards/access_card_' . $member_id . '.pdf';

        $pdf->Output('F', $filename);

        $sql = "UPDATE members SET access_card_pdf_path = '$filename' WHERE member_id = $member_id";
        $conn->query($sql);
        $conn->close();

        $_SESSION['success_message'] = 'Great job, Your gym has a new member!';
        header('location: admin_dashboard.php');
        exit();
    }

?>