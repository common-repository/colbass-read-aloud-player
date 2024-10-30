<?php
/**
 * Plugin Name: Colbass - Read Aloud Player
 * Plugin URI: https://wordpress.org/plugins/colbass-read-aloud-player
 * Description: This plugin will add a read-aloud (Text-To-Speech) player to your website
 * Requires at least: 1.3.10
 * Requires PHP: 7.2
 * Version: 1.3.11
 * Author: Colbass
 * Author URI: http://colbass.com/
 * License: GPL2
 */

// Register activation hook
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

register_activation_hook(__FILE__, 'colbass_activation_function');
 //define('COLBASS_SERVER', 'http : // local host: 57640/');
 define('COLBASS_SERVER', 'https://colbass.com/');
 define('NAME_VOICE_GENERIC', "colbass");// server side
 
global $cb_isDivBlockLoaded;
$cb_isDivBlockLoaded=false;
function colbass_activation_function() {
    
    // Redirect user to the plugin settings page after activation

}
function my_audio_player_scripts() {
    wp_enqueue_style('my-audio-player-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}
function getSourceVoices_G() {
   
    $url = COLBASS_SERVER . 'Service/VoicesWebService.asmx/GetSourceVoices_G';
    
    $response = wp_remote_post($url, array(
        'method'    => 'POST',
        'headers'   => array(
            'Content-Type' => 'application/json'
        ),
        'body'      => wp_json_encode(new stdClass()), // Include body data if needed
        'timeout'   => 15,
        'sslverify' => true,
    ));

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        return "WP_Error: " . $error_message;
    } else {
        return wp_remote_retrieve_body($response);
    }
}
add_action('admin_enqueue_scripts', 'my_audio_player_scripts');
function my_audio_player_menu() {
    add_menu_page('Audio Player Settings',
                    'Colbass',
                    'manage_options',
                    'colbass-settings',
                    'my_audio_player_settings_page',
                    'dashicons-playlist-audio');
}
add_action('admin_menu', 'my_audio_player_menu');


function add_div_above_content($content) {
    if (is_single() && in_the_loop() && is_main_query()) {
        $enable_all_posts = get_option('enable_all_posts');
        $enable_all_posts_published = get_option('enable_all_posts_published');
        
        if (($enable_all_posts == 'on' && $enable_all_posts_published == 'off') || ($enable_all_posts == 'off' && $enable_all_posts_published == 'on')) {
          //  $post_id = get_the_ID();
            $selectorTitle = get_option('selectorTitle', '');
            $selectorBody = get_option('selectorBody', '');
            $selectorBrief = get_option('selectorBrief', '');
            $selectorExclude = get_option('skip_html_tags', '');
            $selectorcolor = get_option('selectorColor', '');


            $custom_div = '<div id="colbass-tts-wrap"  support="https://colbass.com" selectorcolor="' . esc_attr($selectorcolor) . '" selectorBody="' . esc_attr($selectorBody) . '" selectorBrief="' . esc_attr($selectorBrief) . '" selectorTitle="' . esc_attr($selectorTitle) . '" selectorExclude="' . esc_attr($selectorExclude) . '" selectorCategory="" optionRun="1" d="">';
            $custom_div .= '</div>';

            global $cb_isDivBlockLoaded;
            $page_id =   get_the_ID();
            $page_post = get_post($page_id);
            $page_content = $page_post->post_content;
           
            if ($cb_isDivBlockLoaded == false && !has_shortcode( $page_content,  'colbass_tts_wrap')) {
                $content = $custom_div . $content;
                $cb_isDivBlockLoaded=true;
           }    
       

        }
    }
    return $content;
}
add_filter('the_content', 'add_div_above_content');

function enqueue_custom_script() {
    ?>
<script>
// document.addEventListener('DOMContentLoaded', function() {
//     window.playText = function(postId) {
//         fetch('< ?php echo admin_url('admin-ajax.php'); ?>', {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/x-www-form-urlencoded',
//                 },
//                 body: new URLSearchParams({
//                     action: 'get_post_data',     // This triggers wp_ajax_get_post_data
//                     post_id: postId
//                 })
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     const postTitle = data.data.title;
//                     const postContent = data.data.content;
//                     const fullText = postTitle + " " + postContent;

//                     if ('speechSynthesis' in window) {
//                         const utterance = new SpeechSynthesisUtterance(fullText);
//                         const voices = window.speechSynthesis.getVoices();
//                         let desiredVoice = voices.find(voice => voice.lang === 'ar-XA' && voice.name
//                             .includes('Wavenet-A'));

//                         if (!desiredVoice) {
//                             desiredVoice = voices.find(voice => voice.lang === 'ar-XA');
//                         }
//                         if (desiredVoice) {
//                             utterance.voice = desiredVoice;
//                         }
//                         speechSynthesis.speak(utterance);
//                     } else {
//                         alert('Your browser does not support text-to-speech functionality.');
//                     }
//                 } else {
//                     console.error('Error fetching post data:', data.data);
//                     alert('Post title or content not found.');
//                 }
//             })
//             .catch(error => {
//                 console.error('Error:', error);
//                 alert('An error occurred while fetching the post data.');
//             });
//     };
//});
</script>
<?php
  }
// add_action('wp_enqueue_scripts', 'enqueue_custom_script');

// Handle AJAX request to get post data
// function get_post_data() {
//     $post_id = intval($_POST['post_id']);
//     $post = get_post($post_id);
    
//     if ($post) {
//         wp_send_json_success([
//             'title' => $post->post_title,
//             'content' => $post->post_content
//         ]);
//     } else {
//         wp_send_json_error('Post not found.');
//     }
// }
// add_action('wp_ajax_get_post_data', 'get_post_data'); // fromm client call to server side
// add_action('wp_ajax_nopriv_get_post_data', 'get_post_data');


function my_audio_player_settings_page() {
    ?>
<div class="wrap cb_wrap">
    <!-- <h2>General Configuration</h2> -->
    <?php if (isset($_GET['message']) && isset($_GET['status'])  &&  isset($_GET['_wpnonce'])  &&  wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'my_nonce_action')   ): 
    // Sanitize the inputs
    $status = sanitize_key($_GET['status']); // Since 'status' could be a specific key like 'success', 'error', etc.
    $message = sanitize_text_field($_GET['message']); // Sanitize the message input
?>
    <div class="notice notice-<?php echo esc_attr($status); ?>">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php endif; ?>

    
    <?php
    //things to do before settings pages loaded
        $isDefined_enable_all_posts_published  = get_option('enable_all_posts_published');
        $isDefined_enable_all_posts  = get_option('enable_all_posts');

        if ($isDefined_enable_all_posts_published === false && $isDefined_enable_all_posts=== false) {
            update_option('enable_all_posts', 'off');
            update_option('enable_all_posts_published', 'on');
        }
        $isDefined_allow_logo = get_option('enable_footer');
        if ($isDefined_allow_logo === false  ) {
             update_option('enable_footer', 'on');
        }
    ?>



    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('my_plugin_settings_nonce'); ?>
        <input type="hidden" name="action" value="save_my_plugin_settings">


        <div id="selecttvoiceid" class="cb_container">
        <div class="cb_heading">Select Voice</div>
      
        <div class="cb_configuration">
             
            <div class="cb_selects">
                <select id="language-select" name="language_select">
                    <option selected disabled>Select Language</option>
                </select>
                <!-- <select name="voice_theme">
                    <option selected disabled>Select Voice</option>
                    <option value="character" <?php selected(get_option('voice_theme'), 'character'); ?>>Character
                    </option>
                </select> -->
            </div>
            <div class="cb_options-container" id="voice-options"></div>
            <input type="hidden" name="language_code" id="language_code"
                value="<?php echo esc_attr(get_option('language_code')); ?>">
            <input type="hidden" name="name_voice_source" id="name_voice_source"
                value="<?php echo esc_attr(get_option('name_voice_source')); ?>">
        </div>
        </div>
        <div id="postManagement" class="cb_container">
            <div class="cb_heading">Post Management</div>
            <div class="cb_content-box">
                <label for="manage_posts" class="cb_label">In which articles should the player be displayed?</label>
                <input type="radio" id="enable_all_posts_published" name="manage_posts"  
                    value="enable_all_posts_published"
                    <?php checked(get_option('enable_all_posts_published'), 'on'); ?>> Enable on all new posts
                  <p> </p>  
                <input type="radio" id="enable_all_posts" name="manage_posts" value="enable_all_posts"
                    <?php checked(get_option('enable_all_posts'), 'on'); ?>> Enable on all posts including archive
          
            
                <!-- <p>Enable Colbass Player on all posts for selected date range</p> -->
            </div>
        </div>

        <div id="textualConfiguration" class="cb_container">
            <div class="cb_heading">Textual Configuration</div>
            <div class="cb_content-box">
           <i>  <a target="_blank" href="https:colbass.com/" style="color: #5f6162;">Need help filling out this form? Please contact us</a></i>
            <label for="selectorTitle" class="cb_label" style="margin-top: 5px;">Title selector:</label>
                <input type="text" id="selectorTitle" placeholder="E.g: #title" name="selectorTitle" class="cb_input-field"
                    value="<?php echo esc_attr(get_option('selectorTitle')); ?>">

                    <label for="selectorBrief" class="cb_label">Brief selector:</label>
                <input type="text" id="selectorBrief" placeholder="E.g: h3.brief_class" name="selectorBrief" class="cb_input-field"
                    value="<?php echo esc_attr(get_option('selectorBrief')); ?>">

                    <label for="selectorBody" class="cb_label">Body selector:</label>
                <input type="text" id="selectorBody" placeholder="E.g: article > p,div.content,h4" name="selectorBody" class="cb_input-field"
                    value="<?php echo esc_attr(get_option('selectorBody')); ?>">


                <label for="skip_html_tags" class="cb_label">Skip selector:</label>
                <input type="text" id="skip_html_tags" placeholder="E.g: div.ads,div.comments"  name="skip_html_tags" class="cb_input-field"
                    value="<?php echo esc_attr(get_option('skip_html_tags')); ?>">
   
       
 
            </div>
        </div>

        <div class="cb_container">
    <div class="cb_heading">Configuration</div>
    <div class="cb_content-box">
        <label for="enable_footer" class="cb_label">Help us reach new users</label>
        <input type="checkbox" id="enable_footer" name="enable_footer"
            <?php checked(get_option('enable_footer'), 'on'); ?>> Allow our logo on read-aloud player
 
            <label for="selectorColor" class="cb_label" style="margin-top:8px">Select color</label>
            <input type="color" id="selectorColor" name="selectorColor" class="color-picker"
                value="<?php echo esc_attr(get_option('selectorColor')); ?>">
    </div>
</div>

<div class="cb_login-form">
    <h1>Enter Account Key</h1>
    <input type="text" name="account_key" placeholder="Account key"
        value="<?php echo esc_attr(get_option('account_key')); ?>">
        <div>
        <a target="_blank" href="https://pay.sumit.co.il/2l6dsl/79rrep/">Click here to get a key</a>
        <label style="display: block;"><b>First month free!</b> After that, payments will start automatically unless you cancel by <a href="mailto:info@colbass.com">emailing us.</a></label> Cancel anytime, no commitment required.    </div>          
</div>



    <div id="saveSettingsWrapBtn">
        <button type="submit">SAVE</button>
    </div>
    <input type="hidden" name="message" id="message">
    
    <div class="cb_container">
    <div class="cb_heading">Position</div>
    <div class="cb_content-box">
        <label>To change the default position of the player, please use the shortcode: `[colbass_tts_wrap]`.</label>
        
    </div>
</div>
</form>
</div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
          
            const enable_all_posts_publishedRadio = document.getElementById('enable_all_posts_published');
      
            const otherRadioButtons = document.querySelectorAll(
                'input[name="manage_posts"]:not(#enable_all_posts_published)');
            const textualConfigurationDiv = document.getElementById('textualConfiguration');

            const toggleTextualConfiguration = () => {
                if (enable_all_posts_publishedRadio.checked ) {
                    textualConfigurationDiv.style.display = 'none';
                } else {
                    textualConfigurationDiv.style.display = 'block';
                    
                }               
                var windowHeight = document.querySelector('.cb_wrap').offsetHeight;
                document.querySelector('#wpfooter').style.top = (windowHeight + 80) + 'px';
            };

            enable_all_posts_publishedRadio.addEventListener('change', toggleTextualConfiguration);
            otherRadioButtons.forEach(radio => {
                radio.addEventListener('change', toggleTextualConfiguration);
            });
            toggleTextualConfiguration();
        });
        </script>

 <script>
       var audio_colbass = new Audio();
        function playSound(audioUrl) {
       
        if (audio_colbass.paused) {
            audio_colbass.setAttribute("src",audioUrl)
            audio_colbass.play();
        } else {
            audio_colbass.pause();
        }
   
    }
