<?php
/*
Controller name: Contact
Controller description: Contact Us form
*/

class MOBILE_API_Contact_Controller {
	function send() {
		global $mobile_api;
		$username = (isset($_POST["name"])?esc_html($_POST["name"]):"");
		$email = (isset($_POST["email"])?esc_html($_POST["email"]):"");
		$message = (isset($_POST["message"])?strip_tags($_POST["message"]):"");
		if ($username == "") {
			$mobile_api->error(esc_html__("There are required fields (name).","mobile-api"));
		}
		if ($email == "") {
			$mobile_api->error(esc_html__("There are required fields (email).","mobile-api"));
		}
		if (!is_email($email)) {
			$mobile_api->error(esc_html__("Please write correctly email.","mobile-api"));
		}
		if ($message == "") {
			$mobile_api->error(esc_html__("There are required fields (message).","mobile-api"));
		}
		$email_title = esc_html__("Message from the mobile","mobile-api");
		$email_title = mobile_api_send_mail(
			array(
				'content' => $email_title,
				'title'   => true,
				'break'   => '',
			)
		);
		$send_text = mobile_api_send_mail(
			array(
				'content' => $message,
			)
		);
		$last_message_email = mobile_api_email_code($send_text);
		mobile_api_send_mails(
			array(
				'fromEmail'     => $email,
				'fromEmailName' => $username,
				'title'         => $email_title,
				'message'       => $last_message_email,
				'email_code'    => 'code',
			)
		);
		return array("status" => true,"message" => esc_html__("Your message was successfully sent.","mobile-api"));
	}
}?>