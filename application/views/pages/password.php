<?php $this->load->view('pages/header');?>
        <div class="form_container">
            <h1 class="heading"><?php _tran($trans->Reset_password);?></h1>
            <div class="container">
                <div id="toast-container-log" class="toaster_log_container">
                </div>
                <form action="<?= $site->site_url;?>recover/resetpassword" data-type="reset" id="reset_pass">
                <div class="field">
                    <input type="text" name="email" placeholder="<?php _tran($trans->Your_email_address);?>"/>
                </div>
                <div class="field">
                    <input type="password" name="password" placeholder="<?php _tran($trans->New_Password);?>"/>
                </div>
                <div class="field">
                    <input type="password" name="confirm_password" placeholder="<?php _tran($trans->Confirm_Password);?>"/>
                </div>
                <div class="field bt">
                    <input type="hidden" name="id" value="<?= $id;?>"/>
                    <input type="hidden" name="ver" value="<?= $ver;?>"/>
                    <button type="submit" class="button blue"><?php _tran($trans->Reset_password);?></button>
                </div>
                </form>
            </div>
</div>
<?php $this->load->view('pages/footer');?>