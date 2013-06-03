<?php
/**
 * The template for Search Results.
 */

get_header(); 

global $post; 

?>

<!-- **************** CONTENT *************** -->

	<div class="space25"></div>
	<div class="content container" id="search">
		
		<div id="search_title_block">
			
			<div class="column quorter left">
				<form role="search" method="get" id="searchform" action="<?php get_bloginfo('url'); ?>">
					<input type="search" name="s" id="s" class="radius6 test_suits_research" value="<?php echo $_GET['s']; ?>" placeholder="Search Term" />
					<input type="submit" id="search_test_suite_submit" value="" />
				</form>
			</div>
			<div class="column three_quorters right nopaddingleft">
				<h5 class="search_test_suite_results"><?php if (have_posts()) { ?>Showing 1- 10 of <b><?php echo $wp_query->found_posts; ?></b> Results for "<b><?php echo get_search_query(); ?></b>"<?php } else { ?>Nothing found...<?php } ?></h5>
			</div>
			<div class="clear"></div>
			
		</div> <!-- end search_title_block -->
		
	<?php
	if ($_GET['post_type']=='test-suite') { 
		// Template Test Suite - Search Results : Goes here
		
	?>
		<div id="search_result_block">
			<div class="column sixth left"> 
					<?php 
						$issuers_all= array();
						$issuers_nb = array();
						$issuers = array();
						
						$types_all = array();							
						$types_nb = array();
						$types = array();
						
						$dates_y_all = array();
						$dates_y_nb = array();
						$dates_y = array();
						
						
						$statuses_all = array();
						$statuses_nb = array();
						$statuses = array();
						
						
						while (have_posts()) : the_post(); 
							$issuer = get_post_meta($post->ID, 'ts_issuer', true);
							array_push($issuers_all,$issuer); 
							if (!in_array($issuer, $issuers)) {
								array_push($issuers,$issuer); 
							}
							
							$term_list = wp_get_post_terms($post->ID, 'test_suite_type', array("fields" => "all"));
							$type = $term_list[0]->name;
							array_push($types_all,$type);
							if (!in_array($type, $types)) {
								array_push($types,$type); 
							}
							
							$date_field=get_post_meta($post->ID, 'ts_issue_date', true); 
							$date_y = date("Y", strtotime($date_field)); // 2012
							array_push($dates_y_all,$date_y);
							if (!in_array($date_y, $dates_y)) {
								array_push($dates_y,$date_y); 
							}	
							
							$status = get_post_meta($post->ID, 'ts_status', true);
							array_push($statuses_all,$status);
							if (!in_array($status, $statuses)) {
								array_push($statuses,$status); 
							}
							endwhile;
						
							foreach (array_count_values($issuers_all) as $value){
									array_push($issuers_nb, $value);
							}
							
							foreach (array_count_values($types_all) as $value){
									array_push($types_nb, $value);
							}
							
							foreach (array_count_values($dates_y_all) as $value){
									array_push($dates_y_nb, $value);
							}
							
							foreach (array_count_values($statuses_all) as $value){
									array_push($statuses_nb, $value);
							}
		
						?>

				<form action="" method="post" id="form_filter">
				<div class="expandable">
					<h6 class="exp_title">Type</h6>
					<div class="exp_content">
					<?php
						foreach ($types as $key => $type_name){
								foreach($types_nb as $key2 => $type_matches){
									if ($key == $key2)	{
										//echo $val.' - '.$issue_matches.'<br />';
										$input_name =  str_replace(' ','_',$type_name); 
										echo '<label for="'.$input_name.'" class="blue_txt"><input type="checkbox" name="type_name[]"'.(in_array($input_name, $_POST['type_name']) ? ' checked="checked"' : '').' value="'.$input_name.'" id="'.$input_name.'" class="input_filter"> '
										.$type_name. ' ('.$type_matches.')</label><div class="clear"></div>';
									}
								}
							}
					?>
					</div>
				</div>
				
				<div class="expandable">
					<h6 class="exp_title">Issuer</h6>
					<div class="exp_content">
					<?php
						foreach ($issuers as $key => $issue_name){
								foreach($issuers_nb as $key2 => $issue_matches){
									if ($key == $key2)	{
										//echo $val.' - '.$issue_matches.'<br />';
										$input_name =  str_replace(' ','_',$issue_name); 
										echo '<label for="'.$input_name.'" class="blue_txt"><input type="checkbox" name="issue_name[]"'.(in_array($input_name, $_POST['issue_name']) ? ' checked="checked"' : '').' value="'.$input_name.'" id="'.$input_name.'" class="input_filter"> '
										.$issue_name. ' ('.$issue_matches.')</label><div class="clear"></div>';
									}
								}
							}
					?>
					</div>
				</div>
				
				<div class="expandable">
					<h6 class="exp_title">Year of Issue</h6>
					<div class="exp_content">
					<?php
						foreach ($dates_y as $key => $date_name){
								foreach($dates_y_nb as $key2 => $date_matches){
									if ($key == $key2)	{
										//echo $val.' - '.$issue_matches.'<br />';
										$input_name =  str_replace(' ','_',$date_name); 
										echo '<label for="'.$input_name.'" class="blue_txt"><input type="checkbox" name="date_name[]"'.(in_array($input_name, $_POST['date_name']) ? ' checked="checked"' : '').' value="'.$input_name.'" id="'.$input_name.'" class="input_filter"> '
										.$date_name. ' ('.$date_matches.')</label><div class="clear"></div>';
									}
								}
							}
					?>
					</div>
				</div>
				
				<div class="expandable">
					<h6 class="exp_title">Status</h6>
					<div class="exp_content">
						<?php
						foreach ($statuses as $key => $status_name){
								foreach($statuses_nb as $key2 => $status_matches){
									if ($key == $key2)	{
										//echo $val.' - '.$issue_matches.'<br />';
										$input_name =  str_replace(' ','_',$status_name); 
										echo '<label for="'.$status_name.'" class="blue_txt"><input type="checkbox" name="status_name[]"'.(in_array($input_name, $_POST['status_name']) ? ' checked="checked"' : '').' value="'.$input_name.'" id="'.$input_name.'" class="input_filter"> '
										.$status_name. ' ('.$status_matches.')</label><div class="clear"></div>';
									}
								}
							}
						?>
					</div>
				</div>
				</form>
			</div>
			<div class="column five_sixths right nopaddingleft">
				<?php if (have_posts()) {
					
					 ?>
				<div class="grid  dark_gray_txt">
					<div class="grid_head">
						<div class="grid_row nopaddingbottom nopaddingtop">
							<div class="grid_cell nopaddingtop width35P">Name</div>
							<div class="grid_cell nopaddingtop width20P">Issuer</div>
							<div class="grid_cell nopaddingtop width20P">Type</div>
							<div class="grid_cell nopaddingtop width15P">Date</div>
							<div class="grid_cell nopaddingtop width10P">Status</div>
								<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body radius15">
						
						<?php 
						//Types
						$occ_types = array();
						foreach($_POST['type_name'] as $vall){
							$checked =  str_replace('_',' ',$vall); 
							array_push($occ_types,$checked);
							}
						
						//Issuers
						$occ_issuers = array();
						foreach($_POST['issue_name'] as $vall){
							$checked =  str_replace('_',' ',$vall); 
							array_push($occ_issuers,$checked);
							}
						
						//Date
						$occ_dates = array();
						foreach($_POST['date_name'] as $vall){
							$checked =  str_replace('_',' ',$vall); 
							array_push($occ_dates,$checked);
							}
						
						//Status
						$occ_statuses = array();
						foreach($_POST['status_name'] as $vall){
							$checked =  str_replace('_',' ',$vall); 
							array_push($occ_statuses,$checked);
							}
						
						while (have_posts()) : the_post(); 	
							$ts_issuer = get_post_meta($post->ID, 'ts_issuer', true);
							$test_type = wp_get_post_terms($post->ID, 'test_suite_type', array("fields" => "all"));
							$test_suite_type = $test_type[0]->name;
							$date_filter=get_post_meta($post->ID, 'ts_issue_date', true); 
							$date_prt2 = date("Y", strtotime($date_filter)); // format Nov 2012
							$ts_status = get_post_meta($post->ID, 'ts_status', true);
							
							if (!empty($occ_types)) { 
								//issuers
								if (!empty($occ_issuers)){
									//dates
									if(!empty($occ_dates)){
										//statuses
										if(!empty($occ_statuses)){
											$test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($ts_issuer,$occ_issuers)) && (in_array($date_prt2,$occ_dates)) && (in_array($ts_status,$occ_statuses)) );
										} // status not set
										else $test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($ts_issuer,$occ_issuers)) && (in_array($date_prt2,$occ_dates)) );
									}
									//dates NOT set
									else {
										//date NOT set ; status set
										if(!empty($occ_statuses)){
											$test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($ts_issuer,$occ_issuers)) && (in_array($ts_status,$occ_statuses)) );
										} //date & status NOT set
										else $test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($ts_issuer,$occ_issuers)) );
									}
								}
								//issuers NOT set
								else {
									if(!empty($occ_dates)){
										//date set										
										if(!empty($occ_statuses)){
											// issuers NOT set ; dates, statuses set
											$test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($date_prt2,$occ_dates)) && (in_array($ts_status,$occ_statuses)) );
											}
										else{
											//issuers , statuses NOT set
											$test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($date_prt2,$occ_dates)) );
											}	
										}
										else {
											//date NOT set
											if(!empty($occ_statuses)){
											// issuers,dates  NOT set ; statuses set
											$test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($ts_status,$occ_statuses)) );
											}
											else{
												//issuers, date, statuses NOT set
												$test_search =  (in_array($test_suite_type,$occ_types));
												}	
											}
								}
							}
							else {
								//type NOT set
								if(!empty($occ_issuers)){
									//issuers set
									if(!empty($occ_dates)){
										//issuers, dates set
										if(!empty ($occ_statuses)){
											//issuers, dates, status set
											$test_search = ( (in_array($ts_issuer,$occ_issuers)) && (in_array($date_prt2,$occ_dates)) && (in_array($ts_status,$occ_statuses)) );
											}
											else{
												// status not set
												$test_search = ( (in_array($ts_issuer,$occ_issuers)) && (in_array($date_prt2,$occ_dates)) );
												}
										}
										//date not set
										else {
											if(!empty ($occ_statuses)){
											//date not set
											$test_search = ( (in_array($ts_issuer,$occ_issuers)) && (in_array($ts_status,$occ_statuses)) );
											}
											else{
												// type,date, status not set
												$test_search =  (in_array($ts_issuer,$occ_issuers));
												}
											}	
									}
									else {
										//type , issuers NOT set
									if(!empty($occ_dates)){
										//issuers, dates set
										if(!empty ($occ_statuses)){
											// dates, status set
											$test_search = (  (in_array($date_prt2,$occ_dates)) && (in_array($ts_status,$occ_statuses)) );
											}
											else{
												// status not set
												$test_search = (in_array($date_prt2,$occ_dates)) ;
												}
										}
										//type, issuer, date not set
										else {
											if(!empty ($occ_statuses)){
											//date not set
											$test_search = (in_array($ts_status,$occ_statuses));
											}
											else{
												// type,issuer, date, status not set
												$test_search =  true;
												}
											}
										
										}
								}
							if($test_search){
								
							?>
						
							<div class="grid_row">
								<div class="grid_cell width35P">
									<a href="<?php the_permalink(); ?>" class="blue_txt"><h5><?php the_title(); ?></h5></a>
									<?php the_excerpt(); ?>
								</div>
								<div class="grid_cell width20P"><a href="#"><?php echo $ts_issuer; ?></a></div>
								<div class="grid_cell width20P">
									<?php echo $test_suite_type; ?>
								</div>
								<div class="grid_cell width15P">
									<?php
									$date_prt=get_post_meta($post->ID, 'ts_issue_date', true); 
									echo date("M Y", strtotime($date_prt)); // format Nov 2012
									 ?>
								</div>
								<?php
									if(get_post_meta($post->ID, 'ts_status', true) == 'Active') {
								?>
									<div class="grid_cell width10P"><a class="button green_bcg white_txt button_small radius3">ACTIVE</a></div>
								<?php } else { ?>
									<div class="grid_cell width10P"><a class="button orange_bcg white_txt button_small radius3">ON HOLD</a></div>
								<?php } ?>
								<div class="clear"></div>
							</div>
						
						<?php  
							} 
						endwhile; ?>
						
						
					</div>
				</div>
			
				<?php } else { ?>
					<p>Please try again..</p>
				<?php } ?>
				
			</div>
			<div class="clear"></div>
			<div class="column">
				<!-- needs to be in while loop -->
				<div class="pagination" style="display:none">
									<?php global $wp_query;
									$big = 999999999;
									echo paginate_links( array(
										'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
										'format' => '?paged=%#%',
										'current' => max( 1, get_query_var('paged') ),
										'total' => $wp_query->max_num_pages
									) ); ?>
								</div>
			
			</div>
		</div> <!-- end search_result_block -->
		<?php } else if ($_GET['post_type']=='product-service'){
			// Template Product / Service Search Results : Goes here
			?>
		<div id="search_result_block">
			<div class="column sixth left">
				<?php
						$owners_all= array();
						$owners_nb = array();
						$owners = array();
						
						$types_all = array();							
						$types_nb = array();
						$types = array();
						
						$dates_y_all = array();
						$dates_y_nb = array();
						$dates_y = array();
						
						$statuses_all = array();
						$statuses_nb = array();
						$statuses = array();
						
						while (have_posts()) : the_post(); 
							$owner = get_post_meta($post->ID, 'product_owner', true);
							array_push($owners_all,$owner); 
							if (!in_array($owner, $owners)) {
								array_push($owners,$owner); 
							}
							
							$type = get_post_meta($post->ID, 'product_type', true);
					
							array_push($types_all,$type);
							if (!in_array($type, $types)) {
								array_push($types,$type); 
							}
							
							$date_field=get_post_meta($post->ID, 'product_date', true); 
							$date_y = date("Y", strtotime($date_field)); // 2012
							array_push($dates_y_all,$date_y);
							if (!in_array($date_y, $dates_y)) {
								array_push($dates_y,$date_y); 
							}	
							
							$status = get_post_meta($post->ID, 'product_status', true);
							array_push($statuses_all,$status);
							if (!in_array($status, $statuses)) {
								array_push($statuses,$status); 
							}
							endwhile;
						
							foreach (array_count_values($owners_all) as $value){
									array_push($owners_nb, $value);
							}
							
							foreach (array_count_values($types_all) as $value){
									array_push($types_nb, $value);
							}
							
							foreach (array_count_values($dates_y_all) as $value){
									array_push($dates_y_nb, $value);
							}
							
							foreach (array_count_values($statuses_all) as $value){
									array_push($statuses_nb, $value);
							}
						?>
				<form action="" method="post" id="form_filter2">
				<div class="expandable">
					<h6 class="exp_title">Type</h6>
					<div class="exp_content">
					<?php
						foreach ($types as $key => $type_name){
								foreach($types_nb as $key2 => $type_matches){
									if ($key == $key2)	{
										//echo $val.' - '.$issue_matches.'<br />';
										$input_name =  str_replace(' ','_',$type_name); 
										echo '<label for="'.$input_name.'" class="blue_txt"><input type="checkbox" name="type_name[]"'.(in_array($input_name, $_POST['type_name']) ? ' checked="checked"' : '').' value="'.$input_name.'" id="'.$input_name.'" class="input_filter2"> '
										.$type_name. ' ('.$type_matches.')</label><div class="clear"></div>';
									}
								}
							}
					?>
					</div>
				</div>
				
				<div class="expandable">
					<h6 class="exp_title">Owner</h6>
					<div class="exp_content">
					<?php
						foreach ($owners as $key => $owner_name){
								foreach($owners_nb as $key2 => $owner_matches){
									if ($key == $key2)	{
										//echo $val.' - '.$issue_matches.'<br />';
										$input_name =  str_replace(' ','_',$owner_name); 
										echo '<label for="'.$input_name.'" class="blue_txt"><input type="checkbox" name="owner_name[]"'.(in_array($input_name, $_POST['owner_name']) ? ' checked="checked"' : '').' value="'.$input_name.'" id="'.$input_name.'" class="input_filter2"> '
										.$owner_name. ' ('.$owner_matches.')</label><div class="clear"></div>';
									}
								}
							}
					?>
					</div>
				</div>
				
				<div class="expandable">
					<h6 class="exp_title">Year of Issue</h6>
					<div class="exp_content">
					<?php
						foreach ($dates_y as $key => $date_name){
								foreach($dates_y_nb as $key2 => $date_matches){
									if ($key == $key2)	{
										//echo $val.' - '.$issue_matches.'<br />';
										$input_name =  str_replace(' ','_',$date_name); 
										echo '<label for="'.$input_name.'" class="blue_txt"><input type="checkbox" name="date_name[]"'.(in_array($input_name, $_POST['date_name']) ? ' checked="checked"' : '').' value="'.$input_name.'" id="'.$input_name.'" class="input_filter2"> '
										.$date_name. ' ('.$date_matches.')</label><div class="clear"></div>';
									}
								}
							}
					?>
					</div>
				</div>
				
				<div class="expandable">
					<h6 class="exp_title">Status</h6>
					<div class="exp_content">
						<?php
						foreach ($statuses as $key => $status_name){
								foreach($statuses_nb as $key2 => $status_matches){
									if ($key == $key2)	{
										//echo $val.' - '.$issue_matches.'<br />';
										$input_name =  str_replace(' ','_',$status_name); 
										echo '<label for="'.$status_name.'" class="blue_txt"><input type="checkbox" name="status_name[]"'.(in_array($input_name, $_POST['status_name']) ? ' checked="checked"' : '').' value="'.$input_name.'" id="'.$input_name.'" class="input_filter2"> '
										.$status_name. ' ('.$status_matches.')</label><div class="clear"></div>';
									}
								}
							}
						?>
					</div>
				</div>
				</form>
			</div>
			<div class="column five_sixths right nopaddingleft">
				<?php if (have_posts()) { ?>
				<div class="grid  dark_gray_txt">
					<div class="grid_head">
						<div class="grid_row nopaddingbottom nopaddingtop">
							<div class="grid_cell nopaddingtop width35P">Name</div>
							<div class="grid_cell nopaddingtop width20P">Owner</div>
							<div class="grid_cell nopaddingtop width20P">Type</div>
							<div class="grid_cell nopaddingtop width15P">Date</div>
							<div class="grid_cell nopaddingtop width10P">Status</div>
								<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body radius15">
						<?php 
						//Types
						$occ_types = array();
						foreach($_POST['type_name'] as $vall){
							$checked =  str_replace('_',' ',$vall); 
							array_push($occ_types,$checked);
							}
						
						//Owners
						$occ_owners = array();
						foreach($_POST['owner_name'] as $vall){
							$checked =  str_replace('_',' ',$vall); 
							array_push($occ_owners,$checked);
							}
						
						//Date
						$occ_dates = array();
						foreach($_POST['date_name'] as $vall){
							$checked =  str_replace('_',' ',$vall); 
							array_push($occ_dates,$checked);
							}
						
						//Status
						$occ_statuses = array();
						foreach($_POST['status_name'] as $vall){
							$checked =  str_replace('_',' ',$vall); 
							array_push($occ_statuses,$checked);
							}
							
						while (have_posts()) : the_post();
							$ts_owner = get_post_meta($post->ID, 'product_owner', true);
							$test_suite_type = get_post_meta($post->ID, 'product_type',true);
							$date_filter=get_post_meta($post->ID, 'product_date', true); 
							$date_prt2 = date("Y", strtotime($date_filter)); // format Nov 2012
							$ts_status = get_post_meta($post->ID, 'product_status', true);
						if (!empty($occ_types)) { 
								//issuers
								if (!empty($occ_owners)){
									//dates
									if(!empty($occ_dates)){
										//statuses
										if(!empty($occ_statuses)){
											$test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($ts_owner,$occ_owners)) && (in_array($date_prt2,$occ_dates)) && (in_array($ts_status,$occ_statuses)) );
										} // status not set
										else $test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($ts_owner,$occ_owners)) && (in_array($date_prt2,$occ_dates)) );
									}
									//dates NOT set
									else {
										//date NOT set ; status set
										if(!empty($occ_statuses)){
											$test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($ts_owner,$occ_owners)) && (in_array($ts_status,$occ_statuses)) );
										} //date & status NOT set
										else $test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($ts_owner,$occ_owners)) );
									}
								}
								//issuers NOT set
								else {
									if(!empty($occ_dates)){
										//date set										
										if(!empty($occ_statuses)){
											// issuers NOT set ; dates, statuses set
											$test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($date_prt2,$occ_dates)) && (in_array($ts_status,$occ_statuses)) );
											}
										else{
											//issuers , statuses NOT set
											$test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($date_prt2,$occ_dates)) );
											}	
										}
										else {
											//date NOT set
											if(!empty($occ_statuses)){
											// issuers,dates  NOT set ; statuses set
											$test_search = ( (in_array($test_suite_type,$occ_types)) && (in_array($ts_status,$occ_statuses)) );
											}
											else{
												//issuers, date, statuses NOT set
												$test_search =  (in_array($test_suite_type,$occ_types));
												}	
											}
								}
							}
							else {
								//type NOT set
								if(!empty($occ_owners)){
									//issuers set
									if(!empty($occ_dates)){
										//issuers, dates set
										if(!empty ($occ_statuses)){
											//issuers, dates, status set
											$test_search = ( (in_array($ts_owner,$occ_owners)) && (in_array($date_prt2,$occ_dates)) && (in_array($ts_status,$occ_statuses)) );
											}
											else{
												// status not set
												$test_search = ( (in_array($ts_owner,$occ_owners)) && (in_array($date_prt2,$occ_dates)) );
												}
										}
										//date not set
										else {
											if(!empty ($occ_statuses)){
											//date not set
											$test_search = ( (in_array($ts_owner,$occ_owners)) && (in_array($ts_status,$occ_statuses)) );
											}
											else{
												// type,date, status not set
												$test_search =  (in_array($ts_owner,$occ_owners));
												}
											}	
									}
									else {
										//type , issuers NOT set
									if(!empty($occ_dates)){
										//issuers, dates set
										if(!empty ($occ_statuses)){
											// dates, status set
											$test_search = (  (in_array($date_prt2,$occ_dates)) && (in_array($ts_status,$occ_statuses)) );
											}
											else{
												// status not set
												$test_search = (in_array($date_prt2,$occ_dates)) ;
												}
										}
										//type, issuer, date not set
										else {
											if(!empty ($occ_statuses)){
											//date not set
											$test_search = (in_array($ts_status,$occ_statuses));
											}
											else{
												// type,issuer, date, status not set
												$test_search =  true;
												}
											}
										
										}
								}
							if($test_search){
								
							?>
							<div class="grid_row">
								<div class="grid_cell width35P">
									<a href="<?php the_permalink(); ?>" class="blue_txt"><h5><?php the_title(); ?></h5></a>
									<?php the_excerpt(); ?>
								</div>
								<div class="grid_cell width20P"><a href="#"><?php echo get_post_meta($post->ID, 'product_owner', true); ?></a></div>
								<div class="grid_cell width20P">
									<?php
										/*$term_list = wp_get_post_terms($post->ID, 'test_suite_type', array("fields" => "all"));
										echo $term_list[0]->name;*/
										echo get_post_meta($post->ID, 'product_type', true);
									?>
								</div>
								<div class="grid_cell width15P">
									<?php 
										$date=get_post_meta($post->ID, 'product_date', true); 
										echo date("M Y", strtotime($date)); // format Nov 2012
									?>
									</div>
								<?php
									if(get_post_meta($post->ID, 'product_status', true) == 'Active') {
								?>
									<div class="grid_cell width10P"><a class="button green_bcg white_txt button_small radius3">ACTIVE</a></div>
								<?php } else { ?>
									<div class="grid_cell width10P"><a class="button orange_bcg white_txt button_small radius3">ON HOLD</a></div>
								<?php } ?>
								<div class="clear"></div>
							</div>
						
						<?php 
						}
						endwhile; ?>
						
					</div>
				</div>
				<?php } else { ?>
					<p>Please try again..</p>
				<?php } ?>
			</div>
			<div class="clear"></div>
			<div class="column">
				<!-- needs to be in while loop -->
				<div class="pagination" style="display:none">
									<?php global $wp_query;
									$big = 999999999;
									echo paginate_links( array(
										'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
										'format' => '?paged=%#%',
										'current' => max( 1, get_query_var('paged') ),
										'total' => $wp_query->max_num_pages
									) ); ?>
								</div>
				<div class="pagination tocenter blue_txt" style="display:none">
					<ul class="aligncenter radius6">
						<li><a href="#"><img src="<?php echo get_bloginfo('template_url'); ?>/images/pagination_left.png" /></a></li>
						<li><a href="#">1</a></li>
						<li><a href="#">2</a></li>
						<li><a href="#">3</a></li>
						<li><a href="#">4</a></li>
						<li><a href="#">5</a></li>
						<li><a href="#">6</a></li>
						<li>...</li>
						<li><a href="#">96</a></li>
						<li><a href="#">97</a></li>
						<li class="active"><a href="#">98</a></li>
						<li><a href="#">99</a></li>
						<li><a href="#">100</a></li>
						<li><a href="#"><img src="<?php echo get_bloginfo('template_url'); ?>/images/pagination_right.png" /></a></li>
					</ul>
					<div class="clear"></div>
				</div>
			
			
			</div>
		</div> <!-- end search_result_block -->
	<?php } ?>
	
	</div><!-- end content -->
	<div class="space45"></div>
	
</div><!-- end content-wrapper -->
</div><!-- end content-pattern -->
<div class="clear"></div>
<!-- **************** END CONTENT *************** -->

<div class="clear"></div>
<?php get_footer(); ?>

