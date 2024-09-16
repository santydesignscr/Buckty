<!-- =============================================== -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <?php echo $content;?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Social Settings</a></li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-9 margin_center">
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-no-ajax="true" data-toggle="tab" aria-expanded="true">Facebook login</a></li>
                        <li class=""><a href="#tab_2" data-no-ajax="true" data-toggle="tab" aria-expanded="false">Google Auth / Drive</a></li>
                        <li class=""><a href="#tab_3" data-no-ajax="true" data-toggle="tab" aria-expanded="false">Twitter</a></li>
                        <li class=""><a href="#tab_4" data-no-ajax="true" data-toggle="tab" aria-expanded="false">Dropbox</a></li>
                        <li class=""><a href="#tab_5" data-no-ajax="true" data-toggle="tab" aria-expanded="false">Disqus Comments</a></li>
                        <li class=""><a href="#tab_6" data-no-ajax="true" data-toggle="tab" aria-expanded="false">Pexels Api</a></li>
                    </ul>
                    <form id="Socialsettings" role="form" lpformnum="1">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <div class="box">
                                    <!-- /.box-header -->
                                    <!-- form start -->

                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="fbapiKey">Facebook Api key</label>
                                            <input type="text" class="form-control" name="soc[facebook][id]" id="fbapiKey" placeholder="facebook api key" value="<?php echo isset($social['facebook']['id']) ? $social['facebook']['id']: '';?>" />
                                        </div>
                                        <div class="form-group">
                                            <label for="fbapiSecret">Facebook Api Secret Key</label>
                                            <input type="text" name="soc[facebook][secret]" class="form-control" id="fbapiSecret" placeholder="Api Secret Key" value="<?php echo isset($social['facebook']['secret']) ? $social['facebook']['secret']: '';?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Activate</label>
                                            <select name="soc[facebook][activation]" class="form-control">
                                                <option value="1" <?php echo isset($social['facebook']['activation']) && $social['facebook']['activation'] == 1 ? 'selected': '';?>>Yes</option>
                                                <option value="0" <?php echo isset($social['facebook']['activation']) && $social['facebook']['activation'] == 0 ? 'selected': '';?>>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>

                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_2">
                                <div class="box">
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="fbapiKey">Google Api key</label>
                                            <input type="text" class="form-control" name="soc[google][id]" id="fbapiKey" placeholder="Google api key" value="<?php echo isset($social['google']['id']) ? $social['google']['id']: '';?>" />
                                        </div>
                                        <div class="form-group">
                                            <label for="fbapiSecret">Google Api Secret Key</label>
                                            <input type="text" name="soc[google][secret]" class="form-control" id="fbapiSecret" placeholder="Api Secret Key" value="<?php echo isset($social['google']['secret']) ? $social['google']['secret']: '';?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Activate login</label>
                                            <select name="soc[google][activation]" class="form-control">
                                                <option value="1" <?php echo isset($social['google']['activation']) && $social['google']['activation'] == 1 ? 'selected': '';?>>Yes</option>
                                                <option value="0" <?php echo isset($social['google']['activation']) && $social['google']['activation'] == 0 ? 'selected': '';?>>No</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Google drive activate</label>
                                            <select name="soc[google][drive_activation]" class="form-control">
                                                <option value="1" <?php echo isset($social['google']['drive_activation']) && $social['google']['drive_activation'] == 1 ? 'selected': '';?>>Yes</option>
                                                <option value="0" <?php echo isset($social['google']['drive_activation']) && $social['google']['drive_activation'] == 0 ? 'selected': '';?>>No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_3">
                                <div class="box">
                                    <!-- /.box-header -->
                                    <!-- form start -->
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="twitterapiKey">Api key</label>
                                            <input type="text" class="form-control" name="soc[twitter][id]" id="twitterapiKey" placeholder="twitter api key" value="<?php echo !empty($social['twitter']) ? $social['twitter']['id']: '';?>" />
                                        </div>
                                        <div class="form-group">
                                            <label for="twitterapiSecret">Api Secret Key</label>
                                            <input type="text" name="soc[twitter][secret]" class="form-control" id="twitterapiSecret" placeholder="Api Secret Key" value="<?php echo !empty($social['twitter']) ? $social['twitter']['secret']: '';?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Activate login</label>
                                            <select name="soc[twitter][activation]" class="form-control">
                                                <option value="1" <?php echo isset($social['twitter']['activation']) && $social['twitter']['activation'] == 1 ? 'selected': '';?>>Yes</option>
                                                <option value="0" <?php echo isset($social['twitter']['activation']) && $social['twitter']['activation'] == 0 ? 'selected': '';?>>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>

                            </div>
                            <!-- /.tab-pane -->
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_4">
                                <div class="box">
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="dropapiKey">Api key</label>
                                            <input type="text" class="form-control" name="soc[dropbox][id]" id="dropapiKey" placeholder="Dropbox api key" value="<?php echo isset($social['dropbox']['id']) ? $social['dropbox']['id']: '';?>" />
                                        </div>
                                        <div class="form-group">
                                            <label for="dropapiSecret">Api Secret Key</label>
                                            <input type="text" name="soc[dropbox][secret]" class="form-control" id="dropapiSecret" placeholder="Api Secret Key" value="<?php echo isset($social['dropbox']['secret']) ? $social['dropbox']['secret']: '';?>" />
                                        </div>
                                        <div class="form-group">
                                            <label for="dropappName">App name</label>
                                            <input type="text" name="soc[dropbox][appname]" class="form-control" id="dropappName" placeholder="App name" value="<?php echo isset($social['dropbox']['appname']) ? $social['dropbox']['appname']: '';?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Activate dropbox api</label>
                                            <select name="soc[dropbox][activation]" class="form-control">
                                                <option value="1" <?php echo isset($social['dropbox']['activation']) && $social['dropbox']['activation'] == 1 ? 'selected': '';?>>Yes</option>
                                                <option value="0" <?php echo isset($social['dropbox']['activation']) && $social['dropbox']['activation'] == 0 ? 'selected': '';?>>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>

                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_5">
                                <div class="box">
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="dropapiKey">Disqus shortname eg: buckty</label>
                                            <input type="text" class="form-control" name="soc[disqus][shortname]" id="disqusShortname" placeholder="Disqus Shortname" value="<?php echo isset($social['disqus']['shortname']) ? $social['disqus']['shortname']: '';?>" />
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>

                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_6">
                                <div class="box">
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <p>
                                            Go here to get more details about pexels api: <a href="https://pexels.com/api/" target="_blank">Pexels.com</a><br>
                                            To get access to pexels API write them an email to api@pexels.com. Please explain how and where you want to integrate their photos.
                                        </p>
                                        <div class="form-group">
                                            <label for="pexels_key">Pexels api key</label>
                                            <input type="text" class="form-control" name="soc[pexels][key]" id="pexels_key" placeholder="Pexels api keys" value="<?php echo isset($social['pexels']['key']) ? $social['pexels']['key']: '';?>" />
                                        </div>
                                        <div class="form-group">
                                            <label for="pexels_keywords">Keywords for wallpapers</label>
                                            <input type="text" class="form-control" name="soc[pexels][keywords]" id="pexels_keywords" placeholder="Moutains,techonology,people,cars" value="<?php echo isset($social['pexels']['keywords']) ? $social['pexels']['keywords']: '';?>" />
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>

                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Update</button>
                        </div>
                    </form>
                    <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->