<div class="files_ grid">
<?php
if(!empty($files)){
foreach($files as $file){
$class = 'class="item_'.$file->hash.' file_item "';
echo '<div id="item_'.$file->hash.'" '.$class.'>';
echo '<div class="file_icon ignore">';
    $type = explode('/',$file->file_mime);
    echo $type[0] == 'image' ?
    '<img src="'.$site->site_url.'userfile/'.$file->hash.'?w=200&h=300" class="icon_img ignore" alt="hello">':
    '<div class="file-icon file-icon-xl ignore" data-type="'.$file->file_type.'"></div>';
    echo '</div>';
echo '<div class="det ignore">';
    echo '<a href="'.base_url().'" class="ignore"><h3 class="title ignore">'.$file->file_name.'</h3></a>';
    echo '<span class="size ignore"><b>Size: </b>'.fileOrgSize($file->file_size).'</span>';
    echo '<span class="date ignore">'.$file->file_date.'</span>';
    echo '</div>';
echo '</div>';
}
}
?>
</div>