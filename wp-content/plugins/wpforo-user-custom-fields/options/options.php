<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class WpforoUcfOptions implements WpforoUcfConstants {

	private $dbManager;
	public $defaultTabFile;
	public $tabs;
	private $editable_props = array(
		'isDefault'              => 0,
		'fieldKey'               => '',
		'name'                   => '',
		'title'                  => '',
		'label'                  => '',
		'placeholder'            => '',
		'description'            => '',
		'html'                   => '',
		'values'                 => '',
		'fileSize'               => '',
		'fileExtensions'         => '',
		'minLength'              => 0,
		'maxLength'              => 0,
		'faIcon'                 => '',
		'isRequired'             => 0,
		'isEditable'             => 1,
		'isLabelFirst'           => 0,
		'isMultiChoice'          => 0,
		'isSearchable'           => 1,
		'canEdit'                => array(),
		'canView'                => array(),
		'isWrapItem'             => 1,
		'info'                   => '',
		'allowedGroupIds'        => array(),
		'isDisplayDefaultValues' => 0,
	);

	public function __construct( $dbManager ) {
		$this->dbManager      = $dbManager;
		$this->defaultTabFile = 'fields.php';
		$this->tabs           = array(
			array( 'tabTitle' => __( 'User Fields Manager', 'wpforo-ucf' ), 'tabKey' => self::TAB_FIELDS ),
			array( 'tabTitle' => __( 'Register Form', 'wpforo-ucf' ), 'tabKey' => self::TAB_REGISTER ),
			array( 'tabTitle' => __( 'Member Search Form', 'wpforo-ucf' ), 'tabKey' => self::TAB_SEARCH ),
			array( 'tabTitle' => __( 'Tools', 'wpforo-ucf' ), 'tabKey' => self::TAB_TOOLS ),
			array( 'tabTitle' => __( 'Tab Manager', 'wpforo-ucf' ), 'tabKey' => self::TAB_MEMBER_TABS ),
		);
		add_action( 'admin_post_wpfucfResetFields', array( &$this, 'wpfucfResetFields' ) );
		add_action( 'admin_post_wpfucfExportOptions', array( &$this, 'wpfucfExportOptions' ) );
		add_action( 'admin_post_wpfucfResetRegisterFields', array( &$this, 'wpfucfResetRegisterFields' ) );
		add_action( 'admin_post_wpfucfResetAccountFields', array( &$this, 'wpfucfResetAccountFields' ) );
		add_action( 'admin_post_wpfucfResetProfileFields', array( &$this, 'wpfucfResetProfileFields' ) );
		add_action( 'admin_post_wpfucfResetSearchFields', array( &$this, 'wpfucfResetSearchFields' ) );
		add_action( 'admin_post_wpfucfDeleteTab', array( &$this, 'wpfucfDeleteTab' ) );
		add_action( 'admin_post_wpfucfDisableTab', array( &$this, 'wpfucfDisableTab' ) );
		add_action( 'admin_post_wpfucfEnableTab', array( &$this, 'wpfucfEnableTab' ) );
		add_action( 'admin_init', [ &$this, 'saveOptions' ], 1 );
//        add_filter('wpforo_init_templates', array(&$this, 'initTabs'));
		add_filter( 'wpforo_init_member_templates', array( &$this, 'init_member_templates' ), 100 );
	}

	public function saveOptions() {
		if ( isset( $_POST['wpfucf-option-submit'] ) && check_admin_referer( 'wpfucf-options-nonce' ) ) {
			$this->options();
		}
	}

	public function optionsForm() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die( _e( 'Hacker?', 'wpforo-ucf' ) );
		}

		$yesText          = __( 'Yes', 'wpforo-ucf' );
		$noText           = __( 'No', 'wpforo-ucf' );
		$wpUploadsDir     = wp_upload_dir();
		$wpfucfOptionsDir = $wpUploadsDir['basedir'] . self::UCF_OPTIONS_DIR;
		$wpfucfOptionsUrl = $wpUploadsDir['baseurl'] . self::UCF_OPTIONS_DIR;

		if ( ( isset( $_POST['wpfucf-export-submit'] ) || isset( $_POST['wpfucf-import-submit'] ) ) && check_admin_referer( 'wpfucf-options-nonce' ) ) {
			$this->tools( $wpfucfOptionsDir, $wpfucfOptionsUrl );
		}


		include_once 'options-html.php';
	}

	public function getFields() {
		return WPF()->member->get_fields();
	}

	public function getTrashedFields() {
		return get_option( self::UCF_OPTION_TRASHED_FIELDS, array() );
	}

	public function initFields( $wpforoFields ) {
		if ( ( $wpfucfFields = get_option( self::UCF_OPTION_CUSTOM_FIELDS ) ) ) {
			foreach ( $wpforoFields as $key => $value ) {
				if ( (int) wpfval( $value['isRemovable'] ) && ! wpfkey( $wpfucfFields, $key ) ) {
					unset( $wpforoFields[ $key ] );
				}
			}

			foreach ( $wpfucfFields as $k => $f ) {
				$f             = (array) $f;
				$f['fieldKey'] = $k;
				if ( ! wpfval( $f, 'name' ) ) {
					$f['name'] = $k;
				}
				if ( wpfval( $f, 'canView' ) ) {
					$f['canView'][] = 1;
				} else {
					$f['canView'] = array( 1 );
				}
				if ( wpfval( $f, 'canEdit' ) ) {
					$f['canEdit'][] = 1;
				} else {
					$f['canEdit'] = array( 1 );
				}
				if ( wpfval( $wpforoFields, $k ) ) {
					$f                  = array_intersect_key( $f, $this->editable_props );
					$wpforoFields[ $k ] = array_merge( $wpforoFields[ $k ], $f );
				} else {
					$f['isDefault']     = 0;
					$wpforoFields[ $k ] = $f;
				}
			}
		}

		return $wpforoFields;
	}

	public function getRegisterFields() {
		return $this->fillTrashedFields( WPF()->member->get_register_fields() );
	}

	public function getAccountFields() {
		return $this->fillTrashedFields( WPF()->member->get_account_fields() );
	}

	public function getProfileFields() {
		return $this->fillTrashedFields( WPF()->member->get_profile_fields() );
	}

	public function getSearchFields() {
		return $this->fillTrashedFields( WPF()->member->get_search_fields() );
	}

	public function fillTrashedFields( $fields ) {
		$trashedFields = $this->getTrashedFields();
		foreach ( $fields as $key => $value ) {
			foreach ( $value as $ke => $val ) {
				foreach ( $val as $k => $v ) {
					if ( ! $v ) {
						if ( isset( $trashedFields[ $k ] ) ) {
							$fields[ $key ][ $ke ][ $k ] = $trashedFields[ $k ];
						} else {
							unset( $fields[ $key ][ $ke ][ $k ] );
						}
					}
				}
			}
		}

		return $fields;
	}

	public function wpfucfResetFields() {
		if ( current_user_can( 'manage_options' ) && wpforo_is_admin() && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpfucfResetFields' ) ) {
			delete_option( self::UCF_OPTION_CUSTOM_FIELDS );
			delete_option( self::UCF_OPTION_REGISTER_FIELDS );
			delete_option( self::UCF_OPTION_ACCOUNT_FIELDS );
			delete_option( self::UCF_OPTION_PROFILE_FIELDS );
			delete_option( self::UCF_OPTION_SEARCH_FIELDS );
			$this->getFields();
			$this->getRegisterFields();
			$this->getAccountFields();
			$this->getProfileFields();
			$this->getSearchFields();
			$redirect = admin_url( 'admin.php?page=' . self::UCF_PAGE_MAIN );
			wp_safe_redirect( $redirect );
			exit();
		}
	}

	public function wpfucfResetRegisterFields() {
		if ( current_user_can( 'manage_options' ) && wpforo_is_admin() && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpfucfResetRegisterFields' ) ) {
			delete_option( self::UCF_OPTION_REGISTER_FIELDS );
			$this->getRegisterFields();
			$redirect = admin_url( 'admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_REGISTER );
			wp_safe_redirect( $redirect );
			exit();
		}
	}

	public function wpfucfResetAccountFields() {
		if ( current_user_can( 'manage_options' ) && wpforo_is_admin() && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpfucfResetAccountFields' ) ) {
			delete_option( self::UCF_OPTION_ACCOUNT_FIELDS );
			$this->getAccountFields();
			$redirect = admin_url( 'admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_ACCOUNT );
			wp_safe_redirect( $redirect );
			exit();
		}
	}

	public function wpfucfResetProfileFields() {
		if ( current_user_can( 'manage_options' ) && wpforo_is_admin() && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpfucfResetProfileFields' ) ) {
			delete_option( self::UCF_OPTION_PROFILE_FIELDS );
			$this->getProfileFields();
			$redirect = admin_url( 'admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_PROFILE );
			wp_safe_redirect( $redirect );
			exit();
		}
	}

	public function wpfucfResetSearchFields() {
		if ( current_user_can( 'manage_options' ) && wpforo_is_admin() && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpfucfResetSearchFields' ) ) {
			delete_option( self::UCF_OPTION_SEARCH_FIELDS );
			$this->getSearchFields();
			$redirect = admin_url( 'admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_SEARCH );
			wp_safe_redirect( $redirect );
			exit();
		}
	}

	public function wpfucfExportOptions() {
		if ( current_user_can( 'manage_options' ) && wpforo_is_admin() && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpfucfExportOptions' ) ) {
			$redirect = admin_url( 'admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_TOOLS );
			wp_safe_redirect( $redirect );
			exit();
		}
	}

	public function synchronizeFields( $fields, $pageFields ) {
		$structure = [];
		foreach ( $pageFields as $k1 => $row ) {
			foreach ( $row as $k2 => $cols ) {
				foreach ( $cols as $k3 => $fieldKey ) {
					if ( ! empty( $fields[ $fieldKey ] ) ) {
						$structure[ $k1 ][ $k2 ][ $k3 ] = $fieldKey;
					}
				}
				if ( isset( $structure[ $k1 ][ $k2 ] ) ) {
					$structure[ $k1 ][ $k2 ] = array_values( array_filter( $structure[ $k1 ][ $k2 ] ) );
				}
			}
			if ( isset( $structure[ $k1 ] ) ) {
				$structure[ $k1 ] = array_values( array_filter( $structure[ $k1 ] ) );
			}
		}
		$structure = array_values( array_filter( $structure ) );

		return $structure;
	}

	private function initValues( &$structure ) {
		if ( $structure && is_array( $structure ) ) {
			$wpf = WPF();
			foreach ( $structure as $rowKey => $row ) {
				foreach ( $row as $colKey => $col ) {
					foreach ( $col as $fieldKey => $field ) {
						if ( isset( $field['name'] ) && isset( $field['isDefault'] ) ) {
							$userId = isset( $wpf->current_object ) && isset( $wpf->current_object['user']['userid'] ) ? intval( $wpf->current_object['user']['userid'] ) : 0; //
							if ( ! $field['isDefault'] && $userId ) {
								$value                                                 = wpfucfGetField( $userId, $field['name'], '', '', false );
								$structure[ $rowKey ][ $colKey ][ $fieldKey ]['value'] = $value;
							}
						}
					}
				}
			}
		}
	}

	private function initDataFromStructure( $structure ) {
		$fields     = $this->getFields();
		$fieldsData = array();
		foreach ( $structure as $rowIndex => $row ) {
			foreach ( $row as $colIndex => $col ) {
				foreach ( $col as $fieldIndex => $fieldName ) {
					if ( isset( $fields[ $fieldName ] ) && $fields[ $fieldName ] ) {
						$fieldsData[ $rowIndex ][ $colIndex ][ $fieldIndex ] = $fields[ $fieldName ];
					}
				}
			}
		}

		return $fieldsData;
	}

	private function initStructureFromData( $rows ) {
		$structure = array();
		foreach ( $rows as $rK => $cols ) {
			foreach ( $cols as $cK => $col ) {
				foreach ( $col as $fK => $field ) {
					if ( isset( $field['fieldKey'] ) && $field['fieldKey'] ) {
						$structure[ $rK ][ $cK ][ $fK ] = $field['fieldKey'];
					}
				}
			}
		}

		return $structure;
	}

	private function options() {
		$structure  = isset( $_POST['wpforoucf'] ) ? WpforoUcfHelper::unescapeData( $_POST['wpforoucf'] ) : '';
		$currentTab = ( $tab = filter_input( INPUT_GET, 'tab' ) ) ? $tab : self::TAB_FIELDS;
		if ( wpforo_is_admin() && $structure && is_array( $structure ) ) {
			if ( $currentTab == self::TAB_REGISTER ) {
				update_option( self::UCF_OPTION_REGISTER_FIELDS, $structure );
			} else if ( $currentTab == self::TAB_SEARCH ) {
				update_option( self::UCF_OPTION_SEARCH_FIELDS, $structure );
			} else if ( $currentTab == self::TAB_MEMBER_TABS ) {
				$currentAction = ( $action = filter_input( INPUT_GET, 'action' ) ) ? $action : 'ordering';
				$type          = ( $t = filter_input( INPUT_GET, 'type' ) ) ? $t : 'builder';
				if ( $currentAction === 'ordering' ) {
					$dbTabs = $this->getTabs();
//					var_dump($dbTabs);
//					die;
					$tabs   = array();
					for ( $i = 0; $i < count( $structure ); $i ++ ) {
						$tabs[ $structure[ $i ] ] = $dbTabs[ $structure[ $i ] ];
					}
					update_option( self::UCF_OPTION_MEMBER_TABS, $tabs );
				} else if ( $currentAction === 'add' ) {
					$tabs      = $this->getTabs();
					$validated = $this->validateTabData( $structure, $type );
					if ( $validated['error'] ) {
						foreach ( $validated['error'] as $error ) {
							add_settings_error( 'wpfucf', 'tab_not_created', $error, 'error' );
						}

						return;
					} else {
						$can                               = self::UCF_TAB_CAN_PREFIX . $validated['data']['key'];
						$tabs[ $validated['data']['key'] ] = array(
							'type'                  => $type,
							'key'                   => $validated['data']['key'],
							'ico'                   => $validated['data']['ico'],
							'title'                 => $validated['data']['title'],
							'status'                => $validated['data']['status'],
							'is_default'            => 0,
							'add_in_member_buttons' => $validated['data']['add_in_member_buttons'],
							'can'                   => $can,
						);
						$this->updateWpforoGroupsCans( $can, $validated['data']['allowedGroupids'] );
						update_option( self::UCF_OPTION_MEMBER_TABS, $tabs );
						update_option( 'wpfucf_' . $validated['data']['key'] . '_tab', $type === 'builder' ? $structure : $validated['data']['content'] );
					}
					$redirect = 'admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS;
					if ( $_POST['wpfucf-option-submit'] === 'save' ) {
						$redirect .= '&action=edit&type=' . $type . '&key=' . $validated['data']['key'];
					}
					wp_safe_redirect( admin_url( $redirect ) );
					exit();
				} else if ( $currentAction === 'edit' ) {
					$tabs      = $this->getTabs();
					$validated = $this->validateTabData( $structure, $type, true );
					if ( $validated['error'] ) {
						foreach ( $validated['error'] as $error ) {
							add_settings_error( 'wpfucf', 'tab_not_created', $error, 'error' );
						}

						return;
					} else {
						$is_default = intval( $type === 'default' );
						if ( ! $is_default && $validated['data']['key'] !== $validated['data']['old_key'] ) {
							$newTabs = array();
							foreach ( $tabs as $k => $t ) {
								if ( $k === $validated['data']['old_key'] ) {
									$newTabs[ $validated['data']['key'] ] = array();
								} else {
									$newTabs[ $k ] = $t;
								}
							}
							$tabs = $newTabs;
							delete_option( 'wpfucf_' . $validated['data']['old_key'] . '_tab' );
						}
						$can                               = $is_default ? ( $validated['data']['key'] === 'account' ? '' : $tabs[ $validated['data']['key'] ]['can'] ) : self::UCF_TAB_CAN_PREFIX . $validated['data']['key'];
						$tabs[ $validated['data']['key'] ] = array(
							'type'                  => $is_default ? $tabs[ $validated['data']['key'] ]['type'] : $type,
							'key'                   => $validated['data']['key'],
							'ico'                   => $validated['data']['ico'],
							'title'                 => $validated['data']['title'],
							'status'                => $validated['data']['status'],
							'is_default'            => $is_default,
							'add_in_member_buttons' => $validated['data']['add_in_member_buttons'],
							'can'                   => $can,
						);
						if ( $validated['data']['key'] === 'account' ) {
							unset( $tabs[ $validated['data']['key'] ]['can'] );
						} else {
							$this->updateWpforoGroupsCans( $can, $validated['data']['allowedGroupids'] );
						}
						update_option( self::UCF_OPTION_MEMBER_TABS, $tabs );
						if ( ! $is_default ) {
							update_option( 'wpfucf_' . $validated['data']['key'] . '_tab', $type === 'builder' ? $structure : $validated['data']['content'] );
						} else if ( in_array( $validated['data']['key'], array( 'profile', 'account' ) ) ) {
							update_option( 'wpfucf_' . $validated['data']['key'] . '_fields', $structure );
						}
						$redirect = 'admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS;
						if ( $_POST['wpfucf-option-submit'] === 'save' ) {
							$redirect .= '&action=edit&type=' . $type . '&key=' . $validated['data']['key'];
						}
						wp_safe_redirect( admin_url( $redirect ) );
						exit();
					}
				}
			} else {
				$structure = WpforoUcfHelper::decodeData( $structure );
				$structure = WpforoUcfHelper::jsonDecodeData( $structure );

				update_option( self::UCF_OPTION_CUSTOM_FIELDS, $structure );

				$trashedStructure = isset( $_POST['wpforoucf-trashed'] ) ? WpforoUcfHelper::unescapeData( $_POST['wpforoucf-trashed'] ) : array();
				$trashedStructure = WpforoUcfHelper::decodeData( $trashedStructure );
				$trashedStructure = WpforoUcfHelper::jsonDecodeData( $trashedStructure );

				update_option( self::UCF_OPTION_TRASHED_FIELDS, $trashedStructure );

				$mergedStructure = array_merge( $structure, $trashedStructure );

				$registerFields = $this->initStructureFromData( $this->getRegisterFields() );
				$registerFields = $this->synchronizeFields( $mergedStructure, $registerFields );

				if ( $registerFields && get_option( self::UCF_OPTION_REGISTER_FIELDS ) ) {
					update_option( self::UCF_OPTION_REGISTER_FIELDS, $registerFields );
				}

				$accountFields = $this->initStructureFromData( $this->getAccountFields() );
				$accountFields = $this->synchronizeFields( $mergedStructure, $accountFields );

				if ( $accountFields && get_option( self::UCF_OPTION_ACCOUNT_FIELDS ) ) {
					update_option( self::UCF_OPTION_ACCOUNT_FIELDS, $accountFields );
				}

				$profileFields = $this->initStructureFromData( $this->getProfileFields() );
				$profileFields = $this->synchronizeFields( $mergedStructure, $profileFields );

				if ( $profileFields && get_option( self::UCF_OPTION_PROFILE_FIELDS ) ) {
					update_option( self::UCF_OPTION_PROFILE_FIELDS, $profileFields );
				}
				$searchFields = $this->initStructureFromData( $this->getSearchFields() );
				$searchFields = $this->synchronizeFields( $mergedStructure, $searchFields );

				if ( $searchFields && get_option( self::UCF_OPTION_SEARCH_FIELDS ) ) {
					update_option( self::UCF_OPTION_SEARCH_FIELDS, $searchFields );
				}

				foreach ( $this->getTabs() as $key => $tab ) {
					if ( $tab['type'] === 'builder' ) {
						$tabFields = $this->initStructureFromData( $this->getTabFields( get_option( 'wpfucf_' . $key . '_tab' ) ) );
						$tabFields = $this->synchronizeFields( $mergedStructure, $tabFields );
						update_option( 'wpfucf_' . $key . '_tab', $tabFields );
					}
				}
			}
			add_settings_error( 'wpfucf', 'settings_updated', __( 'Settings Updated!', 'wpforo-ucf' ), 'updated' );
		}
	}

	private function tools( $wpfucfOptionsDir, $wpfucfOptionsUrl ) {
		if ( isset( $_POST['wpfucf-export-submit'] ) ) {
			$exportOptions = $_POST['wpfucfExportOptions'];
			wp_mkdir_p( $wpfucfOptionsDir );
			$options = array();
			foreach ( $exportOptions as $optionKey => $exportOption ) {
				if ( $exportOption ) {
					$option = get_option( $optionKey );
					if ( $option && is_array( $option ) ) {
						$options[ $optionKey ] = $option;
					}
				}
			}
			if ( $options ) {
				if ( wpforo_write_file( $wpfucfOptionsDir . '/' . self::UCF_BACKUP_FILE_NAME . '.txt', @maybe_serialize( $options ) ) ) {
					add_settings_error( 'wpfucf', 'settings_updated', __( 'Options Exported Successfully!', 'wpforo-ucf' ), 'updated' );
				} else {
					add_settings_error( 'wpfucf', 'settings_error', __( 'Error occured! Can not export / backup one of checked options!', 'wpforo-ucf' ), 'error' );
				}
			}
		} else if ( isset( $_POST['wpfucf-import-submit'] ) ) {
			$optionsToImport = isset( $_POST['wpfucfImportOptions'] ) ? $_POST['wpfucfImportOptions'] : "";
			$file            = isset( $_FILES['wpfucfImportOptions'] ) ? $_FILES['wpfucfImportOptions'] : "";
			if ( $optionsToImport && is_array( $optionsToImport ) && $file['name'] ) {
				$fileName = $file['name'];
				if ( $option = wpforo_get_file_content( $file['tmp_name'] ) ) {
					$optionData = @maybe_unserialize( $option );
					if ( $optionData && is_array( $optionData ) ) {
						foreach ( $optionsToImport as $optionKey => $optionToImport ) {
							if ( $optionToImport && isset( $optionData[ $optionKey ] ) && is_array( $optionData[ $optionKey ] ) ) {
								update_option( $optionKey, $optionData[ $optionKey ] );
							}
						}
						add_settings_error( 'wpfucf', 'settings_updated', __( 'Options Imported Successfully!', 'wpforo-ucf' ), 'updated' );
					} else {
						add_settings_error( 'wpfucf', 'settings_error', __( 'Error occured! File content is empty or data is not valid option!', 'wpforo-ucf' ), 'error' );
					}
				} else {
					add_settings_error( 'wpfucf', 'settings_error', __( 'Error occured! Can not get file content or file name is not valid!', 'wpforo-ucf' ), 'error' );
				}
			} else {
				add_settings_error( 'wpfucf', 'settings_error', __( 'Error occured! Choose file!', 'wpforo-ucf' ), 'error' );
			}
		}
	}

	public function getTabs( $getDefaults = true ) {
		return get_option( self::UCF_OPTION_MEMBER_TABS, $getDefaults ? $this->getDefaultTabs() : array() );
	}

	public function getTab( $key, $stripico = false ) {
		$tabs = $this->getTabs();
		$tab  = $tabs[ $key ];
		if ( $tab['is_default'] ) {
			$tab['type'] = 'default';
		} else {
			$tab['content'] = get_option( 'wpfucf_' . $key . '_tab', $tab['type'] === 'builder' ? array() : '' );
		}
		if ( $stripico ) {
			$tab['ico'] = preg_replace( '#<i class=["\']+([^"]+)["\']+></i>#is', '$1', $tab['ico'] );
		}

		return $tab;
	}

	public function getDefaultTabs() {
		$templates = WPF()->tpl->get_member_templates( false, true );
		$tabs      = array();
		foreach ( $templates as $key => $template ) {
			$tabs[ $key ] = array(
				'type'                  => $template['type'],
				'key'                   => $template['key'],
				'ico'                   => $template['ico'],
				'title'                 => $template['title'],
				'is_default'            => $template['is_default'],
				'can'                   => $template['can'],
				'add_in_member_buttons' => $template['add_in_member_buttons'],
				'status'                => $template['status'],
			);
		}

		return $tabs;
	}

	public function getTabFields( $structure ) {
		return $this->fillTrashedFields( WPF()->member->fields_structure_full_array( $structure ) );
	}

	public function validateTabData( &$data, $type, $isEdit = false ) {
		$validated = array();
		$error     = array();
		if ( isset( $data['tab-title'] ) ) {
			if ( $title = trim( $data['tab-title'] ) ) {
				$validated['title'] = wpforo_text( $title, 50, false );
			} else {
				$error[] = __( 'Tab title is empty', 'wpforo-ucf' );
			}
			unset( $data['tab-title'] );
		} else {
			$error[] = __( 'Tab title is empty', 'wpforo-ucf' );
		}
		if ( isset( $data['tab-old-key'] ) ) {
			$validated['old_key'] = $data['tab-old-key'];
			unset( $data['tab-old-key'] );
		} else {
			$validated['old_key'] = null;
		}
		if ( isset( $data['tab-key'] ) ) {
			$key = wpforo_text( preg_replace( '#[^a-z0-9-]#', '', strtolower( $data['tab-key'] ) ), 50, false );
			if ( $key ) {
				if ( ! WPF()->can_use_this_slug( $key ) && ( ! $isEdit || ( $isEdit && $key !== $validated['old_key'] ) ) ) {
					$i       = 2;
					$new_key = $key;
					while ( ! WPF()->can_use_this_slug( $new_key ) ) {
						$new_key = $key . '-' . $i;
						$i ++;
					}
					$validated['key'] = $new_key;
				} else {
					$validated['key'] = $key;
				}
			} else if ( isset( $validated['title'] ) ) {
				$key = wpforo_text( preg_replace( '#[^a-z0-9-]#', '', strtolower( $validated['title'] ) ), 50, false );
				if ( $key ) {
					$i       = 2;
					$new_key = $key;
					while ( ! WPF()->can_use_this_slug( $new_key ) ) {
						$new_key = $key . '-' . $i;
						$i ++;
					}
				} else {
					$new_key = 'tab-' . time();
				}
				$validated['key'] = $new_key;
			}
			unset( $data['tab-key'] );
		} else if ( $type !== 'default' && isset( $validated['title'] ) ) {
			$key = wpforo_text( preg_replace( '#[^a-z0-9-]#', '', strtolower( $validated['title'] ) ), 50, false );
			if ( $key ) {
				$i       = 2;
				$new_key = $key;
				while ( ! WPF()->can_use_this_slug( $new_key ) ) {
					$new_key = $key . '-' . $i;
					$i ++;
				}
			} else {
				$new_key = 'tab-' . time();
			}
			$validated['key'] = $new_key;
		} else {
			$validated['key'] = $validated['old_key'] ? $validated['old_key'] : 'tab-' . time();
		}
		if ( isset( $data['tab-status'] ) ) {
			$validated['status'] = $data['tab-status'] ? 1 : 0;
			unset( $data['tab-status'] );
		} else {
			$validated['status'] = 0;
		}
		if ( isset( $data['tab-ico'] ) ) {
			$validated['ico'] = ( $ico = trim( $data['tab-ico'] ) ) ? '<i class="' . $ico . '"></i>' : '<i class="fas fa-user"></i>';
			unset( $data['tab-ico'] );
		} else {
			$validated['ico'] = '<i class="fas fa-user"></i>';
		}
		if ( isset( $data['tab-allowed-groupids'] ) ) {
			$validated['allowedGroupids'] = is_array( $data['tab-allowed-groupids'] ) ? array_map( 'intval', $data['tab-allowed-groupids'] ) : array();
			unset( $data['tab-allowed-groupids'] );
		}
		if ( isset( $data['tab-add-in-member-buttons'] ) ) {
			$validated['add_in_member_buttons'] = $data['tab-add-in-member-buttons'] ? 1 : 0;
			unset( $data['tab-add-in-member-buttons'] );
		} else {
			$validated['add_in_member_buttons'] = 0;
		}
		if ( isset( $data['tab-content'] ) ) {
			$validated['content'] = $data['tab-content'];
			unset( $data['tab-content'] );
		} else {
			$validated['content'] = '';
		}

		return array( 'error' => $error, 'data' => $validated );
	}

	public function wpfucfDeleteTab() {
		if ( current_user_can( 'manage_options' ) && wpforo_is_admin() && isset( $_GET['_wpnonce'] ) && isset( $_GET['key'] ) && ( $key = trim( $_GET['key'] ) ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpfucfDeleteTab' ) ) {
			$tabs = $this->getTabs();
			if ( isset( $tabs[ $key ] ) ) {
				unset( $tabs[ $key ] );
			}
			update_option( self::UCF_OPTION_MEMBER_TABS, $tabs );
			delete_option( 'wpfucf_' . $key . '_tab' );
			add_settings_error( 'wpfucf', 'tab_deleted', __( 'Tab is removed', 'wpforo-ucf' ), 'updated' );
			wp_safe_redirect( admin_url( 'admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS ) );
			exit();
		}
	}

	public function wpfucfDisableTab() {
		if ( current_user_can( 'manage_options' ) && wpforo_is_admin() && isset( $_GET['_wpnonce'] ) && isset( $_GET['key'] ) && ( $key = trim( $_GET['key'] ) ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpfucfDisableTab' ) ) {
			$tabs = $this->getTabs();
			if ( isset( $tabs[ $key ] ) ) {
				$tabs[ $key ]['status'] = 0;
				update_option( self::UCF_OPTION_MEMBER_TABS, $tabs );
			}
			add_settings_error( 'wpfucf', 'tab_disabled', __( 'Tab is disabled', 'wpforo-ucf' ), 'updated' );
			wp_safe_redirect( admin_url( 'admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS ) );
			exit();
		}
	}

	public function wpfucfEnableTab() {
		if ( current_user_can( 'manage_options' ) && wpforo_is_admin() && isset( $_GET['_wpnonce'] ) && isset( $_GET['key'] ) && ( $key = trim( $_GET['key'] ) ) && wp_verify_nonce( $_GET['_wpnonce'], 'wpfucfEnableTab' ) ) {
			$tabs = $this->getTabs();
			if ( isset( $tabs[ $key ] ) ) {
				$tabs[ $key ]['status'] = 1;
				update_option( self::UCF_OPTION_MEMBER_TABS, $tabs );
			}
			add_settings_error( 'wpfucf', 'tab_enabled', __( 'Tab is enabled', 'wpforo-ucf' ), 'updated' );
			wp_safe_redirect( admin_url( 'admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS ) );
			exit();
		}
	}

	public function fieldsShortcode() {
		add_filter( "mce_buttons", [ &$this, "mceButton" ] );
		add_filter( "mce_external_plugins", [ &$this, "mceExternalPlugin" ] );
		wp_register_script( 'wpfucf-shortcode-js', null );
		wp_enqueue_script( 'wpfucf-shortcode-js' );
		wp_localize_script( 'wpfucf-shortcode-js', "wpfucfObject", [ "image"       => plugins_url( "/assets/img/wpforo-40.png", __DIR__ ),
		                                                             "tooltip"     => __( 'User Fields', 'wpforo-ucf' ),
		                                                             "popup_title" => __( 'User Fields', 'wpforo-ucf' ),
		] );
	}

	public function mceButton( $buttons ) {
		array_push( $buttons, "|", "wpfucf" );

		return $buttons;
	}

	public function mceExternalPlugin( $plugin_array ) {
		$plugin_array["wpfucf"] = esc_url_raw( plugins_url( "assets/js/shortcode.js", __DIR__ ) );

		return $plugin_array;
	}

	public function updateWpforoGroupsCans( $tabcan, $allowedGroupids = array() ) {
		$allowedGroupids = (array) $allowedGroupids;
		$groupids        = array();
		foreach ( WPF()->usergroup->get_usergroups( 'groupid' ) as $id ) {
			$groupids[ $id ] = (int) in_array( $id, $allowedGroupids );
		}
		foreach ( $groupids as $groupid => $can ) {
			$group                    = WPF()->usergroup->get_usergroup( $groupid );
			$group                    = WPF()->usergroup->fix_group( $group );
			$group['cans'][ $tabcan ] = $can;
			WPF()->usergroup->edit( $groupid, $group['name'], $group['cans'], $group['description'], $group['role'], $group['access'], $group['color'], $group['visible'], $group['secondary'] );
		}
		WPF()->notice->clear();
	}

	public function init_member_templates( $templates ) {
//		var_dump($templates);
//		die;
		$tabs        = array();
		$custom_tabs = $this->getTabs( false );
//		var_dump($custom_tabs);
//		die;
//		var_dump($_POST);
		if ( $custom_tabs ) {
			foreach ( $custom_tabs as $key => $tab ) {
				if ( $tab['is_default'] ) {
					$defaultTemplate = isset( $templates[ $key ] ) ? $templates[ $key ] : array();
					$tabs[ $key ]    = array_merge( $defaultTemplate, $tab );
				} else {
					$val          = $this->getTab( $key );
					$tab['value'] = $val['content'];
					$tabs[ $key ] = $tab;
				}
			}
//				    var_dump($templates);
//				    var_dump($tabs);
//	    die;
			$templates = array_merge( $templates, $tabs );
		}
//	    var_dump($templates);
//	    die("***");
		return $templates;
	}

}
