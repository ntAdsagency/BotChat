<?php
require_once plugin_dir_path(__FILE__) . '../db-config.php';
function get_list_productIs()
{
    global $wpdb;

    $product_query = "
        SELECT 
            p.ID AS product_id, 
            p.post_title AS product_name,
            p.post_content AS product_description,
            pm_price.meta_value AS product_price,
            p.post_name,
            (
                SELECT 
                    pp.meta_value  
                FROM 
                    {$wpdb->postmeta} pp 
                WHERE 
                    pp.post_id = pm_image.meta_value 
                    AND pp.meta_key = '_wp_attached_file'
            ) AS url 
        FROM 
            {$wpdb->posts} p
        INNER JOIN 
            {$wpdb->postmeta} pm_price 
            ON p.ID = pm_price.post_id 
            AND pm_price.meta_key = '_price'
        INNER JOIN 
            {$wpdb->postmeta} pm_image 
            ON p.ID = pm_image.post_id 
            AND pm_image.meta_key = '_thumbnail_id'
        WHERE 
            p.post_type = 'product' 
            AND p.post_status = 'publish'
    ";

    $results = $wpdb->get_results($product_query);
    return $results;
}
