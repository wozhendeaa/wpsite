<?php

/* @author    2codeThemes
*  @package   WPQA/payments
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Stripe payment */
add_action('wp_ajax_wpqa_stripe_payment','wpqa_stripe_payment');
add_action('wp_ajax_nopriv_wpqa_stripe_payment','wpqa_stripe_payment');
function wpqa_stripe_payment() {
	$result        = array();
	$user_id       = get_current_user_id();
	$custom        = (isset($_POST['custom'])?esc_html($_POST['custom']):'');
	$item_name     = esc_html($_POST['item_name']);
	$item_number   = esc_html($_POST['item_number']);
	$name          = esc_html($_POST['name']);
	$payer_email   = esc_html($_POST['email']);
	$line1         = (isset($_POST['line1'])?esc_html($_POST['line1']):'');
	$line1         = (isset($_POST['line1'])?esc_html($_POST['line1']):'');
	$postal_code   = (isset($_POST['postal_code'])?esc_html($_POST['postal_code']):'');
	$country       = (isset($_POST['country'])?esc_html($_POST['country']):'');
	$city          = (isset($_POST['city'])?esc_html($_POST['city']):'');
	$state         = (isset($_POST['state'])?esc_html($_POST['state']):'');
	$payment       = floatval($_POST['payment']);
	$item_price    = floatval($payment*100);
	$str_replace   = str_replace('wpqa_'.$item_number.'-','',$custom);
	$currency_code = wpqa_get_currency($user_id);

	if ($line1 != '') {
		update_user_meta($user_id,'line1',$line1);
	}
	if ($line1 != '') {
		update_user_meta($user_id,'line1',$line1);
	}
	if ($postal_code != '') {
		update_user_meta($user_id,'postal_code',$postal_code);
	}
	if ($country != '') {
		update_user_meta($user_id,'country',$country);
	}
	if ($city != '') {
		update_user_meta($user_id,'city',$city);
	}
	if ($state != '') {
		update_user_meta($user_id,'state',$state);
	}
	
	require_once plugin_dir_path(dirname(__FILE__)).'payments/stripe/init.php';
	$stripe_test = wpqa_options("stripe_test");
	$stripe = new \Stripe\StripeClient(wpqa_options(($stripe_test == "on"?"test_":"").'secret_key'));
	try {
		if ((strpos($custom,'wpqa_subscribe-') !== false || strpos($custom,"pay-subscribe-module") !== false) && $str_replace != 'lifetime') {
			$product_id = get_option("wpqa_product_".$str_replace."_".$item_price."_".strtolower($currency_code));
			if ($product_id != "") {
				$get_product = $stripe->products->retrieve($product_id);
			}
			if (!isset($get_product) || (isset($get_product) && !isset($get_product->id))) {
				$product_id = wpqa_stripe_new_product($stripe,$item_name,$str_replace,$item_price,$currency_code);
			}

			$plan_id = get_option("wpqa_plan_".$str_replace."_".$item_price."_".strtolower($currency_code));
			if ($plan_id != "") {
				$get_plan = $stripe->plans->retrieve($plan_id);
				if (isset($get_plan->amount) && $get_plan->amount != $item_price) {
					$get_plan->delete();
					$plan_not_found = true;
				}else if (!isset($get_plan->amount)) {
					$plan_not_found = true;
				}
			}
			if (isset($plan_not_found) || !isset($get_plan) || (isset($get_plan) && !isset($get_plan->id))) {
				$plan_id = wpqa_stripe_new_plan($stripe,$str_replace,$currency_code,$product_id,$item_name,$item_price);
			}
		}else {
			$invoiced = true;
			$product_id = get_option("wpqa_product_".$str_replace."_".$item_price."_".strtolower($currency_code));
			if ($product_id != "") {
				$get_product = $stripe->products->retrieve($product_id);
			}
			if (!isset($get_product) || (isset($get_product) && !isset($get_product->id))) {
				$product_id = wpqa_stripe_new_product($stripe,$item_name,$str_replace,$item_price,$currency_code);
			}

			$price_id = get_option("wpqa_price_".$str_replace."_".$item_price."_".strtolower($currency_code));
			if ($price_id != "") {
				$get_price = $stripe->prices->retrieve($price_id);
			}
			if (!isset($get_price) || (isset($get_price) && !isset($get_price->id))) {
				$price_id = wpqa_stripe_new_price($stripe,$product_id,$item_price,$str_replace,$currency_code);
			}
		}
		if (isset($_POST['payment-intent-id']) && $_POST['payment-intent-id'] != '') {
			$charge = $stripe->paymentIntents->retrieve(esc_html($_POST['payment-intent-id']));
			wpqa_finish_stripe_payment($stripe,$charge->payment_method,$charge->customer);
			if (isset($charge->status) && ($charge->status == 'active' || $charge->status == 'paid' || $charge->status == 'succeeded')) {
				$success = true;
			}else {
				$result['success'] = 0;
				$result['error']   = esc_html__('Transaction has been failed.','wpqa');
			}
		}else if (isset($_POST['payment-method-id']) && $_POST['payment-method-id'] != '') {
			$wpqa_stripe_nonce = get_user_meta($user_id,"wpqa_stripe_nonce",true);
			if ($wpqa_stripe_nonce != "done" && (!isset($_POST['wpqa_stripe_nonce']) || !wp_verify_nonce($_POST['wpqa_stripe_nonce'],'wpqa_stripe_nonce'))) {
				$result['success'] = 0;
				$result['error']   = esc_html__('There is an error, Please reload the page and try again.','wpqa');
			}else {
				$payment_method_id = esc_html($_POST['payment-method-id']);
				$customer_args = array(
					'payment_method'   => $payment_method_id,
					'name'             => $name,
					'email'            => $payer_email,
					'invoice_settings' => array(
						'default_payment_method' => $payment_method_id
					)
				);
				$customer_address = array();
				if ($line1 != '') {
					$customer_address['line1'] = $line1;
				}
				if ($country != '') {
					$customer_address['country'] = $country;
				}
				if ($city != '') {
					$customer_address['city'] = $city;
				}
				if ($state != '') {
					$customer_address['state'] = $state;
				}
				if ($postal_code != '') {
					$customer_address['postal_code'] = $postal_code;
				}
				if (isset($customer_address) && !empty($customer_address)) {
					$customer_args['address'] = $customer_address;
				}
				$customer_description = $item_name;
				if (isset($customer_description) && $customer_description != '') {
					$customer_args['description'] = $customer_description;
				}
				if (isset($customer_metadata)) {
					$customer_args['metadata'] = $customer_metadata;
				}
				$wpqa_stripe_customer = get_user_meta($user_id,'wpqa_stripe_customer',true);
				if ($wpqa_stripe_customer != "") {
					$customer = $stripe->customers->retrieve($wpqa_stripe_customer);
				}
				$customer_id = (isset($customer->id)?$customer->id:"");
				if ($customer_id == "") {
					$customer = $stripe->customers->create($customer_args);
					$customer_id = $customer->id;
					update_user_meta($user_id,'wpqa_stripe_customer',$customer_id);
				}
				$metadata = ['user_id' => $user_id,'custom' => $custom,'str_replace' => $str_replace,'item_number' => $item_number,'item_price' => $item_price,'currency_code' => $currency_code];
				$args = array(
					'customer' => $customer_id,
					'metadata' => $metadata,
				);
				$payment_description = $item_name;
				if (isset($payment_description) && $payment_description != '') {
					$args['description'] = $payment_description;
				}
				if ((strpos($custom,'wpqa_subscribe-') !== false || strpos($custom,"pay-subscribe-module") !== false) && $str_replace != 'lifetime') {
					$_coupon = get_user_meta($user_id,$user_id.'_coupon',true);
					if ($_coupon != '') {
						$coupons = wpqa_options('coupons');
						$wpqa_find_coupons = wpqa_find_coupons($coupons,$_coupon);
						$coupon_name = preg_replace('/[^a-zA-Z0-9._\-]/','',strtolower($coupons[$wpqa_find_coupons]['coupon_name']));
						$coupon_amount = (int)$coupons[$wpqa_find_coupons]['coupon_amount'];
						$coupon_type = $coupons[$wpqa_find_coupons]['coupon_type'];
						$coupon_id = $coupon_amount.'_'.$coupon_name;
						$get_coupon = $stripe->coupons->retrieve($coupon_id);
						if ($coupon_type == "percent") {
							$the_discount = ($payment*$coupon_amount)/100;
							$payment = $payment-$the_discount;
						}else if ($coupon_type == "discount") {
							$payment = $payment-$coupon_amount;
						}
					}

					$args['items']  = [['plan' => $plan_id,'quantity' => ($str_replace == '2years'?2:1)]];
					$args['expand'] = ['latest_invoice.payment_intent'];
					$args['coupon'] = (isset($get_coupon) && isset($get_coupon->id)?$get_coupon->id:'');

					$charge = $stripe->subscriptions->create($args);
					update_user_meta($user_id,"wpqa_subscr_id",$charge->id);
				}else {
					$wpqa_stripe_customer = get_user_meta($user_id,'wpqa_stripe_customer',true);
					if (isset($invoiced)) {
						$invoice = $stripe->invoices->create(['customer' => $customer_id]);
						$invoiceItem = $stripe->invoiceItems->create(['customer' => $customer_id,'description' => $item_name,'currency' => $currency_code,'price' => $price_id,"invoice" => $invoice->id]);
					}

					$args['amount']              = $item_price;
					$args['currency']            = $currency_code;
					$args['confirmation_method'] = 'automatic';
					$args['confirm']             = true;
					$args['payment_method']      = $payment_method_id;

					$charge = $stripe->paymentIntents->create($args);
				}
				if (isset($charge->status) && (($charge->status == 'requires_action' && $charge->next_action->type == 'use_stripe_sdk') || $charge->status == 'incomplete')) {
					if ($charge->status == 'incomplete' && (strpos($custom,'wpqa_subscribe-') !== false || strpos($custom,"pay-subscribe-module") !== false) && isset($payment_method_id)) {
						wpqa_finish_stripe_payment($stripe,$payment_method_id,$charge->customer);
					}
					$result['confirm_card']   = 1;
					$result['success']        = 0;
					$result['client_secret']  = (isset($charge->client_secret)?esc_html($charge->client_secret):(isset($charge->latest_invoice->payment_intent->client_secret)?esc_html($charge->latest_invoice->payment_intent->client_secret):''));
					$result['payment_method'] = $charge->id;
				}else if ($charge->status == 'active' || $charge->status == 'paid' || $charge->status == 'succeeded') {
					if (isset($invoiced)) {
						$invoice = $stripe->invoices->pay($invoice->id,array('paid_out_of_band' => true));
					}
					$success = true;
				}else {
					$result['success'] = 0;
					$result['error']   = esc_html__('Transaction has been failed.','wpqa');
				}
			}
		}else {
			$result['success'] = 0;
			$result['error']   = esc_html__('Transaction has been failed.','wpqa');
		}
		if (isset($success) && $success == true) {
			wpqa_make_final_stripe_step($charge,$user_id);
			$redirect_to = wpqa_get_redirect_link($custom,$item_number,$user_id);
			$result['success']  = 1;
			$result['redirect'] = $redirect_to;
		}else if (!isset($result['confirm_card'])) {
			$result['success'] = 0;
			$result['error']   = esc_html__('Transaction has been failed.','wpqa');
		}
	}catch ( \Stripe\Exception\CardException $e ) {
		$result['success'] = 0;
		$result['error']   = $e->getError()->message;
	}catch ( Exception $e ) {
		$error_message = $e->getMessage();
		if ((strpos($custom,'wpqa_subscribe-') !== false || strpos($custom,"pay-subscribe-module") !== false) && strpos($error_message,'No such plan:') !== false && $str_replace != 'lifetime') {
			wpqa_stripe_new_plan($stripe,$str_replace,$currency_code,$product_id,$item_name,$item_price);
			$result['resubmit_again'] = 1;
		}else if (strpos($error_message,'No such product:') !== false) {
			wpqa_stripe_new_product($stripe,$item_name,$str_replace,$item_price,$currency_code);
			$result['resubmit_again'] = 1;
		}else if (strpos($error_message,'No such price:') !== false) {
			wpqa_stripe_new_price($stripe,$product_id,$item_price,$str_replace,$currency_code);
			$result['resubmit_again'] = 1;
		}else if (strpos($error_message,'No such customer:') !== false) {
			$customer = $stripe->customers->create($customer_args);
			$customer_id = $customer->id;
			update_user_meta($user_id,'wpqa_stripe_customer',$customer_id);
			$result['resubmit_again'] = 1;
		}
		$result['success'] = 0;
		if (!isset($result['resubmit_again'])) {
			$result['error'] = $error_message;
		}
	}
	echo json_encode(apply_filters('wpqa_json_stripe_payment',$result));
	die();
}
/* Create a new product */
function wpqa_stripe_new_product($stripe,$item_name,$str_replace,$item_price,$currency_code) {
	$product = $stripe->products->create([
		'name' => $item_name,
		'type' => 'service',
	]);
	if (isset($product->id)) {
		$product_id = $product->id;
		update_option("wpqa_product_".$str_replace."_".$item_price."_".strtolower($currency_code),$product->id);
		return $product_id;
	}
}
/* Create a new plan */
function wpqa_stripe_new_plan($stripe,$str_replace,$currency_code,$product_id,$item_name,$item_price) {
	$interval = ($str_replace == 'yearly'?'year':'month');
	$interval_count = ($str_replace == 'monthly' || $str_replace == 'yearly' || $str_replace == '2years'?1:($str_replace == '3months'?3:6));
	$plan = $stripe->plans->create([
		'currency'       => $currency_code,
		'interval'       => $interval,
		'interval_count' => $interval_count,
		'product'        => $product_id,
		'nickname'       => $item_name,
		'amount'         => $item_price,
	]);
	if (isset($plan->id)) {
		$plan_id = $plan->id;
		update_option("wpqa_plan_".$str_replace."_".$item_price."_".strtolower($currency_code),$plan->id);
		return $plan_id;
	}
}
/* Create a new price */
function wpqa_stripe_new_price($stripe,$product_id,$item_price,$str_replace,$currency_code) {
	$price = $stripe->prices->create([
		'unit_amount' => $item_price,
		'currency' => $currency_code,
		'product' => $product_id,
	]);
	if (isset($price->id)) {
		$price_id = $price->id;
		update_option("wpqa_price_".$str_replace."_".$item_price."_".strtolower($currency_code),$price->id);
		return $price_id;
	}
}
/* Finish stripe payment */
function wpqa_finish_stripe_payment($stripe,$payment_method_id,$get_the_customer_id) {
	$payment_method = $stripe->paymentMethods->retrieve($payment_method_id);
	$payment_method->attach(['customer' => $get_the_customer_id]);
	$update_customer = $stripe->customers->update(
		$get_the_customer_id,[
			'invoice_settings' => [
				'default_payment_method' => $payment_method_id,
			],
		]
	);
}
/* Final Stripe step */
function wpqa_make_final_stripe_step($charge,$user_id) {
	$wpqa_subscr_id = get_user_meta($user_id,"wpqa_subscr_id",true);
	$response = $charge->jsonSerialize();
	$metadata = $response['metadata'];
	$item_transaction = (isset($response['charges']['data'][0]['balance_transaction'])?$response['charges']['data'][0]['balance_transaction']:(isset($response['latest_invoice']['payment_intent']['charges']['data'][0]['balance_transaction'])?$response['latest_invoice']['payment_intent']['charges']['data'][0]['balance_transaction']:''));
	$latest_charge = (isset($response['latest_charge'])?$response['latest_charge']:(isset($response['latest_invoice']['charge'])?$response['latest_invoice']['charge']:""));
	$item_transaction = ($item_transaction != ""?$item_transaction:$latest_charge);
	$subscr_id = ($wpqa_subscr_id != ""?$wpqa_subscr_id:(isset($response['id'])?$response['id']:$response['id']));
	if (isset($metadata['item_number']) && isset($metadata['item_price']) && isset($metadata['custom']) && isset($metadata['str_replace']) && isset($metadata['currency_code'])) {
		$array = array (
			'item_no'          => $metadata['item_number'],
			'item_number'      => $metadata['item_number'],
			'item_price'       => floatval($metadata['item_price']/100),
			'custom'           => $metadata['custom'],
			'item_currency'    => $metadata['currency_code'],
			'currency_code'    => $metadata['currency_code'],
			'item_name'        => $response['description'],
			'item_transaction' => $item_transaction,
			'latest_charge'    => $latest_charge,
			'sandbox'          => ($response['livemode'] == true?"":"testing"),
			'payment'          => 'Stripe',
			'id'               => ($subscr_id == $response['id']?(isset($response['latest_invoice']['payment_intent']['id'])?$response['latest_invoice']['payment_intent']['id']:$response['id']):$response['id']),
			'customer'         => ($wpqa_subscr_id == ''?$response['customer']:''),
			'subscr_id'        => ((strpos($metadata['custom'],'wpqa_subscribe-') !== false || strpos($metadata['custom'],"pay-subscribe-module") !== false) && $metadata['str_replace'] != 'lifetime'?$subscr_id:''),
		);
		delete_user_meta($user_id,"wpqa_stripe_nonce");
		wpqa_payment_succeeded($user_id,$array);
	}
}
/* Get headers request */
function wpqa_request_headers() {
	if (!function_exists('getallheaders')) {
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if ('HTTP_' === substr($name,0,5)) {
				$headers[str_replace(' ','-',ucwords(strtolower(str_replace('_',' ',substr($name,5)))))] = $value;
			}
		}
		return $headers;
	}else {
		return getallheaders();
	}
}
/* Stripe webhooks */
add_action("wpqa_init","wpqa_stripe_data_webhooks");
function wpqa_stripe_data_webhooks() {
	if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "stripe") {
		$request_headers = array_change_key_case(wpqa_request_headers(),CASE_UPPER);
		$is_valid = empty($request_headers['USER-AGENT']) || preg_match('/Stripe/',$request_headers['USER-AGENT']);
		$is_valid = apply_filters('wpqa_stripe_webhook_is_valid',$is_valid,$request_headers);
		if ($is_valid) {
			require_once plugin_dir_path(dirname(__FILE__)).'payments/stripe/init.php';
			$stripe_test = wpqa_options("stripe_test");
			$secret_key = wpqa_options(($stripe_test == "on"?"test_":"").'secret_key');
			if ($secret_key != "") {
				\Stripe\Stripe::setApiKey($secret_key);

				$webhook_secret = wpqa_options(($stripe_test == "on"?"test_":"").'webhook_secret');

				$payload = @file_get_contents('php://input');
				$event = null;

				try {
					$event = \Stripe\Event::constructFrom(
						json_decode($payload, true)
					);
				}catch(\UnexpectedValueException $e) {
					error_log('⚠️  Webhook error while parsing basic request.');
					http_response_code(400);
					exit();
				}
				if ($webhook_secret != "") {
					$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
					try {
						$event = \Stripe\Webhook::constructEvent(
							$payload, $sig_header, $webhook_secret
						);
					}catch(\Stripe\Exception\SignatureVerificationException $e) {
						error_log('⚠️  Webhook error while validating signature.');
						http_response_code(400);
						exit();
					}
				}
				if ($event->type == 'payment_method.attached') {
					$paymentmethod = $event->data->object;
				}else if ($event->type == 'payment_intent.succeeded') {
					$paymentintent = $event->data->object;
					$item_transaction = (isset($paymentintent->balance_transaction) && $paymentintent->balance_transaction != ""?$paymentintent->balance_transaction:(isset($paymentintent->invoice) && $paymentintent->invoice != ""?$paymentintent->invoice:''));
					$latest_charge = (isset($paymentintent->latest_charge)?$paymentintent->latest_charge:(isset($paymentintent->latest_invoice->charge)?$paymentintent->latest_invoice->charge:""));
					$item_transaction = ($item_transaction != ""?$item_transaction:$latest_charge);
					if (isset($paymentintent->metadata) && !empty($paymentintent->metadata)) {
						global $wpdb;
						$query_sql = $wpdb->prepare("SELECT $wpdb->posts.ID FROM $wpdb->posts WHERE 1 = %s AND ($wpdb->posts.post_content LIKE '%$item_transaction%' OR $wpdb->posts.post_content LIKE '%$latest_charge%') AND $wpdb->posts.post_type = 'statement' AND $wpdb->posts.post_status = 'publish' LIMIT 5",1);
						$query = $wpdb->get_results($query_sql);
						if (!is_array($query) || (is_array($query) && empty($query))) {
							$users = get_users(array('meta_key' => 'wpqa_stripe_customer','meta_value' => $paymentintent->customer,'number' => 1,'count_total' => false));
							$user_id = (isset($users[0]) && isset($users[0]->ID) && $users[0]->ID > 0?$users[0]->ID:0);
							wpqa_make_final_stripe_step($paymentintent,$user_id);
						}
					}else if (isset($paymentintent->customer) && $paymentintent->customer != "") {
						$status = (isset($paymentintent->status)?$paymentintent->status:"");
						if ($status == "active" || $status == "paid" || $status == "succeeded") {
							$args = array(
								'meta_key'       => 'payment_customer',
								'meta_value'     => $paymentintent->customer,
								'post_type'      => 'statement',
								'posts_per_page' => -1
							);
							$query = new WP_Query($args);
							if ($query->have_posts()) {
								$post_id = (isset($query->posts[0]->ID)?$query->posts[0]->ID:0);
								if ($post_id > 0) {
									$user_id = $query->posts[0]->post_author;
									$package_subscribe = get_post_meta($post_id,"payment_replace",true);
									if ($package_subscribe == "") {
										$package_subscribe = get_user_meta($user_id,"package_subscribe",true);
									}
								}
							}else {
								$users = get_users(array('meta_key' => 'subscribe_renew_id','meta_value' => $paymentintent->customer,'number' => 1,'count_total' => false));
								$user_id = (isset($users[0]) && isset($users[0]->ID) && $users[0]->ID > 0?$users[0]->ID:0);
							}
							if (isset($user_id) && $user_id > 0) {
								$package_subscribe = get_user_meta($user_id,"package_subscribe",true);
								$currency_code = wpqa_get_currency($user_id);
								$array = array(
									"free"     => array("key" => "free","name" => esc_html__("Free membership","wpqa")),
									"monthly"  => array("key" => "monthly","name" => esc_html__("Monthly membership","wpqa")),
									"3months"  => array("key" => "3months","name" => esc_html__("Three months membership","wpqa")),
									"6months"  => array("key" => "6months","name" => esc_html__("Six Months membership","wpqa")),
									"yearly"   => array("key" => "yearly","name" => esc_html__("Yearly membership","wpqa")),
									"2years"   => array("key" => "2years","name" => esc_html__("Two Years membership","wpqa")),
									"lifetime" => array("key" => "lifetime","name" => esc_html__("Lifetime membership","wpqa")),
								);
								$payment_description = esc_html__("Paid membership","wpqa").(isset($array[$package_subscribe]["name"]) && $array[$package_subscribe]["name"] != ""?" - ".$array[$package_subscribe]["name"]:"")." ".esc_html__("(Renew)","WPQA");
								$array = array (
									'item_no'          => 'subscribe',
									'item_number'      => 'subscribe',
									'item_name'        => $payment_description,
									'item_price'       => ($paymentintent->amount/100),
									'item_currency'    => $currency_code,
									'currency_code'    => $currency_code,
									'item_transaction' => $item_transaction,
									'payer_email'      => $user->user_email,
									'first_name'       => $user->first_name,
									'last_name'        => $user->last_name,
									'sandbox'          => '',
									'payment'          => 'Stripe',
									"customer"         => $paymentintent->customer,
									'renew'            => 'subscribe',
									'custom'           => (strpos($paymentintent->custom,"pay-subscribe-module") !== false?'pay-subscribe-module'.$package_subscribe:'wpqa_subscribe-'.$package_subscribe),
								);
								wpqa_payment_succeeded($user_id,$array);
							}
						}
					}
				}else if ($event->type == 'charge.refunded') {
					$chargerefunded = $event->data->object;
					$amount_captured = $chargerefunded->amount_captured;
					$amount_refunded = $chargerefunded->amount_refunded;
					$item_transaction = $chargerefunded->id;
					$args = array(
						'meta_key'       => 'payment_item_transaction',
						'meta_value'     => $item_transaction,
						'post_type'      => 'statement',
						'posts_per_page' => -1
					);
					$query = new WP_Query($args);
					if (!$query->have_posts()) {
						$item_transaction = $chargerefunded->balance_transaction;
						$args = array(
							'meta_key'       => 'payment_item_transaction',
							'meta_value'     => $item_transaction,
							'post_type'      => 'statement',
							'posts_per_page' => -1
						);
						$query = new WP_Query($args);
					}
					if ($query->have_posts()) {
						$post_id = (isset($query->posts[0]->ID)?$query->posts[0]->ID:0);
						if ($post_id > 0) {
							$item_transaction_refund = (isset($chargerefunded->refunds) && isset($chargerefunded->refunds->data) && isset($chargerefunded->refunds->data[0]->id)?$chargerefunded->refunds->data[0]->id:"");
							$item_transaction_refund = ($item_transaction_refund != ""?$item_transaction_refund:(isset($response->id)?$response->id:""));
							$user_id = $query->posts[0]->post_author;
							if ($item_transaction_refund != "" && !wpqa_find_refund($item_transaction_refund)) {
								$item_currency = get_post_meta($post_id,"payment_item_currency",true);
								$previous_refunded = $response->data->previous_attributes->amount_refunded;
								$item_price = (isset($amount_refunded) && $amount_refunded > 0?floatval(($amount_refunded-$previous_refunded)/100):get_post_meta($post_id,"payment_item_price",true));
								$response = array(
									"item_name"            => get_the_title($post_id),
									"item_price"           => $item_price,
									"item_currency"        => $item_currency,
									"item_transaction"     => $item_transaction_refund,
									"original_transaction" => $item_transaction,
								);
								wpqa_insert_refund($response,$user_id,"refund");
								wpqa_site_user_money($item_price,"-",$item_currency,$user_id);
								update_post_meta($post_id,"payment_refund","refund");
								update_post_meta($post_id,"payment_original_transaction",$item_transaction_refund);
								if ($amount_captured == $amount_refunded) {
									$item_number = get_post_meta($post_id,"payment_item_number",true);
									wpqa_refund_canceled_payment($user_id,$post_id,$item_number);
								}
							}
						}
					}
				}else {
					error_log('Received unknown event type');
				}
				http_response_code(200);
			}else {
				http_response_code(400);
				die("-1");
			}
		}else {
			http_response_code(400);
			die("-1");
		}
	}
}?>