<div class="context">
  <ul>
  </ul>
</div>
<div id="toast-container" class="toast_container">
  <div class="loading"><div class="loader"></div><span class="text"><?php _tran($trans->Something_is_loading);?>...</span></div>
</div>
<div id="js_uploader" class="drager"></div>
<div class="uploader_queue">
  <div class="overlay"></div>
  <div class="modal_upload_container">
    <div class="container">
      <div class="header">
        <h1 class="title">Select files</h1>
        <a onclick="$(this).parents('.uploader_queue').hide();" class="close"><i class="fa fa-close"></i></a>
      </div>
      <div class="uploading_queue">
        <div onclick="Buckty.uploadSelect();" class="drag"><i class="fa fa-upload"></i></div>
      </div>
      <div class="footer">
        <a onclick="Buckty.uploadSelect();" class="button blue"><?= _tran($trans->Select_files);?></a>
        <a onclick="Buckty.UploaderEmpty();" class="button primary"><?= _tran($trans->Clear_All);?></a>
        <a onclick="$(this).parents('.uploader_queue').hide();" class="button primary pull-right"><?= _tran($trans->Close);?></a>
      </div>
    </div>
  </div>
</div>
  <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-ui.min.js"></script>
  <script type="text/javascript" id="main_app_js" src="<?php echo base_url();?>assets/js/app.js"></script>
</body>
<link rel="canonical" href="http://www.20script.ir" />
</html>