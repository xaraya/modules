<?php
function bbcode_userapi_code($args)
{
    extract($args);
    $is_html_disabled = false;
    // First things first: If there aren't any "[code]" strings in the message, we don't
    // need to process it at all.
    if (!strpos(strtolower($message), "[code]"))
    {
        return $message;    
    }
    
    // Second things second: we have to watch out for stuff like [1code] or [/code1] in the 
    // input.. So escape them to [#1code] or [/code#1] for now:
    $message = preg_replace("/\[([0-9]+?)code\]/si", "[#\\1code]", $message);
    $message = preg_replace("/\[\/code([0-9]+?)\]/si", "[/code#\\1]", $message);
    
    $stack = Array();
    $curr_pos = 1;
    $max_nesting_depth = 0;
    while ($curr_pos && ($curr_pos < strlen($message)))
    {    
        $curr_pos = strpos($message, "[", $curr_pos);
    
        // If not found, $curr_pos will be 0, and the loop will end.
        if ($curr_pos)
        {
            // We found a [. It starts at $curr_pos.
            // check if it's a starting or ending code tag.
            $possible_start = substr($message, $curr_pos, 6);
            $possible_end = substr($message, $curr_pos, 7);
            if (strcasecmp("[code]", $possible_start) == 0)
            {
                // We have a starting code tag.
                // Push its position on to the stack, and then keep going to the right.
                array_push($stack, $curr_pos);
                ++$curr_pos;
            }
            else if (strcasecmp("[/code]", $possible_end) == 0)
            {
                // We have an ending code tag.
                // Check if we've already found a matching starting tag.
                if (sizeof($stack) > 0)
                {
                    // There exists a starting tag. 
                    $curr_nesting_depth = sizeof($stack);
                    $max_nesting_depth = ($curr_nesting_depth > $max_nesting_depth) ? $curr_nesting_depth : $max_nesting_depth;
                    
                    // We need to do 2 replacements now.
                    $start_index = array_pop($stack);

                    // everything before the [code] tag.
                    $before_start_tag = substr($message, 0, $start_index);

                    // everything after the [code] tag, but before the [/code] tag.
                    $between_tags = substr($message, $start_index + 6, $curr_pos - $start_index - 6);

                    // everything after the [/code] tag.
                    $after_end_tag = substr($message, $curr_pos + 7);

                    $message = $before_start_tag . "[" . $curr_nesting_depth . "code]";
                    $message .= $between_tags . "[/code" . $curr_nesting_depth . "]";
                    $message .= $after_end_tag;
                    
                    // Now.. we've screwed up the indices by changing the length of the string. 
                    // So, if there's anything in the stack, we want to resume searching just after it.
                    // otherwise, we go back to the start.
                    if (sizeof($stack) > 0)
                    {
                        $curr_pos = array_pop($stack);
                        array_push($stack, $curr_pos);
                        ++$curr_pos;
                    }
                    else
                    {
                        $curr_pos = 1;
                    }
                }
                else
                {
                    // No matching start tag found. Increment pos, keep going.
                    ++$curr_pos;    
                }
            }
            else
            {
                // No starting tag or ending tag.. Increment pos, keep looping.,
                ++$curr_pos;    
            }
        }
    } // while
    
    if ($max_nesting_depth > 0)
    {
        for ($i = 1; $i <= $max_nesting_depth; ++$i)
        {
            $start_tag = escape_slashes(preg_quote("[" . $i . "code]"));
            $end_tag = escape_slashes(preg_quote("[/code" . $i . "]"));
            
            $match_count = preg_match_all("/$start_tag(.*?)$end_tag/si", $message, $matches);
    
            for ($j = 0; $j < $match_count; $j++)
            {
                $before_replace = escape_slashes(preg_quote($matches[1][$j]));
                $after_replace = $matches[1][$j];
                
                if (($i < 2) && !$is_html_disabled)
                {
                    // don't escape special chars when we're nested, 'cause it was already done
                    // at the lower level..
                    // also, don't escape them if HTML is disabled in this post. it'll already be done
                    // by the posting routines.
                    // $after_replace = htmlspecialchars($after_replace);    
                }
                
                $str_to_match = $start_tag . $before_replace . $end_tag;
             
                $message = preg_replace("/$str_to_match/si", xarML('Code') . ": <div class='bbcode_code' style=' padding: 5px; white-space: normal'> " . bbcode_br2nl($after_replace) . "</div>", $message);
            }
        }
    }
    
    // Undo our escaping from "second things second" above..
    $message = preg_replace("/\[#([0-9]+?)code\]/si", "[\\1code]", $message);
    $message = preg_replace("/\[\/code#([0-9]+?)\]/si", "[/code\\1]", $message);
    return $message;
}
?>
