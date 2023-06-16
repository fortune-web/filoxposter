<?php
/*
 Plugin Name: filoxposter
 Description: A custom plugin for creating posts on the fly!
 Version: 1.0
 Author: Daniel
*/

// Add shortcode for displaying form
function flx_post_shortcode() {
  // check if user is logged in
  if (is_user_logged_in()) {
    // display form HTML here
    return '<form id="flx-post-form">
                <label for="post_title">Post Title</label>
                <input type="text" name="post_title" id="post_title" required>

                <label for="post_content">Post Content</label>
                <textarea name="post_content" id="post_content" required></textarea>

                <button type="submit" id="flx-post-submit-button" disabled>Create Post</button>
            </form>';
  } else {
    // display login link
    return '<a href="' . wp_login_url() . '">Please login to create a post.</a>';
  }
}
add_shortcode('flx_post_shortcode', 'flx_post_shortcode');

function flx_register_rest_routes() {
  register_rest_route('flx/v1', '/post-title-exists/(?P<title>.+)', array(
    'methods' => 'GET',
    'callback' => 'flx_post_title_exists'
  ));

  register_rest_route('flx/v1', '/create-post', array(
    'methods' => 'POST',
    'callback' => 'flx_create_post'
  ));
}
add_action('rest_api_init', 'flx_register_rest_routes');

// Check if post title exists
function flx_post_title_exists($data) {
  $args = array(
    'name' => $data['title'],
    'post_type' => 'post',
    'post_status' => 'publish',
    'numberposts' => 1
  );
  $posts = get_posts($args);

  return rest_ensure_response(array('exists' => !empty($posts)));
}

// Create new post
function flx_create_post($data) {
  $post_id = wp_insert_post(array(
    'post_title' => sanitize_text_field($data['title']),
    'post_content' => wp_kses_post($data['content']),
    'post_status' => 'publish'
  ));

  if ($post_id) {
    $post_link = get_permalink($post_id);
    return rest_ensure_response(array('success' => true, 'link' => $post_link));
  } else {
    return rest_ensure_response(array('success' => false));
  }
}
?>

