<?php
/*
Plugin Name: Focus Groups
Description: Focus Groups plugin
Author: CYAN-ID
Version: 1.5.1
*/

define( "FG_LANG", "focusgroups" );
define( "FG_PATH", plugin_dir_path(__FILE__) );
define( "FG_MAIN_FILE_PATH", __FILE__ );
define( "FG_URL", plugin_dir_url(__FILE__) );

require "engine/FGPlugin.php";
$focusgroups = new FGPlugin();
