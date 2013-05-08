<?php
	/**
	 * Get media object by url
	 *
	 * @param string $media_url Media file url
	 * @param int $width Media object width
	 * @param int $height Media object height
	 * @return string Object markup
	 */
	function su_get_media( $media_url, $width, $height, $preview = false ) {

		// Youtube video
		$video_url = parse_url( $media_url );

		if ( $video_url['host'] == 'youtube.com' || $video_url['host'] == 'www.youtube.com' ) {
			parse_str( $video_url['query'], $youtube );
			$id = uniqid( '', false );
			$return = '
					<iframe width="' . $width . '" height="' . $height . '" src="http://www.youtube.com/embed/' . $youtube['v'] . '?wmode=opaque" frameborder="0" allowfullscreen="true"></iframe>
				';
		}

		// Vimeo video
		$video_url = parse_url( $media_url );

		if ( $video_url['host'] == 'vimeo.com' || $video_url['host'] == 'www.vimeo.com' ) {
			$vimeo_id = mb_substr( $video_url['path'], 1 );
			$return = '<iframe src="http://player.vimeo.com/video/' . $vimeo_id . '?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="' . $width . '" height="' . $height . '" frameborder="0"></iframe>';
		}

		// Images (bmp/jpg/jpeg/png/gif)
		$images = array( '.bmp', '.BMP', '.jpg', '.JPG', '.png', '.PNG', 'jpeg', 'JPEG', '.gif', '.GIF' );
		$image_ext = mb_substr( $media_url, -4 );

		if ( in_array( $image_ext, $images ) ) {
			$return = '<img src="' .   get_bloginfo('template_url')  . '/functions/shortcodes-ultimate/lib/timthumb.php?src=' . $media_url . '&amp;w=' . $width . '&amp;h=' . $height . '&amp;zc=1&amp;q=100" alt="" width="' . $width . '" height="' . $height . '" />';
		}

		// Video file (mp4/flv)
		$videos = array( '.mp4', '.MP4', '.flv', '.FLV' );
		$video_ext = mb_substr( $media_url, -4 );

		if ( in_array( $video_ext, $videos ) ) {
			$player_id = uniqid( '_', false );

			$return = '
					<a href="' . $media_url . '" style="display:block; width:'. $width .'px; height:'. $height .'px;" id="player' . $player_id . '"> </a>
					<script>
					flowplayer("player' . $player_id . '", {src:"' .   get_bloginfo('template_url')  . '/functions/shortcodes-ultimate/lib/flowplayer-3.2.7.swf", wmode: \'Opaque\'}, {
					screen:	{
		bottom: 0	
	},
	plugins: {
		controls: {
			url: \'' .  get_bloginfo('template_url') . '/functions/shortcodes-ultimate/lib/flowplayer.controls-3.2.5.swf\',
			backgroundColor: "transparent",
			backgroundGradient: "none",
			sliderColor: \'#FFFFFF\',
			sliderBorder: \'1.5px solid rgba(160,160,160,0.7)\',
			volumeSliderColor: \'#FFFFFF\',
			volumeBorder: \'1.5px solid rgba(160,160,160,0.7)\',
			timeColor: \'#ffffff\',
			durationColor: \'#535353\',
			tooltipColor: \'rgba(255, 255, 255, 0.7)\',
			tooltipTextColor: \'#000000\'
		}
	},
	clip: {
    	autoPlay: false    }	});
</script>';
		}

		// Audio file (mp3)
		if ( mb_substr( $media_url, -4 ) == '.mp3' ) {
			$player_id = uniqid( '_', false );

						$return = '
					<div id="audio' . $player_id . '" style="display:block; width:'. $width .'px; height:30px;" href="' . $media_url . '"></div>
<script>
	$f("audio' . $player_id . '", {src:"' . get_bloginfo('template_url') . '/functions/shortcodes-ultimate/lib/flowplayer-3.2.7.swf", wmode: \'Opaque\'}, {
	// fullscreen button not needed here
	plugins: {
		audio: {
			url: \'' . get_bloginfo('template_url') . '/functions/shortcodes-ultimate/lib/flowplayer.audio-3.2.2.swf\'
		},
		controls: {
			fullscreen: false,
			height: 30,
			autoHide: false,
			backgroundColor: "transparent",
			backgroundGradient: "none",
			sliderColor: \'#FFFFFF\',
			sliderBorder: \'1.5px solid rgba(160,160,160,0.7)\',
			volumeSliderColor: \'#FFFFFF\',
			volumeBorder: \'1.5px solid rgba(160,160,160,0.7)\',
			timeColor: \'#ffffff\',
			durationColor: \'#535353\',
			tooltipColor: \'rgba(255, 255, 255, 0.7)\',
			tooltipTextColor: \'#000000\'
		}
	},
	clip: {
		autoPlay: false,
		onBeforeBegin: function() {
			$f("player").close();
		}
	}
});
</script>';
		}

		return $return;
	}
?>