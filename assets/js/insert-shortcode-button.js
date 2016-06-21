function focusGroupsInsertShortCode(editor){    var stringtext = "[focus_group id=" + jQuery("#focus-groups-field").val() + "]";    editor.execCommand('mceInsertContent', false, stringtext);    editor.windowManager.close(this);    return false;}function focusGroupsInsertListShortCode(editor){    var viewStr = (jQuery("#selecting-gf-shortcode-view").is(":checked"))? " view='list' " : "" ;    var stringtext = "[focus_groups city='" + jQuery("#focus-groups-cities").val() + "' "+viewStr+"]";    editor.execCommand('mceInsertContent', false, stringtext);    editor.windowManager.close(this);    return false;}function focusGroupsInsertShortCodeSelect(editor){    if(jQuery(".group-focus-groups").is(":visible")){        focusGroupsInsertListShortCode(editor);    }    else{        focusGroupsInsertShortCode(editor);    }}(function() {    // Register plugin    tinymce.create( 'tinymce.plugins.focusgroupshortcode', {        init: function( editor, url )  {            // Add the Insert Gistpen button            editor.addButton( 'focusgroupshortcode', {                //text: 'Insert Shortcode',                icon: 'icons dashicons-groups-fg',                tooltip: 'Insert Shortcode',                cmd: 'plugin_command'            });            editor.addCommand( 'plugin_command', function() {                // Calls the pop-up modal                editor.windowManager.open({                    title: 'Insert Shortcode',                    width: 300,                    height: 100,                    inline: 1,                    id: 'plugin-slug-insert-dialog',                    buttons: [{                        text: 'Insert Shortcode',                        id: 'plugin-slug-button-insert',                        class: 'insert primary button-primary',                        onclick: function( e ) {                            focusGroupsInsertShortCodeSelect(editor);                        }                    },                        {                            text: 'Cancel',                            id: 'plugin-slug-button-cancel',                            onclick: 'close'                        }]                });                appendInsertDialog();            });        }    });    tinymce.PluginManager.add( 'focusgroupshortcode', tinymce.plugins.focusgroupshortcode );    function appendInsertDialog () {        var dialogBody = jQuery( '#plugin-slug-insert-dialog-body' ).append( "<span class='spinner big-gf-load-spinner'></span>" );        // Get the form template from WordPress        jQuery.post( ajaxurl, {            //action: 'focusgroupshortcode_insert_dialog'            action: 'fg_insert_dialog'        }, function( response ) {            template = response;            dialogBody.children( '.loading' ).remove();            dialogBody.append( template );            jQuery( '.spinner' ).hide();        });    }})();