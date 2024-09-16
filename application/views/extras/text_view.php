<div class="text_view">
    <div class="text_container">
       <?php
        $content =  file_get_contents($full_path);

            echo '<pre>';
        
            echo html_escape($content);
        
            echo '</pre>';
        ?>
    </div>
</div>