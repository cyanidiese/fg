<?php

class FGShortcodesView extends StdClass {

	private $postTypeSlug;
	private $shortCodesEngine;

	public function __construct( $postTypeSlug, $shortCodesEngine ) {
		$this->postTypeSlug = $postTypeSlug;
		$this->shortCodesEngine = $shortCodesEngine;
	}

    public function slugify($text)
    {
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        return $text;
    }

    public function get_permalink($postID){
        $permalink = get_permalink($postID);
        $city = get_post_meta($postID, "fg_city", true);
        $national = get_post_meta($postID, "fg_is_national", true);
	    $city = ($national == "yes")?"National":$city;
        $rules = get_option( 'rewrite_rules' );
        $city = $this->slugify($city);
        if ( isset( $rules['([^/]+)/studies/([^/]+)/?$'] ) and !empty($city) ) {
            $permalink = str_replace("/listings/", "/".$city."/studies/", $permalink);
        }
        return $permalink;
    }

	public function render( $typeFunc, $data, $fieldsToShow = [] ) {

		$content = $this->$typeFunc( $data, $fieldsToShow );

		return $content;
	}

	private function getVal( $title, $group, $noAdditions = false ) {
		$cont = trim( $group->fg_values[ $title ] );
		if ( $noAdditions ) {
			return $cont;
		} elseif ( $title == "city" and ($group->fg_values[ "is_national" ] == "yes") ) {

			$cont = "National";
		}  elseif ( $title == "gender" and in_array( $cont, array( "M", "F", "M + F" ) ) ) {
			switch ( $cont ) {
				case "M + F" :
					$rCont = "Male + Female";
					break;
				case "F" :
					$rCont = "Female";
					break;
				case "M" :
					$rCont = "Male";
					break;
				default :
					$rCont = $cont;
			}
			$cont = $rCont;
		} elseif ( $title != "expiration" ) {
			$cont = ( $cont ) ? $cont : "&mdash;";
			$cont = ( trim( $group->fg_values[ $title ] ) and $title == "pay" and ! $noAdditions ) ? "$" . $cont : $cont;
		}

		return $cont;
	}

	private function toShowCurrentField($fieldName, $fieldsToShow){
		return (count($fieldsToShow))? in_array($fieldName, $fieldsToShow): true;
	}

