<?php
/***
* Product/Service Print Page
*/

global $post;

$service = new Service( get_the_ID() );
$service->load();

?>
<!DOCTYPE HTML>
<html>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Service Details - <?php wp_title( '', true, 'right' ); ?></title>
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
    table{border: solid 1px #999; border-collapse: collapse; vertical-align: top; width: 100%}
    th{text-align: left; font-weight: bolid; border: solid 1px #999; padding: 5px;}
    td{border: solid 1px #999;  padding: 5px; vertical-align: top;}
    .clear{
        clear: both;
    }
    .block{
        padding: 10px 0;
        border-top: solid 1px #999;
    }
    .noborder,
    .noborder td,
    .noborder th{ border: none;}
    .td-label{
        font-weight: bold;
        font-size: 13px;
        vertical-align: top;
    }
</style>
<h2 style="text-align: center;">Service Details</h2>
<h3 style="border-bottom:solid 1px #999; line-height: 20px; padding-bottom: 8px; margin-bottom: 5px;">Service: <a href="<?php the_permalink()?>"><?php echo $service->service_name?></a></h3>
<table class="noborder">
    <tr>
        <td style="line-height: 25px; padding-right: 20px; white-space: nowrap;">
            Service Provider: <?php echo get_the_title( $service->service_product_id );?><br />
            Entity ID: <?php echo $service->service_id ;?><br />
            Process: <?php echo get_the_title( $service->service_suite_id );?><br />
            Role: <?php echo implode( ', ', $service->service_roles ) ;?><br />
            Level: <?php echo implode( ', ', $service->service_levels );?><br />
            Protocol:<?php echo $service->service_protocol ;?><br />
            End-Point: <?php echo $service->service_endpoint ;?><br />
            End-Point-Type: <?php echo $service->service_endpoint_type ;?><br />

        </td>
        <td style="padding-top: 10px; width: 80%;">
            <?php echo $service->service_description; ?>
        </td>
    </tr>
</table>
<?php //if($product->relatedProducts){?>
<!--<br />-->
<!--<br />-->
<!--<h5 style="border-bottom: solid 1px #999; padding-bottom: 8px; ">Related Products</h5>-->
<!--<table class="noborder">-->
<!--    --><?php //foreach ($product->relatedProducts as $rp){ ?>
<!--    <tr>-->
<!--        <td><b>--><?php //echo $rp->relationship?><!--:</b></td>-->
<!--        <td>-->
<!--            <a href="--><?php //echo get_permalink($rp->related_product_id)?><!--">--><?php //echo $rp->product_name?><!--</a>-->
<!--        </td>-->
<!--    </tr>-->
<!--    --><?php //} ?>
<!--</table>-->
<?php //}?>
<br />
<br />
<!--<h5>Certifications</h5>-->
<?php
//    $claims = getClaimsByProductId($product->id);
//?>
<?php //if(!$claims){?>
<!--<table><tr><td style="text-align: center">No Data Found!</td></tr></table>-->
<?php //}else{ ?>
<!--<table>-->
<!--    <thead>-->
<!--        <tr>-->
<!--            <th>Issuer</th>-->
<!--            <th>Suite</th>-->
<!--            <th>Role</th>-->
<!--            <th>Level</th>-->
<!--            <th>Status</th>-->
<!--            <th>Date</th>-->
<!--        </tr>-->
<!--    </thead>-->
<!--    --><?php //
//        foreach($claims as $claim){
//            $group = groups_get_group(array('group_id' => get_post_meta($claim->suite_id, 'community_id', true)));
//    ?>
<!--            <tr>-->
<!--                <td><a href="--><?php //echo bp_get_group_permalink($group)?><!--">--><?php //echo $claim->issuer?><!--</a></td>-->
<!--                <td><a href="--><?php //echo get_permalink($claim->suite_id)?><!--">--><?php //echo get_the_title($claim->suite_id)?><!--</a></td>-->
<!--                <td>--><?php //echo $claim->conformance_level?><!--</td>-->
<!--                <td>--><?php //echo $claim->role?><!--</td>-->
<!--                <td>-->
<!--                    --><?php //if($claim->status == 'Verified'){ ?>
<!--                    <span style="color: #4aa31e;">--><?php //echo $claim->status?><!--</span>-->
<!--                    --><?php //}else{ ?>
<!--                    <span style="color: #666;">--><?php //echo $claim->status?><!--</span>-->
<!--                    --><?php //} ?>
<!--                </td>-->
<!--                <td>--><?php //echo formatDate($claim->last_updated)?><!--</td>                    -->
<!--            </tr>-->
<!--    --><?php //
//        }
//    ?>
<!--</table>-->
<?php //} ?>
</body>
<script type="text/javascript">
    function print_page()
    {
        window.print();
    }
</script>
</html>