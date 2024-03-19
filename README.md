# Woocommerce-validateOrderBeforePayment

It's related to this issue: https://www.reddit.com/r/woocommerce/comments/17zxa3e

This is a band-aid solution for woocommerce sites that have occasional orders with:
- duplicated shipping items,
- or WC shipping line item cost is not equal to postmeta shipping cost.

As WC filters such as "woocommerce_new_order" and "woocommerce_bacs_process_payment_order_status" don't cover all payment methods (such as bacs), I decided to call my valitor function on "/checkout/order-pay/" URL.

## How to use:
- Upload "validateOrderBeforePayment.php" file into your theme directory then Require it inside your theme "functions.php" file or require it inside your custom plugin:
```
  require_once __DIR__."/validateOrderBeforePayment.php";
```
- Add this code to your theme "cart/cart.php" file before the "wc_print_notices();" line:
```
  if( isset($_GET['pleaseMakeNewOrder']) ){
    wc_add_notice( 'Your Custom Notice Text', 'error' );
  }
```

I hope it helps you solve this mysterious woocommerce issue.
