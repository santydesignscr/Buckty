<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo $content;?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="left">
                <a href="<?= base_url('admin/pages/add')?>" class="btn btn-app" class="btn btn-primary" >
                    <i class="fa fa-plus"></i> Add page
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?php echo 'Pages';?></h3>

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
                                <th>ID</th>
                                <th>Page name</th>
                                <th>Page slug</th>
                                <th>Page status</th>
                                <th>Page position</th>
                            </tr></thead>
                            <tbody id="appendusers">
                            <?php
                            if(!empty($pages)):
                                foreach($pages as $page) {
                                    echo '<tr>';
                                    echo '<td>'.$page->id.'</td>';
                                    echo '<td>'.$page->page_name.'</td>';
                                    echo '<td><a data-no-ajax="true" target="_blank" href="'.$site->site_url.'page/'.$page->page_slug.'">';
                                    echo $page->page_slug;
                                    echo '</a></td>';
                                    echo $page->page_status == 1 ? '<td><span class="label label-success">Published</span></td>':'<td><span class="label label-danger">Not published</span></td>';
                                    echo '<td>'.$page->page_position.'</td>';
                                    echo '<td><div class="btn-group">
                                          <a href="'.base_url('admin/pages/edit').'/'.$page->id.'" class="btn btn-primary">Edit</a>
                                          <a onclick="Buckty.page.removePage(\''.$page->id.'\')" class="btn btn-primary">delete</a>
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