<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>



	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
				<th class="product-tax"><?php esc_html_e( '1HR FEE(S)', 'woocommerce' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Hours/Quantity', 'woocommerce' ); ?></th>
				<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-remove">
							<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								                   'woocommerce_cart_item_remove_link',
								                   sprintf(
								                   	'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
								                   	esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
								                   	esc_html__( 'Remove this item', 'woocommerce' ),
								                   	esc_attr( $product_id ),
								                   	esc_attr( $_product->get_sku() )
								                   ),
								                   $cart_item_key
								                );
								                ?>
								             </td>

								             <td class="product-thumbnail">
								             	<?php
								             	$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

								             	if ( ! $product_permalink ) {
							echo $thumbnail; // PHPCS: XSS ok.
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
						}
						?>
					</td>

					<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
						}
						?>
					</td>

					<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
						<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
								?>
							</td>

							<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
								<?php
								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $_product->get_max_purchase_quantity(),
											'min_value'    => '0',
											'product_name' => $_product->get_name(),
										),
										$_product,
										false
									);
								}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
						?>
					</td>

					<!-- Custom Tax column -->
					<td class="product-tax <?php echo $_product->get_id() ?>" data-title="<?php esc_attr_e( 'Surcharge', 'woocommerce' ); ?>">

						<?php
								//Check exist of plugin Conditional extra fees for WooCommerce By PI Websolution
						if(function_exists('pisol_free_conditional_fees_plugin_link')){
								$cust_surcharge_prod_id = $_product->id; //Get product ID
								$cust_surcharge_prod_quantity = $cart_item['quantity']; //Get product quantity
								?>

								<?php
								$feesloop = get_posts( array('post_per_page' => -1, 'post_type' => 'pi_fees_rule') );
								foreach($feesloop as $fees){
								// $title = $fees->post_title;
									$fees_id = $fees->ID;
								// $fees_type = get_post_meta( $fees_id, 'pi_fees_type', true);
								// $fees = get_post_meta( $fees_id, 'pi_fees', true);
								// $start_time = get_post_meta( $fees_id, 'pi_fees_start_time', true);
								// $end_time = get_post_meta( $fees_id, 'pi_fees_end_time', true);
								// $taxable_val = get_post_meta( $fees_id, 'pi_fees_taxable', true);
								// $tax_class = get_post_meta( $fees_id, 'pi_fees_tax_class', true);
								// $taxable = $taxable_val === 'yes' ? true : false;

							$cust_surcharge_prod_id_meta = get_post_meta( $fees_id); //Get fee's ID
							$cust_surcharge_prod_fees_meta_conditions = get_post_meta( $fees_id, 'pi_metabox', true); //Get fee's meta fields

							$rules = get_post_meta( $fees_id, 'pi_metabox', true);
							// global $cust_surcharge_prod_fees_meta_post_id_array;
							// $cust_surcharge_prod_fees_meta_post_id_array = array();

							foreach($rules as $rule){
									                	$cust_surcharge_prod_fees_meta_post_id = $rule['pi_value']['product']; //Get fee's conditional product ID
									                	$cust_surcharge_prod_fees_meta_quantity = $rule['pi_value']['quantity']; //Get fee's conditional product qty
									                	global $cust_surcharge_prod_fees_meta_post_id_array;
									                	$cust_surcharge_prod_fees_meta_post_id_array[] = $cust_surcharge_prod_fees_meta_post_id;
									                	//var_dump($cust_surcharge_prod_fees_meta_post_id_array);
									                }

									$feesloop_amounts = get_post_meta( $fees_id, 'pi_fees'); //Get all fees amount values
									foreach($feesloop_amounts as $amount){
										// if ($cust_surcharge_prod_id == $cust_surcharge_prod_fees_meta_post_id && $cust_surcharge_prod_quantity <= $cust_surcharge_prod_fees_meta_quantity) {
										if ($cust_surcharge_prod_id == $cust_surcharge_prod_fees_meta_post_id && $cust_surcharge_prod_quantity <= $cust_surcharge_prod_fees_meta_quantity) {
										//Compare product and conditional product ID and Quantity and show fee's amount
											echo get_woocommerce_currency_symbol();
											echo $amount;
											global $cust_surcharge_amount_sum;
											$cust_surcharge_amount_sum = $cust_surcharge_amount_sum + $amount;
											//var_dump($cust_surcharge_amount_sum);
										}
									}
								}
								// if( $cust_surcharge_prod_quantity > $cust_surcharge_prod_fees_meta_quantity){
								// 	_e( 'N/A', 'woocommerce' );
								// }
								$cust_surcharge_prod_fees_meta_post_id_array;
								// var_dump($cust_surcharge_prod_fees_meta_post_id_array);
								if (!in_array($cust_surcharge_prod_id, $cust_surcharge_prod_fees_meta_post_id_array) || $cust_surcharge_prod_quantity > $cust_surcharge_prod_fees_meta_quantity) {
									_e( 'N/A', 'woocommerce' );
								}
							}
							?>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
							<?php

								//Check exist of plugin Conditional extra fees for WooCommerce By PI Websolution
							if(function_exists('pisol_free_conditional_fees_plugin_link')){
								$cust_surcharge_prod_id = $_product->id; //Get product ID
								$cust_surcharge_prod_quantity = $cart_item['quantity']; //Get product quantity
								?>

								<?php
								$feesloop = get_posts( array('post_per_page' => -1, 'post_type' => 'pi_fees_rule') );
								foreach($feesloop as $fees){
								// $title = $fees->post_title;
									$fees_id = $fees->ID;
								// $fees_type = get_post_meta( $fees_id, 'pi_fees_type', true);
								// $fees = get_post_meta( $fees_id, 'pi_fees', true);
								// $start_time = get_post_meta( $fees_id, 'pi_fees_start_time', true);
								// $end_time = get_post_meta( $fees_id, 'pi_fees_end_time', true);
								// $taxable_val = get_post_meta( $fees_id, 'pi_fees_taxable', true);
								// $tax_class = get_post_meta( $fees_id, 'pi_fees_tax_class', true);
								// $taxable = $taxable_val === 'yes' ? true : false;

							$cust_surcharge_prod_id_meta = get_post_meta( $fees_id); //Get fee's ID
							$cust_surcharge_prod_fees_meta_conditions = get_post_meta( $fees_id, 'pi_metabox', true); //Get fee's meta fields

							$rules = get_post_meta( $fees_id, 'pi_metabox', true);
							foreach($rules as $rule){
									                	$cust_surcharge_prod_fees_meta_post_id = $rule['pi_value']['product']; //Get fee's conditional product ID
									                	$cust_surcharge_prod_fees_meta_quantity = $rule['pi_value']['quantity']; //Get fee's conditional product qty
									                }

									$feesloop_amounts = get_post_meta( $fees_id, 'pi_fees'); //Get all fees amount values
									foreach($feesloop_amounts as $amount){
// var_dump($cust_surcharge_prod_id);
// 										global $cust_surcharge_prod_fees_meta_post_id_array;
// var_dump($cust_surcharge_prod_fees_meta_post_id_array);

										// if ($cust_surcharge_prod_id == $cust_surcharge_prod_fees_meta_post_id && $cust_surcharge_prod_quantity == $cust_surcharge_prod_fees_meta_quantity) {
										if ($cust_surcharge_prod_id == $cust_surcharge_prod_fees_meta_post_id && $cust_surcharge_prod_quantity <= $cust_surcharge_prod_fees_meta_quantity) {
										//Compare product and conditional product ID and Quantity and show fee's amount
											// echo get_woocommerce_currency_symbol();
											// echo $amount;

																				// $custom_subtotal = WC()->cart->get_product_subtotal( $_product, $cart_item['quantity']);
											// $custom_subtotal = substr($custom_subtotal, 1);
											$custom_subtotal = (float) wc_get_price_to_display( $_product );
											$custom_subtotal_calc = intval($amount) + $custom_subtotal;
											echo '<span class="amount custom_subtotal_column--add_surcharge">';
											echo get_woocommerce_currency_symbol();
											echo number_format($custom_subtotal_calc, 2, '.', ' ');
											echo '</span>';


										} else{
	// echo $custom_subtotal = (int) wc_get_price_to_display( $_product );
											echo '<span class="amount custom_subtotal_column--default">';
//echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
											echo '</span>';
										}
									}
								}
							}
							global $cust_surcharge_prod_fees_meta_post_id_array;
								// var_dump($cust_surcharge_prod_fees_meta_post_id_array);
							if (!in_array($cust_surcharge_prod_id, $cust_surcharge_prod_fees_meta_post_id_array) || $cust_surcharge_prod_quantity > $cust_surcharge_prod_fees_meta_quantity) {
									echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
								}




								?>
							</td>
						</tr>
						<?php
					}
				}
				?>

				<?php do_action( 'woocommerce_cart_contents' ); ?>

				<tr>
					<td colspan="7" class="actions">

						<?php if ( wc_coupons_enabled() ) { ?>
							<div class="coupon">
								<label for="coupon_code"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
								<?php do_action( 'woocommerce_cart_coupon' ); ?>
							</div>
						<?php } ?>

						<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

						<?php do_action( 'woocommerce_cart_actions' ); ?>

						<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
					</td>
				</tr>

				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</tbody>
		</table>
		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>

	<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

	<div class="cart-collaterals">
		<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );
		?>
	</div>

	<?php do_action( 'woocommerce_after_cart' ); ?>