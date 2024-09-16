<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Add page
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Pages</a></li>
            <li class="active">Add</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">Page Body</h3>
                        <!-- tools box -->
                        <div class="pull-right box-tools">
                            <button class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse"><i class="fa fa-minus"></i></button>
                        </div><!-- /. tools -->
                    </div><!-- /.box-header -->
                    <form onsubmit="return Buckty.page.publish($(this));">
                    <div class="box-body pad">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pageTitle">Page Title</label>
                                    <input type="text" class="form-control" name="pageTitle" id="pageTitle" placeholder="Page title eg: My Page Title" required/>
                                </div>
                                <div class="form-group">
                                    <label for="pageSlug">Page Slug</label>
                                    <input type="text" class="form-control" name="pageSlug" id="pagSlug"  placeholder="Page slug eg: my_page_title" required/>
                                </div>
                                <div class="form-group">
                                    <label for="inFooter">Visible in footer ?</label>
                                    <select  class="form-control" name="inFooter" id="inFooter">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pageStatus">Page Status</label>
                                    <select class="form-control" id="pageStatus" name="pageStatus">
                                        <option value="1">Published</option>
                                        <option value="2">Not Published</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="pagePosition">Page Position (Let it be different from other pages)</label>
                                    <input type="text" name="pagePosition" class="form-control" id="pagePosition" placeholder="Page position eg: 3"/>
                                </div>
                                <!--<div class="form-group">
                                    <label for="inSitemap">Available in sitemap ?</label>
                                    <select  class="form-control" name="inSitemap" id="inSitemap">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>-->
                            </div>
                            <div class="col-lg-9">
                                <div class="form-group">
                                    <textarea id="editor1" name="pagetext" rows="10" cols="80" required></textarea>
                                </div>
                            </div>

                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-info pull-right">Publish</button>
                    </div>
                    </form>
                </div><!-- /.box -->
            </div><!-- /.col-->
        </div><!-- ./row -->
    </section><!-- /.content -->
    <script type="text/javascript">
        $(function(){
            var editor = CKEDITOR.replace('editor1');
        });
    </script>
</div><!-- /.content-wrapper -->