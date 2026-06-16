<?php
function app_restapi_setup() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'app_restapi_setup');

function app_restapi_api_init() {
    register_rest_field(['page'], 
        'featured_image',
        [
            'get_callback' => 'get_featured_image'
        ]
    );
}
add_action('rest_api_init', 'app_restapi_api_init');

function get_featured_image($post) {

    if(!$post['featured_media']) {
        return null;
    }
    $image_sizes = get_intermediate_image_sizes();
    $images = [];
    foreach ($image_sizes as $size) {
        if($size === '2048x2048') continue;
        
        $image = wp_get_attachment_image_src($post['featured_media'], $size);
        $images[$size === '1536x1536'? 'full': $size ] = [
            'size' => $size,
            'url' => $image[0],
            'width' => $image[1],
            'height' => $image[2]
        ];
    }
    

    return $images;
}
