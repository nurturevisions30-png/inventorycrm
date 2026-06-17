<?php
session_start();

include "config.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ================= LIBRARIES ================= */

require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* ================= GET ID ================= */

if(!isset($_GET['id'])){
    die("Invalid Request");
}

$id = (int)$_GET['id'];

/* ================= FETCH QUOTATION ================= */

$q = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM quotations WHERE id=$id"
));

if(!$q){
    die("Quotation not found");
}

$email = $q['client_email'];

if(empty($email)){
    die("Client email not found");
}

/* ================= SEND EMAIL ================= */

if(isset($_POST['send_email'])){

    /* PDF HTML */
    ob_start();

    include "pdf_template.php";

    $html = ob_get_clean();

    /* DOMPDF */
    $dompdf = new Dompdf([
        "isRemoteEnabled" => true
    ]);

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    $pdf_output = $dompdf->output();

    /* MAIL */
    $mail = new PHPMailer(true);

    try{

        $mail->isSMTP();

        /* SMTP */
        $mail->Host = 'smtp.hostinger.com';

        $mail->SMTPAuth = true;

        $mail->Username = 'quotation@asasafety.in';

        $mail->Password = 'Quotation@asa1';

        $mail->SMTPSecure = 'tls';

        $mail->Port = 587;

        /* FROM */
        $mail->setFrom(
            'quotation@asasafety.in',
            'AS Associates'
        );

        /* TO */
        $mail->addAddress($email);

        /* BCC */
        $mail->addBCC('info.asassociates@yahoo.com');

        /* PDF ATTACHMENT */
        $mail->addStringAttachment(
            $pdf_output,
            'quotation.pdf'
        );

        $mail->isHTML(true);

        $mail->Subject =
        "Quotation - ".$q['quotation_no'];

        $mail->Body = "
        Dear ".$q['client_name'].",<br><br>

        Please find attached your quotation PDF.<br><br>

        Regards,<br>
        AS Associates
        ";

        $mail->send();

        echo "
        <script>
        alert('Email Sent Successfully');
        window.location='preview_draft.php?id=$id';
        </script>
        ";

    }catch(Exception $e){

        echo "
        MAIL ERROR:<br><br>
        ".$mail->ErrorInfo;

    }
}
?>