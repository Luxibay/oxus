<?php

/* Registers a WPML Language Condition in Oxygen */
function oxus_lang_condition()
{
    $oxus_lang_active = apply_filters('wpml_active_languages', NULL);
    $oxus_lang_list = array();
    foreach ($oxus_lang_active as $lang) {
        $oxus_lang_list[] = $lang['code'];
    }
    oxygen_vsb_register_condition(

        //Condition Name
        'Language',

        //Values
        array(
            'options' => $oxus_lang_list,
            'custom' => false
        ),
        //Operators
        array('==', '!='),

        //Callback Function
        'oxus_lang_condition_callback',

        //Condition Category
        'WPML'
    );
}

function oxus_lang_condition_callback($value, $operator)
{
    $current_lang = apply_filters('wpml_current_language', NULL);
    global $OxygenConditions;
    return $OxygenConditions->eval_string($current_lang, $value, $operator);
}


//load the function 
oxus_lang_condition();
