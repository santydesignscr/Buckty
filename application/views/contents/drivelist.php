<div class="block_title">
    <h1 class="title">Google Drive</h1>
</div>
<div class="files_container default-skin">
    <!-- Main files -->
    <?php if($parent != '' || $parent != NULL) {
        echo '<a class="folder_back" onclick="Buckty.getDriveList(\'' . $parent . '\')">';
        echo '<i class="fa fa-folder-o icon_orange"></i>';
        echo '<span class="f_name">..</span>';
        echo '</a>';
    } ?>
    <section data-view="<?php echo $in == 'n' ? '1': '2';?>" class="files_ list">
        <?php
        if(!empty($files)):
                foreach ($files as $key => $v) {
                    $class = 'class="file_item "';
                    $onclick = $v['type'] == 'folder' ? 'onclick="Buckty.getDriveList(\'' . $v['item_id'] . '\')"': '';
                    $icon = $v['type'] != 'folder'  ? 'fa-file' : 'fa-folder-o icon_orange';
                    echo '<div '.$class.' '.$onclick.'>';
                    echo '<div class="file_icon ignore">';
                    echo '<i class="fa icon_i '.$icon.'"></i>';
                    echo '</div>';
                    echo '<div class="det ignore">';
                    echo '<a '.$onclick.' class="ignore"><h3 class="title ignore">'.$v['item_name'].'</h3></a>';
                    echo '<span class="size ignore"><b>'.tran($trans->Size).': </b>'.fileOrgSize($v['item_size']).'</span>';
                    if ($v['type'] != 'folder'):
                        echo '<a onclick="Buckty.getDrive(\'' . $v['item_id'] . '\');" class="button blue">'.tran($trans->Download).'</a>';
                    endif;
                    echo '</div>';
                    echo '</div>';
                }
        else:
            echo '<li><div class="no_list">NO files/folder</div>';
        endif;
        ?>

    </section>
</div>