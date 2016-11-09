<?php


if ( ! defined( "FG_VERSION_MAIN" ) ) {
	define( "FG_VERSION_MAIN", "1" );
}
if ( ! defined( "FG_VERSION_BUILD" ) ) {
	define( "FG_VERSION_BUILD", "2" );
}
if ( ! defined( "FG_VERSION" ) ) {
	define( "FG_VERSION", FG_VERSION_MAIN . "." . FG_VERSION_BUILD );
}
if ( ! defined( "FG_EMBED" ) ) {
	define( "FG_EMBED", false );
}

require_once "FGProposed.php";
require_once "FGMetabox.php";
require_once "FGShortcodes.php";
require_once "FGWidget.php";

class FGPlugin {
	private $pluginSlug;
	private $pluginName;
	private $postTypeSlug;

	private $metaboxes;
	private $shortcodes;
	private $proposed;

	public function registerScripts() {
		wp_register_style( 'fg-bootstrap-css', FG_URL . "assets/bootstrap/css/bootstrap.css", array(), "3.2.0" );
		wp_register_style( 'fg-bootstrap-switch-css', FG_URL . "assets/bootstrap/css/bootstrap-switch.css", array( 'fg-bootstrap-css' ), "3.0.2" );
		wp_register_style( 'fg-bootstrap-range-css', FG_URL . "assets/bootstrap/css/bootstrap-slider.css", array( 'fg-bootstrap-css' ), "1.0.1" );
		wp_register_style( 'fg-bootstrap-spin-css', FG_URL . "assets/bootstrap/css/bootstrap-touchspin.css", array( 'fg-bootstrap-css' ), "1.0" );
		wp_register_style( 'fg-bootstrap-select-css', FG_URL . "assets/bootstrap/css/bootstrap-select.css", array( 'fg-bootstrap-css' ), "1.6.3" );
		wp_register_style( 'fg-bootstrap-dialog-css', FG_URL . "assets/bootstrap/css/bootstrap-dialog.css", array( 'fg-bootstrap-css' ), "1.6.3" );
		wp_register_style( 'fg-bootstrap-table-css', FG_URL . "assets/bootstrap/css/bootstrap-table.css", array( 'fg-bootstrap-css' ), "1.6.3" );
		wp_register_style( 'fg-bootstrap-selectize-css', FG_URL . "assets/bootstrap/css/bootstrap-selectize-core.css", array( 'fg-bootstrap-css' ), "0.12.1" );
		wp_register_style( 'fg-bootstrap-selectize-v3-css', FG_URL . "assets/bootstrap/css/bootstrap-selectize-v3.css", array( 'fg-bootstrap-css' ), "0.12.1" );
		wp_register_style( 'fg-bootstrap-datetime-css', FG_URL . "assets/bootstrap/css/bootstrap-datetimepicker.css", array( 'fg-bootstrap-css' ), "4.15.35" );


		wp_register_style( 'fg-admin-part-css', FG_URL . "assets/css/admin-part.css",
			array(
				'fg-bootstrap-css',
				'fg-bootstrap-switch-css',
				'fg-bootstrap-range-css',
				'fg-bootstrap-spin-css',
				'fg-bootstrap-table-css',
				'fg-bootstrap-select-css',
				'fg-bootstrap-dialog-css',
				'fg-bootstrap-selectize-css',
				'fg-bootstrap-selectize-v3-css',
				'fg-bootstrap-datetime-css',
			),
			FG_VERSION );

		wp_register_script( 'fg-bootstrap-js', FG_URL . "assets/bootstrap/js/bootstrap.js", array( "jquery" ), "3.2.0" );
		wp_register_script( 'fg-bootstrap-switch-js', FG_URL . "assets/bootstrap/js/bootstrap-switch.js", array( 'fg-bootstrap-js' ), "3.0.2" );
		wp_register_script( 'fg-bootstrap-range-js', FG_URL . "assets/bootstrap/js/bootstrap-slider.js", array( 'fg-bootstrap-js' ), "1.0.1" );
		wp_register_script( 'fg-bootstrap-spin-js', FG_URL . "assets/bootstrap/js/bootstrap-touchspin.js", array( 'fg-bootstrap-js' ), "1.0" );
		wp_register_script( 'fg-bootstrap-select-js', FG_URL . "assets/bootstrap/js/bootstrap-select.js", array( 'fg-bootstrap-js' ), "1.6.3" );
		wp_register_script( 'fg-bootstrap-dialog-js', FG_URL . "assets/bootstrap/js/bootstrap-dialog.js", array( 'fg-bootstrap-js' ), "3.0" );
		wp_register_script( 'fg-bootstrap-table-js', FG_URL . "assets/bootstrap/js/bootstrap-table.js", array( 'fg-bootstrap-js' ), "3.0" );
		wp_register_script( 'fg-bootstrap-file-js', FG_URL . "assets/bootstrap/js/bootstrap-filestyle.js", array( 'fg-bootstrap-js' ), "1.1.2" );
		wp_register_script( 'fg-bootstrap-selectize-js', FG_URL . "assets/bootstrap/js/bootstrap-selectize.js", array( 'fg-bootstrap-js' ), "0.12.1" );
		wp_register_script( 'fg-moment-js', FG_URL . "assets/js/moment.min.js", array( "jquery" ), "2.8.3" );
		wp_register_script( 'fg-bootstrap-datetime-js', FG_URL . "assets/bootstrap/js/bootstrap-datetimepicker.js", array(
				'fg-bootstrap-js',
				'fg-moment-js'
			), "4.15.35" );


		wp_register_script( 'fg-admin-part-js', FG_URL . "assets/js/admin-part.js",
			array(
				'fg-bootstrap-js',
				'fg-bootstrap-switch-js',
				'fg-bootstrap-range-js',
				'fg-bootstrap-spin-js',
				'fg-bootstrap-select-js',
				'fg-bootstrap-table-js',
				'fg-bootstrap-dialog-js',
				'fg-bootstrap-selectize-js',
				'fg-bootstrap-datetime-js',
			),
			FG_VERSION );
	}

