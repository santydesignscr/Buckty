<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo $content;?>
          </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
                <div class="left">
                    <a class="btn btn-app" class="btn btn-primary" data-toggle="modal" data-target="#addUser">
                        <i class="fa fa-plus"></i> Add User
                    </a>
                </div>
            </div>
        <div class="row">
    <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo 'Users';?></h3>

              <div class="box-tools">
                  <form onsubmit="return Buckty.user.search($(this));">
                <div class="input-group input-group-sm" style="width: 150px;">
                  <input type="text" name="user_search" class="form-control pull-right" placeholder="Search">

                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                  </form>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                  <thead>
                <tr>
                  <th></th>
                  <th>Username</th>
                  <th>Last Login</th>
                  <th>Status</th>
                  <th>Email</th>
                  <th>Actions</th>
                </tr></thead>
                  <tbody id="appendusers">
                <?php
                if(!empty($users->users)):
                  foreach($users->users as $user) {
                      $what = $user->banned == 0 ? 'b':'u';
                      $banned_t = $user->banned == 0 ? 'Banned':'Un-banned';
                    echo '<tr id="user_'.$user->id.'">';
                    echo '<td><div class="userimg"><img src="'.$user->profile_pic->medium.'"></div></td>';
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
                                <li><a onclick="Buckty.user.ban($(this));" data-id="'.$user->user_hash.'" data-what="'.$what.'">'.$banned_t.'</a></li>
                                <li><a onclick="Buckty.user.remove($(this));" data-id="'.$user->id.'">Delete</a></li>
                              </ul>
                            </div></td>';
                    echo '</tr>';
                  }
                endif;
                ?>
              </tbody></table>
            </div>
            <!-- /.box-body -->
              <div class="box-footer clearfix">
              <?php echo $this->pagination->create_links();?>
            </div>
          </div>
          <!-- /.box -->
        </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->