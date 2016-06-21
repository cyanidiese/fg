jQuery(document).ready(function () {
    jQuery("input.bootstrapSpin").TouchSpin({
        prefix: jQuery(this).data("prefix"),
        postfix: jQuery(this).data("postfix"),
        min: jQuery(this).data("min"),
        max: jQuery(this).data("max"),
        step: jQuery(this).data("step")
    });

    jQuery.map(jQuery('.datetime-picker'), function (val){
        var inp = jQuery(val);
        var defDate = inp.data("def-date");
        inp.datetimepicker({
            defaultDate: defDate
        });
    });

    jQuery("input.bootstrapSwitch")
        .bootstrapSwitch()
        .on('switchChange.bootstrapSwitch', function (event, state) {
            var switchValue = (state) ? "yes" : "no";
            jQuery(".row-of-field-"+jQuery(this).data("toggle-row")).slideToggle("fast");
            jQuery(this).parent().parent().parent().children(".trueValue").val(switchValue);
        });


    jQuery.map(jQuery('input.bootstrapSelectize'), function (val){
        var inp = jQuery(val);
        var $select = inp.selectize({
            delimiter: ',',
            persist: false,
            create: function(input) {
                return {
                    value: input,
                    text: input
                }
            }
        });
        var value = inp.data("value");
        var selectize = $select[0].selectize;
        selectize.setValue(value);
    });

    var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;

    jQuery('button.mediaUploader').click(function (e) {
        e.preventDefault();
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = jQuery(this);
        _custom_media = true;
        wp.media.editor.send.attachment = function (props, attachment) {
            if (_custom_media) {
                var cont_box = button.parent().children(".cont-block").eq(0);
                var id_box = button.parent().children("input.hiddenID").eq(0);
                id_box.val(attachment.id);
                cont_box.show();
                button.html(button.data("media-changetext"));

                    cont_box.children("img").eq(0).attr("src", attachment.url);
            } else {
                return _orig_send_attachment.apply(this, [props, attachment]);
            }
        };

        wp.media.editor.open(button);
        return false;
    });
});