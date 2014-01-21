<?php
/*
 * Template Name: My Test Data
 */


if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
}
get_header();
?>
<div class="content" id="my_testdata">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">
        <?php $description = get_post_meta($post->ID, 'description', true);?>
        <?php if($description): ?>
        <div class="page-title-block column">
            <?php echo $description;?>
        </div>
        <?php endif; ?>
        <div class="column">
            <div class="grid-box table-box" id="my_test_data_profiles">
                <div class="grid-box-body">
                    <div class="thead tr">
                       <div class="td td-profile-name">Profile Name</div>
                       <div class="td td-profile-type">Type</div>
                       <div class="td td-action">Action</div>
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                   <?php
                   if(!$profileInstances){
                       ?>
                       <div class="tr">
                           <div class="td td-full">You have currently not created any data profiles.</div>
                           <div class="clear"></div>
                       </div>
                       <?php
                   }else{
                       foreach($profileInstances as $instance)
                       {
                           $instanceObj = json_decode(base64_decode($instance->content));
                   ?>
                        <div class="tr">
                           <div class="td td-profile-name">
                               <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $instance->id?>" class="view-profile-instance-link" ><?php echo $instance->profile_name?>
                               <?php
                                    if($instanceObj->Profile->Version)
                                    {
                                        $version = array();
                                        foreach(get_object_vars($instanceObj->Profile->Version) as $k=>$v)      
                                        {
                                            $version[] = $v;
                                        }
                                        echo " v" . implode(".", $version);
                                    }
                                ?>
                                </a> 
                               <br />
                               <b>Purpose: </b> <?php echo $instanceObj->Profile->Purpose?>                       
                               <p><?php echo $instanceObj->Profile->Description?></p>                   
                           </div>
                           <div class="td td-profile-type">
                               <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-type')?>&id=<?php echo $instance->type_id?>" rel="custom-popup" cp-type="ajax" class="view-profile-type-link"><?php echo $instance->profile_type_title; ?>
                                <?php
                                    $pJSON = json_decode(base64_decode($instance->schema));                            
                                    if($pJSON->Version)
                                    {
                                        $version = array();
                                        foreach(get_object_vars($pJSON->Version) as $k=>$v)      
                                        {
                                            $version[] = $v;
                                        }
                                        echo " v" . implode(".", $version);
                                    }
                                ?>
                               </a>                    
                           </div>
                           <div class="td td-action">
                                <?php
                                    if($instance->creator_id == get_current_user_id())
                                    {
                                ?>
                                <a href="#edit-profile-box" data-id="<?php echo $instance->id?>" data-type-id="<?php echo $instance->type_id?>" class="edit-profile-instance-link action-btn icon-btn edit-btn has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Edit Profile<span></span></span></a>
                                <a href="<?php echo get_site_url()?>/my-profile?td-action=<?php echo wp_create_nonce('delete-profile-instance')?>&id=<?php echo $instance->id?>&return=<?php echo base64_encode(get_site_url() . "/my-profile")?>" class="action-btn icon-btn delete-btn left10 has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Delete Profile<span></span></span></a>
                                <?php
                                    }
                                ?>
                           </div>
                           <div class="clear"></div>     
                           
                        </div>
                        
                   <?php
                       }
                   ?>
                   <?php
                   }
                   ?>
                   <div class="loading1"></div>
                   </div>
                   
                </div>             
            </div>
            <a href="#need-subscription-box" rel="custom-popup" cp-type="inline" class="action-btn add-new-btn table-bottom-btn right">
                <span class="p"></span>
                <span class="t">Add</span>
            </a>
            <div class="space20"></div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div> <!--end content-->
