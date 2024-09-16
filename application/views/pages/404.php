<?php $this->load->view('pages/header');?>
    <div class="pageContainer">
        <div class="notfound">
            <h1 class="not_found">404 NOT FOUND</h1>
            <p>The page you are looking for was not found. go back to <a href="<?= $site->site_url;?>" class="link">Homepage</a></p>
        </div>
    </div>
<?php $this->load->view('pages/footer');?>