<form onsubmit="return Buckty.loadDb($(this));">
    <div class="field">
        <label for="site_url">Site url: Must end with "/" (slash)</label>
        <input type="text" id="site_url" name="site_url" class="controlinput"
               placeholder="Eg: https://Buckty.com/" required/>
    </div>
    <div class="field">
        <label for="site_folder">Site path: Add folder name if the site is under a subfolder, if not inside subfolder then leave empty.</label>
        <input type="text" id="site_folder" name="site_folder" class="controlinput"
               placeholder="Eg: https://Buckty.com/sub -> path: sub" />
    </div>
    <div class="field">
        <label for="site_name">Site name</label>
        <input type="text" id="site_name" name="site_name" class="controlinput"
               placeholder="Buckty - cloud script" required/>
    </div>
    <div class="field">
        <label for="site_admin_email">Site Admin email address</label>
        <input type="email" id="site_admin_email" name="site_admin_email" class="controlinput"
               placeholder="admin@yoursite.com" required/>
    </div>
    <div class="field">
        <label for="site_keywords">Site Keywords: devided by comma (,)</label>
        <input type="text" id="site_keywords" name="site_keywords" class="controlinput" placeholder="Buckty,cloud host, file sharing" required/>
    </div>

    <div class="field">
        <label for="site_description">Site description</label>
        <input type="text" id="site_description" name="site_description" class="controlinput" placeholder="Describe your site for meta tags" required/>
    </div>
    <div class="field">
        <label for="site_home_tagline">Site home tagline : Big heading</label>
        <input type="text" id="site_home_tagline" name="site_home_tagline" class="controlinput" placeholder="Big heading for homepage" required/>
    </div>
    <div class="field">
        <label for="site_home_description">Site home description: homepage</label>
        <input type="text" id="site_home_description" name="site_home_description" class="controlinput" placeholder="Description for homepage" required/>
    </div>
    <div class="field">
        <label for="site_upload_limit">Site Upload limit (bytes): Total space for every user. convert in bytes here : <a href="http://www.whatsabyte.com/P1/byteconverter.htm">Convert</a></label>
        <input type="text" id="site_upload_limit" name="site_upload_limit" class="controlinput" placeholder="100000" required/>
    </div>
    <div class="field">
        <label for="site_max_file_size">Site max file size (bytes): Maximum size allowed for a file to be uploaded, <a href="http://www.whatsabyte.com/P1/byteconverter.htm">Convert</a></label>
        <input type="text" id="site_max_file_size" name="site_max_file_size" class="controlinput" placeholder="2000000" required/>
    </div>
    <div class="field">
        <label for="site_allowed_extensions">Allowed file extensions devided by comma(,) or use * for all types of file (* is not recommended).</label>
        <input type="text" id="site_allowed_extensions" name="site_allowed_extensions" class="controlinput" placeholder="jpeg,jpg.png,zip,pdf,doc,docx,js,php,html"/>
    </div>
    <div class="field">
        <label for="site_blacklist_extensions">Blackilist file extensions devided by comma(,) or leave it empty</label>
        <input type="text" id="site_blacklist_extensions" name="site_blacklist_extensions" class="controlinput" placeholder="jpeg,jpg.png,zip,pdf,doc,docx,js,php,html"/>
    </div>
    <div class="field">
        <label for="site_disqus_shortname">Disqus shortname for comment section: register here and get it: (not required yet but it is recommended)<a href="https://disqus.com/admin/signup/">Disqus.com/admin/signup</a></label>
        <input type="text" id="site_disqus_shortname" name="site_disqus_shortname" class="controlinput" placeholder="buckty"/>
    </div>
    <hr>
    <h1 class="section_title">Mail settings: Important</h1>
    <span class="describe">Recommended Provider (tested): <a href="https://www.smtp2go.com/" target="_blank">smtp2go.com</a> , important for emails to work.</span>
    <div class="field">
        <label for="site_smtp_host">Smtp host</label>
        <input type="text" id="site_smtp_host" name="site_smtp_host" class="controlinput" placeholder="Smtp host" required/>
    </div>
    <div class="field">
        <label for="site_smtp_port">Smtp port</label>
        <input type="text" id="site_smtp_port" name="site_smtp_port" class="controlinput" placeholder="Smtp port" required/>
    </div>
    <div class="field">
        <label for="site_smtp_user">Smtp user</label>
        <input type="text" id="site_smtp_user" name="site_smtp_user" class="controlinput" placeholder="Smtp user" required/>
    </div>
    <div class="field">
        <label for="site_smtp_password">Smtp user</label>
        <input type="password" id="site_smtp_password" name="site_smtp_password" class="controlinput" placeholder="Smtp password" required/>
    </div>
    <div class="field">
        <div class="loader"></div>
        <button type="submit" class="button blue"><i class="fa fa-check"></i><span>Validate</span>
        </button>
        <button class="button primary" onclick="Buckty.loadInfo();"><i class="fa fa-backward"></i><span>Back</span></button>
    </div>
</form>