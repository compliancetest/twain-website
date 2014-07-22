<?php
/**
* Organisations
*/
require_once (dirname(__FILE__) . '/functions.php');
require_once (dirname(__FILE__) . '/class.organisation.php');
require_once (dirname(__FILE__) . '/controller.php');

add_action("init", "ct_process_organisation_action");
function ct_process_organisation_action()
{
    $action = isset($_REQUEST['_organisation_none']) ? $_REQUEST['_organisation_none'] : null;
    if ($action) {
        $controller = new CT_Organisation_Controller();
        //Purchase Subscription
        if (wp_verify_nonce($action, 'purchase_subscribe')) {
            if (!is_user_logged_in()) {
                echo "Please login to purchase a subcription.";
                exit;
            }
            
            $user_id = get_current_user_id();
            
            //Check Card info
            if(isset($_POST['card_id']) && $_POST['card_id'])
            {
                //Read Card Info
                $card = getUserCardById($_POST['card_id']);
            }
            
            if(!$card)
            {
                $_POST['card_expiry'] = $_POST['exp_month'] . "/" . $_POST['exp_year'];
                $_POST['id'] = '';
                $card_id = cp_user_payment_save();
                if(!is_int($card_id))
                {
                    //Card Error
                    echo $card_id;
                    exit;
                }
                
                $card = getUserCardById($card_id);
            }
            
            if(!$card)
            {
                echo "Your card is not incorrect.";
                exit;        
            }
            
            $result = $controller->subscribe($user_id, $_POST);
            if ($result) {
                echo 'success';
            } else {
                echo $result;
            }
            
            exit;            
        } 
    }
}