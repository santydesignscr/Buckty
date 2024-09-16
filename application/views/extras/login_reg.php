      <div class="right_block login_reg_block right">
          <div id="toast-container-log" class="toaster_log_container">
          </div>
        <div class="top_block">
          <h2 class="form_title"><?= _tran($trans->Login);?></h2>
          <form action="<?php echo $site->site_url;?>checklog" method="post" data-type="login">
            <div class="field">
              <input type="text" name="identity" placeholder="<?= _tran($trans->Username);?>"/>
            </div>
            <div class="field">
              <input type="password" name="password" placeholder="<?= _tran($trans->Password);?>"/>
            </div>
            <div class="field">
              <div class="squaredThree">
              	<input type="checkbox" value="None" id="squaredThree" name="remember" />
              	<label for="squaredThree"></label>
              </div>
              <label for="squaredThree" class="keep_me_log"><?= _tran($trans->Remember_me);?></label>
                <a class="recover_options" onclick="Buckty.Recover();"><span class="text"><?= _tran($trans->Recovery_options);?></span></a>
            </div>
            <div class="bt">
              <?php secureForm();?>

               <div class="loader"></div> <button class="button login_bt primary" type="submit" name="Login"><?= _tran($trans->Login);?></button>
              <?php
                if($site->social == 'Yes'):?>
                <div class="social_buttons">
                <ul>
                <?php foreach($api as $key =>$soc){
                    if(empty($soc['activation'])){
                        continue;
                    }
                    if($key == 'dropbox' || $soc['activation'] == 0){
                        continue;
                    }
                ?>
                  <li>
                      <a onclick="Buckty.socialLog('<?= $key;?>');" data-no-ajax="true" class="<?= $key;?> social_button">
                          <i class="fa fa-<?= $key;?>"></i>
                      </a>
                  </li>
                <?php } ?>
                </ul>
              </div>
                    <?php endif;?>
            </div>
          </form>
        </div>
        <?php if($site->register_active == 1): ?>
        <div class="top_block reg">
        <div id="toast-container-reg" class="toaster_log_container">
          </div>
          <h2 class="form_title"><?= _tran($trans->Register);?></h2>
          <form action="<?php echo $site->site_url;?>/createu" method="post" data-type="register">
            <div class="field">
              <input type="text" name="username" placeholder="<?= _tran($trans->Username);?>"/>
            </div>
            <div class="field">
              <input type="email" name="email" placeholder="<?= _tran($trans->Email_address);?>"/>
            </div>
            <div class="field">
              <input type="password" name="password" placeholder="<?= _tran($trans->Password);?>"/>
            </div>
            <div class="field">
              <input type="password" name="password_confirm" placeholder="<?= _tran($trans->Password_confirm);?>"/>
            </div>
              <div class="field">
                  <div class="squaredThree">
                      <input type="checkbox" value="None" id="terms" name="terms_conditions" required/>
                      <label for="terms"></label>
                  </div>
                  <label for="terms" class="keep_me_log"><?= _tran($trans->Accept_terms);?></label>
              </div>
            <div class="bt">
                <?php secureForm();?>
                <div class="loader"></div> <button class="button blue" type="submit" name="submit"><?= _tran($trans->Register);?></button>
            </div>
          </form>
        </div>
          <?php endif;?>
      </div>