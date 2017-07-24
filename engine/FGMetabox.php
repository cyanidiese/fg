<?php

require_once "views/FGMetaboxView.php";

class FGMetabox extends StdClass {

	private $metaBoxViews;
	private $metaBoxSlug;
	private $metaBoxTitle;
	private $postTypeSlug;


	public function __construct( $postTypeSlug ) {
		$this->postTypeSlug = $postTypeSlug;
		$this->metaBoxSlug  = "focus-groups-meta-box";
		$this->metaBoxTitle = __( "Focus Group Details", FG_LANG );
		$this->metaBoxViews = new FGMetaboxView();
	}

	public function getPostTypeSlug() {
		return $this->postTypeSlug;
	}

	public function getMetaboxSlug() {
		return $this->metaBoxSlug;
	}

	public function getMetaboxTitle() {
		return $this->metaBoxTitle;
	}

    public function getFieldBySlug($fields, $slug){

        foreach($fields as $field){
            if($field['name'] == $slug){
                return $field;
            }
        }

        return false;

    }

	public function outputPostMetabox( $post = null ) {
		$fields = $this->getFields();
		$post_id = ($post) ? $post->ID : 0;
		$values = $this->getValues( $post_id, $fields );
		$this->metaBoxViews->outputPostMetabox(
			$post, $fields, $values
		);
	}

	public function getFields() {
		return array(
            array(
                "title"   => __( "Email", FG_LANG ),
                "type"    => "text",
                "name"    => "email",
                "default" => "",
                "is_email" => true
            ),
			array(
				"title"   => __( "Open", FG_LANG ),
				"type"    => "switch",
				"name"    => "is_open",
				"default" => "yes"
			),
			array(
				"title"   => __( "Expiration Date", FG_LANG ),
				"type"    => "datatime-picker",
				"name"    => "expiration",
				"default" => date("m/d/Y g:i A", strtotime("+1 month"))
			),
			array(
				"title"   => __( "Short Description", FG_LANG ),
				"type"    => "text",
				"name"    => "short_description",
				"default" => ""
			),
            array(
                "title"   => __( "Is National", FG_LANG ),
                "type"    => "switch",
                "name"    => "is_national",
                "default" => "no"
            ),
			array(
				"title"        => __( "City", FG_LANG ),
				"type"         => "text",
				"name"         => "city",
				"default"      => "",
				"autocomplete" => array(),
			),
			array(
				"title"   => __( "Pay", FG_LANG ),
				"type"    => "text",
				"name"    => "pay",
				"prefix"  => "$",
				"default" => ""
			),
			array(
				"title"   => __( "Facility", FG_LANG ),
				"type"    => "text",
				"name"    => "facility",
				"default" => ""
			),
			array(
				"title"   => __( "Logo", FG_LANG ),
				"type"    => "media",
				"mediatype"    => "image",
				"name"    => "logo",
				"default" => ""
			),
			array(
				"title"        => __( "Gender", FG_LANG ),
				"type"         => "text",
				"name"         => "gender",
				"default"      => "",
				"autocomplete" => array(),
			),
			array(
				"title"        => __( "Age Range", FG_LANG ),
				"type"         => "text",
				"name"         => "age_range",
				"default"      => "",
				"autocomplete" => array(),
			),
			array(
				"title"   => __( "Registration Link", FG_LANG ),
				"type"    => "text",
				"name"    => "registration",
				"default" => ""
			),
			array(
				"title"   => __( "Long Description", FG_LANG ),
				"type"    => "tinymce",
				"name"    => "long_description",
				"default" => ""
			),
            array(
                "title"   => __( "Featured Image", FG_LANG ),
                "type"    => "media",
                "mediatype" => "image",
                "name"    => "featured_image",
                "default" => ""
            ),
			array(
				"title"   => __( "Show table with other groups in this city", FG_LANG ),
				"type"    => "switch",
				"name"    => "show_other",
				"default" => "no"
			),
		);
	}

	function getFieldsSimplified(){
		$result = array(
			"date" => "Published",
			"facility" => "Focus Group Facility",
			"city" => "City",
			"gender" => "Demographic",
			"pay" => "Pay",
			"short" => "Short Description",
			"reg" => "Registration",
			"exp" => "Expiration",
		);

		return $result;
	}
	function getValues( $postID, &$fields = array(), $with_autocomplete = true ) {

        $posts = [];
        if($with_autocomplete)
        {
            $args   = array(
                'posts_per_page' => - 1,
                'post_type'      => $this->postTypeSlug,
            );
            $posts = get_posts($args);
        }
		$fields = count( $fields ) ? $fields : $this->getFields();
		$result = array();
        $isNational = false;
		foreach ( $fields as $fieldInd => $field ) {
			if ( isset( $field["name"] ) ) {
				$name            = $field["name"];
				$metaName        = "fg_" . $name;
				$value           = get_post_meta( $postID, $metaName, true );
				$result[ $name ] = $value;

                if ( $name == "is_national" ) {
                    $isNational = ($value == "yes");
                }

                if ( $name == "city" ) {
                    $fields[$fieldInd]["isNational"] = $isNational;
                }

				if ( isset( $field["autocomplete"] ) && $with_autocomplete ) {

					$autocomplete = array();

					if ( count( $posts ) ) {
						foreach ( $posts as $item ) {
							$metaVal = get_post_meta( $item->ID, $metaName, true );
							if ( trim( $metaVal ) ) {
								$autocomplete[] = $metaVal;
							}
						}
					}

					if ( $name == "gender" ) {
						$autocomplete = array_merge( $autocomplete, array( "M", "F", "M + F" ) );
					}

					if ( $name == "city" ) {
						$autocomplete = array_merge( $autocomplete, fg_get_known_cities() );
					}


					$autocomplete = array_unique( $autocomplete );
					sort( $autocomplete );

					if ( count( $autocomplete ) ) {
						$fields[ $fieldInd ]["autocomplete"] = $autocomplete;
					}
				}
			}
		}

		return $result;
	}

	public function addMetaboxesToPostType() {
		add_meta_box( $this->getMetaboxSlug(), $this->getMetaboxTitle(), array(
				$this,
				"outputPostMetabox"
			), $this->postTypeSlug,
			'normal', 'high' );
	}

	function updatePostContentByShortcode( $data, $postarr ) {
		if ( $data['post_type'] == $this->postTypeSlug ) {
			$data['post_content'] = '[focus_group]';
			//$data['post_title'] = apply_filters("the_focus_group_title", (int) $data['ID'], $_POST["fg_postmeta"]);
		}

		return $data;
	}


	public function saveMetaBoxData( $postID ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post' ) ) {
			return;
		}

		$fg_postmeta = $_POST["fg_postmeta"];
		if ( is_array( $fg_postmeta ) and count( $fg_postmeta ) ) {
			foreach ( $fg_postmeta as $name => $value ) {
				$metaName = "fg_" . $name;
				update_post_meta( $postID, $metaName, $value );
			}
		}
	}

	public function getAllSavedCities() {

		return fg_get_known_and_saved_cities();
	}

}
