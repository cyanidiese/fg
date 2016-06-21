jQuery(document).ready(function(){
    jQuery(".fg-embed-link").live("click",function(){
        jQuery(this).parent().children("textarea").eq(0).slideToggle("normal");
    });
    jQuery(".fg-embed-holder-text").focus(function() {
        var $this = $(this);
        $this.select();

        $this.mouseup(function() {
            $this.unbind("mouseup");
            return false;
        });
    });
});