	private function groupView( $focusGroups, $fieldsToShow = [] ) {
		$result = "";
		$result .= "<div class='focus-groups-table'>
			<div class='thead'>";
			if($this->toShowCurrentField("date", $fieldsToShow)) {
				$result .= "<div class='fg-column-date'>Published</div>";
			}
			if($this->toShowCurrentField("facility", $fieldsToShow)) {
				$result .= "<div class='fg-column-facility'>Focus Group Facility</div>";
			}
		if($this->toShowCurrentField("city", $fieldsToShow)) {
				$result .= "<div class='fg-column-city'>City</div>";
		}
		if($this->toShowCurrentField("gender", $fieldsToShow)) {
				$result .= "<div class='fg-column-gender'>Demographic</div>";
		}
		if($this->toShowCurrentField("pay", $fieldsToShow)) {
			$result .= "<div class='fg-column-pay'>Pay</div>";
		}
		if($this->toShowCurrentField("short", $fieldsToShow)) {
				$result .= "<div class='fg-column-short'>Short Description</div>";
		}
		if($this->toShowCurrentField("reg", $fieldsToShow)) {
				$result .= "<div class='fg-column-reg'>Registration</div>";
		}
		if($this->toShowCurrentField("exp", $fieldsToShow)) {
				$result .= "<div class='fg-column-exp'>Expiration</div>";
		}
			$result .= "</div>";
		foreach ( $focusGroups as $i => $focusGroup ) {
			$postID        = $focusGroup->ID;
			$postPermalink = $this->get_permalink( $postID );

			$attachmentID = (int) $this->getVal( "logo", $focusGroup );
			if ( $attachmentID ) {
				$thumb     = wp_get_attachment_image_src( $attachmentID );
				$imgUrl    = $thumb[0];
				$haveThumb = true;
			} else {
				$imgUrl    = FG_URL . "assets/images/holder-camera.png";
				$haveThumb = false;
			}
			$facility          = $this->getVal( "facility", $focusGroup );
			$short_description = $this->getVal( "short_description", $focusGroup );
			$city              = $this->getVal( "city", $focusGroup );
			$pay               = $this->getVal( "pay", $focusGroup );
			$payMeta           = $this->getVal( "pay", $focusGroup, true );
			$gender            = $this->getVal( "gender", $focusGroup );
			$age_range         = $this->getVal( "age_range", $focusGroup );
			$link              = $this->getVal( "registration", $focusGroup, true );
			$expTime           = $this->getVal( "expiration", $focusGroup );
			$timeDate          = strtotime( $focusGroup->post_date );
			$timeDateF         = date( "m/d/Y", $timeDate );
			$timeDateI         = date( "Y-m-d", $timeDate );
			$timeDateExp       = strtotime( $expTime );
			$timeDateFExp      = date( "m/d/Y", $timeDateExp );
			$timeDateIExp      = date( "Y-m-d", $timeDateExp );
			$rowClass          = ( $i % 2 > 0 ) ? "even" : "odd";


			$result .= "<div class='tbody " . $rowClass . "'  itemscope
				     itemtype='http://schema.org/Event'>";

		if($this->toShowCurrentField("date", $fieldsToShow)) {
			$result .= "<div class='fg-column-date'>" . $timeDateF . "
						<meta itemprop='startDate' content='" . $timeDateI . "'>
					</div>";
		}
		if($this->toShowCurrentField("facility", $fieldsToShow)) {
			$result .= "<div class='fg-column-facility'>
						" . $facility . "
						<meta itemprop='name' content='" . $short_description . "'>
						<meta itemprop='image' content='" . $imgUrl . "'>
					</div>";
		}
			if($this->toShowCurrentField("city", $fieldsToShow)) {
			$result .= "<div class='fg-column-city' itemprop='location' itemscope itemtype='http://schema.org/Place'>
						" . $city . "
						<meta itemprop='name' content='" . $city . "'>
						<span itemprop='address' itemscope itemtype='http://schema.org/PostalAddress'>
							<meta itemprop='addressLocality' content='" . $city . "'>
						</span>
					</div>";
			}
			if($this->toShowCurrentField("gender", $fieldsToShow)) {
			$result .= "<div class='fg-column-gender'>" . $gender . "<br><span
							itemprop='typicalAgeRange'>" . $age_range . "</span></div>";
			}
			if($this->toShowCurrentField("pay", $fieldsToShow)) {
				$result .= "<div class='fg-column-pay' itemprop='offers' itemscope itemtype='http://schema.org/Offer'>
						" . $pay . "
						<meta itemprop='price' content='" . $payMeta . "'>
						<meta itemprop='priceCurrency' content='USD'>
						<meta itemprop='url' content='" . ( ( trim( $link ) ) ? $link : $postPermalink ) . "'>
					</div>";
			}
			if($this->toShowCurrentField("short", $fieldsToShow)) {
			$result .= "<div class='fg-column-short'><b><a href='" . $postPermalink . "'>" . $short_description . "</a></b></div>";
			}
			if($this->toShowCurrentField("reg", $fieldsToShow)) {
			$result .= "<div class='fg-column-reg'>
					";
			if ( trim( $link ) != $postPermalink ) {
				$result .= "<a target='_blank' href='" . $link . "'>Click here<br>to sign up</a>";
			}
			$result .= "</div>";
			}
			if($this->toShowCurrentField("exp", $fieldsToShow)) {
			$result .= "<div class='fg-column-exp'>" .
			           ( ( trim( $expTime ) ) ? $timeDateFExp . "<meta itemprop='endDate' content='" . $timeDateIExp . "'>" : "" ) .
			           "
					</div>";
			}
			$result .= "</div>
			";
		}
		$result .= '</div>';

		return $result;
	}

