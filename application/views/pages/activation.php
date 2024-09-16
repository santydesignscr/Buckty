<?php $this->load->view('pages/header');?>
    <div class="form_container">
        <h1 class="heading">Login</h1>
        <div class="container">
            <div id="toast-container-log" class="toaster_log_container">
            <?php if($res == true):?>
                <div class="success"><?php _tran($trans->Your_account_was_activated);?></div>
            <?php else: ?>
                <div class="error"><?php _tran($trans->Account_cant_be_activated);?></div>
            <?php endif; ?>
            </div>
            <form action="<?php echo $site->site_url;?>checklog" method="post" data-type="login">
                <div class="field">
                    <input type="text" name="identity" placeholder="<?php _tran($trans->Username);?>"/>
                </div>
                <div class="field">
                    <input type="password" name="password" placeholder="<?php _tran($trans->Password);?>"/>
                </div>
                <div class="field">
                    <div class="squaredThree">
                        <input type="checkbox" value="None" id="squaredThree" name="remember" />
                        <label for="squaredThree"></label>
                    </div>
                    <label for="squaredThree" class="keep_me_log"><?php _tran($trans->Remember_me);?></label>
                </div>
                <div class="field bt">
                    <?php secureForm();?>
                    <button class="button login_bt primary" type="submit" name="Login"><?php _tran($trans->Login);?></button>
                </div>
            </form>
        </div>
    </div>

<?php $this->load->view('pages/footer');?>