<?php
/***
* Static Page Print
*/
?>
<!DOCTYPE HTML>
<html>
    <head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php wp_title( '', true, 'right' ); ?></title>
    </head>
    <body onload="print_page()">
    <style type="text/css">
        body{font-family: 'Open Sans',Arial,Tahoma,Helevetica,sans-serif; font-size: 12px; line-height: 14px; color: #111;}a{color: #2c80e8; text-decoration: none; }h2{font-size: 20px;}h3{font-size: 18px;}h4{font-size: 16px}h5{font-size: 14px;}table{border: solid 1px #999; border-collapse: collapse; vertical-align: top;}th{text-align: left; font-weight: bolid; border: solid 1px #999; padding: 5px;}td{border: solid 1px #999;  padding: 5px;}
    </style>
     <?php if (have_posts()) while (have_posts()) : the_post(); ?>
        <?php if(!isset($hideHeader)){ ?>
        <div class="page-title-block column">
            <h2 style="text-align: center; margin-bottom: 20px"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="clear"></div>
        </div>        
        <?php } ?>
        <div class="content_inner column">                
            <?php if (has_post_thumbnail()) {
                echo '<a href="'.get_permalink().'">';
                the_post_thumbnail('post-thumb', array('class' => 'page_thumb'));
                echo '</a>';
            }
            the_content(); ?>
        </div>
    <?php endwhile; ?>
    </body>
    <script type="text/javascript">
        function print_page()
        {
            window.print();
        }
    </script>
</html>
