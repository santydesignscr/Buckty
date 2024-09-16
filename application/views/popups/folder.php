<div id="js_create_folder" class="mini_pop">
  <div class="overlay"></div>
  <div class="modal_container">
    <div class="modal">
      <div class="modal_header">
        <h1 class="modal_title"><?php _tran($trans->Create_Folder); ?></h1>
        <a href="javascript:void(0);" class="close"><i class="fa fa-remove"></i></a>
      </div>
      <div class="modal_content">
          <form id="js_submit_folder" method="post">
            <div class="field full">
                <input type="text" name="folder_name" placeholder="<?php _tran($trans->Folder_name) ?>" autofocus/>
            </div>
            <div class="buttons_container margin">
                 <?php secureForm();?>
                <a class="primary button" onclick="Buckty.popup('js_create_folder','c');"><?php _tran($trans->Cancel) ?></a>
                <button class="button blue" type="submit"><?php _tran($trans->Create) ?></button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>