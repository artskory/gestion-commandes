<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Gestion des Commandes'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/style.css">
    <link rel="icon" type="image/png" href="<?php echo $basePath; ?>image/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="<?php echo $basePath; ?>image/favicon.svg">
    <link rel="shortcut icon" href="<?php echo $basePath; ?>image/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $basePath; ?>image/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo $basePath; ?>image/site.webmanifest">
</head>
<body <?php echo $bodyAttr ?? ''; ?>>
