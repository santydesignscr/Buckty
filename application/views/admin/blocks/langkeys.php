<div id="keys" class="collapse row">
    <div class="col-xs-12">
        <div class="box overflow">
            <div class="box-header">
                <h3 class="box-title">Manage Keys</h3>
            </div>
            <div id="keysappend" class="keyscontainer">
                <?php foreach($keys as $key){
                    $keyname = str_replace(' ','_',$key['key_name']);
                echo '<div class="margin pull-left key"><span class="label label-primary">'.$key['key_name'].'</span><i class="fa fa-close" onclick="Buckty.key.remove(\''.$keyname.'\')"></i></div>';
                } ?>
            </div><br>
            <div id="add_key_form" class="box-footer collpase">
                <form id="add_key">
                    <div class="form-group">
                        <label for="langName" class="control-label">Add Keys split by comma (eg: key1,key2,key3)</label>
                        <input type="text" class="form-control" id="keyNames" name="keyNames" placeholder="Key Names" />
                    </div>
                    <button type="submit" class="btn btn-p\rimary">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>