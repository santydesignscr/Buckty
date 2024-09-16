<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="<?= $site->site_keywords;?> ">
    <meta name="description" content="<?= $site->site_description;?> ">
    <?php BucktyHead();?>
    <title>
        <?= $site->site_name;?> - <?= $pageTitle;?>
    </title>
    <link rel="stylesheet" href="<?= $site->site_url;?>assets/css/app.css" type="text/css">
</head>
<body class="recover_page">
<div class="main_view">
    <header class="header_top">
        <a data-no-ajax="true" href="<?= $site->site_url;?>" class="logo"><?= $site->site_name;?></a>
    </header>