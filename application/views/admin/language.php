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
                <a class="btn btn-app" class="btn btn-primary" data-toggle="modal" data-target="#languages">
                    <i class="fa fa-plus"></i> Add Language
                </a>
                <a data-target="#keys" class="btn btn-app" class="btn btn-primary" data-toggle="collapse">
                    <i class="fa fa-gear"></i> Manage Keys
                </a>
            </div>
        </div>
<?php 
$this->load->view('admin/blocks/langkeys');
        
$this->load->view('admin/blocks/langs');
?>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->