<?php
if (!empty($folders)) {
    $folders = (object) $folders;
    foreach ($folders as $folder) {
        $shared = $folder->is_shared == 1 ? 'shared_item' : '';
        $starred = $folder->starred == 1 ? 'starred' : '';
        $icon = $folder->is_shared ? 'icon-folder-shared':'icon-folder';
        $class = 'class="item_' . $folder->folder_hash . ' file_item folder_ item_clickable ' . $shared . ' ' . $starred . '"';
        $dataset = 'data-item-type="folder" data-in="' . $in . '" data-title="' . $folder->folder_name . '" data-item="' . $folder->folder_hash . '" data-id="' . $folder->folder_hash . '"';
        $starred = $folder->starred == 1 ? 'starred' : '';
        echo '<div id="folder_' . $folder->folder_hash . '" ' . $class . ' ' . $dataset . '>';
        echo '<div class="file_icon ignore">';
        echo '<i class="icon_i '.$icon.' ignore"></i>';
        echo '</div>';
        echo '<div class="det ignore">';
        echo '<a class="ignore"><h3 class="title ignore">' . $folder->folder_name . '</h3></a>';
        echo '<span class="size ignore"><b>';
        _tran($trans->Size);
        echo ': </b>';
        FolderSize($folder->folder_hash);
        echo '</span>';
        echo '<span class="date ignore">' . $folder->date . '</span>';
        echo '<div class="actions ignore">';
        echo '<ul class="ignore">';
        echo '<li class="ignore">';
        echo '<a  class="star_' . $folder->folder_hash . ' ' . $starred . ' star icon_link ignore tooltip-right" data-tooltip="';
        _tran($trans->Starred);
        echo '">';
        echo '<i class="fa fa-star ignore"></i></a>';
        echo '</li>';
        echo '<li class="ignore">';
        echo '<a  class="icon_link shared_icon ignore tooltip-right" data-tooltip="';
        _tran($trans->Shared_folder);
        echo '">';
        echo '<i class="fa fa-share-alt ignore"></i></a>';
        echo '</li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
?>