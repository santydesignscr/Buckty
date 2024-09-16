<div class="settings_left">
    <div class="top_header">
        <div class="user_icon profile_pic">
            <a onclick="Buckty.user_pic();" class="edit_pic">
                <i class="fa fa-pencil"></i>
                <span class="text"><?php _tran($trans->Edit_Aavatar);?></span>
            </a>
            <img src="<?= $site->site_url; ?>profilepic/<?= $data_keys['user_hash'];?>?s=medium" class="profile_pic_img" alt="Arsh Singh"/>
        </div>
        <div class="details">
            <h2 class="username"><?= $user->name;?></h2>
            <span class="date"><b>Last login:</b> <?= $user->last_login;?></span>
        </div>
        <div class="actions">
            <ul>
                <li>
                    <a class="button primary" onclick="Buckty.Api.UserApiAccess();">
                        <i class="fa fa-cogs"></i>
                        <span><?php _tran($trans->Api_Access);?></span>
                    </a>
                </li>
                <?php if($user->is_admin == true): ?>
                <li>
                    <a class="button primary" data-no-ajax="true" href="<?= $site->site_url;?>admin">
                        <i class="fa fa-gear"></i>
                        <span><?php _tran($trans->Admin_Panel);?></span>
                    </a>
                </li>
                <?php endif;?>
                <li>
                    <a class="button primary" href="<?= $site->site_url;?>folders">
                        <i class="fa fa-file-o"></i>
                        <span><?php _tran($trans->My_Files);?></span>
                    </a>
                </li>
               <?php if(!empty($site->site_info->dropbox) && $site->site_info->dropbox['activation'] != '0'):
                    if(!empty($data_keys['dropbox_token'])):?>
                <li>
                    <a class="button blue" onclick="Buckty.getDropboxList();">
                        <i class="fa fa-file-o"></i>
                        <span><?php _tran($trans->Dropbox_Files);?></span>
                    </a>
                </li>
                <?php endif; endif;?>
            </ul>
        </div>
    </div>
    <div class="field_container">
        <div class="field dropbox">
            <label>Multidrive Connection</label>
            <?php if((empty($site->site_info['dropbox']) || $site->site_info['dropbox']['activation'] == '0')
                    && (empty($site->site_info['google']) || $site->site_info['google']['drive_activation'] == '0')):?>
                <p class="not_a">Dropbox / drive not available</p>
            <?php endif;?>
            <?php if(!empty($site->site_info['dropbox']) && $site->site_info['dropbox']['activation'] != '0'): ?>
            <a <?php echo empty($data_keys['dropbox_token']) ? 
                'onclick="Buckty.AuthDropbox();"': 'onclick="Buckty.RemoveDropbox();"';?> class="button dropbox">
                <i class="fa fa-dropbox"></i>
                <span><?php echo empty($data_keys['dropbox_token']) ? tran($trans->Connect_to_dropbox): tran($trans->Disconnect);?></span>
            </a>
            <?php endif;?>
            <?php
            if(!empty($site->site_info['google']) && $site->site_info['google']['drive_activation'] != '0'): ?>
            <a <?php echo empty($data_keys['gdrive_token']) ?
                'onclick="Buckty.AuthGdrive();"': 'onclick="Buckty.RemoveGdrive();"';?> class="button gdrive">
                <i class="fa fa-google"></i>
                <span><?php echo empty($data_keys['gdrive_token']) ? tran($trans->Connect_to_google_drive): tran($trans->Disconnect);?></span>
            </a>
            <?php endif;?>
        </div>
        <form id="updateUser">
            <div class="field">
                <label for="email"><?php _tran($trans->Email_Address);?></label>
                <input type="text" name="email" id="email" placeholder="<?php _tran($trans->Email_Address);?>" value="<?= $user->email;?>"/>
            </div>
            <div class="field">
                <label for="username"><?php _tran($trans->Username);?></label>
                <input type="text" name="username" id="username" value="<?= $user->name;?>"/>
            </div>
            <div class="field">
                <label for="password"><?php _tran($trans->New_Password);?></label>
                <input type="text" name="password" id="password" placeholder="<?php _tran($trans->New_Password);?>"/>
            </div>
            <div class="field">
                <label for="confirm_password"><?php _tran($trans->Confirm_Password);?></label>
                <input type="text" name="confirm_password" id="confirm_password" placeholder="<?php _tran($trans->Confirm_Password);?>"/>
            </div>

            <div class="field button_container">
                <button type="submit" class="button blue"><?php _tran($trans->Update);?></button>
            </div>
        </form>
    </div>
</div>