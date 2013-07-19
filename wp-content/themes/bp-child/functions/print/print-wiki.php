<?php
/***
* Wiki Page Print
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
        body{
            font-family: 'Open Sans',Arial,Tahoma,Helevetica,sans-serif; font-size: 12px; line-height: 14px; color: #111;            
            padding: 20px 20px;
        }
        a{color: #2c80e8; text-decoration: none;}
        h2{font-size: 20px; margin:0 0 20px;}
        h3{font-size: 18px; margin:0 0 15px;}
        h4{font-size: 16px;  margin:0 0 10px;}
        h5{font-size: 14px;  margin:0 0 8px;}
        table{border: solid 1px #333; border-collapse: collapse; vertical-align: top;}
        th{text-align: left; font-weight: bolid; border: solid 1px #333; padding: 5px;}
        td{border: solid 1px #333;  padding: 5px;}
        .clear{
            clear: both;
        }
        .block{
            padding: 10px 0;
            border-top: solid 1px #999;
        }
        .page-title-avatar{
            float: left;
        }
        .page-title-avatar img{
            height: 75px;
            width: auto !important;
        }
        #item-header-content{
            float: left;
            margin: 10px 0 0 15px;
        }
    </style>
     <?php if (have_posts()) while (have_posts()) : the_post(); ?>
        <?php
            cp_wiki_header();
        ?>
        <div class="clear"></div>
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
