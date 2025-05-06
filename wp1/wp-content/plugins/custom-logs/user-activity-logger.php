<?php
/**
 * Plugin Name: User Activity Logger
 * Description: Record user activities on the front-page
 * Version: 1.0
 * Author: Jigang Guo
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once 'init.php';
require_once __DIR__ .'/UidProcessor.php';

// Create log recorder
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

// Set up logger
$log = new Logger('user_activity');

// Define the log format
$logFormat = "msg=%message%,timestamp=%datetime%,level_name=%level_name%,user_ip=%extra.user_ip%,request_id=%extra.request_id%,";
$logFormat .= "duration=%context.duration%\n"; 

// Use a StreamHandler with custom formatting
$handler = new StreamHandler(__DIR__ . '/user-activity.log', Logger::INFO);
$handler->setFormatter(new LineFormatter($logFormat, null, true, true));

$log->pushHandler($handler);
$log->pushProcessor(new UidProcessor());

function log_user_activity_on_click() {
    global $log;

    if ( ! isset( $_GET['action'] ) ) {
        return;
    }

    $action = sanitize_text_field( $_GET['action'] );
    $user_ip = $_SERVER['REMOTE_ADDR'];
    // Deal with different user behaviours
    switch ( $action ) {
        case 'page_scrolled_to_bottom':
            log_scroll_event( 'User scroll down to the bottom' );
            break;
        
        case 'page_scrolled_to_50_percent':
            log_scroll_event( 'User scroll down to the 50% position' );
            break;
        
        case 'page_stay_duration':
            log_page_stay_duration( );
            break;
        
        case 'accordion_toggle':
            log_accordion_toggle( );
            break;
        
        default:
            log_click_event( );
            break;
    }
}

// Record user scroll down
function log_scroll_event( $message ) {
    global $log;
    $duration = isset( $_GET['duration'] ) ? intval( $_GET['duration'] ) : 0;
    $context = [];
    if ($duration !== null) {
        $context['duration'] = $duration;
    }
    $log->info( $message, $context );
}

// Record page stay time
function log_page_stay_duration( ) {
    global $log;
    $duration = isset( $_GET['duration'] ) ? intval( $_GET['duration'] ) : 0;
    $context = [];
    if ($duration !== null) {
        $context['duration'] = $duration;
    }
    $log->info( 'User stayed on the page', $context );
}

// Record accordion toggle behaviour
function log_accordion_toggle( ) {
    global $log;
    $accordion_id = isset( $_GET['accordion_id'] ) ? sanitize_text_field( $_GET['accordion_id'] ) : 'unknown';
    $title = isset( $_GET['title'] ) ? sanitize_text_field( $_GET['title'] ) : 'unknown';
    $state = isset( $_GET['state'] ) ? sanitize_text_field( $_GET['state'] ) : 'unknown';
    
    $log->info( 'User accordion behaviour ', [
        'accordion_id' => $accordion_id,
        'title' => $title,
        'state' => $state,
    ] );
}

// Record Click behaviour
function log_click_event( ) {
    global $log;
    $duration = isset( $_GET['duration'] ) ? intval( $_GET['duration'] ) : 0;
    $context = [];
    if ($duration !== null) {
        $context['duration'] = $duration;
    }
    $log->info( 'User triggered click event', $context );
}

add_action('init', 'log_user_activity_on_click');
 
// Trigger user behaviour logs
function add_user_activity_scripts() {
    if ( is_front_page() ) {
        ?>
        <script type="text/javascript">

            document.addEventListener('DOMContentLoaded', function () {
                let startTime = Date.now(); 
                window.addEventListener('beforeunload', function () {
                    let endTime = Date.now();
                    let duration = Math.round((endTime - startTime) / 1000); // Calculate duration time
                    navigator.sendBeacon('<?php echo esc_url( home_url() ); ?>/?action=page_stay_duration&duration=' + duration);
                });

                const button = document.querySelector('.kb-button');
                if (button) {
                    button.addEventListener('click', function () {
                        fetch('<?php echo esc_url( home_url() ); ?>/?action=clicked_link', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        }).then(response => {
                        }).catch(error => console.log('Request failed', error));
                    });
                }

                const submit = document.querySelector('.kb-forms-submit');
                if (submit) {
                    submit.addEventListener('click', function () {
                        fetch('<?php echo esc_url( home_url() ); ?>/?action=submitted_form', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        }).then(response => {
                        }).catch(error => console.log('Request failed', error));
                    });
                }

                const accordionButtons = document.querySelectorAll(".kt-blocks-accordion-header");

                accordionButtons.forEach(button => {
                    button.addEventListener("click", function () {
                        const accordionId = this.getAttribute("id");
                        const isExpanded = this.getAttribute("aria-expanded") === "true";
                        const title = this.querySelector(".kt-blocks-accordion-title")?.innerText || "Unknown Title";
                        const state = isExpanded ? "Opened" : "Closed";
                        fetch('<?php echo esc_url( home_url() ); ?>/?action=accordion_toggle&accordion_id=' + encodeURIComponent(accordionId) + '&title=' + encodeURIComponent(title) + '&state=' + encodeURIComponent(state), {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        }).then(response => {
                            console.log('Accordion event logged:', { accordionId, title, state });
                        }).catch(error => console.log('request failed', error));
                    });
                });

                // Scroll down to 50% position
                window.addEventListener('scroll', function() {
                    var scrollPercentage = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
                    if (scrollPercentage >= 50) {
                        fetch('?action=page_scrolled_to_50_percent');
                    }
                });

                
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'add_user_activity_scripts');
