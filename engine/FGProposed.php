<?php

class FGProposed extends StdClass {


	public function __construct( $postTypeSlug ) {
		$this->postTypeSlug = $postTypeSlug;
	}

	public function addProposedFocusGroup($data) {

		$exists = get_page_by_title($data['title'], OBJECT, 'post');

		$can_add = false;

		if(is_null($exists)){
			$can_add = true;
		}
		else{
			$city = get_post_meta($exists->ID, 'fg_city', true);
			$descr = get_post_meta($exists->ID, 'fg_short_description', true);
			if(($city != $data['city']) and ($descr != $data['short_description'])){
				$can_add = true;
			}
		}

		if($can_add) {
			$my_post = array(
				'post_title' => wp_strip_all_tags($data['title']),
				'post_content' => '',
				'post_status' => 'pending',
				'post_type' => $this->postTypeSlug,
			);
			$post_id = wp_insert_post($my_post);

			if ($post_id) {
				unset($data['title']);

				foreach ($data as $key => $value) {
					update_post_meta($post_id, 'fg_' . $key, $value);
				}
				update_post_meta($post_id, 'fg_is_open', 'yes');
			}
		}
	}

}