	public function includeScriptsAndStyles() {
		global $post_type;
		if ( $this->postTypeSlug == $post_type ) {
			wp_enqueue_media();
			wp_enqueue_style( 'fg-admin-part-css' );
			wp_enqueue_script( 'fg-admin-part-js' );
		}
	}

	public function includeGlobalAdminScriptsAndStyles() {
		wp_enqueue_style( 'fg_backend_styles', FG_URL . "assets/css/global-admin.css", array(), FG_VERSION );
		wp_enqueue_script( 'fg_backend_scripts', FG_URL . "assets/js/global-admin.js", array(), FG_VERSION );
	}

	public function includeFrontendScriptsAndStyles() {
		wp_enqueue_style( 'fg_frontend_styles', FG_URL . "assets/css/focus-groups.css", array(), FG_VERSION );
		wp_enqueue_style( 'fg_frontend_jquery_ui_css', "//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css", array(), FG_VERSION );
		wp_enqueue_script( 'fg_frontend_scripts', FG_URL . "assets/js/focus-groups.js", array("jquery"), FG_VERSION, true );
		wp_enqueue_script( 'fg_frontend_validation', "//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.43/jquery.form-validator.min.js", array("jquery"), FG_VERSION, false );
		wp_enqueue_script( 'fg_frontend_jquery_ui_js', "//code.jquery.com/ui/1.11.4/jquery-ui.js", array("jquery"), FG_VERSION, false );
	}


