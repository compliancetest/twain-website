<?php

/**
 * BuddyPress - Users Header
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

$profileID = bp_current_user_id();

?>

<?php do_action('bp_before_member_header'); ?>
<div class="page-title-block column">
    <div id="item-header-avatar" class="profile-avatar">
        <a href="<?php bp_displayed_user_link(); ?>">
            <?php bp_displayed_user_avatar('type=full'); ?>
        </a>
    </div><!-- #item-header-avatar -->

    <div id="item-header-content" class="profile-title">
        <h3 class="left">
            <a href="<?php bp_displayed_user_link(); ?>">
                <?php
                echo get_user_meta($profileID, 'first_name', true) . ' ' . get_user_meta($profileID, 'last_name', true);
                ?>
            </a>
        </h3>

        <div class="clear"></div>
        <?php

        //Display User Detal for Support and Admins
        if (cp_is_customer_support_or_admin($profileID)) {
            $profileDetail = get_userdata($profileID);
            ?>
            <p>
                <b>Email Address:</b> <a
                    href="mailto:<?php echo $profileDetail->user_email ?>"><?php echo $profileDetail->user_email ?></a><br/>
                <b>Phone Number:</b> <?php echo get_user_meta($profileID, 'phone_number', true); ?>
            </p>
            <?php
        }

        ?>

        <?php
        $biography = get_user_meta($profileID, 'description', true);
        ?>
        <p class="profile-biography"><?php echo _convertLineSymbolToBR($biography) ?></p>
        <?php do_action('bp_before_member_header_meta'); ?>

    </div><!-- #item-header-content -->
    <div class="clear"></div>
    <?php do_action('bp_after_member_header'); ?>

    <?php do_action('template_notices'); ?>
</div>