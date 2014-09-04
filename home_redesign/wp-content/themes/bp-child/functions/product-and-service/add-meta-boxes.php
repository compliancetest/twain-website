<?php
/**
* Products & Service Section
*/
function add_products_and_services_metaboxes(){
//    add_meta_box("test_suites_metabox", "Select Certifications (Test Suites) ", 'products_and_services_test_suites_metabox_html', "product-service", "normal", "high");
    add_meta_box("related_products_metabox", "Select Related Products / Services ", 'products_and_services_related_products_metabox_html', "product-service", "normal", "high");
}

add_action('admin_init', 'add_products_and_services_metaboxes');


function products_and_services_related_products_metabox_html(){
    global $post;
    
    $myProducts = getUserProductsAndServices(null, array($post->ID));
    $product = new ProductAndService($post->ID);
    $product->loadRelatedProducts();
    
    ?>
    <?php if($myProducts){ ?>
    <table id="related-products-table">
        <thead>
            <tr>
                <th>Related Product</th>
                <th>Relationship</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($product->relatedProducts as $row){ ?>
            <tr>
                <td>
                    <select class="select" name="related-product[]">
                       <option value=""></option>
                       <?php foreach($myProducts as $p){ ?>
                       <option value="<?php echo $p->ID?>" <?php echo $p->ID == $row->related_product_id ? 'selected="selected"' : '' ?>><?php echo get_post_meta($p->ID, 'product_name', true)?></option>
                       <?php } ?>
                   </select>
                </td>
                <td>
                    <select class="select" name="related-product-relation[]">
                       <option value="Depends On" <?php echo $row->relationship == 'Depends On' ? 'selected="selected"' : '' ?>>Depends On</option>
                       <option value="Newer Version Of" <?php echo $row->relationship == 'Newer Version Of' ? 'selected="selected"' : '' ?>>Newer Version Of</option>
                   </select>
                </td>
                <td>
                    <a href="#" class="remove-btn">Remove</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="#" class="button" id="add-related-product">Add Related Product</a>
    <?php }else{ ?>
    <p>No Product/Service Found!</p>
    <?php } ?>
 
   <script type="text/javascript">
   jQuery(document).ready(function(){
       jQuery('#add-related-product').click(function(){
            jQuery('#related-products-table tbody').append('<tr>' + 
                '<td>' +
                    '<select class="select" name="related-product[]">' +
                       '<option value=""></option>' +
                       <?php foreach($myProducts as $p){ ?>
                       '<option value="<?php echo $p->ID?>"><?php echo get_post_meta($p->ID, 'product_name', true)?></option>' +
                       <?php } ?>
                   '</select>' +
                '</td>' +
                '<td>' +
                    '<select class="select" name="related-product-relation[]">' +
                       '<option value="Depends On">Depends On</option>' +
                       '<option value="Newer Version Of">Newer Version Of</option>' +
                   '</select>' +
                '</td>' +
                '<td>' +
                    '<a href="#" class="remove-btn">Remove</a>' +
                '</td>' +
            '</tr>');
            return false;
        });
        jQuery("#related-products-table").on('click', '.remove-btn', function(){
            jQuery(this).parents('tr').remove();
            return false;
        })
   })
       
   </script>
    <?php
    
}


//Save Product and service on admin
add_action('save_post', 'save_product_and_service_on_admin');

function save_product_and_service_on_admin($post_id) {
    global $post, $wpdb;
    
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    
    //Check Post Type
    if($post->post_type != 'product-service')
    {
        return $post_id;
    }
    
    if( wp_is_post_revision( $post_id ) )
    {
        return $post_id;
    }
    
    //Save Related Products
    $related_products = isset($_POST['related-product']) ? $_POST['related-product'] : array();
    $related_products_relations = isset($_POST['related-product-relation']) ? $_POST['related-product-relation'] : array();
    
    //remove old entries
    $query = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "products_relationships WHERE product_id=%d", $post_id);
    $wpdb->query($query);
    
    foreach($related_products as $i => $p)
    {
        if(!$p)
            continue;
        $wpdb->insert($wpdb->prefix . "products_relationships", array('product_id' => $post_id, 'related_product_id' => $p, 'relationship' => $related_products_relations[$i]));
    }    
    
} 

function _get_current_related_products($pid)
{
    $rows = get_post_meta($pid, 'related_products');

    return $rows;
}

function _get_certified_test_suites($pid)
{
    $rows = get_post_meta($pid, 'test_suites');
        
    return $rows;
}

