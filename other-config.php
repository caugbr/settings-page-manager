<?php

/**
 * The variables you want to work with must be here
 * See below for an example of each possible field type
 */

$other_settings = [
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
    "radio_list" => [
        "type" => "radio-group",
        "description" => 'Choose one',
        "label" =>  'Radio group',
        "options" => [
            [ "value" => "yes", "label" => "Yes" ],
            [ "value" => "no", "label" => "No" ],
            [ "value" => "maybe", "label" => "Maybe" ]
        ],
        "default_value" => 'yes'
    ]
];