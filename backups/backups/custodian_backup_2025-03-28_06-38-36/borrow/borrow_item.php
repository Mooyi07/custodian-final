<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-200">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex-1 p-4">
            <h1 class="text-2xl font-bold mb-4">Borrowed Items</h1>
            <div class="flex">
                <div class="w-1/2">
                    <div class="mb-4">
                        <label class="block text-gray-700">Item Name:</label>
                        <input type="text" class="w-full p-2 border border-gray-300 rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Borrower Name:</label>
                        <input type="text" class="w-full p-2 border border-gray-300 rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Borrow Date:</label>
                        <div class="relative">
                            <input type="text" class="w-full p-2 border border-gray-300 rounded" placeholder="mm/dd/yyyy">
                            <i class="fas fa-calendar-alt absolute right-2 top-2 text-gray-500"></i>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Return Date:</label>
                        <div class="relative">
                            <input type="text" class="w-full p-2 border border-gray-300 rounded" placeholder="mm/dd/yyyy">
                            <i class="fas fa-calendar-alt absolute right-2 top-2 text-gray-500"></i>
                        </div>
                    </div>
                    <button class="bg-blue-500 text-white w-full py-2 rounded">Add Borrowed Item</button>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>