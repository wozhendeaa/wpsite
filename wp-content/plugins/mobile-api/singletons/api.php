<?php

class MOBILE_API {
	
	function __construct() {
		$this->query = new MOBILE_API_Query();
		$this->introspector = new MOBILE_API_Introspector();
		$this->response = new MOBILE_API_Response();
		add_action('template_redirect', array(&$this, 'template_redirect'));
		add_action('update_option_mobile_api_base', array(&$this, 'flush_rewrite_rules'));
	}
	
	function template_redirect() {
		// Check to see if there's an appropriate API controller + method    
		$controller = strtolower($this->query->get_controller());
		
		if ($controller) {
			
			if (empty($this->query->dev)) {
				error_reporting(0);
			}
			
			$controller_path = $this->controller_path($controller);
			if (file_exists($controller_path)) {
				require_once $controller_path;
			}
			$controller_class = $this->controller_class($controller);
			
			if (!class_exists($controller_class)) {
				$this->error("Unknown controller '$controller_class'.");
			}
			
			$this->controller = new $controller_class();
			$method = $this->query->get_method($controller);
			
			if ($method) {
				
				$this->response->setup();
				
				// Run action hooks for method
				do_action("mobile_api", $controller, $method);
				do_action("mobile_api-{$controller}-$method");
				
				// Error out if nothing is found
				if ($method == '404' || $method == 'error') {
					$this->error('Not found');
				}
				
				// Run the method
				$result = $this->controller->$method();
				
				// Handle the result
				$this->response->respond($result);
				
				// Done!
				exit;
			}
		}
	}
	
	function save_option($id, $value) {
		$option_exists = (get_option($id, null) !== null);
		if ($option_exists) {
			update_option($id, $value);
		} else {
			add_option($id, $value);
		}
	}
	
	function get_controllers() {
		$controllers = array();
		$dir = mobile_api_dir();
		$this->check_directory_for_controllers("$dir/controllers", $controllers);
		$this->check_directory_for_controllers(get_stylesheet_directory(), $controllers);
		$controllers = apply_filters('mobile_api_controllers', $controllers);
		return array_map('strtolower', $controllers);
	}
	
	function check_directory_for_controllers($dir, &$controllers) {
		$dh = opendir($dir);
		while ($file = readdir($dh)) {
			if (preg_match('/(.+)\.php$/i', $file, $matches)) {
				$src = file_get_contents("$dir/$file");
				if (preg_match("/class\s+MOBILE_API_{$matches[1]}_Controller/i", $src)) {
					$controllers[] = $matches[1];
				}
			}
		}
	}
	
	function controller_class($controller) {
		return "mobile_api_{$controller}_controller";
	}
	
	function controller_path($controller) {
		$mobile_api_dir = mobile_api_dir();
		$mobile_api_path = "$mobile_api_dir/controllers/$controller.php";
		$theme_dir = get_stylesheet_directory();
		$theme_path = "$theme_dir/$controller.php";
		if (file_exists($theme_path)) {
			$path = $theme_path;
		} else if (file_exists($mobile_api_path)) {
			$path = $mobile_api_path;
		} else {
			$path = null;
		}
		$controller_class = $this->controller_class($controller);
		return apply_filters("{$controller_class}_path", $path);
	}
	
	function flush_rewrite_rules() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
	
	function error($message = 'Unknown error', $status = false) {
		$this->response->respond(array('error' => $message), $status);
	}
	
	function include_value($key) {
		return $this->response->is_value_included($key);
	}
	
}?>