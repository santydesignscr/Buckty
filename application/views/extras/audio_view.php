<div class="audio player">
    <div class="mediaplayer">
        <audio width="400" height="30" controls="controls" type="<?= $item->file_mime;?>" src="<?= $site->site_url.'userfile/'.$item->hash;?>" autoplay></audio>
    </div>
</div>
<script type="text/javascript">
    jQuery(window).load(function(){
        jQuery('audio').mediaelementplayer();
    });
</script>