<?php

/**
 * The variables you want to work with must be here
 * See below for an example of each possible field type
 */

$theme_settings = [
    "some_percentage" => [
        "type" => "range",
        "description" => 'Blah',
        "label" =>  'Range',
        "default_value" => '0',
        "min" => 10,
        "max" => 200,
        "step" => 10
    ],
    "default_post" => [
        "type" => "post-picker",
        "description" => 'Select the posts you want',
        "label" =>  'Select a post',
        "default_value" => [ "1" => "Hello world" ],
        "post_type" => 'post',
        // "multiple" => '1'
    ],
    "hide_wp_bar" => [
        "type" => "switch",
        "description" => 'Hide Wordpress admin bar on frontend overriding user setting',
        "label" =>  'Hide WP admin bar',
        "default_value" => '0'
    ],
    "sidebar_location" => [
        "type" => "select",
        "options" => [
            [ "label" => "On the left", "value" => "left" ],
            [ "label" => "On the right", "value" => "right" ],
            [ "label" => "Don't use sidebar", "value" => "none" ]
        ],
        "description" => 'Define where to show sidebar',
        "label" =>  'Sidebar position',
        "default_value" => 'left'
    ],
    "post_formats" => [
        "type" => "checkbox-group",
        "options" => "all_post_formats",
        "description" => 'Select the post formats that will have support in your theme.',
        "label" =>  'Post formats support',
        "default_value" => ['aside', 'gallery']
    ],
    "text_field" => [
        "type" => "text",
        "description" => 'Text field',
        "label" =>  'Type something',
        "placeholder" => "Type here",
        "default_value" => ''
    ],
    "area_field" => [
        "type" => "textarea",
        "description" => 'Test area field',
        "label" =>  'Type a long text',
        "default_value" => 'blah'
    ],
    "radio_list" => [
        "type" => "radio-group",
        "description" => 'Choose one',
        "label" =>  'Radio group',
        "inline" => true,
        "options" => [
            [ "value" => "yes", "label" => "Yes" ],
            [ "value" => "no", "label" => "No" ],
            [ "value" => "maybe", "label" => "Maybe" ]
        ],
        "default_value" => 'yes'
    ]
];

function all_post_formats() {
    return [
        [ "label" => __('Aside'), "value" => 'aside' ],
        [ "label" => __('Gallery'), "value" => 'gallery' ],
        [ "label" => __('Link'), "value" => 'link' ],
        [ "label" => __('Image'), "value" => 'image' ],
        [ "label" => __('Quote'),  "value" => 'quote' ],
        [ "label" => __('Status'), "value" => 'status' ],
        [ "label" => __('Video'), "value" => 'video' ],
        [ "label" => __('Audio'), "value" => 'audio' ],
        [ "label" => __('Chat'), "value" => 'chat' ]
    ];
}