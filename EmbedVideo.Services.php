<?php

# Build services list (may be augmented in LocalSettings.php)
$wgEmbedVideoServiceList = array(
    'dailymotion' => array(
        'url' => 'http://www.dailymotion.com/swf/$1'
    ),
    'divshare' => array(
        'url' => 'http://www.divshare.com/flash/video2?myId=$1',
    ),
    'edutopia' => array(
        'extern' =>
            '<object id="flashObj" width="$3" height="$4">' .
                '<param name="movie" value="http://c.brightcove.com/services/viewer/federated_f9?isVid=1&isUI=1" />' .
                '<param name="flashVars" value="videoId=$2&playerID=85476225001&domain=embed&dynamicStreaming=true" />' .
                '<param name="base" value="http://admin.brightcove.com" />' .
                '<param name="allowScriptAccess" value="always" />' .
                '<embed src="http://c.brightcove.com/services/viewer/federated_f9?isVid=1&isUI=1" ' .
                    'flashVars="videoId=$2&playerID=85476225001&domain=embed&dynamicStreaming=true" '.
                    'base="http://admin.brightcove.com" name="flashObj" width="$3" height="$4" '.
                    'seamlesstabbing="false" type="application/x-shockwave-flash" allowFullScreen="true" ' .
                    'allowScriptAccess="always" swLiveConnect="true" ' .
                    'pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">' .
                '</embed>' .
            '</object>',
        'default_width' => 326,
        'default_ratio' => 326/399,
    ),
    'funnyordie' => array(
        'url' =>
            'http://www.funnyordie.com/v1/flvideo/fodplayer.swf?file='.
            'http://funnyordie.vo.llnwd.net/o16/$1.flv&autoStart=false'
    ),
    'googlevideo' => array(
        'id_pattern'=>'%[^0-9\\-]%',
        'url' => 'http://video.google.com/googleplayer.swf?docId=$1'
    ),
    'interiavideo' => array(
        'url' => 'http://video.interia.pl/i/players/iVideoPlayer.05.swf?vid=$1',
    ),
    'interia' => array(
        'url' => 'http://video.interia.pl/i/players/iVideoPlayer.05.swf?vid=$1',
    ),
    'revver' => array(
        'url' => 'http://flash.revver.com/player/1.0/player.swf?mediaId=$1'
    ),
    'sevenload' => array(
        'url' => 'http://page.sevenload.com/swf/en_GB/player.swf?id=$1'
    ),
    'teachertube' => array(
        'extern' =>
            '<embed src="http://www.teachertube.com/embed/player.swf" ' .
                'width="$3" ' .
                'height="$4" ' .
                'bgcolor="undefined" ' .
                'allowscriptaccess="always" ' .
                'allowfullscreen="true" ' .
                'flashvars="file=http://www.teachertube.com/embedFLV.php?pg=video_$2' .
                    '&menu=false' .
                    '&frontcolor=ffffff&lightcolor=FF0000' .
                    '&logo=http://www.teachertube.com/www3/images/greylogo.swf' .
                    '&skin=http://www.teachertube.com/embed/overlay.swf volume=80' .
                    '&controlbar=over&displayclick=link' .
                    '&viral.link=http://www.teachertube.com/viewVideo.php?video_id=$2' .
                    '&stretching=exactfit&plugins=viral-2' .
                    '&viral.callout=none&viral.onpause=false' .
                '"' .
            '/>',
    ),
    'youtube' => array(
        'url' => 'http://www.youtube.com/v/$1'
    ),
    'youtubehd' => array(
        'url' => 'http://www.youtube.com/v/$1&ap=%2526fmt%3D22',
        'default_width' => 720,
        'default_ratio' => 16/9
    ),
    'vimeo' => array(
        'url'=>'http://vimeo.com/moogaloop.swf?clip_id=$1&;server=vimeo.com&fullscreen=0&show_title=1&show_byline=1&show_portrait=0'
	),
	'asianfuse' => array(
		'extern' => 
			'<object width="$3" height="$4" type="application/x-shockwave-flash" data="http://asianfuse.tv/flowplayer/dist/flowplayer.commercial-3.2.5.swf?0.0567059932269135" name="fp_98242867_api" id="fp_98242867_api">
				<param value="true" name="allowfullscreen">
				<param value="always" name="allowscriptaccess">
				<param value="high" name="quality">
				<param value="true" name="cachebusting">
				<param value="#000000" name="bgcolor">
				<param value="config={&quot;key&quot;:&quot;#$b453a3df7cfee525468&quot;,&quot;clip&quot;:{&quot;url&quot;:&quot;http://asianfuse.tv/files/$1&quot;,&quot;autoPlay&quot;:false},&quot;plugins&quot;:{&quot;controls&quot;:{&quot;url&quot;:&quot;http://asianfuse.tv/flowplayer/dist/flowplayer.controls-3.2.0.swf&quot;},&quot;gatracker&quot;:{&quot;url&quot;:&quot;http://asianfuse.tv/flowplayer/dist/flowplayer.analytics-3.2.1.swf&quot;,&quot;labels&quot;:{&quot;start&quot;:&quot;Start&quot;,&quot;play&quot;:&quot;Play&quot;,&quot;pause&quot;:&quot;Pause&quot;,&quot;resume&quot;:&quot;Resume&quot;,&quot;seek&quot;:&quot;Seek&quot;,&quot;stop&quot;:&quot;Stop&quot;,&quot;finish&quot;:&quot;Finish&quot;,&quot;mute&quot;:&quot;Mute&quot;,&quot;unmute&quot;:&quot;Unmute&quot;,&quot;fullscreen&quot;:&quot;Full Screen&quot;,&quot;fullscreenexit&quot;:&quot;Full Screen Exit&quot;},&quot;debug&quot;:false,&quot;trackingMode&quot;:&quot;AS3&quot;,&quot;googleId&quot;:&quot;UA-10700419-6&quot;},&quot;pseudo&quot;:{&quot;url&quot;:&quot;http://asianfuse.tv/flowplayer/dist/flowplayer.pseudostreaming-3.2.2.swf&quot;},&quot;pseudoInstream&quot;:{&quot;url&quot;:&quot;http://asianfuse.tv/flowplayer/dist/flowplayer.pseudostreaming-3.2.2.swf&quot;}},&quot;playerId&quot;:&quot;fp_98242867&quot;,&quot;playlist&quot;:[{&quot;url&quot;:&quot;http://asianfuse.tv/files/$1&quot;,&quot;autoPlay&quot;:false}]}" name="flashvars">
			</object>'
	),
);
