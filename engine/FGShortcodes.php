<?php

require_once "views/FGShortcodesView.php";

class FGShortcodes extends StdClass {

	private $metaboxes;
	private $shortcodeView;
	private $postTypeSlug;

	function __construct( $metaboxes, $postTypeSlug, $propose ) {
		$this->metaboxes     = $metaboxes;
		$this->shortcodeView = new FGShortcodesView($postTypeSlug, $propose);
		$this->postTypeSlug = $postTypeSlug;
	}

	function getValues( $postID ) {
		return $this->metaboxes->getValues( $postID );
	}

	function activateShortCodes() {

		$shortcodes = array(
			"focus_groups" => "showFocusGroups",
			"focus_group"  => "showSingleFocusGroup",
			"focus_group_propose"  => "proposeFocusGroup",
		);

		foreach ( $shortcodes as $name => $callback ) {
			add_shortcode( $name, array( $this, $callback ) );
		}

	}

	function showFocusGroups( $atts ) {

		$atts = shortcode_atts( array(
			'city'      => '',
			'pay'       => '',
			'gender'    => '',
			'age_range' => '',
			'exclude'   => '',
			'view'   => 'table',
			'fields'   => '',
		), $atts );

		$paramsStr = "";
		foreach($atts as $key => $att){
			if(trim($att) != ""){
				$paramsStr .= " $key='$att'";
			}
		}
		$shortcodeBody = "[focus_groups".$paramsStr."]";

		$fieldsToShow = explode(",",$atts["fields"]);
		$fieldsToShow = array_diff( $fieldsToShow, array( '' ) );
		unset( $atts["fields"] );

		$args = array(
			'posts_per_page' => - 1,
			'orderby'        => 'menu_order post_date',
			'order'          => 'ASC',
			'post_status'    => array( "publish", "future" ),
			'post_type'      => $this->metaboxes->getPostTypeSlug()
		);

		$isListView = ($atts["view"] == "list");
		unset( $atts["view"] );

		if ( (int) trim( $atts["exclude"] ) ) {
			$excludeID       = (int) $atts["exclude"];
			$args["exclude"] = $excludeID;
			unset( $atts["exclude"] );
		}

		$metaQuery = array();
		$metaCity = array();
		foreach ( $atts as $name => $value ) {
			if ( trim( $value ) ) {
			    if($name == 'city'){
                    $metaCity[] = [
                        'key'   => "fg_" . $name,
                        'value' => $value,
                    ];
                }
                else
                {
                    $metaQuery[] = [
                        'key'   => "fg_" . $name,
                        'value' => $value,
                    ];
                }
            }
		}


		if ( count( $metaCity ) ) {

            $metaCity['relation'] = "OR";
            $metaCity[] = array(
				'key'   => "fg_is_national",
				'value' => "yes",
			);

            $metaQuery[] = $metaCity;
		}

        $metaQuery[] = [
            'key'   => "fg_is_open",
            'value' => 'no',
            'compare' => '!='
        ];
        $metaQuery['relation'] = "AND";

        $args["meta_query"] = $metaQuery;

		$postsArray = get_posts( $args );

		if ( count( $postsArray ) < 1 ) {
			return "";
		}
		$newPostsArray = array();
		if ( count( $postsArray ) ) {
			foreach ( $postsArray as $ind => $postItem ) {
                $fields = [];
                $postItem->fg_values = $this->metaboxes->getValues( $postItem->ID, $fields, false );
                $newPostsArray[] = $postItem;
			}
		}

		if ( count( $newPostsArray ) < 1 ) {
			return "";
		}
		if($isListView){
			$nationalTitle = "National";
			$resNewArray = array();
			foreach($newPostsArray as $item){
				$city = $item->fg_values["city"];
				$city = ($item->fg_values["is_national"] == "yes")? $nationalTitle : $city;
				$resNewArray[$city][] = $item;
			}
			ksort($resNewArray);

			$shortcodeOutput =  $this->shortcodeView->render( "groupViewList", $resNewArray );
		}
		else {
			$shortcodeOutput =  $this->shortcodeView->render( "groupView", $newPostsArray, $fieldsToShow );
		}

		$shortcodeEmbedOutput = "";
		if(!isset($_REQUEST["embedgroup"]) and FG_EMBED){
			$shortcodeEmbedUrl = add_query_arg(
				array(
					"embedgroup" => base64_encode(base64_encode($shortcodeBody))
				),
				get_bloginfo("url")
			);

			$shortcodeEmbed = "<iframe style='width: 100%; height:300px' src='".$shortcodeEmbedUrl."'>";

			$shortcodeEmbedOutput = "<div class='fg-embed-holder'>
				<a class='fg-embed-link'>Toggle Embed Code</a>
				<textarea readonly class='fg-embed-holder-text'>".esc_html($shortcodeEmbed)."</textarea>
				</div>";
		}

		return $shortcodeOutput . $shortcodeEmbedOutput;
	}

	function addPopupContent(){
		$this->shortcodeView->getShortCodePopupContent();
		exit;
	}

	function showSingleFocusGroup($atts) {

		$atts = shortcode_atts( array(
			'id' => '',
		), $atts );

		$ID = ( (int) $atts["id"] ) ? (int) $atts["id"] : get_the_ID();

		if ( ! $ID or get_post_type($ID) != $this->postTypeSlug ) {
			return "";
		}
		$postItem = get_post( $ID );
		if ( ! $postItem ) {
			return "";
		}
		$postItem->fg_values = $this->metaboxes->getValues( $ID );

		return $this->shortcodeView->render( "singleView", $postItem );
	}

	function proposeFocusGroup($atts) {

        $fields = $this->metaboxes->getFields();
        $values = $this->metaboxes->getValues( 0, $fields );

        $defaultCities = $this->metaboxes->getFieldBySlug($fields, 'city');
        $defaultGenders = $this->metaboxes->getFieldBySlug($fields, 'gender');
        $defaultRanges = $this->metaboxes->getFieldBySlug($fields, 'age_range');

        $defaults = array(
            'cities' => ($defaultCities) ? $defaultCities['autocomplete'] : array(),
            'genders' => ($defaultGenders) ? $defaultGenders['autocomplete'] : array(),
            'ranges' => ($defaultRanges) ? $defaultRanges['autocomplete'] : array(),
        );

		return $this->shortcodeView->render( "proposeFocusGroup", $defaults );
	}

}