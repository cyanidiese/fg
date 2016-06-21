<?php

class FGWidget extends WP_Widget {

	private $metaboxes;
	private $shortcodes;

	function __construct() {
		global $fgMetaboxes, $fgShortcodes;

		$this->metaboxes = $fgMetaboxes;
		$this->shortcodes = $fgShortcodes;

		parent::__construct(
			'fgwidget',
			__( 'Focus Groups', 'focusgroups' ),
			array( 'description' => __( 'Widget for displaying focus groups in sidebar', 'focusgroups' ), )
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		if($instance["list_view"] == "yes"){
			$dop_str = "view='list'";
		}
		else{
			$dop_str = "fields='".implode(",",$instance["fields"])."'";
		}
		$shortcode = "[focus_groups city='".$instance["city"]."' ".$dop_str."]";
		echo do_shortcode($shortcode);

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Focus Groups', 'text_domain' );
		$city = ! empty( $instance['city'] ) ? $instance['city'] : "";
		$viewFields = ! empty( $instance['fields'] ) ? $instance['fields'] : ["pay","short"];
		$list_view = ! empty( $instance['list_view'] ) ? $instance['list_view'] : "no";
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'city' ); ?>"><?php _e( 'City:' ); ?></label>
			<?php
			$cities = apply_filters( "the_focus_group_cities", "" );
			?>
			<select  class="widefat" id="<?php echo $this->get_field_id( 'city' ); ?>" name="<?php echo $this->get_field_name( 'city' ); ?>">
				<option <?php selected($city, "");?>
					value="">ALL
				</option>
				<option value="national" <?php selected($city, "national");?>>National
				</option>
				<?php
				if ( count( $cities ) ) {
					foreach ( $cities as $cityItem ) {
						?>
						<option
							value="<?php echo esc_attr( $cityItem ); ?>" <?php selected($city, $cityItem);?>><?php echo $cityItem; ?></option>
					<?php
					}
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'list_view' ); ?>"><?php _e( 'List View:' ); ?></label>
			<select  class="widefat is_list_view_select" id="<?php echo $this->get_field_id( 'list_view' ); ?>" name="<?php echo $this->get_field_name( 'list_view' ); ?>">
				<option value="no" <?php selected($list_view, "no");?>><?php _e("No")?></option>
				<option value="yes" <?php selected($list_view, "yes");?>><?php _e("Yes")?></option>
			</select>
		</p>
		<p class="not_list_view_fields" style="<?php echo ($list_view == "yes")?" display:none; ":"";?>">
			<?php
			$fields = $this->metaboxes->getFieldsSimplified();
				if ( count( $fields ) ) {
					foreach ( $fields as $fieldItem => $fieldItemTitle ) {
						?>
						<label for="<?php echo $this->get_field_id( 'fields' )."_".$fieldItem; ?>">
						<input type="checkbox"  id="<?php echo $this->get_field_id( 'fields' )."_".$fieldItem; ?>"
						         name="<?php echo $this->get_field_name( 'fields' ); ?>[]"
						         value="<?php echo esc_attr( $fieldItem ); ?>"
							<?php checked(in_array($fieldItem, $viewFields), true);?>>
							<?php echo $fieldItemTitle; ?></label>
					<?php
					}
				}
				?>
		</p>
	<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['city'] = ( ! empty( $new_instance['city'] ) ) ? strip_tags( $new_instance['city'] ) : '';
		$instance['fields'] = ( isset($new_instance['fields']) ) ? (array) $new_instance['fields']: ["pay","short"];
		$instance['list_view'] = ! empty( $new_instance['list_view'] ) ? $new_instance['list_view'] : "no";

		return $instance;
	}

}