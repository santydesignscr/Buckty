<header class="item_header">
    <div class="title">
        <?php 
        echo $type == 'file'? '<div class="file-icon file-icon-xl ignore" data-type="'.$item->file_type.'"></div>': '<i class="fa fa-folder-o"></i>';?>
            <h3 class="text"><?php
            echo $type == 'file' ? $item->file_name: $item->folder_name;?></h3>
    </div>
    <a href="javascript:void(0);" class="close" onclick="$(this).parents('.item_preview').empty().hide();"><i class="fa fa-close"></i></a>
</header>
<div class="content">
    <div class="icon">
        <?php 
                if($type == 'file'){
                  $type_e = explode('/',$item->file_mime);
                echo $type_e[0] == 'image' ? 
                '<img src="'.$site->site_url.'userfile/'.$item->hash.'?w=400&h=900" class="icon_img ignore" alt="'.$item->file_name.'">': 
                '<div class="file-icon file-icon-xl ignore" data-type="'.$item->file_type.'"></div>';   
                } else {
                    echo '<i class="fa fa-folder-o"></i>';
                }
            ?>
    </div>
    <div class="details">
        <div class="pull-right">
            <a onclick="Buckty.DownloadSingle('<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>','<?= $type;?>');" class="button blue">
                <i class="fa fa-download"></i>
                <span>Download</span>
            </a>
            <a onclick="Buckty.Share('<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>','<?= $type;?>');" class="button primary">
                <i class="fa fa-share"></i>
                <span>Share</span>
            </a>
        </div>
        <div class="shared_">
            <h1 class="_info">Connections</h1>
            <ul>
                <?php
                   foreach($users as $user):
                  ?>
                    <li class="user_<?= $user->hash;?> tooltip-top" data-tooltip="<?= $user->name; ?>">
                        <div class="icon <?php echo $user->owner == 1 ? 'owner': '';?>">
                            <img src="<?= $site->site_url.'profilepic/'.$user->hash.'?s=medium';?>">
                        </div>
                    </li>
                    <?php endforeach;?>
            </ul>
        </div>
        <div class="field">
            <span class="bold pull_left">Size</span>
            <span class="thin pull_left"><?php echo $type == 'file' ? fileOrgSize($item->file_size): '-';?></span>
        </div>
        <?php if($type == 'file' && $type_e[0] == 'image'): ?>
            <div class="field">
                <span class="bold pull_left">Resolution</span>
                <span class="thin pull_left"><?php echo $item->image_width.'x'.$item->image_height;?></span>
            </div>
            <?php endif; ?>
                <div class="field">
                    <span class="bold pull_left">Creation Date</span>
                    <span class="thin pull_left"><?php echo $type == 'file' ? $item->file_date: $item->date;?></span>
                </div>
        <div class="field">
            <span class="bold pull_left">Owner</span>
            <span class="thin pull_left"><?php echo $item_owner->name;?></span>
        </div>
        <div class="field">
            <span class="bold pull_left">Location</span>
            <span class="thin pull_left"><?php if(!empty($item_parent)): echo $item_parent->folder_name; else: echo '-'; endif?></span>
        </div>
    </div>
</div>