	private function groupViewList( $focusGroups ) {
		$result = "";
		foreach ( $focusGroups as $cityKey => $focusGroupsPart ) {
			$result .= "<h5>" . $cityKey . "</h5>";
			$result .= "<ul>";
			foreach ( $focusGroupsPart as $i => $focusGroup ) {


				$postID        = $focusGroup->ID;
				$postPermalink = $this->get_permalink( $postID );

				$attachmentID = (int) $this->getVal( "logo", $focusGroup );
				if ( $attachmentID ) {
					$thumb     = wp_get_attachment_image_src( $attachmentID, 'full' );
					$imgUrl    = $thumb[0];
					$haveThumb = true;
				} else {
					$imgUrl    = FG_URL . "assets/images/holder-camera.png";
					$haveThumb = false;
				}
				$facility          = $this->getVal( "facility", $focusGroup );
				$short_description = $this->getVal( "short_description", $focusGroup );
				$city              = $this->getVal( "city", $focusGroup );
				$pay               = $this->getVal( "pay", $focusGroup );
				$payMeta           = $this->getVal( "pay", $focusGroup, true );
				$gender            = $this->getVal( "gender", $focusGroup );
				$age_range         = $this->getVal( "age_range", $focusGroup );
				$link              = $this->getVal( "registration", $focusGroup, true );
				$expTime           = $this->getVal( "expiration", $focusGroup );
				$timeDate          = strtotime( $focusGroup->post_date );
				$timeDateF         = date( "m/d/Y", $timeDate );
				$timeDateI         = date( "Y-m-d", $timeDate );
				$timeDateExp       = strtotime( $expTime );
				$timeDateFExp      = date( "m/d/Y", $timeDateExp );
				$timeDateIExp      = date( "Y-m-d", $timeDateExp );
				$rowClass          = ( $i % 2 > 0 ) ? "even" : "odd";


				$postID        = $focusGroup->ID;
				$postPermalink = $this->get_permalink( $postID );

				$result .= "<li itemscope itemtype='http://schema.org/Event'>
					<a href='" . $postPermalink . "'>
						<meta itemprop='startDate'
						      content='" . $timeDateF . "'>
						" . $facility . " -
						<span itemprop='offers' itemscope itemtype='http://schema.org/Offer'>
						" . $pay . "
							<meta itemprop='price' content='" . $payMeta . "'>
						<meta itemprop='priceCurrency' content='USD'>
							<meta itemprop='url' content='" . ( ( trim( $link ) ) ? $link : $postPermalink ) . "'>
					</span>

						<meta itemprop='name'
						      content='" . $short_description . "'>
						<meta itemprop='image' content='" . $imgUrl . "'>
					<span itemprop='location' itemscope itemtype='http://schema.org/Place'>
						<meta itemprop='name' content='" . $city . "'>
						<span itemprop='address' itemscope itemtype='http://schema.org/PostalAddress'>
							<meta itemprop='addressLocality' content='" . $city . "'>
						</span>
					</span>
						<meta itemprop='typicalAgeRange' content='" . $age_range . "'>
					<span>" . $short_description . "

					</span>

					</a>
				</li>
			";
			}
			$result .= "</ul>";
		}

		return $result;
	}

