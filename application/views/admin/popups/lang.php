<div class="modal fade" id="languages" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Add New Language</h4>
            </div>
            <form id="make_language">
                <div class="modal-body">
                    <div id="success_o_error" style="display:none;" class="alert alert-success alert-dismissible">
                        ciao
                    </div>
                    <div class="form-group">
                        <label for="langName" class="control-label">Language Name</label>
                        <input type="text" class="form-control" id="langName" name="langName" placeholder="Language Name" />
                    </div>
                    <div class="form-group">
                        <label for="langSlug" class="control-label">Language Slug</label>
                        <input type="text" class="form-control" id="langSlug" name="langSlug" placeholder="Language Slug" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="lang_close" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" data-no-ajax="true" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>