<?php
function bbcode_userapi_quote($args)
{
    extract($args);
    $tags_found = array();
    //Create an associative array, including the character
    //offset of each end/start tag in the message.
    for($j = 0; ($j = strpos($message, '[', $j)); $j++) {
        if(strcasecmp(substr($message, $j, 7), '[quote]') == 0)
            $tags_found[$j] = 's';
        elseif(strcasecmp(substr($message, $j, 8), '[/quote]') == 0)
            $tags_found[$j] = 'e';
    }

      /*
      This will be faster, but no stripos() until PHP 5.0 :-( 
      for($j = 0; ($j = stripos($message, '[quote]', $j)); $j++) 
        $tags_found[$j] = 's';
      for($j = 0; ($j = stripos($message, '[/quote]', $j)); $j++) 
        $tags_found[$j] = 'e';
      ksort($tags_found);
      */
  
    //If no tags found, return.
    if(empty($tags_found)) {
        return $message;
    }

    $stack = array();
    $is_well_formed = TRUE;
    foreach($tags_found as $k => $v) {
        //If we have a start tag, hold on to it in the stack
        if($v == 's') {
            array_push($stack, $k);
        }
        //If we have an end tag
        else{
        //If we have a pending start tag, we have a matach
            if(!empty($stack)) {
                array_pop($stack);
            }
            //If we don't have a pending start tag, mark string
            //as malformed and remove extranneous end tag from our list.
            else {
                $is_well_formed = FALSE;
                unset($tags_found[$k]); //This is safe because 'foreach' operates on a copy
            }
        }
    }
  
    //Fast Path: is we know our string is well formed, then we can do a batch replace
    if($is_well_formed && empty($stack)) {
        //No str_ireplace until PHP 5.0 :-(
        $message = preg_replace('/\[quote\]/i', '<p>' . xarML('Quote') . ':</p><blockquote><div style="width: 90%; height: 100px; overflow: auto;">', $message);
        $message = preg_replace('/\[\/quote\]/i', '</div></blockquote>', $message);
        return $message;
    }

    //Get rid of extra start tags, if any
    foreach($stack as $v) {
        unset($tags_found[$v]);
    }

    //Now rebuild the string using $tags_found
    $new_offset = 0;
    foreach($tags_found as $k => $v) {
        if($v == 's') {
            $message = & substr_replace($message, '<p>' . xarML('Quote') . ':</p><blockquote><div style="width: 90%; height: 100px; overflow: auto;">', $k + $new_offset, 7);
            $new_offset += 11;
        } else {
            $message = & substr_replace($message, '</div></blockquote>', $k + $new_offset, 8);
            $new_offset += 5;
        }
    }
    return $message;
}
?>