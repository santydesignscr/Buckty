<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?php echo $content;?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Language Name</th>
                            <th>Language Slug</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="filters">
                        <?php 
                  foreach($langs as $lang){  
                    echo '<tr id="lang_'.$lang->id.'">';
                    echo '<td>'.$lang->id.'</td>';
                    echo '<td>'.$lang->lang_title.'</td>';
                    echo '<td>'.$lang->lang_slug.'</td>';
                    echo $lang->default == 1 ? '<td><span class="label label-success">Default</span></td>': '<td><span class="label label-danger">Not Default</span></td>';
                    echo '<td><div class="btn-group">
                              <button type="button" onclick="Buckty.setLang(\''.$lang->id.'\');" class="btn btn-danger">Default</button>
                              <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                              </button>
                              <ul class="dropdown-menu" role="menu">
                                <li><a href="#" onclick="Buckty.getLangFields(\''.$lang->id.'\');"><i class="fa fa-pencil"></i>Edit</a></li>';
                    if($lang->default == 0) {
                        echo '<li><a href="#" onclick="Buckty.deleteLang(\'' . $lang->id . '\');"><i class="fa fa-trash"></i>Delete</a></li>';
                    }
                    echo '</ul>
                            </div></td>';
                    echo '</tr>';
                  }
                ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-6">
        <div class="box" id="lang_fields">
            <div class="box-header">
                <h3 class="box-title"><?php echo 'Translations'?></h3>
                <p>Select language to edit</p>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <form onsubmit="return Buckty.saveTranslation($(this));">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Key Name</th>
                            <th>Translation</th>
                        </tr>
                    </thead>

                    <tbody id="fields_append">

                    </tbody>

                </table>
                    <div class="form-group" style="margin-left:10px;">
                      <button type="submit" class="btn btn-info pull-righ" id="lang_update" style="display:none;"> Update</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>