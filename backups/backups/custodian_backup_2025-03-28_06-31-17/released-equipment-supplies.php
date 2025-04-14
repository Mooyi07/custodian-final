<?php 

  include '../config/config.php';
  include '../config/conn.php';

  if (!isset($_SESSION['isLoggedIn'])) {
    header('location: C:\xampp\htdocs\custodian\login.php');
  }


?> 

<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
        function searchTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("inventoryTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let tdArray = tr[i].getElementsByTagName("td");
                let found = false;
                for (let j = 0; j < tdArray.length; j++) {
                    if (tdArray[j]) {
                        if (tdArray[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }

        function printReport() {
            window.print();
        }
    </script>
</head>
<body class="bg-gray-200">
    <div class="max-w-5xl mx-auto p-4 bg-gray-100 shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <button class="bg-gray-400 text-white py-2 px-4 rounded-full flex items-center">
                <i class="fas fa-sync-alt mr-2"></i> UPDATE
            </button>
            <div class="relative">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search Box" class="bg-gray-700 text-white py-2 px-4 rounded-full w-64">
                <i class="fas fa-print absolute right-3 top-3 text-red-500 cursor-pointer" onclick="printReport()"></i>
            </div>
        </div>
        <h1 class="text-2xl font-bold mb-4">INVENTORY REPORT</h1>
        <div class="overflow-x-auto">
            <table id="inventoryTable" class="min-w-full bg-white">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="py-2 px-4 border">EQUIPMENT/ITEM</th>
                        <th class="py-2 px-4 border">SERIAL/CODE #</th>
                        <th class="py-2 px-4 border">QTY</th>
                        <th class="py-2 px-4 border">BRAND/MODEL</th>
                        <th class="py-2 px-4 border">DATE PURCHASE</th>
                        <th class="py-2 px-4 border">LOCATION</th>
                        <th class="py-2 px-4 border">USEFUL LIFE</th>
                        <th class="py-2 px-4 border">REMARKS</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Empty table body -->
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>