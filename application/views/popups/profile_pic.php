<div id="js_profile_pic" class="mini_pop">
  <div class="overlay"></div>
  <div class="modal_container">
    <div class="modal">
      <div class="modal_header">
        <h1 class="modal_title"><?= _tran($trans->Change_avatar);?> </h1>
        <a href="javascript:void(0);" class="close"><i class="fa fa-remove"></i></a>
      </div>
      <div class="modal_content">
            <div class="image_container">
            </div>
            <div class="buttons_container">
                <a class="primary button" onclick="Buckty.popup('js_profile_pic','c');"><?= _tran($trans->Cancel);?></a>
                <input type="file" style="display:none;" onchange="Buckty.profile_u($(this).prop('files')[0]);" name="user_image" id="user_upload_image"/>
                <button class="button blue" onclick="$('#user_upload_image').click();"><?= _tran($trans->Change);?></button>
                <span class="information">Recommended Size: 400x400</span>
            </div>
      </div>
    </div>
  </div>
</div>