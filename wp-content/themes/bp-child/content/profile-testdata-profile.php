<?php
/**
* Profile - My Test Data Profiles
*/
if(!defined('ABSPATH'))
    die('Invalid Request!');    

$profileInstances = getCustomerProfileInstances();
?>
<div class="column left three_fifths nopadding">
    <div class="grid-box table-box" id="my_test_data_profiles">
        <div class="grid-box-header">
            <h5 class="left">My Test Data Management</h5>
            <?php
                if(count($subscriptions) > 0){
            ?>
                <a class="gbh-btn gbh-btn-add right" id="add-new-test-data-link" href="#edit-profile-box">Add<span class="simple_tooltip radius6">Add Test Data<span></span></span></a>
            <?php }else{ ?>
                <a class="gbh-btn gbh-btn-add right" href="#need-subscription-box" rel="custom-popup" cp-type="inline" >Add<span class="simple_tooltip radius6">Add Test Data<span></span></span></a>
            <?php } ?>
            <div class="clear"></div>
        </div>
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
                   <div class="td td-full">No data found.</div>
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
</div>
<?php $my_test_data_profiles_desc = get_post_meta($post->ID, 'my_test_data_profiles_desc', true);?>
<?php if ($my_test_data_profiles_desc): ?>
<div class="right two_fifths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        <?php echo $my_test_data_profiles_desc;?>
    </div>
</div>
<?php endif; ?>
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
                                <input type="file" name="profile_instance_file" class="input-file" id="profile_instance_file" file-type="image" file-extensions="(.jpg, .png, .gif or .jpeg file)" />                                
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