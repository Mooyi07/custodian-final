<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QR Code with Canvas Padding</title>
  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>
<body>
  <input
    type="text"
    id="input"
    value="<?php echo isset($_GET['qrText']) ? $_GET['qrText'] : '' ?>"
    onkeyup="createQR(this.value)"
    placeholder="Enter text to encode"
  />
  <h1>QR Code with Padding</h1>
  <canvas id="canvas"></canvas>

  <script>
    const padding = 20; // pixels

    function createQR(data) {
      if (!data) return;

      // Create a temporary canvas
      const tempCanvas = document.createElement('canvas');

      QRCode.toCanvas(tempCanvas, data, { margin: 0 }, function (error) {
        if (error) return console.error(error);

        const originalSize = tempCanvas.width;
        const newSize = originalSize + padding * 2;

        const finalCanvas = document.getElementById('canvas');
        finalCanvas.width = newSize;
        finalCanvas.height = newSize;

        const ctx = finalCanvas.getContext('2d');

        // Optional background color
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, newSize, newSize);

        // Draw QR in center with padding
        ctx.drawImage(tempCanvas, padding, padding);

        console.log('QR code with padding generated!');
      });
    }

    // Generate QR on page load if input has value
    window.onload = function () {
      const initialValue = document.getElementById('input').value;
      if (initialValue) {
        createQR(initialValue);
      }
    };
  </script>
</body>
</html>
