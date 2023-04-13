<?php

/*
 * Plugin Name: wpForo - User Custom Fields
 * Description:  Allows to can create custom Registration Form with custom fields, add custom fields in User Profile system and in Members Search form.
 * Version: 3.0.0
 * Author: gVectors Team
 * Author URI: https://gvectors.com/
 * Plugin URI: http://wpforo.com/
 * Text Domain: wpforo-ucf
 * Domain Path: /languages/
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

define( 'WPFUCF_WPFORO_REQUIRED_VERSION', '2.0.0' );
define( 'WPFUCF_DIR_PATH', dirname( __FILE__ ) );
define( 'WPFUCF_DIR_NAME', basename( WPFUCF_DIR_PATH ) );
define( 'WPFUCF_BASENAME_FILE', basename( __FILE__ ) );

include_once 'includes/constants.php';
include_once 'includes/gvt-api-manager.php';
include_once 'includes/dbManager.php';
include_once 'options/options.php';
include_once 'includes/helper.php';
include_once 'includes/helper-ajax.php';
include_once 'includes/dbManager.php';
include_once 'includes/fields/abstractField.php';
include_once 'includes/importers/buddypress.php';

class WpforoUcf implements WpforoUcfConstants {

	private $version;
	private $dbManager;
	public $options;
	public $helper;
	public $helperAjax;
	public $buddypress;

	public function __construct() {
		$this->version   = get_option( self::UCF_VERSION, '1.0.0' );
		$this->dbManager = new WpforoUcfDBManager();
		register_activation_hook( __FILE__, array( &$this, 'searchToFilter' ) );
		register_activation_hook( __FILE__, array( &$this->dbManager, 'alterProfilesTable' ) );
		add_action( 'admin_notices', array( &$this, 'requirments' ), 1000 );
		add_action( 'admin_init', array( &$this, 'changeVersion' ), 1000 );
		add_action( 'wpforo_core_inited', array( &$this, 'dependencies' ) );
		add_filter( "wpforo_usergroup_cans", [ &$this, "addCansForTabs" ], 1 );
	}

	public function searchToFilter() {
		if ( function_exists( 'WPF' ) ) {
			$memberOptions = WPF()->settings->members;
			if ( $memberOptions && isset( $memberOptions['search_type'] ) && $memberOptions['search_type'] == 'search' ) {
				$memberOptions['search_type'] = 'filter';
				wpforo_update_option( 'member_options', $memberOptions );
			}
		}
	}

	public function changeVersion() {
		$pluginData = get_plugin_data( __FILE__ );
		if ( version_compare( $pluginData["Version"], $this->version, ">" ) ) {
			$fields = get_option( self::UCF_OPTION_CUSTOM_FIELDS, array() );
			if ( wpfval( $fields, 'secondary_groupids', 'type' ) === 'checkbox' &&
			     ( $values = wpfval( $fields, 'secondary_groupids', 'values' ) ) && is_string( $values ) ) {
				$rows     = explode( PHP_EOL, $values );
				$groupIds = array();
				foreach ( $rows as $row ) {
					$value = explode( '=>', $rows );
					if ( $val = intval( wpfval( $value, 0 ) ) ) {
						$groupIds[] = $val;
					}
				}
				if ( $groupIds ) {
					$fields['secondary_groupids']['allowedGroupIds'] = $groupIds;
				}
				$fields['secondary_groupids']['values'] = '';
				$fields['secondary_groupids']['type']   = 'secondary_groups';
				update_option( self::UCF_OPTION_CUSTOM_FIELDS, $fields );
			}
			update_option( self::UCF_VERSION, $pluginData["Version"] );
		} else {
			update_option( self::UCF_VERSION, $this->version );
		}
	}

	public function requirments() {
		if ( ! function_exists( 'WPF' ) || ( function_exists( 'WPF' ) && version_compare( WPFORO_VERSION, WPFUCF_WPFORO_REQUIRED_VERSION, '<' ) ) ) {
			echo "<div class='error'><p>" . __( 'wpForo User Custom Fields requires wpForo to be installed and wpForo version < ' . WPFUCF_WPFORO_REQUIRED_VERSION . '!!!', 'wpforo-ucf' ) . "</p></div>";
		}
	}

	public function dependencies() {

		if ( ! defined( 'WPFORO_VERSION' ) ||
		     version_compare( WPFORO_VERSION, WPFUCF_WPFORO_REQUIRED_VERSION, '<' ) ||
		     ! function_exists( 'WPF' ) ) {
			return;
		}

		//new GVT_API_Manager( __FILE__, self::UCF_PAGE_MAIN, self::UCF_PAGE_MAIN );
		$this->options    = new WpforoUcfOptions( $this->dbManager );
		$this->helper     = new WpforoUcfHelper( $this->dbManager, $this->options );
		$this->helperAjax = new WpforoUcfHelperAjax( $this->dbManager, $this->options, $this->helper );
		$this->buddypress = new WpforoUcfBuddypress( $this->dbManager, $this->options, $this->helper );

//            add_action('admin_menu', array(&$this, 'optionsPage'), 999);
		add_action( 'wpforo_admin_base_menu', array( &$this, 'optionsPage' ), 999 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'backendFiles' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'frontendFiles' ) );
		$page = ( $p = filter_input( INPUT_GET, 'page' ) ) ? $p : '';
		if ( $page == self::UCF_PAGE_MAIN ) {
			add_action( 'admin_footer', array( &$this, 'addPopupContainers' ) );
		}
		add_filter( 'custom_menu_order', array( &$this, 'subMenuOrder' ) );
		add_action( 'wp_ajax_wpfucfBPImportFields', array( &$this->buddypress, 'wpfucfBPImportFields' ) );
		add_action( 'wp_ajax_wpfucfBPUpdateUsersData', array( &$this->buddypress, 'wpfucfBPUpdateUsersData' ) );
	}

	public function optionsPage() {
		$title = __( 'Member Fields', 'wpforo-ucf' );
		add_submenu_page( self::WPFORO_PAGE_MAIN, $title, $title, 'manage_options', self::UCF_PAGE_MAIN, array(
			&$this->options,
			'optionsForm'
		) );
	}

	public function subMenuOrder( $menu_order ) {
		if ( apply_filters( 'wpfucf_submenu_order', false ) ) {
			global $submenu;
			$wpforoMenu = isset( $submenu[ self::WPFORO_PAGE_MAIN ] ) ? $submenu[ self::WPFORO_PAGE_MAIN ] : '';
			if ( $wpforoMenu && is_array( $wpforoMenu ) ) {
				$memberIndex = 0;
				$ucfIndex    = 0;
				$ucfItem     = '';
				foreach ( $wpforoMenu as $k => $v ) {
					if ( $v && is_array( $v ) && isset( $v[2] ) ) {
						if ( $v[2] == self::WPFORO_PAGE_MEMBERS ) {
							$memberIndex = $k;
						} else if ( $v[2] == self::UCF_PAGE_MAIN ) {
							$ucfItem  = $submenu[ self::WPFORO_PAGE_MAIN ][ $k ];
							$ucfIndex = $k;
						}
					}
				}
				if ( $ucfItem ) {
					array_splice( $wpforoMenu, $memberIndex + 1, 0, array( $memberIndex + 1 => $ucfItem ) );
					unset( $wpforoMenu[ $ucfIndex + 1 ] );
					$submenu[ self::WPFORO_PAGE_MAIN ] = $wpforoMenu;
				}
			}
		}

		return $menu_order;
	}

	public function backendFiles() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == self::UCF_PAGE_MAIN ) {
			$emptyRow = $this->helper->getEmptyTemplate( 'row' );
			$emptyCol = $this->helper->getEmptyTemplate( 'col' );
			$vars     = array(
				'wpfucfAjaxUrl'                => admin_url( 'admin-ajax.php' ),
				'wpfucfEmptyRow'               => $emptyRow,
				'wpfucfEmptyCol'               => $emptyCol,
				'msgConfirmResetOptions'       => __( 'Do you really want to reset options?', 'wpforo-ucf' ),
				'msgCannotDeleteDefaultFields' => __( 'You can not delete default field(s)', 'wpforo-ucf' ),
				'msgConfirmRowDelete'          => __( 'Do you really want to delete a row? Users who has filled the fields in row will lose the data!', 'wpforo-ucf' ),
				'msgConfirmFieldDelete'        => __( 'Do you really want to delete a field? Users who has filled this field in their account will lose the data!', 'wpforo-ucf' ),
				'msgConfirmTabDelete'          => __( 'Do you really want to delete a tab?', 'wpforo-ucf' ),
				'msgCanEditAtLeastOne'         => __( 'At least one group must have "edit" permission!', 'wpforo-ucf' ),
				'msgCanViewAtLeastOne'         => __( 'At least one group must have "view" permission!', 'wpforo-ucf' ),
				'msgUsergroupAtLeastOne'       => __( 'At least one user group must be checked!', 'wpforo-ucf' ),
				'msgInvalidFieldName'          => __( 'Field with this name already exists!', 'wpforo-ucf' ),
				'msgCannotBeInactive'          => __( 'Sorry, this field can not be inactive in this tab!', 'wpforo-ucf' ),
				'msgNonRemovableRow'           => __( 'Sorry, there is a required field in this row, remove it and try again please!', 'wpforo-ucf' ),
				'msgResetTimezoneLocation'     => __( 'Do you really want to reset field values?', 'wpforo-ucf' ),
				'importSupportPlugins'         => array( self::UCF_BUDDYPRESS ),
				'msgImportItemsPerRequest'     => __( 'Fields Imported! ' . PHP_EOL . 'How many items update per request?' . PHP_EOL . 'Recommended value is between 50 and 300!' . PHP_EOL . 'Allowed values are from 10 to 500!', 'wpforo-ucf' ),
				'msgFieldAlreadyExists'        => __( 'Field with this name already exists. Please make the field name unique!', 'wpforo-ucf' ),
			);

			wp_enqueue_script( 'jquery-ui-sortable' );

			wp_enqueue_style( 'wpfucf-lity-css', plugins_url( WPFUCF_DIR_NAME . '/assets/third-party/lity/lity.min.css' ) );
			wp_register_script( 'wpfucf-lity-js', plugins_url( WPFUCF_DIR_NAME . '/assets/third-party/lity/lity.min.js' ), array( 'jquery' ) );
			wp_enqueue_script( 'wpfucf-lity-js' );

			wp_enqueue_style( 'wpfucf-fontawesome-css', plugins_url( WPFUCF_DIR_NAME . '/assets/third-party/font-awesome-5.0.6/css/fontawesome-all.min.css' ), null, '5.0.6' );
			wp_enqueue_style( 'wpfucf-iconpicker-css', plugins_url( WPFUCF_DIR_NAME . '/assets/third-party/iconpicker/fontawesome-iconpicker.min.css' ) );

			wp_register_script( 'wpfucf-iconpicker-js', plugins_url( WPFUCF_DIR_NAME . '/assets/third-party/iconpicker/fontawesome-iconpicker.js' ), array( 'jquery' ) );
			wp_enqueue_script( 'wpfucf-iconpicker-js' );

			wp_enqueue_style( 'wpfucf-backend-css', plugins_url( WPFUCF_DIR_NAME . '/assets/css/backend.css' ) );
			wp_register_script( 'wpfucf-backend-js', plugins_url( WPFUCF_DIR_NAME . '/assets/js/backend.js' ), array( 'jquery' ) );
			wp_enqueue_script( 'wpfucf-backend-js' );
			wp_localize_script( 'wpfucf-backend-js', 'wpfucfVars', $vars );
			wp_register_script( 'wpfucf-formbuilder-js', plugins_url( WPFUCF_DIR_NAME . '/assets/js/formbuilder.js' ), array( 'jquery' ) );
			wp_enqueue_script( 'wpfucf-formbuilder-js' );

			if ( isset( $_GET['tab'] ) && $_GET['tab'] == self::TAB_TOOLS ) {
				wp_register_script( 'wpfucf-fields-importer-js', plugins_url( WPFUCF_DIR_NAME . '/assets/js/fields-importer.js' ), array( 'jquery' ) );
				wp_enqueue_script( 'wpfucf-fields-importer-js' );
				wp_localize_script( 'wpfucf-fields-importer-js', 'wpfucfVars', $vars );
			}
		}
	}

	public function frontendFiles() {
		$vars = array(
			'msgInvalidMinLength'   => __( 'Minimum length is', 'wpforo-ucf' ),
			'msgInvalidMaxLength'   => __( 'Maximum length is', 'wpforo-ucf' ),
			'msgRequiredCheckboxes' => __( 'Required', 'wpforo-ucf' ),
		);

		wp_register_style( 'wpfucf-frontend-css', plugins_url( WPFUCF_DIR_NAME . '/assets/css/frontend.css' ) );
		wp_enqueue_style( 'wpfucf-frontend-css' );
		wp_register_script( 'wpfucf-frontend-js', plugins_url( WPFUCF_DIR_NAME . '/assets/js/frontend.js' ), array( 'jquery' ) );
		wp_enqueue_script( 'wpfucf-frontend-js' );
		wp_localize_script( 'wpfucf-frontend-js', 'wpfucfVars', $vars );
	}

	public function addPopupContainers() {
		include_once WPFUCF_DIR_PATH . '/includes/fields/popups/popup-field-types.php';
		include_once WPFUCF_DIR_PATH . '/includes/fields/popups/popup-field-add-edit.php';
		include_once WPFUCF_DIR_PATH . '/includes/fields/popups/popup-fields-for-import.php';
		include_once WPFUCF_DIR_PATH . '/includes/fields/popups/popup-field-shortcodes.php';
	}

	public function getFields( $userId ) {
		$fields = $this->dbManager->getFields( $userId );

		return $fields && is_array( $fields ) ? $fields : array();
	}

	public function getField( $userId, $fieldName ) {
		$data   = '';
		$fields = $this->dbManager->getFields( $userId );
		if ( $fields && is_array( $fields ) && isset( $fields[ $fieldName ] ) ) {
			$data = $fields[ $fieldName ];
		}

		return $data;
	}

	public function addCansForTabs( $cans ) {
		$tabs = get_option( self::UCF_OPTION_MEMBER_TABS, array() );
		foreach ( $tabs as $key => $tab ) {
			if ( ! $tab['is_default'] ) {
				$cans[ $tab['can'] ] = sprintf( __( 'Front - Can view %1$s tab', 'wpforo-ucf' ), __( $tab['title'], 'wpforo-ucf' ) );
			}
		}

		return $cans;
	}

}

$wpforoUcf = new WpforoUcf();

if ( ! function_exists( 'wpfucfGetFields' ) ) {

	/**
	 * @param type $userId user ID in db
	 *
	 * @return type array user all fields
	 */
	function wpfucfGetFields( $userId ) {
		global $wpforoUcf;

		return $wpforoUcf->getFields( $userId );
	}

}

if ( ! function_exists( 'wpfucfGetField' ) ) {

	/**
	 * @param type $userId user ID in db
	 * @param type $fieldName field name in fields array
	 *
	 * @return type mixed the field value by given field name
	 */
	function wpfucfGetField( $userId, $fieldName ) {
		global $wpforoUcf;

		return $wpforoUcf->getField( $userId, $fieldName );
	}

}

if ( ! function_exists( 'wpfucfTheField' ) ) {

	/**
	 * @param type $userId the user id who data will be return or print
	 * @param type $fieldName field name in fields array
	 * @param type $before this will be printed before field
	 * @param type $after this will be printed after field
	 */
	function wpfucfTheField( $userId, $fieldName, $before = '', $after = '' ) {
		global $wpforoUcf;
		$result     = '';
		$fieldValue = $wpforoUcf->getField( $userId, $fieldName );

		if ( $fieldValue ) {
			if ( is_array( $fieldValue ) ) {
				foreach ( $fieldValue as $fValue ) {
					$result .= $before ? $before : '';
					$result .= $fValue;
					$result .= $after ? $after : '';
				}
			} else {
				$result .= $before ? $before : '';
				$result .= $fieldValue;
				$result .= $after ? $after : '';
			}
		}
		echo $result;
	}

}
