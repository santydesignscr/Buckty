<div class="viewer_container popup">
    <div class="overlay">
    </div>
        <div class="view_contain">
            <div class="header">
                <div class="title">
                    <?php 
                    echo $type == 'file'? 
                    '<div class="file-icon file-icon-xl ignore" data-type="'.$item->file_type.'"></div>': 
                    '<i class="fa fa-folder-o"></i>';?>
                        <span class="text"><?php echo $type == 'file' ? $item->file_name: $item->folder_name; ?></span>
                </div>
                <div class="actions">
                    <ul class="action_ul">
                        <li class="list_item">
                            <a onclick="Buckty.details('<?= $hash; ?>','<?= $type;?>')" class="action tooltip-bottom" data-tooltip="<?php _tran($trans->Details);?>">
                                <i class="fa fa-info-circle"></i>
                                <span class="text"><?php _tran($trans->Details);?></span>
                            </a>
                        </li>
                        <li class="list_item">
                            <a onclick="Buckty.MoveFiles(0, 0);" class="action tooltip-bottom" data-tooltip="<?php _tran($trans->Move);?>">
                                <i class="fa fa-folder"></i>
                                <span class="text"><?php _tran($trans->Move);?></span>
                            </a>
                        </li>
                        <li class="list_item">
                            <a onclick="Buckty.DownloadSingle('<?= $hash; ?>','<?= $type; ?>');" class="action tooltip-bottom" data-tooltip="<?php _tran($trans->Download);?>">
                                <i class="fa fa-download"></i>
                                <span class="text"><?php _tran($trans->Download);?></span>
                            </a>
                        </li>
                        <li class="list_item">
                            <a onclick="$(this).parents('.viewer_container').remove();" class="action tooltip-bottom" data-tooltip="<?php _tran($trans->Close);?>">
                                <i class="fa fa-close"></i>
                                <span class="text"><?php _tran($trans->Close);?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="container_box">
                <?php
                    switch($type){
                        case 'file':
                            $mime = explode('/',$item->file_mime);
                            switch($mime[0]){
                                case 'image':
                                   $this->load->view('extras/image_preview'); 
                                break;
                                case 'text';
                                    $this->load->view('extras/text_view'); 
                                break;
                                case 'video';
                                    $this->load->view('extras/video_view'); 
                                break;
                                case 'audio';
                                    $this->load->view('extras/audio_view'); 
                                break;
                                case 'application';
                                     if(in_array($item->file_mime,$texttypes)):
                                     $this->load->view('extras/document_view'); 
                                    else:
                                    $this->load->view('extras/undefined_view');
                                    endif;
                                break;
                            }
                            break;
                        case 'folder':
                            
                    }
                ?>
            </div>
        </div>
</div>