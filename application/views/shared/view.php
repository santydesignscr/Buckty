<?php $this->load->view('shared/header.php');?>
<div class="viewer_container">
        <div class="view_contain">
            <div class="header">
                <div class="title">
                    <?php 
                    echo $type == 'file'? 
                    '<div class="file-icon file-icon-xl ignore" data-type="'.$item->file_type.'"></div>': 
                    '<i class="fa fa-folder-o"></i>';?>
                        <span class="text"><?php echo $type_item == 'file' ? $item->file_name: $item->folder_name; ?></span>
                </div>
                <?php if($user->is_logged):?>
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
                                    <a onclick="Buckty.loadURL('<?= $site->site_url;?>admin')" class="drp-li" data-no-ajax="true">
                                        <i class="fa fa-user icon_brown"></i>
                                        <span><?php _tran($trans->Admin_Panel);?></span>
                                    </a>
                                </li>
                            <?php endif;?>
                            <li>
                                <a onclick="Buckty.loadURL('<?= $site->site_url;?>user/settings');" data-no-ajax="true" class="drp-li">
                                    <i class="fa fa-gear icon_blue"></i>
                                    <span><?php _tran($trans->Settings);?></span>
                                </a>
                            </li>
                            <li>
                                <a onclick="Buckty.user_pic();" data-no-ajax="true" class="drp-li">
                                    <i class="fa fa-pencil icon_orange"></i>
                                    <span> <?php _tran($trans->Edit_Aavatar);?></span>
                                </a>
                            </li>
                            <li>
                                <a onclick="Buckty.logout();" data-no-ajax="true" class="drp-li">
                                    <i class="fa fa-sign-out icon_gray"></i>
                                    <span> <?php _tran($trans->Logout);?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
    <?php endif;?>
                <div class="actions">
                    <ul class="action_ul">
                        <li class="list_item">
                            <a onclick="Buckty.DownloadSingle('<?= $hash; ?>','<?= $type; ?>');" class="action tooltip-bottom" data-tooltip=" <?php _tran($trans->Download);?>">
                                <i class="fa fa-download"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="container_box">
                <?php
                    switch($type_item){
                        case 'file':
                            $mime = explode('/',$item->file_mime);
                            switch($mime[0]){
                                case 'image':
                                   $this->load->view('extras/image_preview'); 
                                break;
                                case 'text';
                                    $this->load->view('extras/text_view'); 
                                break;
                                case 'video';
                                    $this->load->view('extras/video_view'); 
                                break;
                                case 'audio';
                                    $this->load->view('extras/audio_view'); 
                                break;
                                case 'application';
                                    if(in_array($item->file_mime,$texttypes)):
                                     $this->load->view('extras/document_view'); 
                                    else:
                                    $this->load->view('extras/undefined_view'); 
                                    endif;
                                break;
                            }
                            break;
                        case 'folder':
                            $this->load->view('extras/folder_view');
                        break;
                    }
                ?>
            </div>
            <?php if($site->ad_320):?>
            <div class="advertise ad_320">
                <?= $site->ad_320;?>
            </div>
            <?php endif;?>
        </div>
    <a onclick="Buckty.toggleComments()" class="commentsToggle"><i class="fa fa-comment"></i></a>
</div>
<div class="side_bar">
    <div class="field_share">
        <div class="social-icon socialshare facebook tooltip-bottom" data-tooltip="Facebook" data-action="facebook" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-facebook"></i></div>

        <div class="social-icon socialshare google tooltip-bottom" data-tooltip="Google" data-action="google" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-google-plus"></i></div>
        <div class="social-icon socialshare twitter tooltip-bottom" data-tooltip="Twitter" data-action="twitter" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-twitter"></i></div>
        <div class="social-icon socialshare pinterest tooltip-bottom" data-tooltip="Pinterest" data-action="pinterest" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-pinterest"></i></div>
        <div class="social-icon socialshare tumblr tooltip-bottom" data-tooltip="Tumblr" data-action="tumblr" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-tumblr"></i></div>
        <a onclick="Buckty.toggleComments();" class="closeComment"><i class="fa fa-close"></i></a>
    </div>
