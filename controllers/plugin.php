<?php defined( 'ABSPATH' ) or die( "This page intentionally left blank." );

if (!class_exists('FMAD_Class_Plugin__rotating_banners')) {

	/**
	 * Class FMAD_Class_Plugin__rotating_banners
	 */
	class FMAD_Class_Plugin__rotating_banners {

		public $file, $dir, $url, $wp_option_name, $data, $message;

		/**
		 * FMAD_Class_Plugin__rotating_banners constructor.
		 *
		 * @param $file
		 */
		public function __construct($file) {

			$this->data     = new stdClass();
			$this->file     = $file;                         // EX :: /home/account/site.com/wp-content/plugins/plugin-name/index.php
			$this->dir      = plugin_dir_path($this->file);  // EX :: /home/account/site.com/wp-content/plugins/plugin-name/
			$this->url      = plugin_dir_url($this->file);   // EX :: http://site.com/wp-content/plugins/plugin-name/
			$this->wp_option_name = "fmad_rotating-banners"; // EX :: fmad_pluginname1 // Also change this name in uninstall.php

			// limit posts
			$this->data->limit_posts = 3;

			// Assets debug mode
			//todo remove/comment this for production
//            $this->data->debug_assets = 1;//used to modify the css/js ?ver= on each pageload to force the redownload of assets (debug only, for servers with cache)

			// Add activation hooks // activated / deactivated / uninstalled
			$this->activation_hooks();

			// Add Shortcode
			add_shortcode('rotating_banners', array($this, 'add_shortcode'));

			// Add Shortcode support on Text Widgets
			add_filter('widget_text', 'do_shortcode');

			// add admin menu pages
			add_action('admin_menu', array($this, 'add_admin_menu'));

			// add/create Rotating Banners Custom Post Type
			add_action( 'init', array($this, 'create_Rotating_Banners_post_type'), 0 );

			// add meta box
			add_action( 'add_meta_boxes', array($this, 'add_metaboxes' ) );

			// Save function for the MetaBox
			// This function Fires everytime the CPT is changing Status (Publish -> Draft // Publish -> Trash // ETC)
			add_action( 'save_post', array($this, 'changed_post_status'), 10, 2 );

			// Add CSS on fmrb custom post type in admin
			add_action('admin_print_styles', array($this, 'add_admin_css'));

			// add the limitation
			add_action('load-post-new.php', array($this, 'limit_rotating_banners') );

			// Add AJAX
			add_action( 'wp_ajax__fmad_ajax_rotating_banners', array($this, 'ajax_actions') );
			add_action( 'wp_ajax_nopriv__fmad_ajax_rotating_banners', array($this, 'ajax_actions') );

		}//end constructor

		########################################

		function activation_hooks() {
			// What to do when Plugin is Activated ?
			register_activation_hook( $this->file, array($this, 'hook_activate') );
			// What to do when Plugin is DeActivated ?
//			register_deactivation_hook( $this->file, array($this, 'hook_deactivate') );
		}
		// What to do when Plugin is Activated ?
		function hook_activate() {
			// Plugin Activated
			// Create the needed/default option in wp_options
			$this->get_options_or_create_if_not_exists();
			// recheck license key (expired)
			$this->recheck_license_key();

		}
		// What to do when Plugin is DeActivated ?
		function hook_deactivate() {
			// Plugin Deactivated
			// No jobs to do on deactivation (for now) // it is not hooked into wp yet
//			delete_option('fmad_rotating-banners');
		}

		########################################

		// Add Main Pages into sidebar
		function add_admin_menu() {
			add_menu_page('Rotating Headers', _x( 'Rotating Banners', 'Menu Title', TDFM_rb ), 'activate_plugins', 'edit.php?post_type=fmrb');
			add_submenu_page( 'edit.php?post_type=fmrb', 'Rotating Banners', _x( 'All Rotating Banners', 'Menu Title', TDFM_rb ), 'activate_plugins', 'edit.php?post_type=fmrb');//dont use _x on the missing ones because it isnt showing anywhere
			add_submenu_page( 'edit.php?post_type=fmrb', 'Rotating Banners', _x( 'Add Rotating Banner', 'Menu Title', TDFM_rb ), 'activate_plugins', 'post-new.php?post_type=fmrb');//dont use _x on the missing ones because it isnt showing anywhere

			add_submenu_page( 'edit.php?post_type=fmrb', _x( 'Rotating Banners Group', 'Page Title', TDFM_rb ), _x( 'Group', 'Menu Title', TDFM_rb ), 'activate_plugins', 'group', array($this, 'page_Group' ));
			add_submenu_page( 'edit.php?post_type=fmrb', _x( 'Rotating Banners License', 'Page Title', TDFM_rb ), _x( 'License', 'Menu Title', TDFM_rb ), 'activate_plugins', 'license', array($this, 'add_page_License'));
			add_submenu_page( 'edit.php?post_type=fmrb', _x( 'Training', 'Page Title', TDFM_rb ), _x( 'Training', 'Menu Title', TDFM_rb ), 'activate_plugins', 'training', array($this, 'add_page_Training' ));
		}

		########################################

		function ajax_actions() {
			check_ajax_referer( 'fmad-rotating-banners-ajax-actionnr1', 'security' );
			$return = array();

			if ( !empty($_POST['a2']) ) {
				$a2 = $_POST['a2'];

				// ajax front static/cache page :: dynamic/ajax rotating banner
				if ( $a2 == 'front_dynamic_rotating_banner' ) { $this->ajax__front_dynamic_rotating_banner($return); }

				// another function ??

			}

			header('Content-Type: application/json');
			echo json_encode($return);

			wp_die(); // this is required to terminate immediately and return a proper response
		}

		function ajax__front_dynamic_rotating_banner(&$return) {
			if ( empty( $_POST['group'] ) ) { return; }

			// echo shortcode view

			ob_start();

			$this->shortcode_view( $_POST['group'] );

			$output_string = ob_get_contents();
			ob_end_clean();

			require_once 'php-html-css-js-minifier.php';
			$return['return'] = minify_html($output_string);

		}

		########################################

		function add_page_Training() {

			// add assets for this page
//			$this->add_assets('training');

			// include view
			include $this->dir . 'views/training_page.php';

		}

		########################################

		// Add Page: Group Page
		function page_Group() {

			$group_id = 'main';

			// Get Settings & change current header if is time ( may be modified in save_Groups() )
			$this->data->dbsettings = $this->change_Banner_If_Time__return_what('', 'dbsettings');

			// saving part at the top
			$this->save_Group_actions($group_id);

			if ( empty( $this->data->dbsettings['groups'][ $group_id ] ) ) { return; }//normally this couldn't be, but just to be sure...

			// Get this Group
			$this->data->selected_group = $this->data->dbsettings['groups'][ $group_id ]; // shorter variant

			// Get All Rotating Banners
			$this->data->all_rotating_banners = $this->get_all_rotating_banners_custom_post_type('publish');//all rb // it is used to create $rb_names


			//Get all rotating banners of this group // it is used to find current banner name
			$this->data->rotating_banners_of_this_group = $this->get_all_rotating_banners_custom_post_type('publish', $this->data->selected_group['ids']);

			// all missing rb
			$all_rotating_banners_IDs = $this->get_all_rotating_banners_custom_post_type_IDS('publish', 'all');
			$missing_IDs = array_diff($all_rotating_banners_IDs, $this->data->selected_group['ids']);
			$all_missing_rotating_banners = $this->get_all_rotating_banners_custom_post_type('publish', $missing_IDs);

			// debug // todo: remove/comment this for production
//			for ($i = 0; $i <= 200; $i++) { $all_missing_rotating_banners["00$i"] = new stdClass(); $all_missing_rotating_banners["00$i"]->ID = "00$i"; $all_missing_rotating_banners["00$i"]->post_title = "Demo Item #$i"; }
			// debug // todo: remove/comment this for production

			// help variable // to not used a bigger in length var
			$dbsettings_group = $this->data->dbsettings['groups'][$group_id];

			// START creating variables to be used in view

			if ( count($all_missing_rotating_banners) > 20 ) {//select2 version
				$no_rb_to_this_group = _x("<br>** You don't have any Rotating Banners added in this Group. **<br>** You can Search & Select items from the Available Rotating Banners column! **", 'groups', TDFM_rb);
			} else {//sortable column 1 version
				$no_rb_to_this_group = _x("<br>** You don't have any Rotating Banners added in this Group. **<br>** You can Drag and Drop from the First Column into Second Column! **", 'groups', TDFM_rb);
			}


			// current banner name // help text if none exists
			$currentHeaderName = $no_rb_to_this_group;//default//used in view
			if ( !empty($dbsettings_group['ids']) && is_array($dbsettings_group['ids']) && count($dbsettings_group['ids']) > 0 && !empty($dbsettings_group['ids'][key($dbsettings_group['ids'])]) ) {
				$currID = $dbsettings_group['ids'][key($dbsettings_group['ids'])];//shorter variant for next line
				$currentHeaderName = $this->find_current_HeaderName($currID, $this->data->rotating_banners_of_this_group);//used in view
			}

			// select
			$selectEveryArr = array("months" => __("Months", TDFM_rb), "days" => __("Days", TDFM_rb), "hours" => __("Hours", TDFM_rb), "minutes" => __("Minutes", TDFM_rb), "seconds" => __("Seconds", TDFM_rb));//used in view
			// timer vars
			$currTimeChange = $dbsettings_group['changeeach'];//used in view
			$currTimeChangeArr = explode(' ', $currTimeChange);
			$formInputTime = $currTimeChangeArr[0];//used in view
			$formSelectTime = $currTimeChangeArr[1];//used in view

			$groups_rbs = [];//used in view
			foreach ( $dbsettings_group['ids'] as $id ) {
				foreach ( $this->data->rotating_banners_of_this_group as $post ) {
					if ( $id == $post->ID ) {
						$groups_rbs[$id] = $post->post_title;
					}
				}
			}


			// END creating variables to be used in view




			// create nice $groups to send to js
			foreach ( $this->data->all_rotating_banners as $post ) {
				$rb_names[$post->ID] = $post->post_title;
			}

			$send_to_js = [
					'plugin_url' => $this->url,
					'ajax_url' => admin_url('admin-ajax.php'),
					'ajax_nonce' => wp_create_nonce( "fmad-rotating-banners-ajax-actionnr1" ),
					'ajax_action' => '_fmad_ajax_rotating_banners',
					'groups' => $this->data->dbsettings['groups'],
					'rb_names' => (!empty($rb_names))?$rb_names:array(),
					'translate' => [
						'no_rb_to_this_group' => $no_rb_to_this_group,
						'you_need_to_save_first' => _x("(You need to click Save Group Config first)", 'groups forms', TDFM_rb),
						'delete_group_confirmation_title' => _x("Are you sure you want to delete this group?", 'groups delete group confirmation title', TDFM_rb),
//						'delete_group_confirmation_body' => _x("Please click OK if you want to delete, or Cancel if you don't want to delete!", 'groups delete group confirmation body', TDFM_rb),
						'select2_placeholder' => _x("Search Rotating Banner to add", 'groups select2 placeholder', TDFM_rb),
						'copied_to_clipboard' => _x("ShortCode Copied to ClipBoard:", 'group copied to clipboard', TDFM_rb),
					],//end translate
//
				'debug_count_select2_if' => count($all_missing_rotating_banners),
			];

			// add assets for this page
			$this->add_assets('group', $send_to_js);

			// include view
			include $this->dir . 'views/group_page.php';

		}

		/**
		 * Save Group function // different forms on the Group Page
		 *
		 * @param $group_id
		 *
		 * @return string
		 *
		 */
		function save_Group_actions($group_id) {

			//Start If POST
			if (!empty($_POST) ) {

				//check nonce
				$is_valid_nonce = ( isset( $_POST[ 'fmrb_nonce_sp' ] ) && wp_verify_nonce( $_POST[ 'fmrb_nonce_sp' ], basename( $this->file ) ) ) ? true : false;
				if ( !$is_valid_nonce ) {
					$this->message(_x("Security verifications failed. Try again!", 'forms verify nonce', TDFM_rb), 'error');
					return;
				}
				//check if this group_id really exists
				if ( ! empty( $this->data->dbsettings['groups'][$group_id] ) ) {
					$dbsettings_group = $this->data->dbsettings['groups'][$group_id];
				} else {
					$this->message(_x("This group don't exist!", 'group message', TDFM_rb), 'error');
					return;
				}

				// all ok, continue

				$update = 0;

				//start

				// change time (from 1 seconds to 5 seconds, 10 days to 1 month, etc
				if (isset($_POST['changetimeform'])) {
					if ( isset($_POST['changetime']) && isset($_POST['selecttime']) ) {
						$currTS_p = time();
						$changetime = $_POST['changetime'];
						$selecttime = $_POST['selecttime'];
						switch ($selecttime) {
							case 'months':
								$plus = 2629743;
								break;
							case 'days':
								$plus = 86400;
								break;
							case 'hours':
								$plus = 3600;
								break;
							case 'minutes':
								$plus = 60;
								break;
							case 'seconds':
								$plus = 1;
								break;

							default:
								break;
						}//end switch

						$plus *= $changetime;
						$fmad_nextTS_p = $plus + $currTS_p;
						$dbsettings_group['changeeach'] = $changetime.' '.$selecttime;//new value
						$dbsettings_group['ts_plus'] = $plus;//new value
						$dbsettings_group['ts_start'] = $currTS_p;//new value
						$dbsettings_group['ts_end'] = $fmad_nextTS_p;//new value
						$dbsettings_group['ids'] = $dbsettings_group['ids'];//copy

						// modify the array to be updated in db
						$this->data->dbsettings['groups'][$group_id] = $dbsettings_group;
						$update = 1;
					}//end isset changetime and select
				}//end isset chagetime form

				// Save Group Config
				if ( isset( $_POST['save_group_config'] ) ) {

					$new_group_config = json_decode($_POST['group_config']);

					if ( is_array( $new_group_config ) ) {
						// modify the array to be updated in db
						$this->data->dbsettings['groups'][$group_id]['ids'] = $new_group_config;
						$update = 1;
					}

				}

				//end


				if ( $update == 1 ) {
					// update
					$this->updateOption($this->data->dbsettings);
					// message
					if ( empty($this->message) ) {
						$this->message( _x("Settings <strong>saved</strong> successfully.", 'groups', TDFM_rb) );
					}
					// Get settings again to update what was modified
					$this->data->dbsettings = $this->change_Banner_If_Time__return_what('', 'dbsettings');
				} else {
					// message
					if ( empty($this->message) ) {
						$this->message( _x("You didn't modified anything", 'groups', TDFM_rb) );
					}
				}

			} // end isset post
			//End If POST
		}

		/**
		 * Find currently active Header Name
		 *
		 * @param $currID
		 * @param $results
		 *
		 * @return string|bool
		 */
		function find_current_HeaderName($currID, $results) {
			foreach ($results as $k => $v) {
				if ( $v->ID == $currID ) {
					$return = $v->post_title;
				}
			}
			return (!empty($return)) ? $return : false;
		}

		########################################

		// Register Custom Post Type
		function create_Rotating_Banners_post_type() {
			$labels = array(
				'name'                => _x( 'Rotating Banners', 'custom post type :: Post Type General Name', TDFM_rb ),
				'singular_name'       => _x( 'Rotating Banner', 'custom post type :: Post Type Singular Name', TDFM_rb ),
				'menu_name'           => _x( 'Rotating Banners', 'custom post type :: menu_name', TDFM_rb ),
				'name_admin_bar'      => _x( 'Rotating Banners', 'custom post type :: name_admin_bar', TDFM_rb ),
				'parent_item_colon'   => _x( 'Parent Rotating Banner:', 'custom post type', TDFM_rb ),
				'all_items'           => _x( 'All Rotating Banners', 'custom post type', TDFM_rb ),
				'add_new_item'        => _x( 'Add New Rotating Banner', 'custom post type', TDFM_rb ),
				'add_new'             => _x( 'Add New', 'custom post type', TDFM_rb ),
				'new_item'            => _x( 'New Rotating Banner', 'custom post type', TDFM_rb ),
				'edit_item'           => _x( 'Edit Rotating Banner', 'custom post type', TDFM_rb ),
				'update_item'         => _x( 'Update Rotating Banner', 'custom post type', TDFM_rb ),
				'view_item'           => _x( 'View Rotating Banner', 'custom post type', TDFM_rb ),
				'search_items'        => _x( 'Search Rotating Banner', 'custom post type', TDFM_rb ),
				'not_found'           => _x( 'Rotating Banner Not found', 'custom post type', TDFM_rb ),
				'not_found_in_trash'  => _x( 'Rotating Banner Not found in Trash', 'custom post type', TDFM_rb ),
			);
			$args = array(
				'label'               => _x( 'Rotating Banner', 'custom post type :: label', TDFM_rb ),
				'description'         => _x( 'Rotating Banners Custom Post Type', 'custom post type :: description', TDFM_rb ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor' ),
//				'taxonomies'          => array( 'fmrbgroup' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,//allow to add new post
				'show_in_menu'        => false,
				'menu_position'       => 5,
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'rewrite'             => false,
				'capability_type'     => 'post',
			);
			register_post_type( 'fmrb', $args );
		}

		########################################

		function add_metaboxes() {

			// Add the metabox for Simple/Advanced Switcher
			add_meta_box( 'fmrb_metabox_switcher',
					_x("Rotating Banner Switcher", 'Switcher MetaBox Description', TDFM_rb),
					array($this, 'view_for_metabox_switcher'),
					'fmrb', 'side', 'high'
			);

			// Add the metabox for html/css/js
			add_meta_box( 'fmrb_metabox_htmlcssjs',
					_x("Rotating Banner Advanced HTML/CSS/JS", 'Advanced MetaBox Description', TDFM_rb),
					array($this, 'view_for_metabox_htmlcssjs'),
					'fmrb', 'normal', 'high'
			);


			// Remote the slug metabox
			remove_meta_box( 'slugdiv', 'fmrb', 'normal' );
		}

		// Include View File for the Switcher MetaBox
		function view_for_metabox_switcher( $params ) {

			// based on switcher (simple or advanced) decide if:
			// - we remove the fmrb_metabox_htmlcssjs metabox or not
			// - we show wp editor or not

//			$dbsettings = $this->getOption();

			$method_meta = get_post_meta( $params->ID, 'method', true );

			$method = [];
			if ( $method_meta == 'simple' || empty($method_meta) ) {
				$method['input']['simple'] = 'disabled';
				$method['input']['advanced'] = '';
				$method['radio']['simple'] = 'checked';
				$method['radio']['advanced'] = '';
				$simple = 1;
				$method['input']['text_simple'] = _x("Simple Mode is Active!", 'metabox method', TDFM_rb);
				$method['input']['text_advanced'] = _x("Switch to Advanced Mode", 'metabox method', TDFM_rb);
			}
			elseif ( $method_meta == 'advanced' ) {
				$method['input']['simple'] = '';
				$method['input']['advanced'] = 'disabled';
				$method['radio']['simple'] = '';
				$method['radio']['advanced'] = 'checked';
				$advanced = 1;
				$method['input']['text_simple'] = _x("Switch to Simple Mode", 'metabox method', TDFM_rb);
				$method['input']['text_advanced'] = _x("Advanced Mode is Active!", 'metabox method', TDFM_rb);
			}

			$send_to_js = [
				'simple_active' => _x("Simple Mode is Active!", 'metabox method', TDFM_rb),
				'advanced_active' => _x("Advanced Mode is Active!", 'metabox method', TDFM_rb),
				'switch_to_simple' => _x("Switch to Simple Mode", 'metabox method', TDFM_rb),
				'switch_to_advanced' => _x("Switch to Advanced Mode", 'metabox method', TDFM_rb),
				'method' => ( $method_meta == 'simple' || empty($method_meta) ) ? 'simple' : 'advanced',
			];

			// add assets needed for this metabox
			$this->add_assets('metabox_switcher', $send_to_js);

			// include view
			include $this->dir . 'views/metabox_switcher.php';
		}

		// Include View File for the HTML-CSS-JS MetaBox
		function view_for_metabox_htmlcssjs( $params ) {

			// add assets needed for this metabox
			$this->add_assets('metabox_htmlcssjs');

			// get the 3 textareas info
			$html = esc_html( get_post_meta( $params->ID, 'html', true ) );
			$css = esc_html( get_post_meta( $params->ID, 'css', true ) );
			$js = esc_html( get_post_meta( $params->ID, 'js', true ) );

			// Defaults (help text) if none exists (for new page or pages without any texts written in the 3 textareas
			$html = (!empty($html)) ? $html
//				    : '<!-- Please put your HTML or shortcode code here! -->' . "\n";//todo: fix this, make it work with commented html code
					: _x("Please put your HTML code or shortcode(s) here!", 'metabox', TDFM_rb) . "\n";
			$css = (!empty($css)) ? $css
					: _x("/* Please put your css below this line */", 'metabox', TDFM_rb) . "\n";
			$js = (!empty($js)) ? $js
					: _x("/* Please put your JS code below this line */", 'metabox', TDFM_rb) . "\n";

			// include view
			include $this->dir . 'views/metabox_htmlcssjs.php';
		}

		/**
		 * this is run everytime the post is saved
		 *
		 * @param $post_id
		 * @param $params
		 */
		function changed_post_status( $post_id, $params ) {
			// Check post type if is fmrb // only run this if fmrb post type is being edited/saved/deleted/etc

			if ( $params->post_type == 'fmrb' ) {

				//debug
//	            echo "<pre>";
//	            echo "\n\nPOST: \n";print_r($_POST);
//	            echo "\n\nparams: \n";print_r($params);
//	            exit();
				//debug

				$is_autosave = wp_is_post_autosave( $post_id );
				$is_revision = wp_is_post_revision( $post_id );

				// Exits script depending on save status
				if ( $is_autosave || $is_revision ) {
					return;
				}

				// Store data in post meta table if present in post data
				if ( isset( $_POST['html'] ) ) {
					update_post_meta( $post_id, 'html', $_POST['html'] );
				}
				if ( isset( $_POST['css'] ) ) {
					update_post_meta( $post_id, 'css', $_POST['css'] );
				}
				if ( isset( $_POST['js'] ) ) {
					update_post_meta( $post_id, 'js', $_POST['js'] );
				}

				if ( isset( $_POST['fmrb_mtb_switcher'] ) ) {
					update_post_meta( $post_id, 'method', $_POST['fmrb_mtb_switcher'] );
				}

//				exit();
			}
		}

		########################################

		// Include View File for the Shortcode
		function add_shortcode($attr) {

			// shortcode dynamic // add assets
			$data_to_send = [
				'admin_url' => admin_url('admin-ajax.php'),
				'action' => '_fmad_ajax_rotating_banners',
				'security' => wp_create_nonce( "fmad-rotating-banners-ajax-actionnr1" ),
				'a2' => 'front_dynamic_rotating_banner',
			];
			$this->add_assets('front', $data_to_send);

			$group_slug = 'main';

			// echo or return the shortcode view
			if ( isset( $attr['widget'] ) && $attr['widget'] == 'true' ) {
				ob_start();
				$this->shortcode_view($group_slug);// return the shortcode view // is in widget
				$output_string = ob_get_contents();
				ob_end_clean();
				return $output_string;
			} else {
				$this->shortcode_view($group_slug); // echo the shortcode view // is not in widget
			}
		}

		function shortcode_view($group_slug) {

			// get last id for this group
			if ( $lastId = $this->change_Banner_If_Time__return_what($group_slug) ) {

				$method = get_post_meta( $lastId, 'method', true );

				if ( $method == 'simple' || empty($method) ) {
					// use wp editor content
					$the_content = get_post_field('post_content', $lastId);
				} elseif ( $method == 'advanced' ) {
					// use metabox content
					$html = get_post_meta( $lastId, 'html', true );
					$css = get_post_meta( $lastId, 'css', true );
					$js = get_post_meta( $lastId, 'js', true );
				}

				include $this->dir . 'views/shortcode.php';
			}
		}

		########################################

		function add_admin_css() {
			global $typenow;
			if( $typenow == 'fmrb' ) {
				$this->add_assets('fmrb_post_type');
			}
		}

		########################################

		function limit_rotating_banners() {
			global $typenow;

			# Not our post type, bail out
			if( 'fmrb' !== $typenow )
				return;

			// first check the license and modify data->limit_posts
			$dbsettings = $this->getOption();
			if ( ! empty( $dbsettings['license_nr'] ) ) {
				$this->data->limit_posts = ( $dbsettings['license_nr'] == '-1' ) ? 99999 : $dbsettings['license_nr'] ;
			}

			# Grab all our CPT, adjust the status as needed
			$total = get_posts( array(
					'post_type' => 'fmrb',
					'numberposts' => -1,
					'post_status' => 'publish,future,draft'
			));

			# Condition match, block new post
			if( $total && count( $total ) >= $this->data->limit_posts ) {
				// what to do when limit is reached?
				$url = admin_url('edit.php?post_type=fmrb&page=license&l=1');
				wp_redirect($url);
			}
		}

		// add License Page
		function add_page_License() {

			$limit = (isset($_GET['l'])) ? 1 : 0;
			if ( $limit ) {
				$this->message(_x("Limit Reached! Upgrade your account!", 'license', TDFM_rb), 'error');
			}
			if ( isset( $_GET['aweber_return'] ) ) {
				$this->message("Your License Key was sent to your email! Please check the email and Copy Paste the License Key here!");
			}

			$this->save_License_page();//don't set another $message after this point because it will overwrite the ones set by the save function

			$dbsettings = $this->getOption();

			$license_status = empty($dbsettings['license_status']) ? 0 : 1;
			$license_key = empty($dbsettings['license_key']) ? "" : $dbsettings['license_key'];

			if ( ! empty( $dbsettings['license_nr'] ) ) {
				$view_license_nr = ( $dbsettings['license_nr'] == '-1' ) ? _x("Unlimited", 'license key form', TDFM_rb) : $dbsettings['license_nr'] ;
			} else {
				$view_license_nr = $this->data->limit_posts;
			}

//			$view_license_nr =
			if ( $license_status == 1 ) {
				$view_license_color = "green";
				$view_license_status = _x("active", 'license status', TDFM_rb);
			} else {
				$view_license_color = "red";
				$view_license_status = _x("inactive", 'license status', TDFM_rb);
			}


			// include view
			include $this->dir . 'views/license_page.php';
		}

		function save_License_page() {
			if ( isset( $_POST ) && isset($_POST['action']) ) {
				$is_valid_nonce = ( isset( $_POST[ 'fmrb_nonce_lp' ] ) && wp_verify_nonce( $_POST[ 'fmrb_nonce_lp' ], basename( $this->file ) ) ) ? true : false;
				if ( !$is_valid_nonce ) {
					$this->message(_x("Security verifications failed. Try again!", 'forms verify nonce', TDFM_rb), 'error');
					return;
				}
				//all ok, continue
				$action = $_POST['action'];

				if ( $action == 'license_key' ) {
					$this->delete_license_key(1);
					$license_key = $_POST['fmrb_license_key'];
					// check if $license_key is ok
					$license_check = $this->check_license_key($license_key);
					if ( !empty($license_check) && $license_check['status'] == 1 && !empty($license_check['nr']) ) {
						$dbsettings = $this->getOption();
						$dbsettings['license_key'] = $license_key;
						$dbsettings['license_status'] = 1;
						$dbsettings['license_nr'] = $license_check['nr'];
						$this->updateOption($dbsettings);
						$nr = ($license_check['nr'] == '-1') ? 'Unlimited' : $license_check['nr'];
						$this->message(_x(sprintf("License Key is good. Saved it! You can now create & use %s Rotating Banners!", $nr), 'license', TDFM_rb));
					} else {
						if ( empty($this->message) ) {}//what's the logic of this?
						if ( empty( $license_check['msg'] ) ) {
							$this->message(_x("License Key is invalid!", 'license', TDFM_rb), 'error');
						} else {
							$this->message($license_check['msg'], 'error');
						}
					}

				}
			}
		}
		function delete_license_key($do=0) {
			if ( $do == 1 ) {
				$dbsettings = $this->getOption();
				unset( $dbsettings['license_key'], $dbsettings['license_status'], $dbsettings['license_nr'] );
				$this->updateOption($dbsettings);
			}
		}

		function check_license_key($license_key, $first_or_recheck = 'first') {
			// first of all, the license key need to be 32 chars in length
			if ( strlen($license_key) != 32 ) {
				return false;
			}
			// sent a post to our service and check if this license key is good
			$url = "http://marketinghack.fr/fmad/rotating-banners/check_license_key_1.php";
			global $current_user;
			$data = array(
					'license_key' => $license_key,
					'site_url' => site_url(),//if the license key exists, make it available only for this site
					'user_email' => (is_object($current_user)&&is_object($current_user->data)&&!empty($current_user->data->user_email))?$current_user->data->user_email:'',
					'first_or_recheck' => $first_or_recheck,
//					'debug' => 1,// TODO // DELETE/COMMENT THIS FOR PRODUCTION
			);
			$response = wp_remote_post( $url, array(
							'method' => 'POST',
							'timeout' => 45,
							'redirection' => 5,
							'httpversion' => '1.0',
							'blocking' => true,
							'headers' => array(),
							'body' => $data,
							'cookies' => array()
					)
			);

			if ( is_wp_error( $response ) && $first_or_recheck == 'first' ) {
				$this->message($response->get_error_message(), 'error');
			} else {
				// CURL succeded
//				echo 'Response:<pre>';print_r( $response );echo '</pre>';
				if ( !empty($response['body']) ) {
					$return = json_decode($response['body'], 1);
					if (json_last_error() === 0) {
						// JSON is valid
						return $return;
					}
				}
			}
			// if something failed, return false;
			return false;
		}

		function recheck_license_key() {
			$dbsettings = $this->getOption();
			if ( !empty( $dbsettings['license_key'] ) ) {
				// license key exists
				$license_check = $this->check_license_key($dbsettings['license_key']);
				if ( !empty($license_check) && isset($license_check['status']) ) {
					// returned data from server
					if ( $license_check['status'] != 1 ) {
						// the license is not active anymore, deactivate it
						$dbsettings['license_status'] = 0;
						if ( isset( $dbsettings['license_nr'] ) ) { unset( $dbsettings['license_nr'] ); }
						$this->updateOption($dbsettings);
					}
				}
			}
		}

		########################################

		########## Class Help Functions ##########

		/**
		 * Set $this->message with $mesaj and $class
		 *
		 * @param $mesaj :: updated/error
		 * @param string $class
		 */
		function message($mesaj, $class = 'updated') {
			$this->message = "<div class='$class' id='message'><p>" . $mesaj . "</p></div>";
		}

		/**
		 * @param string $after
		 *
		 * @return array|mixed|object
		 */
		function getOption( $after = "" ) {
			return json_decode(get_option($this->wp_option_name . $after), true);//current settings from db as json
		}

		/**
		 * @param $array
		 * @param string $after
		 */
		function updateOption($array,  $after = "" ) {
			update_option($this->wp_option_name . $after, json_encode($array), 'no');
		}

		/**
		 * Add assets for a function
		 *
		 * @param $what
		 * @param array $additional_data
		 */
		function add_assets($what, $additional_data = array()) {
			if (isset($this->data->debug_assets)) {
				$ver = mt_rand(1,9999);
			} else {
				$ver = false;
			}

			if ( $what == 'front' ) {
				wp_enqueue_script( 'fmrb-front-js', $this->url . "assets/js/front.js", array('jquery'), $ver );
				wp_localize_script( 'fmrb-front-js', 'fmrb_front', $additional_data ); //pass 'object_name' to script.js
			}

			// on all GET post_type=fmrb
			if ( $what == 'fmrb_post_type' ) {
				wp_enqueue_style( 'admin-fmrb-pages-css', $this->url . "assets/css/admin_fmrb_pages.css", array(), $ver );
			}

			if ( $what == 'group' ) {

				// include select2 css/js only if needed
				if ( isset($additional_data['debug_count_select2_if']) && $additional_data['debug_count_select2_if'] > 20 ) {
					unset($additional_data['debug_count_select2_if']);
					wp_enqueue_style( 'select2-css', esc_url_raw("//cdn.jsdelivr.net/select2/4.0.0/css/select2.min.css"), array(), $ver );
					wp_enqueue_script( 'select2-js', esc_url_raw("//cdn.jsdelivr.net/select2/4.0.0/js/select2.min.js"), array(), $ver, true );
				}

				wp_enqueue_style( 'admin-fmrb-group-font1', esc_url_raw("https://fonts.googleapis.com/css?family=Raleway:400,500,700"), array(), $ver );
				wp_enqueue_style( 'admin-fmrb-group-css', $this->url . "assets/css/group.css", array(), $ver );

				wp_enqueue_script( 'fmrb-group-page-js', $this->url . "assets/js/group_page.js", array( 'jquery-ui-sortable' ), $ver);
				wp_localize_script( 'fmrb-group-page-js', 'fmrb_groups_page', $additional_data ); //pass 'object_name' to script.js

			}

			if ( $what == 'metabox_switcher' ) {

				wp_enqueue_script( 'fmrb-metabox-switcher-js', $this->url . "assets/js/metabox_switcher.js", array( 'jquery' ), $ver);
				wp_localize_script( 'fmrb-metabox-switcher-js', 'fmrb_metabox_switcher', $additional_data ); //pass 'object_name' to script.js

			}

			if ( $what == 'metabox_htmlcssjs' ) {

				// Include Needed Libs
				// ace.js v 1.1.3 is the last one to work with modified emmet.min.js // changed `var _ =` to `var emmet =` into emmet.min.js to fix compatibility issues with wordpress Add Media button
				wp_enqueue_script( 'acejs-js', esc_url_raw( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js' ), array(), $ver, true );
				wp_enqueue_script( 'ace-theme-monokai-js', esc_url_raw( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/theme-monokai.js' ), array(), $ver, true );
				wp_enqueue_script( 'ace-mode-html-js', esc_url_raw( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/mode-html.js' ), array(), $ver, true );
				wp_enqueue_script( 'ace-mode-css-js', esc_url_raw( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/mode-css.js' ), array(), $ver, true );
				wp_enqueue_script( 'ace-mode-js-js', esc_url_raw( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/mode-javascript.js' ), array(), $ver, true );

				wp_enqueue_script( 'emmet-js', $this->url . "assets/js/emmet.min.js", array(), $ver, true );
				wp_enqueue_script( 'ext-emmet-js', esc_url_raw( 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ext-emmet.js' ), array(), $ver, true );

				// Include My JS (uses Libs upthere)
				wp_enqueue_script( 'fmrb-metabox-htmlcssjs-js', plugins_url('rotating-banners/assets/js/metabox_htmlcssjs.js'), array(), $ver, true );

			}
		}

		########################################

		// Private Help Functions

		//todo this is used also in shortcode.php // in front // replace it there with a new better function // optimised this function, now is better (?)
		function change_Banner_If_Time__return_what($group_slug = '', $what = null) {

			// get options from db or create default ones if not exist
			$dbsettings = $this->get_options_or_create_if_not_exists();

			$update = 0;

			// if no param set, check all groups
			if ( empty($group_slug) ) {
				// check all
				foreach ( $dbsettings['groups'] as $k_group_slug => $group ) {
					$return = $this->check_this_group__if_update_needed_return_new_group($group);
					if ( isset($return['group']) ) {
						$dbsettings['groups'][$k_group_slug] = $return['group'];
						$update = 1;
					}
				}
			} else { // is param is set, check only this group
				// check only this group
				if ( isset($dbsettings['groups'][$group_slug]) ) {
					$return = $this->check_this_group__if_update_needed_return_new_group($dbsettings['groups'][$group_slug]);
					if ( isset($return['group']) ) {
						$dbsettings['groups'][$group_slug] = $return['group'];
						$update = 1;
					}
				}
			}

			// if update is needed
			if ( $update == 1 ) {
				$this->updateOption($dbsettings);
			}

			// if $what is set & == dbsettings, return all dbsettings // this is used when you want to get the dbsettings, but first, check if an update is needed
			if (!empty($what) && $what == 'dbsettings') {// Return DB Settings
				return ( !empty($dbsettings) ) ? $dbsettings : false ;
			} else {//If $what is null, return Last ID
				return ( !empty($group_slug) && !empty($return['lastId']) ) ? $return['lastId'] : false ;
			}
		}
		function check_this_group__if_update_needed_return_new_group($group) {

			$return = [];

			if ( ! empty( $group ) && is_array( $group ) && !empty( $group['ids'] ) && !empty( $group['ids'][key($group['ids'])] ) ) {

				$currTS = time();
				$dbTS = $group['ts_end'];//timestamp when will change
				$fmad_dbID = $group['ids'][key($group['ids'])];//current id

				if ( isset($dbTS) && $currTS >= $dbTS ) {
					// it is time to change the header, change it
					$this->moveElement($group['ids'], 0, count($group['ids']));//move first element to the end: 1234->2341
					$lastId = $group['ids'][key($group['ids'])];

					$group['ts_start'] = $currTS;
					$group['ts_end'] += $group['ts_plus'];

					if ($group['ts_end'] < $currTS) {
						$group['ts_end'] = $currTS + $group['ts_plus'];
					}

					//todo: create a new updateOption function for this, only for front, for a better performance // optimised this function, now is better (?)
//					$this->updateOption($group);
					$return['group'] = $group;
					$return['lastId'] = $lastId;
				} else {
					$return['lastId'] = $fmad_dbID;
				}
			}
			return $return;
		}

		/**
		 * Help function that moves the first array element to the end of array
		 *
		 * @param $array
		 * @param $a
		 * @param $b
		 */
		function moveElement(&$array, $a, $b) {//function that moves first array element to the end of array
			$out = array_splice($array, $a, 1);
			array_splice($array, $b, 0, $out);
		}


		// Get Rotating Banners
		/**
		 * Return wpdb object with all rotating banners  // custom post type fmrb
		 *
		 * @param string $status
		 *
		 * @return array|null|object
		 */
		function get_all_rotating_banners_custom_post_type($status='', $ids='all') {

			$args = [
				'post_type' => 'fmrb',
				'posts_per_page' => -1,
				'post_status' => ( empty( $status ) ) ? 'any' : $status,
			];

			if ( ! empty( $ids ) && is_array($ids) && count($ids) > 0 ) {
				$args['post__in'] = $ids;
			} elseif ( empty($ids) ) {
				$args['post__in'] = array(0);
			} elseif ( $ids == 'all' ) {
				// do nothing, default is get all
			}

			return get_posts($args);

		}
		/**
		 * Return an array of IDs of all Rotating Banners // custom post type fmrb
		 *
		 * @param string $status
		 *
		 * @return array
		 */
		function get_all_rotating_banners_custom_post_type_IDS($status='', $ids=[]) {
			$results = $this->get_all_rotating_banners_custom_post_type($status, $ids);
			$IDs = array();
			if (!empty($results) && is_array($results) && count($results) > 0) {
				foreach ($results as $cpt) {
					$IDs[] = $cpt->ID;
				}
			}
			return $IDs;
		}

		/**
		 * Get options from DB (wp_options) or create and get them if they dont exist
		 *
		 * @return array|mixed|object
		 */
		function get_options_or_create_if_not_exists() {
			// If settings don't exist -> First time
			// Insert into wp_options the default settings for plugin
			$dbsettings = $this->getOption();
			if ( !$dbsettings ) {
				$dbsettings = array(
					'groups' => [
						'main' => [
							'name' => "Main",
							'ids' => [],
							'changeeach' => '1 days',
							'ts_plus' => '86400',
							'ts_start' => time(),
							'ts_end' => time() + 86400,
						]
					]
				);
				$this->updateOption($dbsettings);
			}
			return $dbsettings;
		}
	}
}

