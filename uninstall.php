<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

 
delete_option('language_select');
delete_option('selectedVoice');
delete_option('voice_theme');
delete_option('language_codes');
delete_option('voice_source');
delete_option('name_voice_source');
delete_option('enable_footer');
delete_option('meta_box_position');
delete_option('enable_all_posts');
delete_option('enable_all_posts_published');
delete_option('account_key');
delete_option('key_valid');

delete_option('selectorTitle');
delete_option('selectorBody');
delete_option('selectorBrief');
delete_option('selectorColor');
delete_option('skip_html_tags');


 