	public function registerCustomPostType()
    {

        $labels = array(
            "name" => "city",
            "label" => "city",
        );

        $args = array(
            "labels" => $labels,
            "hierarchical" => false,
            "label" => "city",
            "show_ui" => false,
            "query_var" => true,
            "rewrite" => array('slug' => 'city', 'with_front' => true),
            "show_admin_column" => false,
        );
        register_taxonomy("city", array($this->postTypeSlug), $args);


        $labels = array(
            "name" => "Focus Groups",
            "singular_name" => "Focus Group",
            "menu_name" => "Focus Groups",
            "all_items" => "All Groups",
            "add_new" => "Add New",
            "add_new_item" => "Add New Group",
            "edit_item" => "Edit Group",
            "new_item" => "New Group",
            "view_item" => "View Group",
            "search_items" => "Search Group",
            "not_found" => "No Focus Groups found",
            "not_found_in_trash" => "No Focus Groups found in Trash",
        );

        $args = array(
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "show_ui" => true,
            "has_archive" => false,
            "show_in_menu" => true,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => array("slug" => "listings", "with_front" => true),
            "query_var" => true,
            "menu_icon" => "dashicons-groups",
            "taxonomies" => array("city"),
            "supports" => array("title", "page-attributes"),
            'register_meta_box_cb' => array($this->metaboxes, "addMetaboxesToPostType")
        );

        register_post_type($this->postTypeSlug, $args);


        $args = array(
            'posts_per_page'   => -1,
            'post_type'        => $this->postTypeSlug,
            'post_status'      => 'publish'
        );
        $all_groups = get_posts($args);
        if(is_array($all_groups) and count($all_groups)){
            foreach($all_groups as $item_group){
                $city = get_post_meta($item_group->ID, 'fg_city', true);
                if(get_post_meta( $item_group->ID, 'fg_is_national', true ) == "yes"){
                    $city = 'National';
                };
                if(!empty($city)){
                    $term_list = wp_get_post_terms($item_group->ID, 'city', array("fields" => "all"));
                    $need_to_add = true;
                    foreach($term_list as $term_single) {
                        if($term_single->name == $city){
                            $need_to_add = false;
                            break;
                        }
                    }
                    if($need_to_add){
                        wp_set_post_terms( $item_group->ID, array($city), 'city' );
                    }
                }
            }
        }
    }

	public function slugify( $text ) {
		$text = preg_replace( '~[^\\pL\d]+~u', '-', $text );
		$text = trim( $text, '-' );
		$text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );
		$text = strtolower( $text );
		$text = preg_replace( '~[^-\w]+~', '', $text );

