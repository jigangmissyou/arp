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
require_once __DIR__ .'/UidProcessor.php';
// Create log recorder
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;


// Set up logger
$log = new Logger('user_activity');
$handler = new StreamHandler(__DIR__ . '/user-activity.log', Logger::INFO);
$handler->setFormatter(new JsonFormatter());

$log->pushHandler($handler);
$log->pushProcessor(new UidProcessor());

// $log->pushProcessor(new UidProcessor());

// 记录用户点击链接的事件

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
            log_scroll_event( 'User scroll down to the bottom', $user_ip );
            break;
        
        case 'page_scrolled_to_50_percent':
            log_scroll_event( 'User scroll down to the 50% position', $user_ip );
            break;
        
        case 'page_stay_duration':
            log_page_stay_duration( $user_ip );
            break;
        
        case 'accordion_toggle':
            log_accordion_toggle( $user_ip );
            break;
        
        default:
            log_click_event( $user_ip );
            break;
    }
}

// Record user scroll down
function log_scroll_event( $message, $user_ip ) {
    global $log;
    $log->info( $message, [ 'user_ip' => $user_ip ] );
}

// Record page stay time
function log_page_stay_duration( $user_ip ) {
    global $log;
    $duration = isset( $_GET['duration'] ) ? intval( $_GET['duration'] ) : 0;
    $log->info( 'User stayed on the page ', [ 'duration' => $duration . ' 秒', 'user_ip' => $user_ip ] );
}

// Record accordion toggle behaviour
function log_accordion_toggle( $user_ip ) {
    global $log;
    $accordion_id = isset( $_GET['accordion_id'] ) ? sanitize_text_field( $_GET['accordion_id'] ) : 'unknown';
    $title = isset( $_GET['title'] ) ? sanitize_text_field( $_GET['title'] ) : 'unknown';
    $state = isset( $_GET['state'] ) ? sanitize_text_field( $_GET['state'] ) : 'unknown';
    
    $log->info( 'User accordion behaviour ', [
        'accordion_id' => $accordion_id,
        'title' => $title,
        'state' => $state,
        'user_ip' => $user_ip
    ] );
}

// Record Click behaviour
function log_click_event( $user_ip ) {
    global $log;
    $log->info( 'User triggered click event ', [
        'url' => $_SERVER['HTTP_REFERER'] ?? 'unknown',
        'user_ip' => $user_ip
    ] );
}



add_action('init', 'log_user_activity_on_click');


// 添加前端脚本，用于触发日志记录
function add_user_activity_scripts() {
    // 只在首页启用
    if ( is_front_page() ) {
        ?>
        <script type="text/javascript">

            document.addEventListener('DOMContentLoaded', function () {
                let startTime = Date.now(); // 记录进入页面的时间
                // 监听用户离开页面
                window.addEventListener('beforeunload', function () {
                    let endTime = Date.now(); // 记录离开页面的时间
                    let duration = Math.round((endTime - startTime) / 1000); // 计算停留秒数
                    // 发送日志到服务器
                    navigator.sendBeacon('<?php echo esc_url( home_url() ); ?>/?action=page_stay_duration&duration=' + duration);
                });

                // 获取按钮元素
                const button = document.querySelector('.kb-button');
                // 如果按钮存在
                if (button) {
                    // 监听按钮点击事件
                    button.addEventListener('click', function () {
                        fetch('<?php echo esc_url( home_url() ); ?>/?action=clicked_link', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        }).then(response => {
                            console.log('点击记录已发送');
                        }).catch(error => console.log('请求失败', error));
                    });
                }

                const submit = document.querySelector('.kb-forms-submit');
                if (submit) {
                    // 监听按钮点击事件
                    submit.addEventListener('click', function () {
                        fetch('<?php echo esc_url( home_url() ); ?>/?action=submitted_form', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        }).then(response => {
                            console.log('form submit记录已发送');
                        }).catch(error => console.log('请求失败', error));
                    });
                }

                const accordionButtons = document.querySelectorAll(".kt-blocks-accordion-header");

                accordionButtons.forEach(button => {
                    button.addEventListener("click", function () {
                        // 获取按钮的 ID
                        const accordionId = this.getAttribute("id");
                        // 获取折叠状态
                        const isExpanded = this.getAttribute("aria-expanded") === "true";
                        // 获取标题内容
                        const title = this.querySelector(".kt-blocks-accordion-title")?.innerText || "Unknown Title";
                        const state = isExpanded ? "Opened" : "Closed";

                        // 发送日志请求到服务器
                        fetch('<?php echo esc_url( home_url() ); ?>/?action=accordion_toggle&accordion_id=' + encodeURIComponent(accordionId) + '&title=' + encodeURIComponent(title) + '&state=' + encodeURIComponent(state), {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        }).then(response => {
                            console.log('Accordion event logged:', { accordionId, title, state });
                        }).catch(error => console.log('请求失败', error));
                    });
                });

                window.addEventListener('scroll', function() {
                    // 用户滚动到页面的底部时触发
                    if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight) {
                        fetch('?action=page_scrolled_to_bottom');
                    }
                });

                // 记录用户滚动到页面的 50% 位置
                window.addEventListener('scroll', function() {
                    var scrollPercentage = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
                    if (scrollPercentage >= 50 && !window.scrolled50) {
                        window.scrolled50 = true; // 防止多次记录
                        fetch('?action=page_scrolled_to_50_percent');
                    }
                });

                
            });


            // 用户观看视频时，记录日志
            document.querySelectorAll('.video-player').forEach(function(player) {
                player.addEventListener('play', function() {
                    const videoId = player.getAttribute('data-video-id');
                    fetch('?action=watched_video&video_id=' + videoId);
                });
            });

        </script>
        <?php
    }
}
add_action('wp_footer', 'add_user_activity_scripts');


