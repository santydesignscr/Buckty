<?php $this->load->view('pages/header');?>
        <div class="pageContainer">
            <div class="title"><h1 class="page_title"><?= $content->page_name;?></h1></div>
            <div class="pagebody">
                <?= $content->page_body;?>
            </div>
        </div>
<?php $this->load->view('extras/pageslinks');?>
<?php $this->load->view('pages/footer');?>