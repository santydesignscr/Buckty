<?php
/*
 *  Get folder list
 *  $folders = array();
 *
 */
foreach($folders as $folder) {
    echo '<tr>';
    echo '<td>'.$folder->folder_name.'</td>';
    echo '<td><a href="'.$site->site_url.'shared/file/'.$folder->folder_hash.'"><span class="label label-success">View</span></a></td>';
    echo '<td>'.$folder->date.'</td>';
    echo '<td>'.$folder->folder_hash.'</td>';
    echo '<td>'.$folder->email.'</td>';
    echo '<td><div class="btn-group">
                              <button type="button" onclick="Buckty.folder.delete($(this));"  data-toggle="modal" data-target="#editUser" data-id="'.$folder->folder_hash.'" class="btn btn-danger">Delete</button>
                            </div></td>';
    echo '</tr>';
}
?>