<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= e($pageTitle ?? 'Online Computer Shop') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Separated external CSS -->
    <?php
    $appBase = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if ($appBase === '/' || $appBase === '\\') {
        $appBase = '';
    }
?>
    <link rel="stylesheet" href="<?= e($appBase) ?>/public/css/style.css?v=2">
</head>
<body class="app-shell">
