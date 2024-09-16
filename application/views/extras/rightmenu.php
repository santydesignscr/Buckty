<div class="right_menu right">
    <div class="menu_list">
        <ul>
            <li>
                <a class="men_item buckty_notifications" onclick="Buckty.loadNotes();">
                    <i class="fa fa-bell-o"></i><span class="have_note"></span>
                </a>
            </li>
        </ul>
    </div>
    <?php $this->load->view('extras/notifications'); ?>
        <div class="profile_menu">
            <div data-drop="profile_dropdown" class="drop_m profile_pic">
                <img src="<?= $user->profile_pic->medium;?>" class="profile_pic_img" alt="" />
            </div>
            <div id="profile_dropdown" class="drop_down">
                <h4 class="u_name">@<?php echo $user->name;?></h4>
                <ul>
                    <?php
          if($user->is_admin == true):?>
                        <li>
                            <a onclick="Buckty.loadURL('<?= $site->site_url;?>admin');" class="drp-li" data-no-ajax="true">
                                <i class="fa fa-user icon_brown"></i>
                                <span><?php _tran($trans->Admin_Panel);?></span>
                            </a>
                        </li>
                        <?php endif;?>
                            <li>
                                <a href="<?= $site->site_url;?>user/settings" class="drp-li">
                                    <i class="fa fa-gear icon_blue"></i>
                                    <span><?php _tran($trans->Settings);?></span>
                                </a>
                            </li>
                            <li>
                                <a onclick="Buckty.user_pic();" class="drp-li">
                                    <i class="fa fa-pencil icon_orange"></i>
                                    <span> <?php _tran($trans->Edit_Aavatar);?></span>
                                </a>
                            </li>
                            <li>
                                <a onclick="Buckty.logout();" class="drp-li">
                                    <i class="fa fa-sign-out icon_gray"></i>
                                    <span> <?php _tran($trans->Logout);?></span>
                                </a>
                            </li>
                </ul>
            </div>
        </div>
</div>