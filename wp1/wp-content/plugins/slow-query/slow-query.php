<?php
/*
Plugin Name: Slow Query Simulator
Description: This plugin simulates slow queries for testing purposes by using the `SLEEP()` SQL function.
Version: 1.0
Author: Jigang Guo
License: GPL2
*/

add_action('init', function() {
    global $wpdb;

    if ( isset($_GET['do_slow_query']) ) {
        $start = microtime(true);

        $wpdb->query("SELECT SLEEP(5)");

        $end = microtime(true);

        echo "Query executed in " . ($end - $start) . " seconds.";
        
        exit;
    }
});
