<?php
/**
 * mimotic security functions
 *
 */

 // Removes some links from the header

add_action( 'init', function () {
  remove_action( 'wp_head', 'wp_generator' ); 
});