document.addEventListener('DOMContentLoaded', function() {

    const savedLanguage = "<?php echo esc_js(get_option('language_select')); ?>";
    const savedVoice = "<?php echo esc_js(get_option('selectedVoice')); ?>";
    const savedLanguageCode = "<?php echo esc_js(get_option('language_code')); ?>";//like en-US
    const savedNameVoiceSource = "<?php echo esc_js(get_option('name_voice_source')); ?>"; //like en-wavenet-D

    fetch('<?php echo esc_url(home_url('/')); ?>wp-admin/admin-ajax.php?action=get_source_voices_g')
    .then(response => response.json())
    .then(data => {
        // Continue processing if data is valid JSON
            const languages = Object.keys(data.d);
            const languageSelect = document.getElementById('language-select');
            languages.forEach(language => {
                const option = document.createElement('option');
                option.textContent = language;
                option.value = language;
                if (language === savedLanguage) {
                    option.selected = true;
                }
                languageSelect.appendChild(option);
            });

            function populateVoiceOptions(language) {
                const voiceOptions = data.d[language].map(voice => {
                    return `
                        <label class="cb_option">
                            <input type="radio" name="selectedVoice" value="${voice.SourceVoiceExample.PathSampleAudio}" data-language-code="${voice.SourceVoiceExample.LanguageCodes}" data-name-voice-source="${voice.SourceVoiceExample.NameVoiceSource}" ${voice.SourceVoiceExample.PathSampleAudio === savedVoice ? 'checked' : ''}>
                            <span class="sound-icon" onclick="playSound('https://colbass.com/${voice.SourceVoiceExample.PathSampleAudio}')">&#128362;</span>
                            ${voice.SourceVoiceExample.NameSpeaker}
                        </label>
                    `;
                }).join('');
                const voiceOptionsContainer = document.getElementById('voice-options');
                voiceOptionsContainer.innerHTML = voiceOptions;
                if (savedVoice) {
                    document.getElementById('language_code').value = savedLanguageCode;
                    document.getElementById('name_voice_source').value = savedNameVoiceSource;
                }
            }

            // If saved language is available, populate the voice options
            if (savedLanguage) {
                populateVoiceOptions(savedLanguage);
            }

            languageSelect.addEventListener('change', function() {
                const selectedLanguage = this.value;
                populateVoiceOptions(selectedLanguage);
            });
            var cb_cbWrapHeight = document.querySelector('.cb_wrap').offsetHeight;
             document.querySelector('#wpfooter').style.top = (cb_cbWrapHeight + 80) + 'px';

        }) .catch(   error =>  console.error('Error fetching data:', error)  );

    document.getElementById('voice-options').addEventListener('change', function(event) {
        if (event.target.name === 'selectedVoice') {
            const selectedOption = event.target;
            document.getElementById('language_code').value = selectedOption.getAttribute(
                'data-language-code');
            document.getElementById('name_voice_source').value = selectedOption.getAttribute(
                'data-name-voice-source');
              //   document.getElementById('voice-form').submit();//no need>
        }
    });


});
</script>



