<?php
class FGMetaboxView extends StdClass{

    private function getFieldName($fieldName, $is_array = false)
    {
        $result = "fg_postmeta[" . $fieldName . "]";
        return $is_array ? $result . "[]" : $result;
    }

    private function getFieldID($fieldName, $append = false)
    {
        $result = "fg_postmeta_" . $fieldName;
        return $append ? $result . "_" . $append : $result;
    }


    private function outputFieldSpin($field, $value)
    {

        $step = ($field["step"]) ? $field["step"] : 1;
        $min = ($field["min"]) ? $field["min"] : 1;
        $max = ($field["max"]) ? $field["max"] : 20;
        $prefix = ($field["prefix"]) ? $field["prefix"] : "";
        $postfix = ($field["postfix"]) ? $field["postfix"] : "";
        $value = ($value) ? $value : $field["default"];
        ?>
        <div class="col-xs-12 col-md-4 " style="padding-left: 0px">

            <input type="text" value="<?php echo $value; ?>" class="bootstrapSpin form-control"
                   data-min="<?php echo $min; ?>" data-max="<?php echo $max; ?>" data-step="<?php echo $step; ?>"
                   data-prefix="<?php echo $prefix; ?>" data-postfix="<?php echo $postfix; ?>"
                   name="<?php echo $this->getFieldName($field["name"]); ?>"
                   id="<?php echo $this->getFieldID($field["name"]); ?>">

        </div>
    <?php
    }

    private function outputFieldSwitch($field, $value)
    {

        $value = ($value) ? $value : $field["default"];
        $checked = ($value == "yes") ? "checked" : "";
        ?>
        <input type="checkbox"  <?php echo $checked; ?> class="bootstrapSwitch"
               data-on-color="success" data-off-color="danger"
               data-on-text="<?php _e("Yes"); ?>" data-off-text="<?php _e("No"); ?>"
               data-toggle-row="<?php echo ($field["name"] == "is_national")?"city":"none";?>"
            >
        <input type="hidden" name="<?php echo $this->getFieldName($field["name"]); ?>"
               id="<?php echo $this->getFieldID($field["name"]); ?>"
               value="<?php echo $value; ?>" class="trueValue">
    <?php
    }

    private function outputFieldText($field, $value)
    {

        $value = ($value) ? $value : $field["default"];

        if ($field["prefix"] or $field["postfix"]) {
            echo '<div class="input-group">';
        }
        if ($field["prefix"]) {
            echo '<span class="input-group-addon">' . $field["prefix"] . '</span>';
        }

	    $autocomplete = (isset($field["autocomplete"]) and is_array($field["autocomplete"]) and count($field["autocomplete"]));
	    $autocompleteFields = ($autocomplete) ? $field["autocomplete"] : array();
        ?>

        <input type="text" class="form-control <?php echo ($autocomplete)?" bootstrapSelectize ":"";?>"
               name="<?php echo $this->getFieldName($field["name"]); ?>"
               id="<?php echo $this->getFieldID($field["name"]); ?>"
               data-value="<?php echo $value ?>"
               value="<?php echo ($autocomplete)? implode(",",$autocompleteFields) : $value ?>">
        <?php

        if ($field["postfix"]) {
            echo '<span class="input-group-addon">' . $field["postfix"] . '</span>';
        }
        if ($field["prefix"] or $field["postfix"]) {
            echo '</div>';
        }
    }


    private function outputFieldTextarea($field, $value)
    {

        $value = ($value) ? $value : $field["default"];
        $rows = ($field["rows"]) ? $field["rows"] : 4;

        ?>
        <textarea class="form-control" rows="<?php echo $rows; ?>"
                  name="<?php echo $this->getFieldName($field["name"]); ?>"
                  id="<?php echo $this->getFieldID($field["name"]); ?>"
            ><?php echo $value; ?></textarea>
    <?php

    }

    private function outputFieldSelect($field, $value)
    {

        $options = (is_array($field["options"]) and count($field["options"])) ? $field["options"] : array();
        $value = ($value) ? $value : $field["default"];
        $livesearch = ($field["autosearch"]) ? ' data-live-search="true" ' : "";

        ?>
        <select class="selectpicker" <?php echo $livesearch ?>
                name="<?php echo $this->getFieldName($field["name"]); ?>"
                id="<?php echo $this->getFieldID($field["name"]); ?>">
            <?php if (count($options)) { ?>
                <?php foreach ($options as $val => $title) {
                    $selected = ($value == $val) ? " selected " : "";
                    echo '<option ' . $selected . ' value="' . $val . '">' . $title . '</option>';
                }?>
            <?php } ?>
        </select>
    <?php
    }

