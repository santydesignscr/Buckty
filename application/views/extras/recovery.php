<div class="right_block recovery_block right">
          <div id="toast-container" class="toaster_log_container">
          </div>
        <div class="top_block">
          <h2 class="form_title"><?= _tran($trans->Reset_password);?></h2>
            <div class="field">
              <input type="text" name="recovery_email" id="emailR" placeholder="<?= _tran($trans->Email_address);?>"/>
            </div>
            <div class="bt">
                <div class="loader"></div>
                <button class="button login_bt primary" data-w="p" onclick="Buckty.loadRecover($(this));"><?= _tran($trans->Reset_password);?></button>
                <button class="button login_bt blue"  onclick="Buckty.Recover();"><?= _tran($trans->Login);?></button>
            </div>
        </div>
</div>