<?php
}

function cb_getNirmulBusinessName()
{
    $BusinessName = esc_url(home_url('/')); //data-businessname="http://mytest.local/"
    $BusinessName = preg_replace('#^https?://#', '', $BusinessName);
    $BusinessName = rtrim($BusinessName, '/');
    return $BusinessName;
}
add_action('admin_post_save_my_plugin_settings', 'save_my_plugin_settings');

function save_my_plugin_settings() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized user');
    }

    check_admin_referer('my_plugin_settings_nonce');
    if (isset($_POST['language_select'])) {
        update_option('language_select', sanitize_text_field($_POST['language_select']));
    }
    if (isset($_POST['selectedVoice'])) {
        update_option('selectedVoice', sanitize_text_field($_POST['selectedVoice']));
    }
    if (isset($_POST['voice_theme'])) {
        update_option('voice_theme', sanitize_text_field($_POST['voice_theme']));
    }
    if (isset($_POST['language_code'])) {
        update_option('language_code', sanitize_text_field($_POST['language_code']));
    }
    if (isset($_POST['name_voice_source'])) {
        update_option('name_voice_source', sanitize_text_field($_POST['name_voice_source']));
    }
    if (isset($_POST['selectorColor'])) {
        update_option('selectorColor', sanitize_hex_color($_POST['selectorColor']));
    }
    update_option('enable_footer', isset($_POST['enable_footer']) ? 'on' : 'off');
  //  update_option('meta_box_position', sanitize_text_field($_POST['meta_box_position']));
    update_option('skip_html_tags', sanitize_text_field($_POST['skip_html_tags']));
    update_option('selectorTitle', sanitize_text_field($_POST['selectorTitle']));
    update_option('selectorBrief', sanitize_text_field($_POST['selectorBrief']));
    update_option('selectorBody', sanitize_text_field($_POST['selectorBody']));
    if (isset($_POST['manage_posts'])) {
        if ($_POST['manage_posts'] === 'enable_all_posts') {//enable_all_posts = also on archive posts
            update_option('enable_all_posts', 'on');
            update_option('enable_all_posts_published', 'off');
        } elseif ($_POST['manage_posts'] === 'enable_all_posts_published') {
            update_option('enable_all_posts', 'off');
            update_option('enable_all_posts_published', 'on');
        }
    }

    //validate all settings were set
    $isDefined_name_voice_source  = get_option('name_voice_source');//like en-wavenet-D
    if ($isDefined_name_voice_source === false || $isDefined_name_voice_source===""  ) {
        $status = 'error';
        $message = 'Please choose a voice.';
        wp_redirect(add_query_arg(array('message' => urlencode($message), 'status' => $status,'_wpnonce' => wp_create_nonce('my_nonce_action')), admin_url('admin.php?page=colbass-settings')));
        exit;
    }


    // Validate account key
    $radioenable_all_posts = get_option('enable_all_posts');
    $IsToShowPlayerAnyway=0;
    if($radioenable_all_posts=='on')
    {
        $IsToShowPlayerAnyway=1;
    }
  
    $BusinessName = cb_getNirmulBusinessName();
    $is_allow_logo = get_option('enable_footer')=='on' ? true : false;
    
    $account_key = sanitize_text_field($_POST['account_key']);
   
    $apiUrl =  COLBASS_SERVER.'api/IsNewValidKey';
    $data = array(
        'Key' => $account_key,
        'BusinessName' =>  $BusinessName,
        'IsToShowPlayerAnyway' => $IsToShowPlayerAnyway,
        'HideLogo' => !$is_allow_logo,
    );

    $response = wp_remote_post($apiUrl, array(
        'method'    => 'POST',
        'headers'   => array(
            'Content-Type' => 'application/json'
        ),
        'body'      => wp_json_encode($data),
        'timeout'   => 15,
        'sslverify' => true,
    ));


    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        update_option('key_valid', false);
        $status = 'error';
        $message = "WP_Error: " . $error_message;
    } else {
        $responseData = json_decode(wp_remote_retrieve_body($response), true);
        if ($responseData && isset($responseData['IsSucceed'])) {
            if ($responseData['IsSucceed']) {
                update_option('account_key', $account_key);
                update_option('key_valid', true);
                $status = 'success';
                $message = 'Key is valid. Settings saved';
            } else {
                update_option('key_valid', false);
                $status = 'error';
                $message = 'Key is not valid.';
            }
        } else {
            update_option('key_valid', false);
            $status = 'error';
            $message = 'No response from API.';
        }
    }
 
    wp_redirect(add_query_arg(array('message' => urlencode($message), 'status' => $status,'_wpnonce' => wp_create_nonce('my_nonce_action')), admin_url('admin.php?page=colbass-settings')));
    exit;
}

