<form onsubmit="return Buckty.user.update($(this));">
    <div class="form-group">
        <label for="username" class="control-label">Username</label>
        <input type="text" class="form-control" name="username" id="username" value="<?= $data->name; ?>" required readonly/>
    </div>
    <div class="form-group">
        <label for="email" class="control-label">Email Address</label>
        <input type="email" class="form-control" name="email" id="email" value="<?= $data->email;?>" required />
    </div>
    <div class="form-group">
        <label for="password" class="control-label">Password</label>
        <input type="password" class="form-control" name="password" id="password" value="" />
    </div>
    <div class="form-group">
        <label for="confirm_password" class="control-label">Confirm Password</label>
        <input type="password" class="form-control" name="confirm_password" id="confirm_password" />
    </div>

    <div class="form-group">
        <label>Role</label>
        <?php if(!empty($groups)):
            foreach($groups as $g):
        ?>
         <label class="checkbox-inline">
            <input type="checkbox" name="role[]" value="<?= $g->id;?>" <?php if(array_key_exists($g->name,$data->userGroups)): echo 'checked'; endif; ?>/> <?= $g->name;?>
         </label>
        <?php endforeach; endif; ?>
    </div>

    </div>
    <div class="modal-footer">
        <input type="hidden" name="user_id" value="<?= $data->id;?>"/>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>