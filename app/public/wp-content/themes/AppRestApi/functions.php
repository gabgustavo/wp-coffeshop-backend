<?php

add_filter('acf/settings/rest_api_format', function(){
    //path &acf_format=standard
    return 'standard';
});

function app_restapi_setup() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'app_restapi_setup');

function app_restapi_api_init() {
    register_rest_field(['page', 'post', 'product'], 
        'featured_image',
        [
            'get_callback' => 'get_featured_image'
        ]
    );

    register_rest_field(['post'], 'category_list', [
        'get_callback' => 'get_post_categories'
    ]);

    register_rest_field(['page'], 'gallery', [
        'get_callback' => 'get_gallery'
    ]);
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

function get_post_categories($post) {

    return array_map(function($category_id){
        $category = get_category($category_id, 'ARRAY_A');
        return [
            'name'=> $category['name'],
            'slug'=> $category['slug'],
            'id'=> $category['cat_ID'],
        ];
    }, $post['categories']);
}

function get_gallery($post) {
    if($post['slug'] !== 'galeria') {
        return [];
    }
    $gallery = get_post_gallery($post['id'], false);
    $gallery_ids = array_map('intval', explode(',', $gallery['ids']));


    return array_map(
        function ($image_id) {
            $large_image = wp_get_attachment_image_src($image_id, 'large');
            $full_image = wp_get_attachment_image_src($image_id, 'full');
            return [
                'large' => [
                    'url' => $large_image[0],
                    'width' => $large_image[1],
                    'height' => $large_image[2],
                ],
                'full' => [
                    'url' => $full_image[0],
                    'width' => $full_image[1],
                    'height' => $full_image[2],
                ],
            ];
        }, $gallery_ids
    );
}
