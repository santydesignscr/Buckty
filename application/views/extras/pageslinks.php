<div class="main_links">
    <ul>
        <?php if(!empty($pages)): foreach($pages as $page):
        echo '<li><a data-no-ajax="true" href="'.$site->site_url.'page/'.$page->page_slug.'"><span>'.$page->page_name.'</span></a></li>';
            endforeach; endif; ?>
        <li class="copyright"><span>2016 ICODEAPPS</span></li>
    </ul>
</div>