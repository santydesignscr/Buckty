<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" sizes="16x16 32x32 64x64" href="<?php echo $site->site_url;?>assets/img/favicon.ico">
    <link rel="icon" type="image/png" sizes="196x196" href="<?php echo $site->site_url;?>assets/img/favicon-192.png">
    <link rel="icon" type="image/png" sizes="160x160" href="<?php echo $site->site_url;?>assets/img/favicon-160.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo $site->site_url;?>assets/img/favicon-96.png">
    <link rel="icon" type="image/png" sizes="64x64" href="<?php echo $site->site_url;?>assets/img/favicon-64.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $site->site_url;?>assets/img/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $site->site_url;?>assets/img/favicon-16.png">
    <link rel="apple-touch-icon" href="<?php echo $site->site_url;?>assets/img/favicon-57.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $site->site_url;?>assets/img/favicon-114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $site->site_url;?>assets/img/favicon-72.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $site->site_url;?>assets/img/favicon-144.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo $site->site_url;?>assets/img/favicon-60.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $site->site_url;?>assets/img/favicon-120.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $site->site_url;?>assets/img/favicon-76.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $site->site_url;?>assets/img/favicon-152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $site->site_url;?>assets/img/favicon-180.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="<?= $site->site_keywords;?> ">
    <meta name="description" content="<?= $site->site_description;?> ">
    <title>
        <?= $site->site_name;?>
    </title>
    <link rel="stylesheet" href="<?php echo $site->site_url;?>assets/css/app.css" type="text/css">
    <?php BucktyHead();?>
</head>

<body class="index_page">
    <div class="main_container">
        <header class="header_bar">
            <a href="<?php echo $site->site_url.'folders';?>" class="logo_on_bar"></a>
                <?php $this->load->view('extras/searchbox');?>
            <a href="javascript:void(0);" class="show_vert_menu" onclick="Buckty.showMenu($(this));"><i class="fa fa-navicon"></i></a>
            <a href="javascript:void(0);" class="show_uploader" onclick="Buckty.uploaderQueue();"><i class="fa fa-cloud-upload"></i></a>
            <div class="button_container">
                <a href="javascript:void(0);" class="drop_m add_btn" data-drop="add_menu"><i class="fa fa-plus"></i><span><?= _tran($trans->New);?></span></a>
                <div class="drop_down" id="add_menu">
                    <ul>
                        <li><a href="javascript:void(0);" onclick="Buckty.folder_create();" class="drp-li"><i class="fa fa-folder icon_blue"></i><span><?php _tran($trans->Create_Folder);?></span></a></li>
                        <li><a href="javascript:void(0);" onclick="Buckty.uploadSelect();" class="upload_button"><i class="fa fa-upload"></i><span class="button_text"><?php _tran($trans->Upload); ?></span></a></li>
                    </ul>
                </div>
            </div>
                <?php $this->load->view('extras/rightmenu');?>
        </header>
        </div>