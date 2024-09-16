<?php $this->load->view('shared/header.php');?>
    <div class="viewer_container">
        <div class="formContainer">
            <div class="form">
              <div class="file_info">
                  <?php echo $type == 'file' ? '<i class="fa fa-file"></i>': '<i class="fa fa-folder"></i>';?>
                  <span class="name"><?php echo $type_item == 'file' ? $item->file_name: $item->folder_name; ?></span>
              </div>
            <form onsubmit="Buckty.checkPassword($(this));">
                <div class="field passwordinput">
                    <input type="password" name="password" placeholder="Enter the password"/>
                    <span class="info"></span>
                </div>
                <div class="field bt">
                    <div class="loader"></div><button type="submit" class="button blue">Validate</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <div class="side_bar">
        <div class="field_share">
            <div class="social-icon socialshare facebook tooltip-bottom" data-tooltip="Facebook" data-action="facebook" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-facebook"></i></div>

            <div class="social-icon socialshare google tooltip-bottom" data-tooltip="Google" data-action="google" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-google-plus"></i></div>
            <div class="social-icon socialshare twitter tooltip-bottom" data-tooltip="Twitter" data-action="twitter" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-twitter"></i></div>
            <div class="social-icon socialshare pinterest tooltip-bottom" data-tooltip="Pinterest" data-action="pinterest" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-pinterest"></i></div>
            <div class="social-icon socialshare tumblr tooltip-bottom" data-tooltip="Tumblr" data-action="tumblr" data-type="<?= $type;?>" data-hash="<?php echo $type == 'file' ? $item->hash: $item->folder_hash;?>"><i class="fa fa-tumblr"></i></div>
            <a onclick="Buckty.toggleComments();" class="closeComment"><i class="fa fa-close"></i></a>
        </div>
        <div class="comment_container">
            <div id="disqus_thread"></div>
        </div>
    </div>
<?php $this->load->view('shared/footer');?>