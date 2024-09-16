<div id="js_share" class="mini_pop">
    <div class="overlay"></div>
    <div class="modal_container">
        <div class="modal extended">
            <div class="modal_header">
                <h1 class="modal_title"><?= _tran($trans->Share);?> <?php echo $type == 'file' ? $item->file_name: $item->folder_name;?></h1>
                <a href="javascript:void(0);" class="close"><i class="fa fa-remove"></i></a>
            </div>
            <div class="modal_content">
                <div class="share_content">
                    <div class="icon">
                        <?php 
                                if($type == 'file'){
                                $item_t = explode('/',$item->file_mime);
                                  echo $item_t[0] == 'image' ? 
                                  '<img src="'.$site->site_url.'userfile/'.$item->hash.'?w=200&h=300" class="icon_img ignore" alt="hello">': 
                                  '<div class="file-icon file-icon-xl ignore" data-type="'.$item->file_type.'"></div>';
                                } else {
                                  echo '<i class="fa fa-folder-o"></i>';
                                }
                            ?>
                    </div>
                    <div class="field withbtn">
                        <input type="text" id="shareablelink" readonly name="shareablelink" value="<?= $link;?>" />
                        <a onclick="Buckty.Copy('shareablelink','target')" class="button blue rightshape">
                            <i class="fa fa-link"></i><?= _tran($trans->Copy);?>
                        </a>
                    </div>
                    <div class="field social_field">
                        <div class="social-icon socialshare facebook tooltip-top" data-tooltip="Facebook" data-action="facebook" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-facebook"></i></div>

                        <div class="social-icon socialshare google tooltip-top" data-tooltip="Google" data-action="google" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-google-plus"></i></div>
                        <div class="social-icon socialshare twitter tooltip-top" data-tooltip="Twitter" data-action="twitter" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-twitter"></i></div>
                        <div class="social-icon socialshare pinterest tooltip-top" data-tooltip="Pinterest" data-action="pinterest" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-pinterest"></i></div>
                        <div class="social-icon socialshare tumblr tooltip-top" data-tooltip="Tumblr" data-action="tumblr" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-tumblr"></i></div>
                    </div>

                    <div class="field advanced_field">
                        <?php if($item->permission == '1'):?>
                        <a class="button blue add_people"><i class="fa fa-users"></i><span><?= _tran($trans->Add_people);?></span></a>
                        <?php endif;?>
                        <a class="button primary send_email"><i class="fa fa-send"></i><span><?= _tran($trans->Email);?></span></a>
                        <?php if($item->permission == '1'):?>
                            <a class="button blue add_pass tooltip-top" data-tooltip="<?= _tran($trans->Add_pass);?>"><i class="fa fa-lock"></i></a>
                        <?php endif;?>
                    </div>
                </div>
                <?php if($item->permission == '1'):?>
                    <div class="margin_small add_password">
                        <form onsubmit="return Buckty.addPassword($(this));">
                            <div class="field">
                                <input type="password" name="password" placeholder="Type your password">
                                <span class="info"><?php echo $item->password != NULL || '' ? 'Currently Protected': 'Not using password';?></span>
                            </div>
                            <div class="field">
                                <input type="hidden" name="item" value="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>/<?= $type;?>"/>
                               <button type="submit" class="button blue"><i class="fa fa-lock"></i><span><?= _tran($trans->Update_password);?></span></button>
                            </div>
                        </form>
                    </div>
                <?php endif;?>
                <?php if($item->permission == '1'):?>
                    <div class="margin_small shared_users">
                        <div class="shared_">
                            <ul id="users_sug">
                                <?php 
                                foreach($users as $u):
                                ?>
                                <li class="user_<?= $u->hash;?>">
                                    <div class="icon <?php echo $u->owner == 1 ? 'owner': '';?>">
                                    <img src="<?= $site->site_url.'profilepic/'.$u->hash.'?s=medium';?>" >
                                    </div>
                                    <div class="user_det">
                                        <h3 class="username tooltip-top" data-tooltip="<?= $u->name; ?>">
                                            <?= $u->name;?> <?php echo $u->owner == 1 && $u->user_id == $user->id ? '(You)': '';?>
                                        </h3>
                                        <span class="text"><?= $u->email;?></span>
                                    </div>
                                    <div class="action">
                                        <ul>
                                            <?php if($u->owner != 1 && $u->user_id != $user->id): ?>
                                            <li> <a href="javascript:void(0);" class="permission">
                                                    <i class="fa fa-lock"></i><span><?= _tran($trans->Permission);?></span>
                                                </a>
                                                <ul>
                                                    <li><a href="javascript:void(0);" onclick="Buckty.SuggestUsers.changePermission('<?= $u->hash;?>','<?= $type;?>','<?= $hash;?>','1');" class="per <?php echo $u->permission == '1' ? 'active': ''; ?>">Can edit</a></li>
                                                    <li><a href="javascript:void(0);" onclick="Buckty.SuggestUsers.changePermission('<?= $u->hash;?>','<?= $type;?>','<?= $hash;?>','2');" class="per <?php echo $u->permission == '2' ? 'active': ''; ?>">Can view</a></li>
                                                </ul>
                                            </li>
                                             <?php endif;?>
                                            <?php if($u->owner != 1 && $u->user_id != $user->id): ?>
                                            <li>
                                                <a class="remove" onclick="Buckty.SuggestUsers.unlinkUser('<?= $u->hash;?>','<?= $type;?>','<?= $hash;?>');">
                                                    <i class="fa fa-close"></i>
                                                </a>
                                            </li>
                                            <?php endif;?>
                                        </ul>
                                    </div>
                                </li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                        <div class="add_user_form">
                            <form onsubmit="return Buckty.SuggestUsers.addUsers($(this));" autocomplete="off">
                                <div class="field input_tags">
                                    <input type="text" name="users" id="addUserstoitem" placeholder="<?= _tran($trans->Username_or_email);?>" autocomplete="off"/>
                                    <div class="suggestion">
                                        <ul>
                                        </ul>
                                    </div>
                                </div>
                                <div class="field select_permission">
                                    <select id="users_permission" name="permission">
                                        <option value="1"><?= _tran($trans->Can_edit);?></option>
                                        <option value="2"><?= _tran($trans->Can_view);?></option>
                                    </select>
                                </div>
                                <input type="hidden" name="hash" value="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>/<?php echo $type == 'file' ? 'file': 'folder';?>"/>
                                <button class="button blue" type="submit"><?= _tran($trans->Share);?></button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
                    <div class="margin_small email_item">
                        <form onsubmit="return Buckty.email($(this));" autocomplete="off">
                            <div class="field email_tags">
                                <input type="text" name="users" id="emailTags" placeholder="<?= _tran($trans->Email_addresses);?>" autocomplete="off"/>
                            </div>
                            <div class="field textfield">
                                <textarea name="message" placeholder="<?= _tran($trans->Your_message);?>"></textarea>
                            </div>
                            <input type="hidden" name="hash" value="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>/<?php echo $type == 'file' ? 'file': 'folder';?>"/>
                            <div class="field button_send">
                                <button class="button blue" type="submit"><i class="fa fa-send"></i><?= _tran($trans->Send);?></button>
                            </div>
                        </form>
                    </div>
                    <div class="buttons_container margin">
                        <a class="primary button" onclick="Buckty.popup('js_share','c');"><?= _tran($trans->Close);?></a>
                    </div>
            </div>
        </div>
    </div>
</div>