    private function outputFieldCheckboxes($field, $value)
    {

        $options = (is_array($field["options"]) and count($field["options"])) ? $field["options"] : array();
        $value = (is_array($value) and count($value)) ? $value : $field["default"];
        ?>

        <?php if (count($options)) { ?>
        <?php foreach ($options as $i => $option) {
            ?>
            <label for="<?php echo $this->getFieldID($field["name"], $i); ?>">
                <input type="checkbox" class="form-control old-margins-checkbox"
                    <?php checked((in_array($option["value"], $value)), true); ?>
                       name="<?php echo $this->getFieldName($field["name"], true); ?>"
                       id="<?php echo $this->getFieldID($field["name"], $i); ?>"
                       value="<?php echo $option["value"] ; ?>">
                <?php echo $option["title"];?>
            </label>&nbsp;&nbsp;&nbsp;
        <?php
        }?>
    <?php } ?>
    <?php
    }

	private function outputTinymce( $field, $value) {

		$value = ( $value ) ? $value : $field["default"];
		?>
			<?php
			$settings = Array(
				"wpautop" => true,
				"media_buttons" => false,
				"teeny" => false,
				"quicktags" => true,
				"tinymce" => true,
				"textarea_rows" => 10,
				"textarea_name" => $this->getFieldName($field["name"] )
			);
			$start_value = ($value) ? ($value) : '';
			if (function_exists('wp_editor')) wp_editor($start_value, $this->getFieldID($field["name"] ), $settings);
			?>
	<?php

	}

	private function outputFieldMedia( $field, $value) {

		$value         = ( $value ) ? $value : $field["default"];
		$mediatype     = ( $field["mediatype"] ) ? $field["mediatype"] : "text";
		$is_media_text = ( $mediatype == "text" );

		$attachmentLink              = wp_get_attachment_url( $value );
		$attachmentIsImage           = wp_attachment_is_image( $value );
		$presentedIsValidImage       = ( $attachmentLink and $attachmentIsImage and ! $is_media_text );
		$presentedIsValidButNotImage = ( $attachmentLink and ! $attachmentIsImage and $is_media_text );
		$buttonText                  = ( $presentedIsValidImage or $presentedIsValidButNotImage ) ? __( 'Change' ) : __( 'Add' );
		?>
		<button type="button"
		        data-media-changetext="<?php _e( 'Change' ); ?>"
		        class="btn btn-primary bottom-buffer mediaUploader mediaUploader<?php echo ucfirst( $mediatype ) ?>"><?php echo $buttonText; ?></button>
		<input type="hidden" class="hiddenID" value="<?php echo $value; ?>" name="<?php echo $this->getFieldName($field["name"] ); ?>">
		<?php
		$hiddenStyle = ( $presentedIsValidImage ) ? "" : " display:none; ";
		echo "<div class='center-block cont-block' style='margin-top: 15px; " . $hiddenStyle . "'><img  class='img-responsive' src='" . $attachmentLink . "'></div>";

	}

	private function outputFieldDateTimePicker( $field, $value) {

		$value         = ( $value ) ? $value : $field["default"];
		?>
				<div class="form-group">
					<div class='input-group date datetime-picker' id="<?php echo $this->getFieldID($field["name"] );?>" data-def-date="<?php echo $value;?>">
						<input type='text' value="<?php echo $value;?>" class="form-control" name="<?php echo $this->getFieldName($field["name"] );?>"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar">
                    </span>
                </span>
					</div>
				</div>

		<?php
	}


	private function outputField($field, $value)
    {
        switch ($field["type"]) {
            case "text":
                $this->outputFieldText($field, $value);
                break;
            case "textarea":
                $this->outputFieldTextarea($field, $value);
                break;
            case "switch":
                $this->outputFieldSwitch($field, $value);
                break;
            case "spin":
                $this->outputFieldSpin($field, $value);
                break;
            case "select":
                $this->outputFieldSelect($field, $value);
                break;
            case "checkboxes":
                $this->outputFieldCheckboxes($field, $value);
                break;
            case "tinymce":
                $this->outputTinymce($field, $value);
                break;
            case "media":
                $this->outputFieldMedia($field, $value);
                break;
            case "datatime-picker":
                $this->outputFieldDateTimePicker($field, $value);
                break;
            default:
                $this->outputFieldText($field, $value);
        }
    }

    private function outputOptionsList($fields, $values)
    {
        foreach ($fields as $field) {
            $slug = $field["name"];
            $style = "";
            if($slug == "city"){
                $style = ($field["isNational"])? " display: none; " : $style;
            }
            ?>
            <div class="row bottom-margin row-of-field-<?php echo $slug; ?>" style="<?php echo $style; ?>">
                <div class="col-sm-12">
                        <label class="postmeta-field-label"
                            for="<?php echo $this->getFieldID($field["name"]) ?>"><b><?php echo $field["title"]; ?></b></label>
                </div>
                <div class="col-sm-12">
                        <?php $this->outputField($field, $values[$slug]); ?>
                        <?php if (isset($field["help"])) {
                            echo "<p class='howto'>" . $field["help"] . "</p>";
                        } ?>
                </div>
            </div>
        <?php
        }
    }


    public function outputPostMetabox($post, $fields, $values)
    {

        ?>
        <div id="wrb-meta-wrap">
            <div role="tabpanel">
                    <div class="bottom-buffer">
                            <?php
                            $this->outputOptionsList($fields, $values)?>
                    </div>
            </div>
        </div>
    <?php
    }



}