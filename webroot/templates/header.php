<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>12 Salopards - Urban Terror Server Manager</title>
    <style type="text/css">*{ margin: 0; padding: 0;}</style>
    <link rel="icon" href="/favicon_16.png" sizes="16x16">
    <link rel="icon" href="/favicon_32.png" sizes="32x32">
    <link rel="icon" href="/favicon_48.png" sizes="48x48">
    <link rel="icon" href="/favicon_64.png" sizes="64x64">
    <link rel="icon" href="/favicon_72.png" sizes="72x72">
    <link rel="icon" href="/favicon_150.png" sizes="150x150">
    <link rel="icon" href="/favicon_160.png" sizes="160x160">
    <link rel="icon" href="/favicon_180.png" sizes="180x180">
    <link rel="icon" href="/favicon_192.png" sizes="192x192">
    <link rel="apple-touch-icon" href="/favicon_180.png" />
    <link rel="stylesheet" type="text/css" href="/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <?php
    if(isset($_SESSION['darkmode'])){
        if($_SESSION['darkmode'] == 1){
            $btnmode = "btn-dark";
            $btnoutline = "btn-outline-light";
            ?><link rel="stylesheet" type="text/css" href="/css/dark.css"><?php
        }else{
            $btnmode = "btn-light";
            $btnoutline = "btn-outline-dark";
        }
    }
    ?>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript" src="/js/vendors.min.js"></script>
    <script type="text/javascript" src="/js/urt.js"></script>
</head>
<body>
<nav class="navbar sticky-top navbar-dark navbar-expand-lg bg-dark">
    <a class="navbar-brand" href="#">URT Admin</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbar">
        <ul class="navbar-nav mr-auto"></ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link darkmode" href="#" data-dark="<?=($_SESSION['darkmode'] == 1)?"0":"1";?>"><i class="<?= ($_SESSION['darkmode'] == 1)?"far fa-sun":"fas fa-moon";?>"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $app->Ctrl->Maps->getConfigKey("urlstats");?>"><i class="fas fa-chart-line"></i> Stats</a>
            <li class="nav-item">
                <a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">