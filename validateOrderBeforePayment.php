<?php

if( isset($_GET['key']) ){
    
    $cur=$_SERVER['REQUEST_URI'];
    if( stristr($cur, "/checkout/order-pay/") !== false ){
        
        $id = explode("/checkout/order-pay/", $cur)[1];
        $id = explode("/", $id)[0];
        
        if( stristr($id, "?key") === false ){
            auto_cancel_order_if_it_is_problematic( $id );
        }
        
    }
    
}

function auto_cancel_order_if_it_is_problematic( $order_id, $displayError = false ){
    global $wpdb;
    
    $id = $wpdb->_real_escape($order_id);
    
    if( "auto cancel order if it is problematic"){
        /*    
            - if order has 2 shippings
            or
            - if order shipping cost in postmeta is not equal to shipping itemmeta value
                => order is problematic and should not be purchased
                => set order status (wc-cancelled) and force user to reorder
        */
        if("check order is problematic or not"){
            
            $autoCancelReason = "";
            
            if("check if order has 2 shippings"){
                
                if( $autoCancelReason == "" ){
                    $query = "
                        SELECT count(*) as `order_shippings_rows_count` FROM `".$wpdb->prefix."woocommerce_order_items` WHERE `order_id` = ".$id." and order_item_type = 'shipping'
                    ";
                    $results = $wpdb->get_results($query);
                    foreach( $results as $result ) {
                        $order_shippings_rows_count = $result->order_shippings_rows_count;
                    }
                    
                    if( $order_shippings_rows_count > 1 ){
                        $autoCancelReason = "Order shipping items are duplicated!";
                    }
                }
                
            }
            
            if("check if order shipping cost in postmeta is not equal to shipping itemmeta value"){
                
                if( $autoCancelReason == "" ){
                    $query = "
                        select 
                            * 
                        from 
                            (
                            SELECT 
                                meta_value as `order_shipping_itemmeta_cost_value` 
                                FROM `".$wpdb->prefix."woocommerce_order_itemmeta` 
                                WHERE 
                                    order_item_id = (
                                        SELECT 
                                            order_item_id 
                                        FROM 
                                            `".$wpdb->prefix."woocommerce_order_items` 
                                        WHERE 
                                            `order_id` = ".$id." 
                                            and order_item_type = 'shipping' 
                                        limit 1
                                    ) and meta_key = 'cost'
                            ) t1, 
                            (
                                SELECT 
                                    meta_value as `order_shipping_postmeta_value` 
                                FROM 
                                    `".$wpdb->prefix."postmeta` 
                                WHERE 
                                    `post_id` = ".$id." 
                                    and meta_key = '_order_shipping'
                                limit 1
                            ) t2
                    ";
                    $results = $wpdb->get_results($query);
                    foreach( $results as $result ) {
                        $order_shipping_itemmeta_cost_value = $result->order_shipping_itemmeta_cost_value;
                        $order_shipping_postmeta_value = $result->order_shipping_postmeta_value;
                    }
                    if( $order_shipping_itemmeta_cost_value != $order_shipping_postmeta_value ){
                        $autoCancelReason = "Order woocommerce shipping line item value is not equal to postmeta shipping!";
                    }
                }
                
            }
            
        }
        
        if("cancel order if you have any reason"){
            
            if( $autoCancelReason != "" ){
                
                $addedTextToPostExcerpt = "Unkown error is occured. (".$autoCancelReason.") ";
                
                $query = "
                    update
                        ".$wpdb->prefix."posts
                    set 
                        post_status = 'wc-cancelled'
                        , post_excerpt = concat('".$addedTextToPostExcerpt."', post_excerpt)
                    where
                        ID = ".$id."
                    limit 1
                ";
                
                $updateOrder = $wpdb->get_results($query);
                
                if( $displayError ){
                    die($addedTextToPostExcerpt);
                }
                
                
                // wc_add_notice( $addedTextToPostExcerpt, 'error' );
                /* Note:
                    Adding notice didn't work in my case,
                    => So I decided to:
                        1) Add "pleaseMakeNewOrder" query to the cart url.
                        2) Display my custom notice in the cart page if this query is set.
                        
                        theme/woocommerce/cart/cart.php:
                            <?php
                            if ( ! defined( 'ABSPATH' ) ) {
                            	exit;
                            }
                            if( isset($_GET['pleaseMakeNewOrder']) ){
                                wc_add_notice( 'Your Custom Notice Text', 'error' );
                            }
                            wc_print_notices();
                            
                */
                
                header('Location: /cart/?pleaseMakeNewOrder');
                exit;
                
            }
            
        }
        
    }
    
}
