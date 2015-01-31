<?php do_action( 'bp_before_profile_loop_content' ); ?>

<?php if ( bp_has_profile() ) : ?>
    <?php
        $user_id = bp_current_user_id();        
        
        $biography = get_user_meta($user_id, 'description', true);
        
        $org_membership = ct_get_user_organisation_membership( $user_id );

        if( ! $org_membership ){
            $user_org = get_user_meta( $user_id, 'user_organisation', true);
            $user_org_abn = get_user_meta( $user_id, 'user_organisation_abn', true);
            $user_org_web = get_user_meta( $user_id, 'user_organisation_web', true);
            $user_org_desc = get_user_meta( $user_id, 'user_organisation_desc', true);
        }else {
            $org_detail = new CT_Organisation( $org_membership->organisation_id );
            $user_org = $org_detail->organisation_name;
            $user_org_abn = $org_detail->abn;
            $user_org_web = $org_detail->organisation_website;
            $user_org_desc = $org_detail->organisation_description;
        }
    ?>        
    <div class="grid-box" id="my_org">
        <div class="grid-box-header">
            <h5>Organisation</h5>            
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
            <div class="grid-row">
                <div class="grid-cell width30P"><label>Name</label></div>
                <div data-name="user_organisation" data-value="<?php echo $user_org;?>" class="grid-cell in_input width70P"><?php echo !$user_org ? '-' : $user_org;?></div>
                <div class="clear"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell width30P"><label>Website</label></div>
                <div data-name="user_organisation_web" data-value="<?php echo $user_org_web;?>" class="grid-cell in_input"><?php echo !$user_org_web ? '-' : $user_org_web;?></div>
                <div class="clear"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell width30P"><label>Description</label></div>
                <div data-name="user_organisation_desc" data-value="<?php echo $user_org_desc;?>" class="grid-cell in_input"><?php echo !$user_org_desc ? '-' : $user_org_desc;?></div>
                <div class="clear"></div>
            </div>
            <div class="grid-row">
                <div class="grid-cell width30P"><label>ABN</label></div>
                <div data-name="user_organisation_abn" data-value="<?php echo $user_org_abn;?>" class="grid-cell in_input"><?php echo !$user_org_abn ? '-' : $user_org_abn;?></div>
                <div class="clear"></div>
            </div>            
        </div>
    </div>
	<?php do_action( 'bp_profile_field_buttons' ); ?>

<?php endif; ?>
<?php do_action( 'bp_after_profile_loop_content' ); ?>
