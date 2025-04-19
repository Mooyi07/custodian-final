<?php 

require_once('../config/config.php');
require_once('../config/conn.php');

if (!isset($_SESSION['isLoggedIn'])) {
    header('location: login.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Qr Scanner</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="ht.js"></script>
<style>
  .result{
    background-color: green;
    color:#fff;
    padding:20px;
  }
  .row{
    display:flex;
  }
</style>
<body>
<div class="d-flex vh-100">
<?php include '../includes/sidebar.php'; ?>
<div class="row">
  <div class="col">
    <div style="width:500px;" id="reader"></div>
  </div>
</div>
</body>
  <div class="col" style="padding:30px;">
    <h4>SCAN RESULT</h4>
     <p>Item Name: <span id="itemName"></span></p>
     <p>Item Code: <span id="itemCode"></span></p>
     <p>Item Brand: <span id="itemBrand"></span></p>
     <p>Date Purchase: <span id="purchase"></span></p>
     <p>Item Location: <span id="location"></span></p>

  </div>
</div>
<script type="text/javascript">
function onScanSuccess(qrCodeMessage) {
    const list = qrCodeMessage.split(';');
    console.log(list);
    document.getElementById('itemName').innerHTML = list[0];
    document.getElementById('itemCode').innerHTML = list[1];
    document.getElementById('itemBrand').innerHTML = list[2];
    document.getElementById('purchase').innerHTML = list[3];
    const formatted = list[4].replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    document.getElementById('location').innerHTML = formatted;
    showHint(qrCodeMessage);
}
function onScanError(errorMessage) {
}
var html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", { fps: 10, qrbox: 250 });
html5QrcodeScanner.render(onScanSuccess, onScanError);

</script>