<?php
session_start();

require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
    die("No data for PDF");
}

$data = $_SESSION['draft_data'] ?? [];

ob_start();
?>

<style>
body { font-family: Arial; font-size:12px; }

h2 { margin:0; }

table {
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th, td {
    border:1px solid #000;
    padding:8px;
}
</style>

<h2>AS ASSOCIATES</h2>
<hr>

<b>Client:</b> <?php echo $data['client_name'] ?? ''; ?><br>
<b>Company:</b> <?php echo $data['client_company'] ?? ''; ?><br><br>

<table>
<tr>
<th>Product</th>
<th>Model</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
</tr>

<?php 
$total = 0;
foreach($_SESSION['cart'] as $item){
$total += $item['total'];
?>

<tr>
<td><?php echo $item['name']; ?></td>
<td><?php echo $item['model']; ?></td>
<td><?php echo $item['qty']; ?></td>
<td>₹<?php echo $item['price']; ?></td>
<td>₹<?php echo $item['total']; ?></td>
</tr>

<?php } ?>

<tr>
<td colspan="4"><b>Total</b></td>
<td><b>₹<?php echo $total; ?></b></td>
</tr>

</table>

<?php
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("quotation.pdf", ["Attachment"=>1]);
?>