<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<section class="content">
      <!-- /.row -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Files</h3>
              <div class="box-tools">
                  <form onsubmit="return Buckty.file.search($(this));">
                <div class="input-group input-group-sm" style="width: 150px;">
                  <input type="text" name="file_search" class="form-control pull-right" placeholder="Search">

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
                  <th>Folder Name</th>
                  <th>Preview</th>
                  <th>Date</th>
                  <th>Hash</th>
                  <th>Owner</th>
                  <th>Action</th>
                </tr>
                  </thead>
                  <tbody id="appendfiles">
            <?php
             /*
              *  Get folder list
              *  $folders = array();
              *
              */
              foreach($files as $file) {
                  echo '<tr>';
                  echo '<td><i class="fa fa-file"></i></td>';
                  echo '<td>'.$file->file_name.'</td>';
                  echo '<td><a href="'.$site->site_url.'shared/file/'.$file->hash.'" data-no-ajax="true" target="_blank"><span class="label label-success">View</span></a></td>';
                  echo '<td>'.$file->file_date.'</td>';
                  echo '<td>'.$file->hash.'</td>';
                  echo '<td>'.$file->email.'</td>';
                  echo '<td><div class="btn-group">
                              <button type="button" onclick="Buckty.file.delete($(this));"  data-id="'.$file->hash.'" class="btn btn-danger">Delete</button>
                            </div></td>';
                  echo '</tr>';
              }
            ?>
</tbody></table>
</div>
              <div class="box-footer clearfix">
                  <?php echo $this->pagination->create_links();?>
              </div>
<!-- /.box-body -->
</div>
<!-- /.box -->
</div>
</div></section>
</div>