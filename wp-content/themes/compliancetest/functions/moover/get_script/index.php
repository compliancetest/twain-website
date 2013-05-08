<?php
header ("Content-Type:	application/javascript; charset=utf-8");
$filePath =  __FILE__;
$rootPath = substr(__FILE__,0, strpos(__FILE__,'wp-content/'));
include($rootPath.'wp-config.php');
$mvID = $_GET['id'];
global $wpdb;
$mv_result = $wpdb->get_row('SELECT * FROM ' . MOOVER_TABLE_NAME . ' WHERE moover_id =' . $mvID); 
$wpdb->flush();
$moover = unserialize ($mv_result -> options);
?>
(function($){
	$(document).ready(function(){
		$('.moover_<?php echo $mvID?>').moover({
            disableCSS3 : <?php echo $moover['disableCSS3'] ?>,
            manualMode : false,
            <?php 
			if ( $moover['navigation']==='true' ) echo "navigation : '.moover_$mvID .moover-pagination_$mvID',
			"  ;
			if ( $moover['navigationActive']==='true' ) echo "navigationActive : true,
			"; 
			if ( !empty($moover['playButton']) ) echo "playButton : '".$moover['playButton']."',
			"; 
			if ( !empty($moover['stopButton']) ) echo "stopButton : '".$moover['stopButton']."',
			";
			if ( !empty($moover['nextButton']) ) echo "nextButton : '".$moover['nextButton']."',
			";
			if ( !empty($moover['prevButton']) ) echo "prevButton : '".$moover['prevButton']."',"; ?>

			moveTime : <?php echo $moover['moveTime'] ?>,
            slideTime : <?php echo $moover['slideTime'] ?>,
            moveWidth : <?php echo $moover['moveWidth'] ?>,
            preloader : <?php echo $moover['preloader'] ?>,
            moveImage : <?php echo $moover['moveImage'] ?>,
            scaleImage : <?php echo $moover['scaleImage'] ?>,
            moveOutImage : true,
            waitForImage : true,
            effects : {
            <?php 
            $i=0;
            foreach ($moover['slides'] as $slide) { 
                $i++;
            ?>
            '<?php echo $i?>' : <?php echo $slide['js']?><?php if ($i<count($moover['slides'])) echo','; echo "\n" ?>
            <?php } ?>
			}<?php 
			if (!empty($moover['onStart'])) { echo ",
			onStart : function(){ $moover[onStart] }";
			}
			?><?php 
			if (!empty($moover['onSlideSwitch'])) { echo ",
			onSlideSwitch : function(){ $moover[onSlideSwitch] }";
			}
			?><?php 
			if (!empty($moover['onSlideSwitchEnd'])) { echo ",
			onSlideSwitchEnd : function(){ $moover[onSlideSwitchEnd] }";
			}
			?>
	
		})
		
		$('.slider_pause_<?php echo $mvID?>').click(function () {
			$(this).addClass('psb_inactive');
			$('.slider_play_<?php echo $mvID?>').removeClass('psb_inactive');
		});
		$('.slider_play_<?php echo $mvID?>').click(function () {
			$(this).addClass('psb_inactive');
			$('.slider_pause_<?php echo $mvID?>').removeClass('psb_inactive');
		});
	})
})(jQuery);
