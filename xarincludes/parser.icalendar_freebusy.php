<?php
/**
 *  iCalendar vfreebusy Parser Routine
 *  Returns a valid iCalendar_Event Object
 */


function &parse_vfreebusy(&$content,&$ical)
{
    //$vfreebusy =& new iCalendar_Event;
    $vfreebusy =& $ical->vfreebusy[$ical->num_vfreebusy()-1];
    
    foreach($content as $line_number => $line) {
    
        // check for whitespace at beginning of the line
        // if there is whitespace, then we need to append this
        // content to the previous content.
        
        if(preg_match('/^\s+/',$line) && isset($assign_to)) {
            if((bool)$single) {
                // this is a wrapped line, prepend a space to it
                $vfreebusy->{$assign_to} .= ' '.trim($line);
            } elseif((bool)$multiple) {
                $c = count($vfreebusy->{$assign_to}) - 1;
                // this is a wrapped line, prepend a space to it
                $vfreebusy->{$assign_to}[$c] .= ' '.trim($line);
            }
            // we don't need to run the rest of this function
            continue;
        
        } elseif(isset($assign_to)) {
            if((bool)$single) {
                //find_attributes($assign_to,$vfreebusy);
                $vfreebusy->{$assign_to} = preg_replace('/[\n\r]/','',trim($vfreebusy->{$assign_to}));
                $vfreebusy->{$assign_to} = preg_replace('/^[:;]/','',$vfreebusy->{$assign_to});
            } elseif((bool)$multiple) {
                $c = count($vfreebusy->{$assign_to}) - 1;
                //find_attributes($assign_to,$vfreebusy,$c);
                $vfreebusy->{$assign_to}[$c] = preg_replace('/[\n\r]/','',trim($vfreebusy->{$assign_to}[$c]));
                $vfreebusy->{$assign_to} = preg_replace('/^[:;]/','',$vfreebusy->{$assign_to});
            }
            
            $single = $multiple = false;
            unset($assign_to);
        }
        
        // let's see what we've got here, shall we?
        $single_properties = array(
            'DTSTART',
            'DTSTAMP',
            'ORGANIZER',
            'UID',
            'URL',
            'DTEND',
            'DURATION',
            'CONTACT'
            );
            
       /* can have multiple of these */
        $multiple_properties = array(
            'ATTENDEE',
            'COMMENT',
            'REQUEST-STATUS',
            'FREEBUSY'
            );
        
        foreach($single_properties as $property) {
            if(preg_match('/^'.$property.'(;|:)?(.*)$/',$line,$match)) {
                $assign_to = get_property_varname($property);
                // ok, since we can have lots of stuff from this, let's bust it up            
                $vfreebusy->{$assign_to} = substr($line,strlen($property)+1);
                // we don't need to be in this loop anymore
                $single   = true;
                $multiple = false;
                break;
            }
        }
        
        foreach($multiple_properties as $property) {
            if(preg_match('/^'.$property.'(;|:)?(.*)$/',$line,$match)) {
                $assign_to = get_property_varname($property);
                // ok, since we can have lots of stuff from this, let's bust it up            
                $vfreebusy->{$assign_to}[] = substr($line,strlen($property)+1);
                // we don't need to be in this loop anymore
                $multiple = true;
                $single   = false;
                break;
            }
        }
    }
    return $vfreebusy;
}

?>