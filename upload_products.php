<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "config.php";

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

if(isset($_POST['upload'])){

    if($_FILES['file']['name']){

        $file = $_FILES['file']['tmp_name'];

        $spreadsheet = IOFactory::load($file);

        $sheet = $spreadsheet->getActiveSheet();

        /* CREATE UPLOAD FOLDER */
        if(!file_exists("upload")){
            mkdir("upload",0777,true);
        }

        /* IMAGE MAP */
        $imageMap = [];

        foreach ($sheet->getDrawingCollection() as $drawing) {

            $coordinates = $drawing->getCoordinates();

            $extension = 'png';

            if ($drawing instanceof Drawing) {

                $extension = pathinfo(
                    $drawing->getPath(),
                    PATHINFO_EXTENSION
                );

                $imageName = time().rand(1000,9999).".".$extension;

                copy(
                    $drawing->getPath(),
                    "upload/".$imageName
                );

            } elseif ($drawing instanceof MemoryDrawing) {

                $imageName = time().rand(1000,9999).".png";

                ob_start();

                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );

                $imageContents = ob_get_contents();

                ob_end_clean();

                file_put_contents(
                    "upload/".$imageName,
                    $imageContents
                );
            }

            $imageMap[$coordinates] = $imageName;
        }

        /* ALL DATA */
        $rows = $sheet->toArray();

        foreach($rows as $index => $row){

            /* SKIP HEADER */
            if($index == 0){
                continue;
            }

/*
A = S.NO
B = PRODUCT CATEGORY
C = MODEL
D = DESCRIPTION
E = IMAGE
F = GST
G = HSN CODE
H = READY QTY
I = MSP
J = SLAB 1
K = SLAB 2
L = SLAB 3
*/

$product_category = mysqli_real_escape_string(
    $conn,
    trim($row[1] ?? '')   // B
);

$model = mysqli_real_escape_string(
    $conn,
    trim($row[2] ?? '')   // C
);

$description = mysqli_real_escape_string(
    $conn,
    trim($row[3] ?? '')   // D
);

$gst = (float)($row[5] ?? 0);      // F

$hsn_code = mysqli_real_escape_string(
    $conn,
    trim($row[6] ?? '')   // G
);

$ready_qty = (int)($row[7] ?? 0);  // H

$msp = (float)($row[8] ?? 0);      // I

$slab1 = (float)($row[9] ?? 0);    // J

$slab2 = (float)($row[10] ?? 0);   // K

$slab3 = (float)($row[11] ?? 0);   // L

            /* IMAGE COLUMN I */
            $excelRow = $index + 1;

            $cell = "E".$excelRow;

            $image = $imageMap[$cell] ?? '';

            if(empty($product_category) || empty($model)){
                continue;
            }

            mysqli_query($conn,"
            INSERT INTO products
            (
                product_category,
                model,
                description,
                hsn_code,
                gst,
                image,
                ready_qty,
                msp,
                slab1,
                slab2,
                slab3
            )

            VALUES
            (
                '$product_category',
                '$model',
                '$description',
                '$hsn_code',
                '$gst',
                '$image',
                '$ready_qty',
                '$msp',
                '$slab1',
                '$slab2',
                '$slab3'
            )
            ");
        }

        echo "
        <script>
        alert('Products Uploaded Successfully');
        window.location='manage_products.php';
        </script>
        ";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Upload Product Excel</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI';
}

body{
    background:#f4f7fb;
}

.page{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

.box{

    width:100%;
    max-width:560px;

    background:white;

    padding:35px;

    border-radius:28px;

    box-shadow:0 15px 40px rgba(0,0,0,0.06);

}

.icon{

    width:85px;
    height:85px;

    margin:auto;

    border-radius:24px;

    background:#eff6ff;

    display:flex;
    justify-content:center;
    align-items:center;

    margin-bottom:20px;
}

.icon i{
    font-size:36px;
    color:#2563eb;
}

h2{
    text-align:center;
    font-size:30px;
    color:#111827;
}

.sub{
    text-align:center;
    margin-top:10px;
    color:#6b7280;
    margin-bottom:30px;
    line-height:1.6;
}

.upload-box{

    border:2px dashed #bfdbfe;

    background:#f8fbff;

    border-radius:22px;

    padding:40px 25px;

    text-align:center;
}

.upload-box i{
    font-size:52px;
    color:#2563eb;
}

.upload-box h3{
    margin-top:18px;
    color:#111827;
}

.upload-box p{
    margin-top:8px;
    color:#6b7280;
    font-size:14px;
}

.file-input{
    margin-top:22px;
}

.file-input input{
    width:100%;
    padding:14px;
    border:1px solid #dbeafe;
    border-radius:14px;
    background:white;
    cursor:pointer;
}

button{

    width:100%;
    height:56px;

    border:none;
    border-radius:18px;

    background:linear-gradient(135deg,#2563eb,#1d4ed8);

    color:white;

    font-size:16px;
    font-weight:600;

    margin-top:25px;

    cursor:pointer;

    transition:.3s;
}

button:hover{
    transform:translateY(-2px);
}

.note{

    margin-top:25px;

    background:#eff6ff;

    border-left:5px solid #2563eb;

    padding:18px;

    border-radius:16px;

    color:#1e3a8a;

    line-height:1.8;

    font-size:14px;
}

.format{

    margin-top:12px;

    background:white;

    border-radius:12px;

    padding:12px;

    overflow:auto;

    font-size:13px;
}

.warning{

    margin-top:15px;

    background:#fef3c7;

    color:#92400e;

    padding:14px;

    border-radius:12px;

    line-height:1.7;

    font-size:13px;
}

@media(max-width:600px){

    .box{
        padding:25px;
    }

    h2{
        font-size:24px;
    }

}

</style>

</head>

<body>

<?php include "sidebaar.php"; ?>

<div class="page">

<div class="box">

    <div class="icon">
        <i class="fa-solid fa-file-excel"></i>
    </div>

    <h2>Upload Product Excel</h2>

    <div class="sub">
        Upload single Excel file with embedded product images & details
    </div>

    <form method="POST" enctype="multipart/form-data">

        <div class="upload-box">

            <i class="fa-solid fa-cloud-arrow-up"></i>

            <h3>Select Excel File</h3>

            <p>Supported format: .xlsx</p>

            <div class="file-input">

                <input
                type="file"
                name="file"
                accept=".xlsx"
                required>

            </div>

        </div>

        <button name="upload">

            <i class="fa-solid fa-upload"></i>

            Upload Excel

        </button>

    </form>

    <div class="note">

        <b>Excel Column Format:</b>

        <div class="format">

A = S.NO<br>
B = PRODUCT CATEGORY<br>
C = MODEL<br>
D = DESCRIPTION<br>
E = IMAGE (Embedded)<br>
F = GST (%)<br>
G = HSN CODE<br>
H = READY QTY<br>
I = MSP<br>
J = SLAB 1<br>
K = SLAB 2<br>
L = SLAB 3

        </div>

        <div class="warning">

            <b>Important:</b><br>

            Excel me images insert karo using:<br>
            Insert → Pictures<br><br>

            Images should be inside column E.

        </div>

    </div>

</div>

</div>

</body>
</html>