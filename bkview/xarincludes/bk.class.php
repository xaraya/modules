<?php

/**
 * File: $Id$
 *
 * Classes to model bitkeeper repository objects
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

define('BK_SEARCH_REPO',   8);
define('BK_SEARCH_FILE',   4);
define('BK_SEARCH_CSET',   1);
define('BK_SEARCH_DELTAS', 2);

define('BK_FIELD_MARKER','|');
define('BK_NEWLINE_MARKER','<nl/>');


// Include the repository class
include_once("bkrepo.class.php");

// Include the changeset class
include_once("bkcset.class.php");

// Include the delta class
include_once("bkdelta.class.php");

// Include the repository file class
include_once("bkfile.class.php");


/**
 * UTILITY FUNCTIONS WHICH NEED A PLACE
 *
 */

/**
 * Translate a range to a text string
 *
 * Currently maintained on ad-hoc basis
 */
function bkRangeToText($range='') 
{
  // FIXME: this is FAR FROM COMPLETE
  $text='';
  if ($range=='') return '';

  // Check before/after range
  if (substr($range,0,2)=='..') {
    return 'before '.substr($range,2,strlen($range)-2);
  }
  if (substr($range,-2,2)=='..') {
    return 'after '.substr($range,2,strlen($range)-2);
  }

  $number = (-(int) $range);

  // past?
  if (((int) $range) < 0) {
    $text .='in the last ';
  }
  // Converts range specification to text to display
  switch (strtolower($range[strlen($range)-1])) {
  case 'h':
    $text .=((-(int) $range)==1)?"hour":"$number hours";
    break;
  case 'd':
    $text .=((-(int) $range)==1)?'day':"$number days";
    break;
  case 'w':
    $text .=((-(int) $range)==1)?'week':"$number weeks";
    break;
  case 'm':
    $text .=((-(int) $range)==1)?'month':"$number months";
    break;
  case 'y':
    $text .=((-(int) $range)==1)?'year':"$number years";
    break;
  default:
    $text .= "unknown range $range";
  }
  return $text;
}

function bkAgeToRangeCode($age) 
{
    // Converts an age as output by :AGE: dspec to range code 
    // useable by bk prs (bit lame that prs doesn't do that itself)
    // First part: multiplier
    // Second part: unit:
    //    Y/y - years
    //    M   - months
    //    W/w - weeks
    //    D/d - days
    //    h   - hours
    //    m   - minutues
    //    s   - seconds
    
    $parts = explode(' ',$age);
    switch (strtolower($parts[1][0])) {
        case 'y':
        case 'w':
        case 'd':
        case 'h':
        case 's':
            $ageCode = "-". $parts[0] . $parts[1][0];
            break;
        case 'm':
            if(strtolower($parts[1][1]) =='o') {
                $ageCode = "-". $parts[0] . 'M';
            } else {
                $ageCode = "-". $parts[0] . 'm';
            }
            break;
        default:
            $ageCode = '-1h';
    }
    return $ageCode;
}

?>
