<div id="js_ApiPopup" class="mini_pop">
    <div class="overlay"></div>
    <div class="modal_container">
        <div class="modal">
            <div class="modal_header">
                <h1 class="modal_title"><?php _tran($trans->Api_Access); ?></h1>
                <a href="javascript:void(0);" class="close"><i class="fa fa-remove"></i></a>
            </div>
            <div class="modal_content">
                <div class="container">
                    <div class="field">
                        <div class="sub_field">
                            <a class="text">
                                <b>Api Key:</b>
                            </a>
                            <a class="text" id="appendKey"><?php echo $api_key == NULL | FALSE ? '<span class="link" onclick="Buckty.Api.GenerateApi();">Generate</span>':$api_key ;?></a>
                        </div>
                        <div class="sub_field">
                            <span class="text">
                                <b>User Key:</b>
                            </span>
                            <a class="text" id="appendKey"><?= $user->hash; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>