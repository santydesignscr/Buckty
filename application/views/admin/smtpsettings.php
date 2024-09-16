<!-- =============================================== -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo $content;?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Smtp Settings</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-9 margin_center">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Smtp Settings</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form onsubmit="return Buckty.saveSmtp($(this));" role="form" lpformnum="1">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="smtpHost">Smtp host eg: <b>ssl://mail.smtp2go.com</b></label>
                                <input type="text" class="form-control" name="smtp_host" id="smtpHost" placeholder="Smtp host" value="<?php echo !empty($site->smtp_host) ? $site->smtp_host: ''; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="smtpPort">Smtp Port eg: <b>8465</b></label>
                                <input type="text" class="form-control" name="smtp_port" id="smtpPort" placeholder="Smtp port" value="<?php echo !empty($site->smtp_port) ? $site->smtp_port: ''; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="smtpUser">Smtp User <b>username@example.com</b></label>
                                <input type="text" class="form-control" name="smtp_user" id="smtpPort" placeholder="Smtp user" value="<?php echo !empty($site->smtp_user) ? $site->smtp_user: ''; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="smtpPort">Smtp Password<b>Your smtp account password</b></label>
                                <input type="text" class="form-control" name="smtp_password" id="smtpPort" placeholder="Smtp password" value="<?php echo !empty($site->smtp_password) ? $site->smtp_password: ''; ?>" />
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Save Settings</button>
                        </div>
                    </form>
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