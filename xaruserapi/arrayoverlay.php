<?php

// Recursively overlay one array on top of another.
// A blank element ('') will never over-write a non-blank element.
// This is a generic array function.
// This allows a page to inherit properties of a parent page, by
// overlaying it onto the parent page.
// @param $args[0] base array, to be overlayed
// @param $args[1] top array, to overlay the base
// @returns array the top array overlaying the base array

function xarpages_userapi_arrayoverlay($args)
{
    // Get the arguments.
    if (isset($args['base']) && isset($args['top'])) {
        $array_base = $args['base'];
        $array_top = $args['top'];
    } else {
        list($array_base, $array_top) = $args;
    }

    if (!is_array($array_base) || !is_array($array_top)) {
        return $array_top;
    }

    foreach ($array_top as $key_top => $value_top) {
        if ($value_top != '' || !isset($array_base[$key_top])) {
            if (!isset($array_base[$key_top])) {
                $array_base[$key_top] = $value_top;
            } else {
                $array_base[$key_top] = xarpages_userapi_arrayoverlay(array($array_base[$key_top], $value_top));
            }
        }
    }

    return $array_base;
}
