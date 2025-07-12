<?php
// A simple function to check the active page and apply a style
function active_class($page_name) {
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page == $page_name) {
        return 'bg-gray-900 text-white';
    }
    return 'text-gray-300 hover:bg-gray-700 hover:text-white';
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch POS System</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN for simple interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="h-full">
<div class="min-h-full">
    <nav class="bg-gray-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <!-- You can use an SVG icon or image here -->
                        <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <!-- Navigation links -->
                            <a href="index.php" class="<?= active_class('index.php') ?> rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Sales</a>
                            <a href="products.php" class="<?= active_class('products.php') ?> rounded-md px-3 py-2 text-sm font-medium">Products</a>
                            <a href="repairs.php" class="<?= active_class('repairs.php') ?> rounded-md px-3 py-2 text-sm font-medium">Repairs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                <?php
                // Dynamically set the header title based on the page
                $page_titles = [
                    'index.php' => 'Point of Sale',
                    'products.php' => 'Product Management',
                    'repairs.php' => 'Repair Management'
                ];
                echo $page_titles[basename($_SERVER['PHP_SELF'])] ?? 'Dashboard';
                ?>
            </h1>
        </div>
    </header>
    <main>
        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            <!-- Page content will be inserted here -->