	private function singleView( $focusGroup ) {

		$result = "";

		$postID        = $focusGroup->ID;
		$postPermalink = $this->get_permalink( $postID );

		$attachmentID = (int) $this->getVal( "logo", $focusGroup );
		if ( $attachmentID ) {
			$thumb     = wp_get_attachment_image_src( $attachmentID, 'full' );
			$imgUrl    = $thumb[0];
			$haveThumb = true;
		} else {
			$imgUrl    = FG_URL . "assets/images/holder-camera.png";
			$haveThumb = false;
		}
		$facility         = $this->getVal( "facility", $focusGroup );
		$long_description = $this->getVal( "long_description", $focusGroup );
		$city             = $this->getVal( "city", $focusGroup );
		$pay              = $this->getVal( "pay", $focusGroup );
		$payMeta          = $this->getVal( "pay", $focusGroup, true );
		$gender           = $this->getVal( "gender", $focusGroup );
		$age_range        = $this->getVal( "age_range", $focusGroup );
		$link             = $this->getVal( "registration", $focusGroup, true );
		$expTime          = $this->getVal( "expiration", $focusGroup );
		$timeDate         = strtotime( $focusGroup->post_date );
		$timeDateF        = date( "m/d/Y", $timeDate );
		$timeDateI        = date( "Y-m-d", $timeDate );
		$timeDateExp      = strtotime( $expTime );
		$timeDateFExp     = date( "m/d/Y", $timeDateExp );
		$timeDateIExp     = date( "Y-m-d", $timeDateExp );

		$is_the_same_page = ( $postID == get_the_ID() );

		$result .= "<div class='single-focus-group'>";
		$result .= "<div class='single-focus-group-inner'  itemscope
				     itemtype='http://schema.org/Event'>";
		if ( ! $is_the_same_page ) {
			//$result .= "<h2>" . apply_filters( "the_focus_group_title", $postID ) . "</h2>";
			$result .= "<h2>" . get_the_title($postID) . "</h2>";
		}
		if ( trim( $link ) ) {
			$result .= "<p class='short-info'><a target='_blank' class='reg-single-link' href='" . $link . "'>Click Here to Sign Up</a></p>";
		}
		$result .= "<div class='short-info-block'>";


		$result .= "<div class='contact-card-logo'>";
		$result .= '<img class="' . ( $haveThumb ? "" : "no-photo-logo" ) . '" src="' . $imgUrl . '">';
		$result .= "</div>";
		$result .= "<p class='short-info'><b>Published: </b> " . $timeDateF . "</p>";
		$result .= "<p class='short-info'><b>City: </b> " . $city . "</p>";
		$result .= "<p class='short-info'><b>Pay: </b> " . $pay . "</p>";
		$result .= "<p class='short-info'><b>Gender: </b> " . $gender . "</p>";
		$result .= "<p class='short-info'><b>Age Range: </b> " . $age_range . "</p>";
		$result .= "<p class='short-info'><b>Facility: </b> " . $facility . "</p>";

		if ( trim( $expTime ) ) {
			$result .= "<p class='short-info'><b>Expiration Date: </b> " . $timeDateFExp . "</p>";
			$result .= "<meta itemprop='endDate' content='" . $timeDateIExp . "'>";
		}


		$result .= "</div>";
		if ( trim( $long_description ) ) {
			$result .= "<div class='short-info-block'>";
			$result .= "<p class='short-info'><b>Description:</b></p>";
			$result .= $long_description;
			$result .= "</div>";
		}
		$result .= "<meta itemprop='startDate' content='" . $timeDateI . "'>
		<meta itemprop='name' content='" . get_the_title( $postID ) . "'>
		<meta itemprop='image' content='" . $imgUrl . "'>
		<div itemprop='location' itemscope itemtype='http://schema.org/Place'>
			<meta itemprop='name' content='" . $city . "'>
						<span itemprop='address' itemscope itemtype='http://schema.org/PostalAddress'>
							<meta itemprop='addressLocality' content='" . $city . "'>
						</span>
		</div>
		<div itemprop='offers' itemscope itemtype='http://schema.org/Offer'>
			<meta itemprop='price' content='" . $payMeta . "'>
			<meta itemprop='priceCurrency' content='USD'>
			<meta itemprop='url' content='" . ( ( trim( $link ) ) ? $link : $postPermalink ) . "'>
		</div>";
		$result .= "</div>";

		if($this->getVal( "show_other", $focusGroup ) == "yes") {
			$otherGroups = ( trim( $city ) ) ? do_shortcode( '[focus_groups city="' . $city . '" exclude="' . $postID . '"]' ) : "";
			if ( trim( $otherGroups ) ) {
				$result .= "<div class='short-info-block'>";
				$result .= "<h5 class='other-focus-groups-title'>Other Focus Groups In " . $city . "</h5>";
				$result .= $otherGroups;
				$result .= "</div>";
			}
		}
		$result .= "</div>";

		return $result;

	}


