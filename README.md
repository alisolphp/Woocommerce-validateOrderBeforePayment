# Woocommerce-validateOrderBeforePayment

It's related to this issue: https://www.reddit.com/r/woocommerce/comments/17zxa3e

This is a band-aid solution for woocommerce sites that have occasional orders with:
- duplicated shipping items,
- or WC shipping line item cost is not equal to postmeta shipping cost.

As WC filters such as "woocommerce_new_order" and "woocommerce_bacs_process_payment_order_status" don't cover all payment methods (such as bacs), I decided to call my valitor function on "/checkout/order-pay/" URL.

I hope it helps you solve this mysterious woocommerce issue.
