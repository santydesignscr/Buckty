<div class="block_title">
    <h1 class="title">Dropbox</h1>
</div>
<div class="files_container default-skin">
    <!-- Main files -->
    <?php if($parent != 'n') {
    echo '<a class="folder_back" onclick="Buckty.getDropboxList(\'/\')">';
        echo '<i class="fa fa-folder-o icon_orange"></i>';
        echo '<span class="f_name">..</span>';
        echo '</a>';
    } ?>
    <section data-view="<?php echo $in == 'n' ? '1': '2';?>" class="files_ list">
                            <?php
                            if(!empty($list)):
                                if(!empty($list['contents'])):
                                    foreach ($list['contents'] as $key => $v) {
                                        $path = explode('/', $v['path']);
                                        $name = end($path);
                                        $class = 'class="file_item "';
                                        $onclick = $v['is_dir'] != false ? 'onclick="Buckty.getDropboxList(\'' . $v['path'] . '\',\'' . $path[0] . '\')"': '';
                                        $icon = $v['is_dir'] == false ? 'fa-file' : 'fa-folder-o icon_orange';
                                        echo '<div '.$class.' '.$onclick.'>';
                                        echo '<div class="file_icon ignore">';
                                        echo '<i class="fa icon_i '.$icon.'"></i>';
                                        echo '</div>';
                                        echo '<div class="det ignore">';
                                        echo '<a '.$onclick.' class="ignore"><h3 class="title ignore">'.$name.'</h3></a>';
                                        echo '<span class="size ignore"><b>'.tran($trans->Size).': </b>'.fileOrgSize($v['bytes']).'</span>';
                                        if ($v['is_dir'] == false):
                                            echo '<a onclick="Buckty.getDropbox(\'' . $v['path'] . '\');" class="button blue">'.tran($trans->Download).'</a>';
                                        endif;
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                endif;
                            else:

                                echo '<li><div class="no_list">NO files/folder</div>';

                            endif;
                            ?>

                      </section>
</div>