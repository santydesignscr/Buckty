<div id="toast-container" class="toast_container">
    <div class="loading"><div class="loader"></div><span class="text">Something is loading...</span></div>
</div>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?= $site->site_url;?>assets/js/app.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var ITEM_LINK = "<?= base_url(uri_string());?>";
            var SHARED_IDENTITY = "<?= $hash;?>";
            Buckty.loadDisqus();
        });
    </script>
</body>
<link rel="canonical" href="http://www.20script.ir" />
</html>