// Add AJAX action (from client to server)
add_action('wp_ajax_get_source_voices_g', 'get_source_voices_g');
add_action('wp_ajax_nopriv_get_source_voices_g', 'get_source_voices_g');


function shortcode_exists_in_html($html, $shortcode) {
    $pattern = get_shortcode_regex(array($shortcode));
    preg_match_all('/' . $pattern . '/', $html, $matches);
    return !empty($matches[0]);
}

function get_source_voices_g() {
    $result = getSourceVoices_G(); 
    echo $result;  
    wp_die();
}
function process_colbass($post_ID, $post_after, $post_before) {
    $key_valid = get_option('key_valid');
    if($key_valid==false)
     return;
    
    $BusinessName =  cb_getNirmulBusinessName();;
    $data_languageCode = get_option('language_code');
    $data_nameVoice = get_option('name_voice_source');
    $data_nameVoice = str_replace("Wavenet", NAME_VOICE_GENERIC, $data_nameVoice);
    $key = get_option('account_key');
 

    if(wp_strip_all_tags($post_after->post_excerpt) ==''&&wp_strip_all_tags($post_after->post_content)=='') { return; }
    $cat = get_the_category($post_ID);
    if($cat && isset($cat[0])) {
      $cat = $cat[0]->cat_name;
    } else {
      $cat = 'news';
    }
    $text_to_read = "";
    $brief =strip_shortcodes(wp_strip_all_tags($post_after->post_excerpt));
    if( $brief=="")
    {
        $text_to_read=strip_shortcodes(wp_strip_all_tags($post_after->post_title)) . '. ' . strip_shortcodes(wp_strip_all_tags($post_after->post_content));
    }
    else{
        $text_to_read = strip_shortcodes(wp_strip_all_tags($post_after->post_title)) . '. ' . strip_shortcodes(wp_strip_all_tags($post_after->post_excerpt)) . '. ' . strip_shortcodes(wp_strip_all_tags($post_after->post_content));
    }
    $apiUrl = COLBASS_SERVER.'/api/CreateVoiceArticleNonBlock';
    $data = array(
        'BusinessName' =>  $BusinessName,
        'SubscriptionID' => $key,
        'urlArticle' =>  get_permalink($post_ID),		 
        'xmlString' => $text_to_read,
        'languageCode' => $data_languageCode,
        'nameVoice' => $data_nameVoice,
        'speed' => 1,
        'pitch' => 1,
        'category' =>  $cat					 
    );
 
    $response = wp_remote_post($apiUrl, array(
        'method'    => 'POST',
        'headers'   => array(
            'Content-Type' => 'application/json',
        ),
        'body'      => wp_json_encode($data),
        'timeout'   => 15,
        'sslverify' => true,
    ));

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        // Handle the error appropriately, e.g., logging or displaying an error message
        error_log("WP_Error: " . $error_message);
        return false;
    } else {
        // Optionally process the response if needed
        return true;
    }
}
 
