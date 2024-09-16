<?php
/*
 *  Get folder list
 *  $files = array();
 *
 */
foreach($files as $file) {
    echo '<tr>';
    echo '<td>'.$file->file_name.'</td>';
    echo '<td><a href="'.$site->site_url.'shared/file/'.$file->hash.'"><span class="label label-success">View</span></a></td>';
    echo '<td>'.$file->file_date.'</td>';
    echo '<td>'.$file->hash.'</td>';
    echo '<td>'.$file->email.'</td>';
    echo '<td><div class="btn-group">
                              <button type="button" onclick="Buckty.file.delete($(this));"  data-id="'.$file->hash.'" class="btn btn-danger">Delete</button>
          </div></td>';
    echo '</tr>';
}
?>