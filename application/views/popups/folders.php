<div id="js_move_folder" class="mini_pop">
    <div class="overlay"></div>
    <div class="modal_container">
        <div class="modal">
            <div class="modal_header">
                <h1 class="modal_title">Select the folder</h1>
                <a href="javascript:void(0);" class="close"><i class="fa fa-remove"></i></a>
            </div>
            <div class="modal_content">
                <div class="list_container">
                     <div class="folder_container">
                        <ul class="ul_folders">
                            <?php
                                GetTree('0','move',$ignore);
                            ?>
                        </ul>
                      </div>
                </div>
                <div class="buttons_container margin">
                    <a class="primary button" onclick="Buckty.popup('js_move_folder','c');">Cancel</a>
                    <button class="button blue" id="moveButton" disabled type="submit">Move</button>
                </div>
            </div>
        </div>
    </div>
</div>