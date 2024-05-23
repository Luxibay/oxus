<?php

function oxus_modify_wpml_translated(
    $package_kind,
    $translated_post_id,
    $original_post,
    $string_translations,
    $lang
) {
    if (function_exists('oxy_wpml_translated')) {
        remove_action('wpml_page_builder_string_translated', 'oxy_wpml_translated');
    }

    // Make sure the package is for our plugin
    if (OXY_NAME === $package_kind) {

        // Get the data from the original post
        // We'll then update the data with the translated strings and
        // save to the translated post.
        $json = get_post_meta($original_post->ID, "_ct_builder_json", true);
        $tree = json_decode($json, true);
        $tree = oxus_wpml_translated_recursion($tree, $string_translations, $translated_post_id, $lang);
        $json = json_encode($tree, JSON_UNESCAPED_UNICODE);

        // Save the post data that now includes the translations to the translated post.
        update_post_meta($translated_post_id, "_ct_builder_json", $json);
    }
}

add_action('wpml_page_builder_string_translated', 'oxus_modify_wpml_translated', 10, 5);

function oxus_wpml_translated_recursion($tree, $string_translations, $translated_post_id, $lang)
{
    // Go through all the elements to replace their text
    if (isset($tree['children'])) {
        foreach ($tree['children'] as $key => $element) {
            $string_id = 'oxy-element-' . $element['id'];
            if (isset($element['options']['ct_content'])) {
                if (isset($string_translations[$string_id][$lang]['value'])) {
                    $tree['children'][$key]['options']['ct_content'] = $string_translations[$string_id][$lang]['value'];
                }
            }

            $comp_content = array('testimonial_text', 'testimonial_author', 'testimonial_author_info', 'icon_box_heading', 'icon_box_text', 'title-facebook', 'title-instagram', 'title-twitter', 'title-linkedin', 'title-rss', 'title-youtube', 'pricing_box_package_title', 'pricing_box_package_subtitle', 'pricing_box_package_regular', 'pricing_box_content', 'pricing_box_price_amount_main', 'pricing_box_price_amount_decimal', 'pricing_box_price_amount_currency', 'pricing_box_price_amount_term', 'progress_bar_left_text');

            foreach ($comp_content as $comp_key) {
                $string_new = $string_id . '-' . $comp_key;
                if (isset($element['options']['original'][$comp_key])) {
                    if (isset($string_translations[$string_new][$lang]['value'])) {
                        if ($comp_key == 'icon_box_heading') {
                            update_option('oxus_test', 'heading succeeded');
                        }
                        $tree['children'][$key]['options']['original'][$comp_key] = $string_translations[$string_new][$lang]['value'];
                    }
                }
            }


            /* Add URL Support */
            if (isset($element['options']['original']['url'])) {
                if (isset($string_translations[$string_id][$lang]['value'])) {
                    $tree['children'][$key]['options']['original']['url'] = $string_translations[$string_id][$lang]['value'];
                }
            }


            if (isset($element['children'])) {
                $tree['children'][$key] = oxus_wpml_translated_recursion($element, $string_translations, $translated_post_id, $lang);
            }
        }
    }

    return $tree;
}
