<?php

/**
 * expand an array into a string of tag attributes
 * @parame $args[] array of name/value strings
 * @returns string
 * @return string tag attributes
 */
function autolinks_userapi_htmlattributes($args)
{
    $str = '';
    $attribute_arr = $args;
    $allow_empty_attributes = 0;
    
    if (is_array($attribute_arr))
    {
        foreach($attribute_arr as $name => $value)
        {
            if (!isset($value))
            {
                $value = '';
            }

            if ($value != '' || $allow_empty_attributes)
            {
                // Convert special characters to entities - but use forward
                // references to ensure entities are not done twice.
                // We assume here that all attributes are enclosed in double-quote
                // characters.
                $str .= ' ' . $name . '="' . preg_replace(
                    array('/"/', '/&(?!\w+;)/i', '/</', '/>/'),
                    array('&quot;', '&amp;', '&lt;', '&gt;'), $value
                ) . '"';
            }
        }
    }

    return $str;
}

?>
