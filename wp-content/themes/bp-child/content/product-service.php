<?php
/**
 * Service Content
 */

    $user_id = get_current_user_id();
    $prev_page = wp_get_referer() ? wp_get_referer() : '/products-and-services/';
    $has_edit_access = can_maintain_product_and_service( $user_id, $product->id );
    $can_view = $product->visibility == 'Public' || ( (  $has_edit_access ||  is_super_admin() ) && $product->visibility == 'Private' ) ? true : false;
    if( ! $can_view ){
        addMessage( 'The visibility settings defined by the product owner prevent the display of further information.', 'error' );
        flushMessages();

    }
?>
<div class="product-page">
    <div class="page-title-block">
        <?php if(!$isAjax){ ?>
            <div class="page-title clearfix">
                <h4>Product Details</h4>
                <a href="<?php echo $prev_page;?>" class="left action-btn back-btn has-tooltip left15" title="Back to Products">
                    <span class="p"></span>
                    <span class="t">Back</span>
                </a>
                <?php if( $can_view ):?>
                    <div class="page-title-block-actions">
                        <a href="<?php echo addPrintParams(get_permalink(), 'product')?>" class="action-btn print-btn print-page-btn" id="print-product-btn"><span class="p"></span><span class="t">Print</span></a>
                    </div>
                <?php endif;?>
            </div>
        <?php } ?>
        <div class="column nopaddingtop">

            <div class="product-data clearfix">
                <?php if (has_post_thumbnail()) { ?>
                    <div class="product-thumbnail">
                        <?php the_post_thumbnail('post-thumb', array('class' => 'prod_serv_details')); ?>
                    </div>
                <?php } ?>
                <div class="product-info">
                    <div class="product-identifiers">
                        <div class="product-actions">
                            <?php if(is_super_admin() || $has_edit_access ){ ?>
                                <a href="<?php get_permalink()?>?id=<?php echo $product->id?>&_psnonce=<?php echo wp_create_nonce('delete-product') ?>&return=<?php echo base64_encode("/my-products") ?>" class="action-btn delete-btn right left10"><span class="p"></span><span class="t">Delete</span></a>
                                <a href="/edit-product-and-service?id=<?php echo $product->id?>" class="action-btn edit-btn right"><span class="p"></span><span class="t">Edit</span></a>
                            <?php } else { ?>
                                <?php if( is_user_logged_in() && check_product_from_user_agency( $user_id, $product->id ) ):?>
                                    <a href="/?cp-action=<?php echo wp_create_nonce("insufficient-privilege") ?>&privilege=<?php echo base64_encode('MAINTAIN_PRODUCTS')?>" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn delete-btn right left10"><span class="p"></span><span class="t">Delete</span></a>
                                    <a  href="/?cp-action=<?php echo wp_create_nonce("insufficient-privilege") ?>&privilege=<?php echo base64_encode('MAINTAIN_PRODUCTS')?>" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn edit-btn right"><span class="p"></span><span class="t">Edit</span></a>
                                <?php endif;?>
                            <?php } ?>
                        </div>
                        <div class="product-name">Name: <strong><?php echo $product->name.' v'.get_post_meta( $product->id, 'product_version', true ); ?></strong></div>
                        <?php if( $can_view ):?>
                            <div class="product-id">(ID: <strong><?php echo $product->product_id; ?>)</strong></div>
                        <?php endif;?>
                    </div>
                    <?php if( $can_view ):?>
                        <ul class="product-attributes">
                            <li>Owner: <strong><?php echo $product->owner; ?></strong>
                            <li>Release Date: <strong><?php echo formatDate($product->release_date, "M Y"); ?></strong></li>
                            <li>Version: <strong><?php echo $product->version; ?></strong></li>
                            <!--<li>Type: <strong><?php echo $product->type; ?></strong></li>-->
                            <?php if($product->accessURL): ?>
                                <li>Access URL: <strong><a href="<?php echo get_valid_full_url($product->accessURL)?>" target="_blank"><?php echo $product->accessURL; ?></a></strong></li>
                            <?php endif; ?>
                            <li>Visibility: <strong><?php echo $product->visibility; ?></strong></li>
                        </ul>
                        <div class="product-description"><?php echo $product->descrition; ?></div>
                    <?php endif;?>
                </div>
            </div>
                <?php if( $can_view ):?>
                    <?php
                        $args = array(
                            'post_type' => 'service',
                            'posts_per_page' => -1,
                            'meta_query' => array(
                                array(
                                    'key' => 'service_product_id',
                                    'value' => $product->id,
                                )
                            )

                        );
                        if( ! is_user_logged_in()) {
                            $args['meta_query'][] = array(
                                'key' => 'service_visibility',
                                'value' => 'Public',
                            );
                        }
                        $services = get_posts($args);

                    ?>
                    <div class="tabs-contr">
                        <ul class="tab-nav">
                            <?php $is_active = true;?>
                            <?php if( $product->relatedProducts ):?>
                                <li <?php if( $is_active ):?>class="active"<?php endif;?>>
                                    <a href="javascript: void(0)" rel="tab_related_products">Related Products</a>
                                </li>
                                <?php $is_active = false;?>
                            <?php endif;?>
                            <?php if( $services ):?>
                                <li <?php if( $is_active ):?>class="active"<?php endif;?>>
                                    <a href="javascript: void(0)" rel="tab_related_services">Service Implementations</a>
                                </li>
                            <?php endif;?>
                        </ul>
                            <?php if( $product->relatedProducts ):?>
                                <div class="tab-content" id="tab_related_products" style="display: block;">
                                    <dl class="column related_products">
                                        <?php foreach ($product->relatedProducts as $rp): ?>
                                            <dt><?php echo $rp->relationship ?>:</dt>
                                            <dd><a href="<?php echo get_permalink($rp->related_product_id)?>"><?php echo $rp->product_name ?></a></dd>
                                        <?php endforeach; ?>
                                    </dl>
                                </div>
                            <?php endif;?>
                            <?php if( $services ):?>
                                <div class="tab-content" id="tab_related_services" <?php if( $is_active ):?>style="display: block;"<?php else:?> style="display: none;"<?php endif;?>>
                                    <dl class="column related_products">
                                        <?php foreach ($services AS $s): ?>
                                            <?php $serv = new Service( $s->ID ); $serv->load();?>
                                            <dt><a href="<?php echo get_permalink( $serv->id )?>"><?php echo get_the_title( $serv->id ) ?></a></dt>
                                            <dd><label>Owner:</label> <?php echo $serv->service_owner ?></dd>
                                        <?php endforeach; ?>
                                    </dl>
                                </div>
                            <?php endif;?>
                    </div>
                <?php endif;?>
        </div>
    </div>
    <?php if( $can_view ):?>
        <?php
        $testPlans = getTestPlansByProductId($product->id);
        $product_claims = getClaimsByProductId($product->id);
        $testPlansHtml = '';
        ?>
        <div class="claims-list-wrapper">
            <div class="claims-list-title">
                <h4 class="blue_txt">Test Plans and Compliance Claims</h4>
            </div>
            <?php if(!$testPlans && !$product_claims): ?>
                <div class="no-data">No Data Found!</div>
            <?php else: ?>
                <table class="claims-list">
                    <thead>
                    <tr>
                        <th>Claim ID</th>
                        <th>Issuer</th>
                        <th>Suite</th>
                        <th class="centered">Level</th>
                        <th class="centered">Role</th>
                        <th class="centered">Status</th>
                        <th class="centered">Date</th>
                        <th class="centered">Certificate</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $used_product = $used_suite = 0;
                    $processed_claims = array();
                    foreach( $product_claims AS $product_claim ){
                        $group = groups_get_group(array('group_id' => get_post_meta( $product_claim->suite_id, 'community_id', true)));
                        array_push( $processed_claims, $product_claim->id );
                        $product_claim->conformance_level = trim( str_replace( ';;', ' ', $product_claim->conformance_level ) );
                        $product_claim->role = trim( str_replace( ';;', ' ', $product_claim->role ) );
                        ob_start();?>
                        <tr>
                            <td><?php echo isset( $product_claim->claim_id ) ? $product_claim->claim_id : ''; ?></td>
                            <td><a href="<?php echo bp_get_group_permalink($group); ?>"><?php echo $product_claim->issuer; ?></a></td>
                            <td><a href="<?php echo get_permalink($product_claim->suite_id); ?>"><?php echo get_the_title( $product_claim->suite_id ); ?></a></td>
                            <td class="centered"><?php echo $product_claim->conformance_level; ?></td>
                            <td class="centered"><?php echo $product_claim->role; ?></td>
                            <td class="centered"><span class="status-unverified">Verified</span></td>
                            <td class="centered"><?php echo isset( $product_claim->last_updated ) ? formatDate( $product_claim->last_updated ) : formatDate($product_claim->created_date); ?></td>
                            <td class="centered row-actions">
                                <a href="<?php echo S3Wrapper::getProductClaimLink( $product_claim->token );?>" onclick="window.open('<?php echo S3Wrapper::getProductClaimLink( $product_claim->token );;?>', '', 'height=600');return false;">View</a>&nbsp;|&nbsp;<a href="<?php echo S3Wrapper::getProductClaimLink( $product_claim->token, true );?>">Download</a>
                            </td>
                        </tr>
                    <?php $testPlansHtml .= ob_get_clean();
                    }
                    foreach($testPlans as $testPlan):
                        if( ! get_the_title($testPlan->suite_id) ){
                            continue;
                        }
                        $group = groups_get_group(array('group_id' => get_post_meta($testPlan->suite_id, 'community_id', true)));
                        $claim = getClaimByRole( array( 'product_id' => $product->id, 'suite_id' => $testPlan->suite_id ), $testPlan->level, $testPlan->role );
                        $used_suite = $testPlan->suite_id;
                        $used_product = $product->id;
                        if( in_array( $claim->id, $processed_claims ) ){
                            $claim = false;
                        } else{
                            array_push( $processed_claims, $claim->id );
                        }
                        $testPlan->level = trim( str_replace( ';;', ' ', $testPlan->level ) );
                        $testPlan->role = trim( str_replace( ';;', ' ', $testPlan->role ) );
                        if( $claim ){
                            $claim->role = trim( str_replace( ';;', ' ', $claim->role ) );
                            $claim->conformance_level = trim( str_replace( ';;', ' ', $claim->conformance_level ) );
                        }
                        ?>
                        <?php if( $claim && ( $claim->conformance_level !== $testPlan->level || $claim->role !== $testPlan->role ) ):?>
                        <?php ob_start();?>
                        <tr>
                            <td></td>
                            <td><a href="<?php echo bp_get_group_permalink($group); ?>"><?php echo $testPlan->issuer; ?></a></td>
                            <td><a href="<?php echo get_permalink($testPlan->suite_id); ?>"><?php echo ct_get_suite_max_version( $testPlan->suite_id, true ); ?></a></td>
                            <td class="centered"><?php echo $testPlan->level; ?></td>
                            <td class="centered"><?php echo $testPlan->role; ?></td>
                            <td class="centered"><span class="status-unverified">In Progress</span></td>
                            <td class="centered"><?php echo isset( $claim->last_updated ) ? formatDate( $claim->last_updated ) : formatDate($testPlan->created_date); ?></td>
                            <td></td>
                        </tr>
                        <?php $testPlansHtml .= ob_get_clean();?>
                    <?php endif;?>
                        <?php if( ! isset( $claim->claim_id ) ) ob_start();?>
                        <tr>
                            <td><?php echo isset( $claim->claim_id ) ? $claim->claim_id : ''; ?></td>
                            <td><a href="<?php echo bp_get_group_permalink($group); ?>"><?php echo $testPlan->issuer; ?></a></td>
                            <td><a href="<?php echo get_permalink($testPlan->suite_id); ?>"><?php echo isset( $claim->claim_id ) ? get_the_title( $claim->suite_id ): ct_get_suite_max_version( $testPlan->suite_id, true ); ?></a></td>
                            <td class="centered"><?php echo isset( $claim->claim_id ) ?  $claim->conformance_level : $testPlan->level; ?></td>
                            <td class="centered"><?php echo isset( $claim->claim_id ) ?  $claim->role : $testPlan->role; ?></td>
                            <td class="centered">
                                <?php if( isset( $claim->status ) ){ ?>
                                    <span class="status-verified"><?php echo $claim->status; ?></span>
                                <?php }else{ ?>
                                    <span class="status-unverified">In Progress</span>
                                <?php } ?>
                            </td>
                            <td class="centered"><?php echo isset( $claim->last_updated ) ? formatDate( $claim->last_updated ) : formatDate($testPlan->created_date); ?></td>
                            <td class="centered row-actions">
                                <?php if( isset( $claim->claim_id ) ): ?>
                                    <a href="<?php echo S3Wrapper::getProductClaimLink( $claim->token );?>" onclick="window.open('<?php echo S3Wrapper::getProductClaimLink( $claim->token );;?>', '', 'height=600');return false;">View</a>&nbsp;|&nbsp;<a href="<?php echo S3Wrapper::getProductClaimLink( $claim->token, true );?>">Download</a>
                                <?php endif;?>
                            </td>
                        </tr>
                        <?php if( ! isset( $claim->claim_id ) ) $testPlansHtml .= ob_get_clean() ;?>
                    <?php endforeach; ?>
                    <?php echo $testPlansHtml; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif;?>
</div>