<?php $this->load->view('shared/header.php');?>
<div class="viewer_container">
        <div class="view_contain">
            <div class="header">
                <div class="title">
                    <?php 
                    echo $type == 'file'? 
                    '<div class="file-icon file-icon-xl ignore" data-type="'.$item->file_type.'"></div>': 
                    '<i class="fa fa-folder-o"></i>';?>
                        <span class="text"><?php echo $type_item == 'file' ? $item->file_name: $item->folder_name; ?></span>
                </div>
                <?php if($user->is_logged):?>
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
                                    <a onclick="Buckty.loadURL('<?= $site->site_url;?>admin')" class="drp-li" data-no-ajax="true">
                                        <i class="fa fa-user icon_brown"></i>
                                        <span><?php _tran($trans->Admin_Panel);?></span>
                                    </a>
                                </li>
                            <?php endif;?>
                            <li>
                                <a onclick="Buckty.loadURL('<?= $site->site_url;?>user/settings');" data-no-ajax="true" class="drp-li">
                                    <i class="fa fa-gear icon_blue"></i>
                                    <span><?php _tran($trans->Settings);?></span>
                                </a>
                            </li>
                            <li>
                                <a onclick="Buckty.user_pic();" data-no-ajax="true" class="drp-li">
                                    <i class="fa fa-pencil icon_orange"></i>
                                    <span> <?php _tran($trans->Edit_Aavatar);?></span>
                                </a>
                            </li>
                            <li>
                                <a onclick="Buckty.logout();" data-no-ajax="true" class="drp-li">
                                    <i class="fa fa-sign-out icon_gray"></i>
                                    <span> <?php _tran($trans->Logout);?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
    <?php endif;?>
                <div class="actions">
                    <ul class="action_ul">
                        <li class="list_item">
                            <a onclick="Buckty.DownloadSingle('<?= $hash; ?>','<?= $type; ?>');" class="action tooltip-bottom" data-tooltip=" <?php _tran($trans->Download);?>">
                                <i class="fa fa-download"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="container_box">
                <?php
                    switch($type_item){
                        case 'file':
                            $mime = explode('/',$item->file_mime);
                            switch($mime[0]){
                                case 'image':
                                   $this->load->view('extras/image_preview'); 
                                break;
                                case 'text';
                                    $this->load->view('extras/text_view'); 
                                break;
                                case 'video';
                                    $this->load->view('extras/video_view'); 
                                break;
                                case 'audio';
                                    $this->load->view('extras/audio_view'); 
                                break;
                                case 'application';
                                    if(in_array($item->file_mime,$texttypes)):
                                     $this->load->view('extras/document_view'); 
                                    else:
                                    $this->load->view('extras/undefined_view'); 
                                    endif;
                                break;
                            }
                            break;
                        case 'folder':
                            $this->load->view('extras/folder_view');
                        break;
                    }
                ?>
            </div>
            <?php if($site->ad_320):?>
            <div class="advertise ad_320">
                <?= $site->ad_320;?>
            </div>
            <?php endif;?>
        </div>
    <a onclick="Buckty.toggleComments()" class="commentsToggle"><i class="fa fa-comment"></i></a>
</div>
<!-------------------------------------------------------ADS----------------------------------------------------------->
<h1 style="text-align: center;"><span style="color: #ffffff; background-color: #ff6600;"><strong>AQUI VA TU ANUNCIO</strong></span></h1>
<!-------------------------------------------------------ADS----------------------------------------------------------->
<!-------------------------------------------------------ADS----------------------------------------------------------->
<h1 style="text-align: center;"><span style="color: #ffffff; background-color: #ff6600;"><strong>AQUI VA TU ANUNCIO</strong></span></h1>
<!-------------------------------------------------------ADS----------------------------------------------------------->
</div>
<?php $this->load->view('shared/footer');?>