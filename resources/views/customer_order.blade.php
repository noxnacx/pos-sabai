<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เมนูอาหาร</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin: 0;">
    <div id="app">
        <customer-menu :table-id="{{ $table_id }}"></customer-menu>
    </div>
</body>
</html>
