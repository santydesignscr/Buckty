    <div class="actions">
        <ul>
            <li class="multiple_menu">
                    <a href="javascript:void(0);" id="removeFiles" class="_item tooltip-top" data-tooltip="<?php _tran($trans->Delete_Files);?>">
                    <i class="fa fa-trash-o"></i>
                </a>
                <div class="context_absolute">
                    <ul>

                    </ul>
                </div>
            </li>
            <?php if($in == 'n'):?>
            <li class="multiple_menu">
                <a href="javascript:void(0);" onclick="Buckty.MoveFiles(0,0);" class="_item tooltip-top" data-tooltip="<?php _tran($trans->Move_Files);?>">
                    <i class="fa fa-folder-open"></i>
                </a>
            </li>
            <?php endif;?>
            <?php
          if(!empty($_COOKIE['view'])): if($_COOKIE['view'] == 'grid' || $_COOKIE['view'] == ''): ?>
                <li>
                    <a href="javascript:void(0);" id="change_view" view="list" class="drop_m _item">
                        <i class="fa fa-th"></i>
                        <span class="side_text"><?php _tran($trans->View);?></span>
                    </a>
                </li>
                <?php else: ?>
                    <li>
                        <a href="javascript:void(0);" id="change_view" view="grid" class="drop_m _item">
                            <i class="fa fa-list"></i>
                            <span class="side_text"><?php _tran($trans->View);?></span>
                        </a>
                    </li>
                    <?php endif; else: ?>
                        <li>
                            <a href="javascript:void(0);" id="change_view" view="list" class=" _item">
                                <i class="fa fa-th"></i>
                                <span class="side_text"><?php _tran($trans->View);?></span>
                            </a>
                        </li>
                        <?php endif;?>
                            <li>
                                <a href="javascript:void(0);" class="drop_m _item" data-drop="sort">
                                    <i class="fa fa-sort"></i>
                                    <span class="side_text"><?php _tran($trans->Sort);?></span></a>
                            </li>
        </ul>
    </div>
    <?php if($in == 'n'):?>
    <div id="sort" class="drop_down">
        <ul>
            <li><a onclick="Buckty.Order('file_date','desc');" class="drp-li"><span class="side_text"><?php _tran($trans->Date);?></span></a></li>
            <li><a onclick="Buckty.Order('file_name','desc');" class="drp-li"><span class="side_text"><?php _tran($trans->Name_desc);?></span></a></li>
            <li><a onclick="Buckty.Order('file_name','asc');" class="drp-li"><span class="side_text"><?php _tran($trans->Name_asc);?></span></a></li>
            <li><a onclick="Buckty.Order('file_size','desc');" class="drp-li"><span class="side_text"><?php _tran($trans->Size_Bigger_First);?></span></a></li>
            <li><a onclick="Buckty.Order('file_size','asc');" class="drp-li"><span class="side_text"><?php _tran($trans->Size_Small_First);?></span></a></li>
        </ul>
    </div>
    <?php endif;?>