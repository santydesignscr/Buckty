<div class="player">
    <div class="mediaplayer">
        <video_play width="800" height="400" controls="controls" autoplay>
            <?php /*<source type="<?= $item->file_mime;?>" src="<?= $site->site_url.'userfile/'.$item->hash;?>" />*/ ?>
            <object width="800" height="400" type="application/x-shockwave-flash" data="<?= $site->site_url;?>assets/js/player/flashmediaelement.swf">
                <param name="movie" value="<?= $site->site_url;?>assets/js/player/flashmediaelement.swf" />
                <param name="flashvars" value="controls=true&file=<?= $site->site_url.'userfile/'.$item->hash;?>" />
                <img src="myvideo.jpg" width="320" height="240" title="No video playback capabilities" />
            </object>
        </video_play>
    </div>
</div>
<script type="text/javascript">
    jQuery(window).load(function(){
        jQuery('video').mediaelementplayer();
    });
</script>