	public function getShortCodePopupContent() {
		?>

		<div class="wp-core-ui">

			<form id="wp-link" tabindex="-1">
				<div id="link-selector">
					<div class="fg-shortcode-options single-focus-groups" style="display: none">
						<p class="howto"><?php _e( 'Please, select Focus Group' ); ?></p>

						<div>
							<?php
							$args        = array(
								'numberposts' => - 1,
								'orderby'     => 'post_title',
								'order'       => 'ASC',
								'post_type'   => $this->postTypeSlug,
								'post_status' => array( 'publish', 'future' )
							);
							$posts_array = get_posts( $args );

							?>
							<select id="focus-groups-field" type="text" name="href"
							        style="  width: 100%;  border: 1px solid #ccc;">
								<?php
								if ( count( $posts_array ) ) {
									foreach ( $posts_array as $post ) {
										?>
										<option
											value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
									<?php
									}
								}
								?>
							</select>
						</div>
					</div>

					<div class="fg-shortcode-options group-focus-groups">
						<p class="howto"><?php _e( 'Please, select a city' ); ?></p>

						<div>
							<?php
							$cities = apply_filters( "the_focus_group_cities", "" );

							?>
							<select id="focus-groups-cities"
							        style="width: 100%;  border: 1px solid #ccc;">
								<option
									value="">ALL
								</option>
								<option value="national">National
								</option>
								<?php
								if ( count( $cities ) ) {
									foreach ( $cities as $city ) {
										?>
										<option
											value="<?php echo esc_attr( $city ); ?>"><?php echo $city; ?></option>
									<?php
									}
								}
								?>
							</select>
						</div>
					</div>
					<label
						style=" margin-top: 15px; display: block; cursor:pointer;"
						for="selecting-gf-shortcode-view"><input type="checkbox" id="selecting-gf-shortcode-view"> List
						View</label>

				</div>
			</form>
		</div>
	<?php
	}

