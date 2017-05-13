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
						$autocomplete = array_merge( $autocomplete, $this->getCities() );
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

	    global $wpdb;

        $autocomplete = array();

	    $query = "SELECT distinct(`meta_value`) FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` = 'fg_city' ORDER BY `meta_value` ASC";
        $query_results = $wpdb->get_results($query);

        if ( count( $query_results ) ) {
            foreach ( $query_results as $query_result ) {
                if(trim($query_result->meta_value)) {
                    $autocomplete[] = $query_result->meta_value;
                }
            }
        }
		$autocomplete = array_merge( $autocomplete, $this->getCities() );
		$autocomplete = array_unique( $autocomplete );
		sort( $autocomplete );
		return $autocomplete;
	}


	private function getCities() {
		return array(
			1   => 'Albany',
			2   => 'Alexandria',
			3   => 'Allentown',
			4   => 'Altoona-Johnstown',
			5   => 'Amarillo',
			6   => 'Anchorage',
			7   => 'Arlington',
			8   => 'Atlanta',
			9   => 'Austin',
			10  => 'Baker',
			11  => 'Baltimore',
			12  => 'Baton Rouge',
			13  => 'Beaumont',
			14  => 'Belleville',
			15  => 'Biloxi',
			16  => 'Birmingham',
			17  => 'Bismarck',
			18  => 'Boise',
			19  => 'Boston',
			20  => 'Bridgeport',
			21  => 'Brooklyn',
			22  => 'Brownsville',
			23  => 'Buffalo',
			24  => 'Burlington',
			25  => 'Camden',
			26  => 'Charleston',
			27  => 'Charlotte',
			28  => 'Cheyenne',
			29  => 'Chicago',
			30  => 'Cincinnati',
			31  => 'Cleveland',
			32  => 'Colorado Springs',
			33  => 'Columbus',
			34  => 'Corpus Christi',
			35  => 'Covington',
			36  => 'Crookston',
			37  => 'Dallas',
			38  => 'Davenport',
			39  => 'Denver',
			40  => 'Des Moines',
			41  => 'Detroit',
			42  => 'Dodge City',
			43  => 'Dubuque',
			44  => 'Duluth',
			45  => 'Edmonton (Cananda)',
			46  => 'El Paso',
			47  => 'Erie',
			48  => 'Evansville',
			49  => 'Fairbanks',
			50  => 'Fall River',
			51  => 'Fargo',
			52  => 'Fort Wayne-South Bend',
			53  => 'Fort Worth',
			54  => 'Fresno',
			55  => 'Gallup',
			56  => 'Galveston-Houston',
			57  => 'Gary',
			58  => 'Gaylord',
			59  => 'Grand Island',
			60  => 'Grand Rapids',
			61  => 'Great Falls-Billings',
			62  => 'Green Bay',
			63  => 'Greensburg',
			64  => 'Harrisburg',
			65  => 'Hartford',
			66  => 'Helena',
			67  => 'Honolulu',
			68  => 'Houma-Thibodaux',
			69  => 'Indianapolis',
			70  => 'Jackson',
			71  => 'Jefferson City',
			72  => 'Joliet',
			73  => 'Juneau',
			74  => 'Kalamazoo',
			75  => 'Kansas City',
			76  => 'Kansas City-St. Joseph',
			77  => 'Knoxville',
			78  => 'La Crosse',
			79  => 'Lafayette',
			80  => 'Lafayette in Indiana',
			81  => 'Lake Charles',
			82  => 'Lansing',
			83  => 'Laredo',
			84  => 'Las Cruces',
			85  => 'Las Vegas',
			86  => 'Lexington',
			87  => 'Lincoln',
			88  => 'Little Rock',
			89  => 'Los Angeles',
			90  => 'Louisville',
			91  => 'Lubbock',
			92  => 'Madison',
			93  => 'Manchester',
			94  => 'Marquette',
			95  => 'Memphis',
			96  => 'Metuchen',
			97  => 'Miami',
			98  => 'Military Services',
			99  => 'Milwaukee',
			100 => 'Mobile',
			101 => 'Monterey',
			102 => 'Nashville',
			103 => 'New Orleans',
			104 => 'New Ulm',
			105 => 'New York',
			106 => 'Newark',
			107 => 'Newton for Melkites',
			108 => 'Norwich',
			109 => 'Oakland',
			110 => 'Ogdensburg',
			111 => 'Oklahoma City',
			112 => 'Omaha',
			113 => 'Orange',
			114 => 'Orlando',
			115 => 'Our Lady of Deliverance of Newark for Syrians',
			116 => 'Our Lady of Lebanon of L.A. for Maronites',
			117 => 'Owensboro',
			118 => 'Palm Beach',
			119 => 'Paterson',
			120 => 'Pensacola-Tallahassee',
			121 => 'Peoria',
			122 => 'Philadelphia',
			123 => 'Philadelphia for Ukrainians',
			124 => 'Phoenix',
			125 => 'Pittsburgh',
			126 => 'Portland in Maine',
			127 => 'Portland in Oregon',
			128 => 'Providence',
			129 => 'Pueblo',
			130 => 'Raleigh',
			131 => 'Rapid City',
			132 => 'Reno',
			133 => 'Richmond',
			134 => 'Rochester',
			135 => 'Rockford',
			136 => 'Rockville Centre',
			137 => 'Sacramento',
			138 => 'Saginaw',
			139 => 'Salina',
			140 => 'Salt Lake City',
			141 => 'San Angelo',
			142 => 'San Antonio',
			143 => 'San Bernardino',
			144 => 'San Diego',
			145 => 'San Francisco',
			146 => 'San Jose',
			147 => 'Santa Fe',
			148 => 'Santa Rosa',
			149 => 'Savannah',
			150 => 'Scranton',
			151 => 'Seattle',
			152 => 'Shreveport',
			153 => 'Sioux City',
			154 => 'Sioux Falls',
			155 => 'Spokane',
			156 => 'Springfield in Illinois',
			157 => 'Springfield in Massachusetts',
			158 => 'Springfield-Cape Girardeau',
			159 => 'St. Augustine',
			160 => 'St. Cloud',
			161 => 'St. Josaphat of Parma for Ukrainians',
			162 => 'St. Louis',
			163 => 'St. Maron of Brooklyn for the Maronites',
			164 => 'St. Nicholas of Chicago for Ukrainians',
			165 => 'St. Paul-Minneapolis',
			166 => 'St. Petersburg',
			167 => 'St. Thomas the Apostle of Chicago-Syro-Malabars',
			168 => 'St. Thomas, VI',
			169 => 'Stamford for Ukrainians',
			170 => 'Steubenville',
			171 => 'Stockton',
			172 => 'Superior',
			173 => 'Syracuse',
			174 => 'Toledo',
			175 => 'Trenton',
			176 => 'Tucson',
			177 => 'Tulsa',
			178 => 'Tyler',
			179 => 'Venice',
			180 => 'Victoria',
			181 => 'Washington',
			182 => 'Wheeling-Charleston',
			183 => 'Wichita',
			184 => 'Wilmington',
			185 => 'Winona',
			186 => 'Worcester',
			187 => 'Yakima',
			188 => 'Youngstown',
		);
	}

}
