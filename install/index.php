<?php
$db_config_path = '../application/config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Buckty - icodeapps</title>
    <link rel="stylesheet" href="assets/css/install.css">
    <script>
        <?php
        $url = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' . $_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'] : 'http://' . $_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI']; ?>
        var site_url = '<?php echo $url ?>';
    </script>
</head>
<body>
<div class="main_container extended">
    <div class="header">
        <a href="https://buckty.com/" class="logo">Buckty</a>
        <h1 class="title">Buckty - installation</h1>
    </div>
    <div id="view_container" class="view_container">
        <div class="message">
            <?php if (!is_writable($db_config_path)): ?>
                <span class="error">Please make the application/config/database.php file writable. </span>
            <?php endif; ?>
        </div>
        <?php if (is_writable($db_config_path)): ?>
            <div id="formDb" class="form_container">
                <?php include('temps/info.php');?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/install.js"></script>
</body>
</html>