<?php

/**
 * No Search Results Feedback Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>
<?php
    $bbp_search_community_id = $_GET['bbp_search_community_id'];
    $group = groups_get_group( array( 'group_id' => $bbp_search_community_id ) );
?>

<div class="bbp-template-notice">
	<p>Oh bother! No search results were found here! <a href="<?php bp_group_permalink( $group )?>forum"><b>Back to Forum Page</b></a></p>
</div>