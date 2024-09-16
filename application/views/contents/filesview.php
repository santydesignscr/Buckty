<?php
if (!empty($files)) {
    $files = (object) $files;
    foreach ($files as $file) {
        $shared = $file->is_shared == 1 ? 'shared_item' : '';
        $starred = $file->starred == 1 ? 'starred' : '';
        $class = 'class="item_' . $file->hash . ' file_item item_clickable ' . $shared . ' ' . $starred . '"';
        $dataset = 'data-item-type="file" data-in="' . $in . '" data-title="' . $file->file_name . '" data-item="' . $file->hash . '" data-id="' . $file->hash . '" data-shared-link="' . $file->file_name . '"';
        echo '<div id="item_' . $file->hash . '" ' . $class . ' ' . $dataset . '>';
        echo '<div class="file_icon ignore">';
        $type = explode('/', $file->file_mime);
        echo $type[0] == 'image' ?
            '<img src="' . $site->site_url . 'userfile/' . $file->hash . '?w=150&h=200" class="icon_img ignore" alt="hello">' :
            '<div class="file-icon file-icon-xl ignore" data-type="' . $file->file_type . '"></div>';
        echo '</div>';
        echo '<div class="det ignore">';
        echo '<a class="ignore"><h3 class="title ignore">' . $file->file_name . '</h3></a>';
        echo '<span class="size ignore"><b>';
        _tran($trans->Size);
        echo ': </b>' . fileOrgSize($file->file_size) . '</span>';
        echo '<span class="date ignore">' . $file->file_date . '</span>';
        echo '<div class="actions ignore">';
        echo '<ul class="ignore">';
        echo '<li class="ignore">';
        echo '<a class="star_' . $file->hash . ' ' . $starred . ' star icon_link ignore tooltip-right" data-tooltip="';
        _tran($trans->Star);
        echo '">';
        echo '<i class="fa fa-star ignore"></i></a>';
        echo '</li>';
        echo '<li class="ignore">';
        echo '<a class="icon_link shared_icon ignore tooltip-right" data-tooltip="';
        _tran($trans->Shared_file);
        echo '">';
        echo '<i class="fa fa-share-alt ignore"></i></a>';
        echo '</li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}