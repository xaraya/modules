<?php
/**
 * File: $Id:
 * 
 * Get a specific item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ?modname
 * @author ?author 
 */
/**
 * get a parsed feed from the current parser
 * 
 * @param  $args ['feedfile'] url of the feedfile to pass to the parser
 * @param  $args ['refresh'] optional cache age
 * @param  $args ['truncate'] optionally truncate descriptions
 * @param  $args ['numitems'] optional, number of items to return
 * @returns array
 * @return parsed feed array
 * @raise BAD_PARAM, NO_PERMISSION
 */
function headlines_userapi_getparsed($args)
{ 
    extract($args); 
    $data = array();
    $numitems = isset($numitems) && is_numeric($numitems) ? $numitems : 0;
    $truncate = isset($truncate) && is_numeric($truncate) ? $truncate : 0;
    // set the refresh - all parsers default to 1 hour
    $refresh = isset($refresh) && is_numeric($refresh) ? $refresh : 3600;
    
    $invalid = '';
    // $location = 'Remote'; // feed location.
    if (empty($feedfile)) {
        $invalid = 'No feed URL specified';
    } elseif (strstr($feedfile,'://')) {
        if (!ereg("^http://|https://|ftp://", $feedfile)) {            
            $invalid = 'URLs of this type are not allowed';
        }
        /* provide feedback on feed location (use for local diags maybe?)
        $server = xarServerGetHost();
        if (preg_match("!://($server|localhost|127\.0\.0\.1)(:\d+|)/!",$url)) {
            $location = 'Local';
        }
        */
    } elseif (substr($feedfile,0,1) == '/') {
        $server = xarServerGetHost();
        $protocol = xarServerGetProtocol();
        $feedfile = $protocol . '://' . $server . $feedfile;
        // $location = 'Local';
    } else {
        $baseurl = xarServerGetBaseURL();
        $feedfile = $baseurl . $feedfile;
        // $location = 'Local';
    }
    // return warning to calling function    
    if (!empty($invalid)) {
        $data['warning'] = xarML($invalid);
        return $data;
    }
    // get the current parser
    $curparser = xarModGetVar('headlines', 'parser');
    // check for legacy magpie code, checkme: is this still necessary?
    if (xarModGetVar('headlines', 'magpie')) $curparser = 'magpie';
    // check module available, if not use default parser
    // CHECKME: added this for first run, clean install, value from init doesn't appear to get set
    if (empty($curparser)) $curparser = 'default';
    if ($curparser != 'default' && !xarModIsAvailable($curparser)) $curparser = 'default';

    switch ($curparser) {
        case 'magpie':
            // Set some globals to bring Magpie into line with
            // site and headlines settngs.
            if (!defined('MAGPIE_OUTPUT_ENCODING')) {
                define('MAGPIE_OUTPUT_ENCODING', xarMLSGetCharsetFromLocale(xarMLSGetCurrentLocale()));
            }
            if (!defined('MAGPIE_CACHE_AGE')) {
                define('MAGPIE_CACHE_AGE', $refresh);
            }
            $data = xarModAPIFunc('magpie', 'user', 'process', array('feedfile' => $feedfile));
            break;
        case 'simplepie':
            // Use the SimplePie parser
            // CHECKME: is the cacheing for the block in seconds?
            $data = xarModAPIFunc(
                'simplepie', 'user', 'process',
                array('feedfile' => $feedfile, 'cache_max_minutes' => $refresh)
            );
            break;
        case 'default':
            default:
            // added superrors param for bug 5353, silently dies instead of throwing exception
            $data = xarModAPIFunc('headlines', 'user', 'process', 
                array('feedfile' => $feedfile, 'superrors' => true));
            break;
    } 
    
    if (!isset($data['feedcontent']) || empty($data['feedcontent'])) {
        // $data['warning'] = xarML('#(1) feed failed to load', $location);
        $data['warning'] = xarML('Feed failed to load');
    }
    $data['count'] = count($data['feedcontent']);
    if (!empty($numitems)) {
	    // trim the array to just the items we were asked for 
	    $data['feedcontent'] = array_slice($data['feedcontent'], 0, $numitems);
    }
    $data['numitems'] = count($data['feedcontent']);
    if ($curparser == 'simplepie' || !empty($truncate)) {
        for ($i = 0; $i < count($data['feedcontent']); $i++) {
            $chanitem = $data['feedcontent'][$i]; // current feed item
            if ($curparser == 'simplepie') { // parse item categories
                if (isset($chanitem['categories']) && !empty($chanitem['categories'])) {
                    foreach ($chanitem['categories'] as $catkey => $catobject) {
                        if (!isset($catobject)) continue;
                        $chanitem['categories'][$catkey] = array('term' => $catobject->term, 
                            'scheme' => $catobject->scheme, 'label' => $catobject->label );
                    }
                }
                // handle RSS enclosures while we're here
                if (!empty($chanitem['enclosure'])) {
                    $encobj = $chanitem['enclosure'];
                    // if there are any thumbnails, add them to this item
                    $chanitem['thumbnails'] = $encobj->thumbnails;
                    // see if this object is an image
                    if (strpos($encobj->type, 'image') !== false) {
                        $chanitem['image'] = $encobj->link;
                    } else {
                        // TODO: handle output of other mime types here
                        // $chanitem['embed'] = $encobj->native_embed();
                    }
                }
            }
            if (!empty($truncate)) { // truncate long descriptions
                // only transfrom descriptions longer than specified max
                if (!empty($chanitem['description']) && (strlen($chanitem['description'])+3 > $truncate)) {
                    $chanitem['description'] = substr($chanitem['description'], 0, $truncate).'...';
                }
            }
            
            $data['feedcontent'][$i] = $chanitem;
        }
    }

    // channel image handling included here for consistency
    if (!isset($data['image'])) $data['image'] = array();
    // pass the parser used back too
    $data['parser'] = $curparser;
    
    return $data;

} 
?>