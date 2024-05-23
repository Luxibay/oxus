<?php

function oxus_wpml_register_strings($post, $package_data)
{

    remove_action('wpml_page_builder_register_strings', 'oxy_wpml_register_strings');
    if ('Oxygen Builder' === $package_data['kind']) {
        // get JSON
        $json = get_post_meta($post->ID, "_ct_builder_json", true);
        $tree = json_decode($json, true);

        oxus_wpml_register_strings_recursion($tree, $package_data);
    }
}

add_action('wpml_page_builder_register_strings', 'oxus_wpml_register_strings', 10, 2);


function oxus_wpml_register_strings_recursion($tree, $package_data)
{
    if (isset($tree['children'])) {
        foreach ($tree['children'] as $key => $element) {

            /* Keep Content Support */
            if (isset($element['options']['ct_content'])) {
                do_action(
                    'wpml_register_string',
                    $element['options']['ct_content'],
                    // the actual string value
                    'oxy-element-' . $element['id'],
                    // a unique identifier for this string.
                    $package_data,
                    $element['options']['selector'],
                    // a title for this string
                    'LINE' // the string type: 'LINE', 'TEXTAREA', 'VISUAL', 'LINK'
                );
            }

            $composite_content = array('testimonial_text', 'testimonial_author', 'testimonial_author_info', 'icon_box_heading', 'icon_box_text', 'title-facebook', 'title-instagram', 'title-twitter', 'title-linkedin', 'title-rss', 'title-youtube', 'pricing_box_package_title', 'pricing_box_package_subtitle', 'pricing_box_package_regular', 'pricing_box_content', 'pricing_box_price_amount_main', 'pricing_box_price_amount_decimal', 'pricing_box_price_amount_currency', 'pricing_box_price_amount_term', 'progress_bar_left_text');

            foreach ($composite_content as $comp_key) {
                if (isset($element['options']['original'][$comp_key])) {
                    do_action(
                        'wpml_register_string',
                        $element['options']['original'][$comp_key],
                        // the actual string value
                        'oxy-element-' . $element['id'] . '-' . $comp_key,
                        // a unique identifier for this string.
                        $package_data,
                        $element['options']['selector'] . '-' . $comp_key,
                        // a title for this string
                        'LINE' // the string type: 'LINE', 'TEXTAREA', 'VISUAL', 'LINK'
                    );
                }
            }


            /* Add URL Support */
            if (isset($element['options']['original']['url'])) {
                do_action(
                    'wpml_register_string',
                    $element['options']['original']['url'],
                    // the actual string value
                    'oxy-element-' . $element['id'] . '-url',
                    // a unique identifier for this string.
                    $package_data,
                    $element['options']['selector'] . '-url',
                    // a title for this string
                    'LINE' // the string type: 'LINE', 'TEXTAREA', 'VISUAL', 'LINK'
                );
            }

            //$composite_links = array('icon-facebook', 'icon-instagram', 'icon-twitter', 'icon-linkedin', 'icon-rss', 'icon-youtube');




            if (isset($element['children'])) {
                oxus_wpml_register_strings_recursion($element, $package_data);
            }
        }
    }
}
