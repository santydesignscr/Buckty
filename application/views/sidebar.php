<section class="vert_block">
    <div class="folder_container">
        <ul class="ul_folders">
            <?php
                GetTree('0','side');
            ?>
        </ul>
    </div>
    <div class="side_menu left">
        <ul class="side_ul">
            <li>
                <a href="<?= $site->site_url;?>folders" class="side_item">
                    <i class="fa icon_blue fa-files-o"></i>
                    <span class="side_text"><?php _tran($trans->All_Files);?></span>
                </a>
            </li>
            <li>
                <a href="<?= $site->site_url;?>shared" class="side_item">
                    <i class="fa fa-share-square-o"></i>
                    <span class="side_text"><?php _tran($trans->Shared);?></span>
                </a>
            </li>
            <li>
                <a href="<?= $site->site_url;?>recent" class="side_item">
                    <i class="fa fa-clock-o"></i>
                    <span class="side_text"><?php _tran($trans->Recent);?></span>
                </a>
            </li>
            <li>
                <a href="<?= $site->site_url;?>starred" class="side_item">
                    <i class="fa fa-star-o icon_orange"></i>
                    <span class="side_text"><?php _tran($trans->Starred);?></span>
                </a>
            </li>
            <?php
            if(!empty($site->site_info['dropbox']) && $site->site_info['dropbox']['activation'] != '0'): ?>
            <li>
                <a href="<?= $site->site_url;?>dropbox_list" class="side_item">
                    <i class="fa fa-dropbox icon_blue"></i>
                    <span class="side_text"><?php _tran($trans->dropbox);?></span>
                </a>
            </li>
            <?php endif;?>
            <?php
            if(!empty($site->site_info['google']) && $site->site_info['google']['drive_activation'] != '0'): ?>
            <li>
                <a href="<?= $site->site_url;?>drive_list" class="side_item">
                    <i class="icon-google-drive icon_red"></i>
                    <span class="side_text"><?php _tran($trans->drive);?></span>
                </a>
            </li>
            <?php endif;?>
            <li>
                <a href="<?= $site->site_url;?>trash" class="side_item">
                    <i class="fa fa-trash-o"></i>
                    <span class="side_text"><?php _tran($trans->Trash);?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="userspace space">
        <div class="details">
            <span class="text">Calculating...</span>
        </div>
        <div class="space_seek">
            <div class="loaded_seek" style="width:0%"></div>
        </div>
    </div>
</section>
<div id="item_preview" class="item_preview">
</div>