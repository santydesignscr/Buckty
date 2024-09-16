<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="<?= $site->site_keywords;?> ">
    <meta name="description" content="<?= $site->site_description;?> ">
    <?php BucktyHead();?>
    <title><?= $site->site_name;?> - <?php echo $type == 'file' ? $item->file_name: $item->folder_name;?></title>
    <link rel="stylesheet" href="<?= $site->site_url;?>assets/css/app.css" type="text/css">
    <!-- Open Graph data -->
    <meta property="og:title" content="<?= $site->site_name;?> - <?php echo $type == 'file' ? $item->file_name: $item->folder_name;?>" />
    <meta property="og:type" content="<?= $type_content;?>" />
    <meta property="og:url" content="<?= $site->site_url;?>/shared/<?php echo $type == 'file' ? 'file': 'folder';?>/<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>" />
    <meta property="og:image" content="<?= $type_image;?>" />
    <meta property="og:description" content="<?= $site->site_description;?>" />
    <script type="text/javascript" src="<?= $site->site_url;?>assets/js/jquery.min.js"></script>
  </head>
<body class="shared_page">

    