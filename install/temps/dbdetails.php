<div id="formDb" class="form_container">
    <form onsubmit="return Buckty.loadDatabase($(this));">
        <div class="field">
            <label for="hostname">Hostname</label>
            <input type="text" id="hostname" name="hostname" class="controlinput" placeholder="Hostname" required/>
        </div>
        <div class="field">
            <label for="username">Mysql username</label>
            <input type="text" id="username" name="username" class="controlinput" placeholder="Mysql username" required/>
        </div>
        <div class="field">
            <label for="password">Mysql password</label>
            <input type="password" id="password" name="password" class="controlinput" placeholder="Mysql password" />
        </div>
        <div class="field">
            <label for="database">Database</label>
            <input type="text" id="database" name="database" class="controlinput" placeholder="Database name" required/>
        </div>
        <div class="field">
            <div class="loader"></div>
            <input type="hidden" name="site_url" value="<?= $_POST['site_url'];?>"/>
            <input type="hidden" name="site_folder" value="<?= $_POST['site_folder'];?>"/>
            <input type="hidden" name="site_name" value="<?= $_POST['site_name'];?>"/>
            <input type="hidden" name="site_admin_email" value="<?= $_POST['site_admin_email'];?>"/>
            <input type="hidden" name="site_keywords" value="<?= $_POST['site_keywords'];?>"/>
            <input type="hidden" name="site_description" value="<?= $_POST['site_description'];?>"/>
            <input type="hidden" name="site_upload_limit" value="<?= $_POST['site_upload_limit'];?>"/>
            <input type="hidden" name="site_max_file_size" value="<?= $_POST['site_max_file_size'];?>"/>
            <input type="hidden" name="site_allowed_extensions" value="<?= $_POST['site_allowed_extensions'];?>"/>
            <input type="hidden" name="site_blacklist_extensions" value="<?= $_POST['site_blacklist_extensions'];?>"/>
            <input type="hidden" name="site_home_tagline" value="<?= $_POST['site_home_tagline'];?>"/>
            <input type="hidden" name="site_home_description" value="<?= $_POST['site_home_description'];?>"/>
            <input type="hidden" name="site_smtp_host" value="<?= $_POST['site_smtp_host'];?>"/>
            <input type="hidden" name="site_smtp_port" value="<?= $_POST['site_smtp_port'];?>"/>
            <input type="hidden" name="site_smtp_user" value="<?= $_POST['site_smtp_user'];?>"/>
            <input type="hidden" name="site_smtp_password" value="<?= $_POST['site_smtp_password'];?>"/>
            <input type="hidden" name="site_disqus_shortname" value="<?= $_POST['site_disqus_shortname'];?>"/>
            <button type="submit" class="button blue"><i class="fa fa-check"></i><span>Validate</span></button>
        </div>
    </form>
</div>