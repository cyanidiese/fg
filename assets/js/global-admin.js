jQuery(document).ready(function(){
    jQuery("#selecting-gf-shortcode-type").live("change",function(){
        jQuery(".fg-shortcode-options").slideToggle("fast");
    });

    jQuery(".is_list_view_select").live("change", function(){
        jQuery(".not_list_view_fields").slideToggle("fast");
    });
});