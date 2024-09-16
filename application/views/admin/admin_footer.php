
<div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="addUser">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Add new user</h4>
            </div>
            <form onsubmit="return Buckty.user.adduser($(this));">
            <div class="modal-body">
                    <div class="form-group">
                        <label for="username" class="control-label">Username</label>
                        <input type="text" class="form-control" name="username" id="username" required />
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">Email Address</label>
                        <input type="email" class="form-control" name="email" id="email" required />
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password" required />
                    </div>
                    <div class="form-group">
                        <label for="confirm_password" class="control-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirm" id="confirm_password" required />
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="user_role" class="form-control">
                            <option value="3">User</option>
                            <option value="1">Administrator</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="verification" value="Yes"/>Send verification email ? .
                        </label>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editUser" tabindex="-1" role="dialog" aria-labelledby="editUser">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Edit user</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/popups/lang');?>
<div id="toast-container" class="toast_container">
    <div class="loading"><div class="loader"></div><span class="text"><?php _tran($trans->Something_is_loading);?>...</span></div>
</div>
      <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b>Version</b> 2.3.0
        </div>
        <strong>Copyright &copy; 2014-2015 .</strong> All rights reserved.
      </footer>
    <!-- Bootstrap 3.3.5 -->
    <script src="<?php echo $site->site_url;?>/assets/admin/js/bootstrap.min.js"></script>

    <script src="<?php echo $site->site_url;?>/assets/admin/ckeditor/ckeditor.js"></script>

    <script type="text/javascript" src="<?php echo $site->site_url;?>/assets/admin/js/Chart.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo $site->site_url;?>/assets/admin/js/app.js"></script>
  </body>
</html>