<?php
if(count($subscriptions) > 0){
?>

<?php 
    $profileTypes = getCustomerProfileTypes(get_current_user_id());
    
    $lastTypeID = getUserLastUsedProfileType('tester');
    
    $lastType = null;
?>
<div class="popup-box" id="edit-profile-box" style="display: none; width: 500px;">
    <form name="editProfileForm" id="editProfileForm" action="">
        <div class="popup-box-header radius6 noradiusbottom">Create Profile Instance</div>        
        <div class="popup-box-top-nav">            
            <div class="btn-row">      
                <h5 class="left nomarginbottom lineheight22px">Please Select Profile Type</h5>          
                <a href="#" class="action-btn cancel-btn right close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                <a href="#" class="action-btn process-btn right submit-btn"><span class="p"></span><span class="t">SAVE</span></a>            
                <div class="clear"></div>
            </div>
        </div>
        <div class="popup-box-content grid-box-body noshadow">                    
            <div class="edit-profile-content-inner">
                <select class="select left" name="profile-type-id" id="profile-type-id">
<!--                    <option value="">- Select -</option>-->
                    <?php foreach($profileTypes as $p){ ?>
                    <?php
                        if(!$lastType && !$lastTypeID)
                        {
                            $lastType = $p;
                        }else if(!$lastType && $p->id == $lastTypeID){
                            $lastType = $p;
                        }
                    ?>
                    <option value="<?php echo $p->id?>" <?php echo $lastType && $lastType->id == $p->id ? "selected='selected'" : "" ?>>
                        <?php 
                            echo $p->title;
                            $pJSON = json_decode(base64_decode($p->schema));
                            if($pJSON->Version)
                            {
                                $version = array();
                                foreach(get_object_vars($pJSON->Version) as $k=>$v)      
                                {
                                    $version[] = $v;
                                }
                                echo " v" . implode(".", $version);
                            }
                        ?>
                    </option>
                    <?php } ?>
                </select>
                <div class="clear"></div>
            
                <div id="edit_profile_instance_panel">
                    <div id="upload_profile_panel">                    
                        <div class="field-row">
                            <label class="padding5-10-5-0"> Upload Json file</label> 
                            <div class="grid-cell relative">                                                                    
                                <input type="file" name="profile_instance_file" class="input-file" id="profile_instance_file" file-type="doc" file-extensions="(.txt or .json file)" />                                
                                <a href="#" class="action-btn upload-btn plus" id="profile_instance_upload_btn"><span class="p"></span><span class="t">Upload</span></a>
                                <div class="clear"></div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="enter-values"><span>or</span></div>                
                    <label class="padding5-10-5-0">Enter Values</label> 
                    <div id="create_profile_panel">                
                        <div class="clear"></div>
                    </div>
                    <textarea id="profile_type_txt" class="displaynone"><?php echo $lastType ? base64_decode($lastType->schema) : ''?></textarea>                
                    <textarea id="profile_instance_txt" class="displaynone"></textarea>                
                </div>
            </div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">                            
            <div class="btn-row">
                <a href="#" class="action-btn cancel-btn close-popup-btn right"><span class="p"></span><span class="t">Cancel</span></a>            
                <a href="#" class="action-btn process-btn submit-btn right"><span class="p"></span><span class="t">SAVE</span></a>                            
                <div class="clear"></div>
            </div>
        </div>                        
        <a class="close_btn"></a>                        
        <div class="loading loading-with-text radius6"><div><b>LOADING DATA</b><p>Please wait...</p></div></div>
        <input type="hidden" name="instance-id" id="instance-id" value="" />
        <input type="hidden" id="save-instance-action" value="<?php echo wp_create_nonce('save-tester-instance')?>" />
        <input type="hidden" id="get-profile-ui-action" value="<?php echo wp_create_nonce('get-tester-profile-ui')?>" />
    </form>
</div>
<?php
}else{ 
?>
<div class="popup-box" id="need-subscription-box" style="display: none; width: 500px;">
    <div class="popup-box-header radius6 noradiusbottom">Need a subscription</div>        
    <div class="popup-box-content grid-box-body">                    
        <p class="message notice">You must subscribe to a test suite before you can create a test data profile.</p>
    </div>
    <div class="popup-box-footer radius6 noradiustop">                                    
        <div class="clear"></div>
    </div>                        
    <a class="close_btn"></a>                        
</div>
<?php
}
?>
<?php render_unsubscription_popup(); ?>

<script type="text/javascript">
jQuery(document).ready(function(){
    fixTdHeight(jQuery('#my_test_data_profiles'));
    //Fix Simple ToolTips
    jQuery('.td-status .simple_tooltip').each(function(){
        jQuery(this).css({'top': -1 * jQuery(this).outerHeight() - 6, 'margin-left': -1 * jQuery(this).outerWidth() / 2 + jQuery(this).parent().outerWidth() / 2});
    })
})
</script>
<?php
get_footer();
?>
