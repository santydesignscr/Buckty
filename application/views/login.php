<!DOCTYPE html>
<html xmlns:background="http://www.w3.org/1999/xhtml">
<head>
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" sizes="16x16 32x32 64x64" href="assets/img/favicon.ico">
    <link rel="icon" type="image/png" sizes="196x196" href="assets/img/favicon-192.png">
    <link rel="icon" type="image/png" sizes="160x160" href="assets/img/favicon-160.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/img/favicon-96.png">
    <link rel="icon" type="image/png" sizes="64x64" href="assets/img/favicon-64.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16.png">
    <link rel="apple-touch-icon" href="assets/img/favicon-57.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/img/favicon-114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/img/favicon-72.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/img/favicon-144.png">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/img/favicon-60.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/img/favicon-120.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/favicon-76.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/img/favicon-152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicon-180.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="assets/img/favicon-144.png">
    <meta name="msapplication-config" content="assets/img/browserconfig.xml">
    <meta charset="UTF-8">
    <meta name="keywords" content="THESE ARW KEYWORDS">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php BucktyHead(); ?>
    <title><?= $site->site_name; ?></title>
    <link rel="stylesheet" href="assets/css/app.css" type="text/css">
</head>
<body class="login_page" style="background:#fff url('<?= PexelsBakcground()->image; ?>');">
<section class="login_center">
    <div class="left_block left">
        <a href="javascript:void(0);" class="logo"></a>
        <div class="det">
            <h3 class="heading"><?= $site->site_home_tagline; ?></h3>
            <p class="paragraph"><?= $site->site_home_description; ?></p>
        </div>
    </div>
    <?php $this->load->view('extras/login_reg'); ?>

    <?php $this->load->view('extras/recovery'); ?>
</section>
<?php if (PexelsBakcground()->loaded == 1): ?>
    <div class="credits">
        <a href="<?= PexelsBakcground()->url; ?>" data-no-ajax="true" target="_blank" class="author">Photograph by <span
                class="name"><?= PexelsBakcground()->author; ?></span></a>
    </div>
<?php endif; ?>
<?php $this->load->view('extras/pageslinks'); ?>
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/js/app.js"></script>
</body>
</html>
