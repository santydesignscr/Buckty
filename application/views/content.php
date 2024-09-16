<div class="section_header">
    <?php if($in == 'n'){ ?>
        <div class="breadCrumbs">
            <ul>
                <li>
                    <a href="<?= $site->site_url.'folders';?>" class="<?php echo !empty($folderCrumb) ? 'folder_crumb link' : '';?>" data-id="0">
                        <i class="icon-folder-open"></i>
                        <span class="text">Folders</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
                <?php
                $crumbs = count($folderCrumb);
                $x = 1;
             foreach($folderCrumb as $key => $crumb){
                echo '<li>';
                $link = $x == $crumbs ? '': 'href="'.$crumb['link'].'"';
                $class = $x == $crumbs ? '': ' class="link folder_crumb"';
                echo '<a '.$link.' '.$class.' data-id="'.$crumb['hash'].'">';
                echo '<i class="icon-folder-open"></i>';
                echo '<span class="text">'.$crumb['name'].'</span>';
                echo $x == $crumbs ? '': '<i class="fa fa-angle-right"></i>';
                echo '</a>';
                echo '</li>';
                 $x++;
             }
            ?>
            </ul>
        </div>
        <?php } ?>
            <?php $this->load->view('contents/actions.php');?>
</div>
<div class="files_container">
    <?php
    if($site->ad_780):?>
        <div class="advertise ad_780 ignore">
            <?= $site->ad_780;?>
        </div>
    <?php endif;?>
    <!-- Main files -->
    <section data-view="<?php echo $in == 'n' || $in ==  's' ? '1': '2';?>" class="files_ <?php echo !empty($_COOKIE['view']) ? $_COOKIE['view']: 'grid'; ?>">
    <?php
    $folders = (array) $folders;
    $files = (array) $files;
    $is_empty = empty($folders) && empty($files) ? false:true;
    if($is_empty):
    /*
    * Loading folder view which contains folder markup
    */
    $this->load->view('contents/foldersview.php');
    
    /* 
    * Loading files view which contains files markup  
    */
    
    $this->load->view('contents/filesview.php'); 
     else:
              $ico = $in == 'n' ||  $in == 's' ? 'fa-files-o':'fa-trash';
              $text = $in == 'n' ||  $in == 's' ? 'Start Uploading Files':  tran($trans->No_files_or_folders_in_trash);
              echo '<div class="no_files">';
              echo '<div class="icon"><i class="fa '.$ico.'"></i></div>';
              echo '<div class="text"><span>'.$text.'</span></div>';
              echo '</div>';
    endif;
    ?>
    </section>
    <?php
    if($site->ad_780):?>
        <div class="advertise ad_780 ignore">
            <?= $site->ad_780;?>
        </div>
    <?php endif;?>
</div>