add_action('post_updated', 'process_colbass', 10, 3);

function add_colbass_api_script() {
    $key_valid = get_option('key_valid');
    
    if ($key_valid) {
        $language_code = esc_attr(get_option('language_code'));
        $name_voice_source = esc_attr(get_option('name_voice_source'));
        $name_voice_source = str_replace("Wavenet", NAME_VOICE_GENERIC, $name_voice_source);
        $BusinessName = cb_getNirmulBusinessName();

        // Register and enqueue the script
        wp_register_script('colbass_api_script', COLBASS_SERVER . 'assets/api/v3/tts.js', [], null, true);
        wp_enqueue_script('colbass_api_script');

        // Add data attributes using the script_loader_tag filter
        add_filter('script_loader_tag', function($tag, $handle) use ($BusinessName, $language_code, $name_voice_source) {
            if ('colbass_api_script' === $handle) {
                // Modify the script tag to add data attributes
                $tag = str_replace(
                    '<script ',
                    '<script id="apiColbassJS" type="text/javascript" data-BusinessName="' . esc_attr($BusinessName) . '" data-SubscriptionID="I-9V31PQ83V83A" data-languageCode="' . esc_attr($language_code) . '" data-nameVoice="' . esc_attr($name_voice_source) . '" data-speed="1" data-pitch="1" ',
                    $tag
                );
            }
            return $tag;
        }, 10, 2);
    } else {
        echo '<!-- API key is not valid. Script not loaded. -->';
    }
}
add_action('wp_enqueue_scripts', 'add_colbass_api_script');
 
 

function colbass_tts_wrap_shortcode() {
    global $cb_isDivBlockLoaded;
    if ($cb_isDivBlockLoaded  ) {
       return;
   }    
   else{
       $cb_isDivBlockLoaded=true;
   }

    $selectorTitle = get_option('selectorTitle', '');
    $selectorBody = get_option('selectorBody', '');
    $selectorBrief = get_option('selectorBrief', '');
    $selectorExclude = get_option('skip_html_tags', '');
    $selectorcolor = get_option('selectorColor', '');

    ?>
    
  
    <?php
    
    $custom_div = '<div id="colbass-tts-wrap"  support="https://colbass.com"  selectorcolor="' . esc_attr($selectorcolor) . '" selectorBody="' . esc_attr($selectorBody) . '" selectorBrief="' . esc_attr($selectorBrief) . '" selectorTitle="' . esc_attr($selectorTitle) . '" selectorExclude="' . esc_attr($selectorExclude) . '" selectorCategory="" optionRun="1" b="">';
 
    $custom_div .= '</div>';


        return $custom_div;
 
                    ?>
</div>
<?php
  // return ob_get_clean();
}
add_shortcode('colbass_tts_wrap', 'colbass_tts_wrap_shortcode');