	private function proposeFocusGroup($defaults) {

        $result = "";
        if(defined('FG_DISPLAYED_PROPOSE_FORM')){
            return "";
        }
        else {
            define('FG_DISPLAYED_PROPOSE_FORM', true);
            if(trim($_POST['g-recaptcha-response'])) {
                $this->shortCodesEngine->addProposedFocusGroup($_POST['fg_propose']);
                $result .= '<h4>Thank You. Your focus group is pending review now.</h4>';
            }
            else{
                $result .= "
                    <form class='fg-propose-form' method='post'>
                        <div class='fg-propose-field'>

                            <label for='fg_propose_title'>Title</label>
                            <input type='text' id='fg_propose_title' name='fg_propose[title]' data-validation='required'>
                            
                        </div>
                        <div class='fg-propose-field'>

                            <label for='fg_propose_expiration'>Expiration Date (mm/dd/yyyy)</label>
                            <input type='text' id='fg_propose_expiration' name='fg_propose[expiration]' 
                            value='".date('m/d/Y')."'
                            data-validation='required date' data-validation-format='mm/dd/yyyy'
                            data-validation-require-leading-zero='false'
                            >
                            
                        </div>
                        <div class='fg-propose-field'>

                            <label for='fg_propose_short_description'>Short Description</label>
                            <input type='text' id='fg_propose_short_description' name='fg_propose[short_description]' data-validation='required'>
                            
                        </div>
                        <div class='fg-propose-field'>

                            <label for='fg_propose_city'>City</label>
                            <input type='hidden' name='fg_propose[is_national]' value='no'>
                            <input type='text' id='fg_propose_city' name='fg_propose[city]' data-validation='required'>
                            
                        </div>
                        <div class='fg-propose-field'>

                            <label for='fg_propose_pay'>Pay ($)</label>
                            <input type='text' id='fg_propose_pay' name='fg_propose[pay]' data-validation='required'>
                            
                        </div>
                        <div class='fg-propose-field'>

                            <label for='fg_propose_facility'>Facility</label>
                            <input type='text' id='fg_propose_facility' name='fg_propose[facility]' data-validation='required'>
                            
                        </div>
                        <div class='fg-propose-field'>

                            <label for='fg_propose_gender'>Gender</label>
                            <input type='text' id='fg_propose_gender' name='fg_propose[gender]' data-validation='required'>
                            
                        </div>
                        <div class='fg-propose-field'>

                            <label for='fg_propose_age_range'>Age Range</label>
                            <input type='text' id='fg_propose_age_range' name='fg_propose[age_range]' data-validation='required'>
                            
                        </div>
                        <div class='fg-propose-field'>

                            <label for='fg_propose_registration'>Registration Link</label>
                            <input type='text' id='fg_propose_registration' name='fg_propose[registration]'>
                            
                        </div>
                        <div class='fg-propose-field'>

                            <label for='fg_propose_long_description'>Long Description</label>
                            <textarea id='fg_propose_long_description' name='fg_propose[long_description]' rows='5'></textarea>
                            
                        </div>
                        <div class='fg-propose-field'>
                            
                            <div id='captcha-validation'></div>
                            
                        </div>
                        <div class='fg-propose-field'>

                            <button type='submit' id='fg-propose-submit'>Submit Group For Preview</button>
                            
                        </div>
                    </form>
                    
                        <script type='text/javascript'>
                            var availableCities = ". json_encode($defaults['cities']).";
                            var availableGenders = ". json_encode($defaults['genders']).";
                            var availableRanges = ". json_encode($defaults['ranges']).";
                            var verifyCallback = function() {
                                var captchaBlock = jQuery('#captcha-validation');
                                    if(grecaptcha.getResponse().trim() == ''){
                                        e.preventDefault();
                                        captchaBlock.addClass('need-captcha-validation');
                                    }
                                    else{
                                        captchaBlock.removeClass('need-captcha-validation');
                                    }
                            };
                            var onloadCallback = function() {
                                grecaptcha.render('captcha-validation', {
                                    'sitekey' : '6LeKLSMTAAAAAC79RE1UhbtMqXfOnTFWxNraW9pc',
                                    'callback' : verifyCallback,
                                    'theme' : 'light'
                                });
                            };
                            jQuery.validate({
                                modules : 'date'
                            });
                            jQuery(function() {
                                jQuery( '#fg_propose_expiration' ).datepicker();
                            });
                            jQuery( '#fg_propose_city' ).autocomplete({
                                source: availableCities
                            });
                            jQuery( '#fg_propose_gender' ).autocomplete({
                                source: availableGenders
                            });
                            jQuery( '#fg_propose_age_range' ).autocomplete({
                                source: availableRanges
                            });
                            jQuery(document).ready(function(){
                                jQuery('#fg-propose-submit').click(function(e){
                                    var captchaBlock = jQuery('#captcha-validation');
                                    if(grecaptcha.getResponse().trim() == ''){
                                        e.preventDefault();
                                        captchaBlock.addClass('need-captcha-validation');
                                    }
                                    else{
                                        captchaBlock.removeClass('need-captcha-validation');
                                    }
                                });
                            });
                        </script>
                        <script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit' async defer></script>
                        ";
                $result .= '';
            }

            return $result;
        }

	}

}