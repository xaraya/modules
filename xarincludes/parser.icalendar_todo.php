<?php
/**
 *  iCalendar VEVENT Parser Routine
 *  Returns a valid iCalendar_Event Object
 */


function &parse_vtodo(&$content,&$ical)
{
    //$vtodo =& new iCalendar_Event;
    $vtodo =& $ical->vtodo[$ical->num_vtodo()-1];
    
    foreach($content as $line_number => $line) {
    
        // check for whitespace at beginning of the line
        // if there is whitespace, then we need to append this
        // content to the previous content.
        
        if(preg_match('/^\s+/',$line) && isset($assign_to)) {
            if((bool)$single) {
                $vtodo->{$assign_to} .= ' '.trim($line);
            } elseif((bool)$multiple) {
                $c = count($vtodo->{$assign_to}) - 1;
                $vtodo->{$assign_to}[$c] .= ' '.trim($line);
            }
            // we don't need to run the rest of this function
            continue;
        
        } elseif(isset($assign_to)) {
            if((bool)$single) {
                //find_attributes($assign_to,$vtodo);
                $vtodo->{$assign_to} = preg_replace('/[\n\r]/','',trim($vtodo->{$assign_to}));
                $vtodo->{$assign_to} = preg_replace('/^[:;]/','',$vtodo->{$assign_to});
            } elseif((bool)$multiple) {
                $c = count($vtodo->{$assign_to}) - 1;
                //find_attributes($assign_to,$vtodo,$c);
                $vtodo->{$assign_to}[$c] = preg_replace('/[\n\r]/','',trim($vtodo->{$assign_to}[$c]));
                $vtodo->{$assign_to} = preg_replace('/^[:;]/','',$vtodo->{$assign_to});
            }
            
            $single = $multiple = false;
            unset($assign_to);
        }
        
        // let's see what we've got here, shall we?
        $single_properties = array(
            'CLASS',
            'COMPLETED',
            'CREATED',
            'DESCRIPTION',
            'DTSTAMP',
            'DTSTART',
            'GEO',
            'LAST-MODIFIED',
            'LOCATION',
            'ORGANIZER',
            'PERCENT',
            'PRIORITY',
            'RECURRENCE-ID',
            'SEQUENCE',
            'STATUS',
            'SUMMARY',
            'UID',
            'URL',
            'DUE',
            'DURATION'
        );
       
        /* can have multiple of these */
        $multiple_properties = array(
            'ATTACH',
            'ATTENDEE',
            'CATEGORIES',
            'COMMENT',
            'CONTACT',
            'EXDATE',
            'EXRULE',
            'RDATE',
            'RRULE',
            'RELATED-TO',
            'RESOURCES',
            'REQUEST-STATUS'
        );
        
        foreach($single_properties as $property) {
            if(preg_match('/^'.$property.'(;|:)?(.*)$/',$line,$match)) {
                $assign_to = get_property_varname($property);
                // ok, since we can have lots of stuff from this, let's bust it up            
                $vtodo->{$assign_to} = substr($line,strlen($property)+1);
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
                $vtodo->{$assign_to}[] = substr($line,strlen($property)+1);
                // we don't need to be in this loop anymore
                $multiple = true;
                $single   = false;
                break;
            }
        }
    }
    return $vtodo;
}

?>