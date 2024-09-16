<!-- =============================================== -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo $content;?>
          </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Settings</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- form start -->
            <form id="settings" role="form" lpformnum="1">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Site Settings</h3>
                    </div>
                    <!-- /.box-header -->
                        <div class="box-body">
                            <div class="col-md-6">
                            <div class="form-group">
                                <label for="siteTitle">Site Title</label>
                                <input type="text" class="form-control" name="site_name" id="siteTitle" placeholder="Site Title" value="<?php echo !empty($site->site_name) ? $site->site_name: ''; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="SiteUrl">Site Url</label>
                                <input type="text" name="site_url" class="form-control" id="SiteUrl" placeholder="Site Url" value="<?php echo !empty($site->site_url) ? $site->site_url: ''; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="SiteDescription">Site description</label>
                                <input type="text" name="site_description" class="form-control" id="SiteDescription" placeholder="Site Description" value="<?php echo !empty($site->site_description) ? $site->site_description: ''; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="SiteKeywords">Site keywords</label>
                                <input type="text" name="site_keywords" class="form-control" id="SiteKeywords" placeholder="Site Keywords" value="<?php echo !empty($site->site_keywords) ? $site->site_keywords: '';?>" />
                            </div>
                            <div class="form-group">
                                <label for="SiteHomeTaglin">Homepage Tagline</label>
                                <input type="text" name="site_home_tagline" class="form-control" id="SiteHomeTagline" placeholder="Site Homepage tagline" value="<?php echo !empty($site->site_home_tagline) ? $site->site_home_tagline: ''; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="SiteHomeDescription">Homepage description</label>
                                <input type="text" name="site_home_description" class="form-control" id="SiteHomeDescription" placeholder="Site Homepage description" value="<?php echo !empty($site->site_home_description) ? $site->site_home_description: '';?>" />
                            </div>
                            <div class="form-group">
                                <label for="admin_email">Site Admin Email</label>
                                <input type="email" name="admin_email" class="form-control" id="admin_email" placeholder="admin@admin.com" value="<?php echo !empty($site->admin_email) ? $site->admin_email: ''; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="allowed_extensions">Allowed Extensions<small>(jpg,png,zip)</small></label>
                                <input type="text" name="allowed_extensions" class="form-control" id="allowed_extensions" placeholder="png,jpg,jpeg,dmg,xml,doc" value="<?php echo !empty($site->allowed_extensions) ? $site->allowed_extensions: ''; ;?>" />
                            </div>
                            <div class="form-group">
                                    <label for="blacklist_extensions">Blacklist Extensions<small>(jpg,png,zip)</small></label>
                                    <input type="text" name="blacklist_extensions" class="form-control" id="blacklist_extensions" placeholder="png,jpg,jpeg,dmg,xml,doc" value="<?php echo !empty($site->blacklist_extensions) ? $site->blacklist_extensions: ''; ;?>" />
                            </div>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                                <label for="upload_limit">Upload Limit :<small id="updated_qoute"></small></label>
                                <input type="text" name="upload_limit" class="form-control" id="upload_limit" placeholder="Eg: 20971520" value="<?php echo !empty($site->upload_limit) ? $site->upload_limit: ''; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="maxFileSize">Max file size:<small id="updated_qoute"></small></label>
                                <input type="text" name="max_file_size" class="form-control" id="upload_limit" placeholder="Eg: 20971520" value="<?php echo !empty($site->site_info['max_size']) ? $site->site_info['max_size']: '';?>" />
                            </div>
                            <div class="form-group">
                                <label>Email Activation</label>
                                <select name="email_activation" class="form-control">
                                    <option value="1" <?php echo (!empty($site->email_activation) && $site->email_activation) == '1' ? 'selected': '';?>>Yes</option>
                                    <option value="0" <?php echo (!empty($site->email_activation) && $site->email_activation) == '0' ? 'selected': '';?>>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Activate registration</label>
                                <select name="register_active" class="form-control">
                                    <option value="1" <?php echo (!empty($site->register_active) && $site->register_active) == '1' ? 'selected': '';?>>Yes</option>
                                    <option value="0" <?php echo (!empty($site->register_active) && $site->register_active) == '0' ? 'selected': '';?>>No</option>
                                </select>
                            </div>

                        <div class="form-group">
                            <label for="ad_780">Banner: 728x90</label>
                            <textarea name="ad_780" id="ad_780" class="form-control" placeholder="Advertise code"><?php echo !empty($site->ad_780) ? $site->ad_780:''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="ad_320">Banner: 320x100</label>
                            <textarea name="ad_320" class="form-control" id="ad_320" placeholder="Advertise code"><?php echo !empty($site->ad_320) ? $site->ad_320:''; ?></textarea>
                        </div>
                      </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right">Save Settings</button>
                            </div>
            </form>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->