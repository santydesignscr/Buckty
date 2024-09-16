<?php
if(!empty($users->users)):
    foreach($users->users as $user) {
        echo '<tr>';
        echo '<td><input type="checkbox" class="checkbox" name="user['.$user->id.']"/></td>';
        echo '<td>'.$user->id.'</td>';
        echo '<td>'.$user->name.'</td>';
        echo '<td>';
        echo $user->last_login == '' ? 'never': $user->last_login;
        echo '</td>';
        echo $user->banned == 0 ? '<td><span class="label label-success">Active</span></td>':'<td><span class="label label-danger">Banned</span></td>';
        echo '<td>'.$user->email.'</td>';
        echo '<td><div class="btn-group">
                              <button type="button" onclick="Buckty.user.edit($(this));"  data-toggle="modal" data-target="#editUser" data-id="'.$user->id.'" class="btn btn-danger">Edit</button>
                              <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                              </button>
                              <ul class="dropdown-menu" role="menu">
                                <li><a onclick="Buckty.user.remove($(this));" data-id="'.$user->id.'">Delete</a></li>
                              </ul>
                            </div></td>';
        echo '</tr>';
    }
endif;
?>