<?php

/**
 * Encode string to quoted-printable.  
 * @access private
 * @return string
 * TODO: move this closer to the core, perhaps as part of a more general
 * 'encode string' collection of functions.
 */

function newsgroups_userapi_encode_quoted_printable($args) {
    $le = "\n";

    extract($args);

    if (!isset($string)) {
        return '';
    }

    $encoded = str_replace("\r\n", "\n", $string);
    $encoded = str_replace("\r", "\n", $encoded);
    $encoded = str_replace("\n", $le, $encoded);

    if (substr($encoded, -(strlen($le))) != $le)
        $encoded .= $le;

    // Replace every high ascii, control and = characters
    $encoded = preg_replace('/([\000-\010\013\014\016-\037\075\177-\377])/e',
              "'='.sprintf('%02X', ord('\\1'))", $encoded);
    // Replace every spaces and tabs when it's the last character on a line
    $encoded = preg_replace("/([\011\040])".$le."/e",
              "'='.sprintf('%02X', ord('\\1')).'".$le."'", $encoded);

    // Maximum line length of 76 characters before CRLF (74 + space + '=')
    $encoded = wordwrap($encoded, 74, " =".$le, 1);

    return $encoded;
}

?>