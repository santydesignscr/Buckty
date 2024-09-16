<div class="addition_info">
    <p style="text-align: center;"><span style="font-size:14px"><strong>Welcome to Buckty</strong></span></p>

    <p style="text-align: center;"><span style="font-size:12px"><strong>Please read this carefuly before proceeding to the installation</strong></span></p>

    <p style="text-align: justify;"><span style="font-size:12px; line-height:19.2px">In between the forward steps you will be asked to provide <strong>Site related information which is a &quot;required&quot;</strong> for database and functionality purpose.&nbsp;</span></p>

    <p style="text-align: justify;">https is required<strong>&nbsp;for&nbsp;dropbox api to work! ,&nbsp;</strong>else if you want to use only&nbsp;<strong>http&nbsp;</strong>then please don&#39;t use dropbox api, <strong>you can use drive api with http.&nbsp;</strong>You can setup Social api&#39;s and Google drive , dropbox api inside admin dashboard after the installation is over.</p>

    <p style="text-align: justify;">Also please setup your php.ini according to the file size you allow for your users , if you server does not support large file sizes and you provide large &quot;Max file size&quot; value , it will fail the file to be uploaded. &nbsp;You can change your php.ini according to what you require.</p>

<pre style="text-align: justify;"><span style="color:#808080">
        <code>
            upload_max_filesize = 200M<br>
            post_max_size = 200M<br>
            memory_limit: 820M<br>
        </code></span></pre>

    <p style="text-align: justify;">&nbsp;</p>


    <p style="text-align: justify;">Or you can also change them inside .htaccess (if supported) Below rewrite rules inside .htaccess:&nbsp;</p>

<pre style="text-align: justify;">
<span style="font-family:courier new,courier,monospace">
        <code>
            php_value upload_max_filesize 200M<br>
            php_value post_max_size 200M<br>
            php_value memory_limit: 820M<br>
        </code></span></pre>

    <p style="text-align: justify;">Also make sure that mode_rewrite&nbsp;<strong>mod_rewrite&nbsp;</strong>is enabled &nbsp;for your server , if it&#39;s not you can enable it by uncommeting the following line inside httpd.conf file:</p>

<pre>
<span style="color:#808080">
    <strong>Change:</strong>
    <code>
        #LoadModule rewrite_module modules/mod_rewrite.so #AddModule mod_rewrite.c
    </code>
</span></pre>

    <p><span style="color:#808080"><strong>To:</strong></span></p>

    <p><span style="color:#808080"><code>LoadModule rewrite_module modules/mod_rewrite.so #AddModule mod_rewrite.c</code></span></p>

    <p>&nbsp;</p>

    <p>Smtp settings:&nbsp;</p>

    <p><strong>Please use some smtp service provider to make work emails for Buckty.&nbsp;</strong>The service we used and tested is : <a href="https://smtp2go.com" target="_blank">smtp2go.com</a>&nbsp;. By using the smtp service provider the emails will work without any problem. if any problem occures is please get in touch with us by contact form.</p>

    <p style="text-align: justify;"><strong>Please provide valid database details and valid databse name in forward steps.&nbsp;</strong></p>

    <p style="text-align: justify;"><strong>The above system information is a must. if you are ready to proceed with installation please click the below button and fill in the information we ask for.</strong></p>

</div>
<div class="field">
    <button onclick="Buckty.load();" class="button blue"><i class="fa fa-check"></i>Proceed</button>
</div>