		return $text;
	}

	public function changeGroupPermalink( $permalink, $post ) {
		$city = get_post_meta( $post->ID, "fg_city", true );
		if ( get_post_type( $post->ID ) == $this->postTypeSlug ) {
			$city = $this->slugify( $city );
			if ( ! empty( $city ) ) {
				$permalink = str_replace( "listings", $city . "/listings", $permalink );
			}
		}

		return $permalink;
	}

	function rewriteGroupPermalink() {
		add_rewrite_rule( '([^/]+)/studies/([^/]+)/?$', 'index.php?focusgroup=$matches[2]', 'top' );
	}

	function afterInitWP(){
		if(!is_admin()) {
			if(isset($_REQUEST["embedgroup"]) and FG_EMBED) {
				echo "<link rel='stylesheet' href='".get_bloginfo("stylesheet_url")."'>";
				echo "<link rel='stylesheet' href='".FG_URL."/assets/css/focus-groups.css'>";
				echo "<style>
					html:before,
					html:after,
					body:before,
					body:after{display: none;}
					.focus-groups-table{
					width: 100%;
					}
				</style>";
				$embedCode = base64_decode(base64_decode($_REQUEST["embedgroup"]));
				echo do_shortcode($embedCode);
				exit;
			}
		}
	}

	public function theFocusGroupTitle( $id, $postArr = array() ) {
		if ( count( $postArr ) ) {
			$short    = $postArr["short_description"];
			$city     = $postArr["city"];
			$pay      = $postArr["pay"];
			$national = $postArr["is_national"];
		} else {
			$short    = get_post_meta( $id, "fg_short_description", true );
			$city     = get_post_meta( $id, "fg_city", true );
			$pay      = get_post_meta( $id, "fg_pay", true );
			$national = get_post_meta( $id, "fg_is_national", true );
		}
		$cityPartStr = ( $national ) ? "" : " in " . $city;

		return $short . $cityPartStr . " " . ( ( trim( $pay ) ) ? " - $" . $pay : "" );
	}

	public function changeGroupTitleIfClosed( $title, $id = null ) {
		if ( get_post_type( $id ) == $this->postTypeSlug ) {
			if ( ! is_admin() and $id == get_the_ID() ) {
				$thisPost = get_post($id);
				//$title = apply_filters( "the_focus_group_title", $id );
				$title = apply_filters("the_title", $thisPost->post_title);
			}
			$is_open = get_post_meta( $id, "fg_is_open", true ) != "no";
			$title   = ( $is_open ? "" : "CLOSED - " ) . $title;
		}

		return $title;
	}


	public function editGroupsAdminColumns( $columns ) {

		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'order'             => __( 'Order', FG_LANG ),
			'title'             => __( 'Title', FG_LANG ),
			'facility'          => __( 'Facility', FG_LANG ),
			'city'              => __( 'City', FG_LANG ),
			'pay'               => __( 'Pay', FG_LANG ),
			'gender'            => __( 'Gender', FG_LANG ),
			'age_range'         => __( 'Age Range', FG_LANG ),
			'short_description' => __( 'Short Desc.', FG_LANG ),
			'date'              => __( 'Date', FG_LANG )
		);

		return $columns;
	}

	public function addTinyMCEPlugin( $plugin_array ) {
		$plugin_array['focusgroupshortcode'] = FG_URL . "assets/js/insert-shortcode-button.js";

		return $plugin_array;
	}

	public function registerTinyMCEButtons( $buttons ) {
		array_push( $buttons, "focusgroupshortcode" );

		return $buttons;
	}

	public function addTinyMCEButtons() {
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_external_plugins', array( $this, "addTinyMCEPlugin" ) );
			add_filter( 'mce_buttons_3', array( $this, "registerTinyMCEButtons" ) );
		}
	}

	public function manageGroupsAdminColumns( $column, $post_id ) {
		switch ( $column ) {

			case 'image' :

				$attachmentID = (int) get_post_meta( $post_id, 'fg_logo', true );
				if ( $attachmentID ) {
					$thumb  = wp_get_attachment_image_src( $attachmentID, 'gospelhouse-post-thumbnail' );
					$imgUrl = $thumb[0];
				} else {
					$imgUrl = FG_URL . "assets/images/holder-camera.png";
				}
				echo "<img class='groups-table-thumb' src='" . $imgUrl . "'>";

				break;

			case 'city' :
			case 'pay' :
			case 'gender' :
			case 'facility' :
			case 'age_range' :
			case 'short_description' :

				$content = get_post_meta( $post_id, 'fg_' . $column, true );
				if ( trim( $content ) ) {
					echo ( $column == "pay" ) ? "$" . $content : $content;
				} else {
					if($column == "city" and get_post_meta( $post_id, 'fg_is_national', true ) == "yes"){
						echo "National";
					}else {
						echo "&mdash;";
					}
				}
				break;
			case 'order' :
				$item = get_post( $post_id );
				echo (int) $item->menu_order;
				break;

			case 'date' :
				$content = get_the_title( $post_id );
				echo $content . "-0";

				break;


			default :
				break;
		}
	}


	public function checkAndCloseGroups() {
		$args = array(
			'posts_per_page' => -1,
			'orderby'        => 'menu_order post_date',
			'order'          => 'ASC',
			'post_status'    => array( "publish", "future" ),
			'post_type'      => $this->postTypeSlug
		);

        $metaQuery = [
            [
                [
                    'key'   => "fg_expiration",
                    'value' => '',
                    'compare' => '!='
                ],
            ],
            [
                'key'   => "fg_is_open",
                'value' => 'no',
                'compare' => '!='
            ],
            'relation' => 'AND',
        ];
        $metaQuery['relation'] = "AND";

        $args['meta_query'] = $metaQuery;

		$postsArray = get_posts( $args );

		if ( count( $postsArray ) ) {
			foreach ( $postsArray as $postItem ) {
				$expMeta = get_post_meta( $postItem->ID, "fg_expiration", true );

				if ( trim( $expMeta ) and ( strtotime( $expMeta ) < time() ) ) {
					update_post_meta( $postItem->ID, "fg_is_open", "no" );
				}
			}
		}
	}


	public function addAllActions() {
		add_action( 'init', array( $this, "registerCustomPostType" ) );
        add_action( 'admin_init', array( $this, "addTinyMCEButtons" ) );
		add_action( 'admin_init', array( $this, "registerScripts" ) );

        $action_name = 'check_and_close_expired_groups';
        add_action( 'wp_ajax_' . $action_name, array( $this, "checkAndCloseGroups" ) );
        add_action( 'wp_ajax_nopriv_' . $action_name, array( $this, "checkAndCloseGroups" ) );
        //add_action( 'init', array( $this, "checkAndCloseGroups" ) );

		$hooksToLoadBootstrap = array(
			"post-new.php",
			"post.php",
			"edit.php",
			"widgets.php",
		);
		foreach ( $hooksToLoadBootstrap as $hook ) {
			add_action( 'admin_print_scripts-' . $hook, array( $this, "includeScriptsAndStyles" ) );
		}

		add_action( 'admin_print_scripts', array( $this, "includeGlobalAdminScriptsAndStyles" ) );

		add_action( 'wp_enqueue_scripts', array( $this, "includeFrontendScriptsAndStyles" ) );

		add_filter( 'wp_insert_post_data', array( $this->metaboxes, "updatePostContentByShortcode" ), 99, 2 );
		add_action( 'save_post', array( $this->metaboxes, "saveMetaBoxData" ) );

		add_filter( 'manage_edit-' . $this->postTypeSlug . '_columns', array( $this, 'editGroupsAdminColumns' ) );
		add_action( 'manage_' . $this->postTypeSlug . '_posts_custom_column', array(
				$this,
				'manageGroupsAdminColumns'
			), 10, 2 );

		add_filter( 'the_title', array( $this, 'changeGroupTitleIfClosed' ), 10, 2 );

		add_action( "wp_ajax_fg_insert_dialog", array( $this->shortcodes, "addPopupContent" ) );

		add_filter( 'the_focus_group_title', array( $this, 'theFocusGroupTitle' ), 10, 2 );

		add_filter( 'the_focus_group_cities', array( $this->metaboxes, 'getAllSavedCities' ), 10 );

		//add_filter( 'post_link', array($this, 'changeGroupPermalink'), 10, 2 );

		add_action( 'init', array( $this, 'rewriteGroupPermalink' ) );

		add_action( 'wp', array( $this, 'afterInitWP' ) );

		add_action('widgets_init',
			create_function('', 'return register_widget("FGWidget");')
		);

		$this->shortcodes->activateShortCodes();
	}

	public function __construct() {
		load_plugin_textdomain( FG_LANG, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

		global $fgMetaboxes, $fgShortcodes;
		$this->pluginSlug   = "fg_admin";
		$this->postTypeSlug = "focusgroup";
		$this->pluginName   = "FocusGroups";

		$this->proposed  = new FGProposed( $this->postTypeSlug );
		$this->metaboxes  = new FGMetabox( $this->postTypeSlug );
		$this->shortcodes = new FGShortcodes( $this->metaboxes, $this->postTypeSlug, $this->proposed );
		$fgMetaboxes = $this->metaboxes;
		$fgShortcodes = $this->shortcodes;

		$this->addAllActions();
	}

}