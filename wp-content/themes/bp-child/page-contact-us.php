<?php
/*
 * Template Name: Contact Us Page
 */
get_header();

?>

    <div class="content container"><!-- Start Content Container-->        
            
            <?php if (have_posts()) while (have_posts()) : the_post(); ?>
            <div class="page-title-block column">
                <h2 class="nomarginbottom"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            </div>        
            <div class="column">
                <?php
                    the_content(); 
                    
                    //Getting Address from theme options
                    $us_company_name = of_get_option('us_company_name');
                    $us_company_address = of_get_option('us_company_address');                    
                    $us_company_phone = of_get_option('us_company_phone');
                    
                    $au_company_name = of_get_option('au_company_name');
                    $au_company_address = of_get_option('au_company_address');
                    $au_company_phone = of_get_option('au_company_phone');
                    
                    $us_company_address_url = urlencode(str_replace(array("\r\n", "\n"), array(" ", " "), $us_company_address));
                    $au_company_address_url = urlencode(str_replace(array("\r\n", "\n"), array(" ", " "), $au_company_address));
                    
                    //Getting Contact Us Form ID
                    $formID = get_custom_field('contact-form-id');
                ?>
                <div class="clear"></div>
                <div class="contact-info-block left">                    
                    <img src="//maps.googleapis.com/maps/api/staticmap?center=<?php echo $us_company_address_url?>&zoom=13&size=470x300&sensor=false&maptype=roadmap&markers=color:red|<?php echo $us_company_address_url?>" />
                    <h3>Get in Touch: United States</h3>
                    <p class="company-name"><b><?php echo $us_company_name?></b></p>
                    <address><?php echo str_replace("\n", '<br />', $us_company_address)?></address>
                    <p>Phone: <b><?php echo $us_company_phone?></b></p>
                </div>
                <div class="contact-info-block right">
                    <img src="//maps.googleapis.com/maps/api/staticmap?center=<?php echo $au_company_address_url?>&zoom=13&size=470x300&sensor=false&maptype=roadmap&markers=color:red|<?php echo $au_company_address_url?>" />
                    <h3>Get in Touch: Australia</h3>
                    <p class="company-name"><b><?php echo $au_company_name?></b></p>
                    <address><?php echo str_replace("\n", '<br />', $au_company_address)?></address>
                    <p>Phone: <b><?php echo $au_company_phone?></b></p>
                </div>
                <div class="clear"></div>
                <div id="contact-form-wrapper">
                <?php echo do_shortcode('[contact-form-7 id="' . $formID . '" title="Contact Us Form"]'); ?>
                </div>
            </div>
            <?php endwhile; ?>
            
        
        <div class="clear"></div>
        
    </div> <!--End content container-->    

<?php
get_footer();
?>
