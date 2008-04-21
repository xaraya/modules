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
 * @returns array
 * @return parsed feed array
 * @raise BAD_PARAM, NO_PERMISSION
 */
function headlines_userapi_getparsed($args)
{ 
    extract($args); 
    $data = array();
    // this validation is done at create and update and in blocks, 
    // as well as in display functions, probably not really necessary here
    $invalid = '';
    // $location = 'Remote';
    if (empty($feedfile)) {
        $invalid = 'No feed URL specified';
    } elseif (strstr($feedfile,'://')) {
        if (!ereg("^http://|https://|ftp://", $feedfile)) {            
            $invalid = 'URLs of this type are not allowed';
        }
        /* provide feedback on feed location (use for local diags maybe)
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
    // check module available if not default parser
    if ($curparser != 'default' && !xarModIsAvailable($curparser)) $curparser = 'default';
    // set the refresh - default 1 hour
    $refresh = !isset($refresh) ? 3600 : $refresh;
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
            // CHECKME: make sure exceptions are handled here for  magpie cfr. Bug 5490
            $data = xarModAPIFunc('magpie', 'user', 'process', array('feedfile' => $feedfile));
            break;
        case 'simplepie':
            // Use the SimplePie parser
            // CHECKME: is the cacheing for the block in seconds?
            $data = xarModAPIFunc(
                'simplepie', 'user', 'process',
                array('feedfile' => $feedfile, 'cache_max_minutes' => $refresh)
            );
            // CHECKME: make sure exceptions are handled here for simplepie cfr. Bug 5490
            break;
        case 'default':
            default:
            // added superrors param for bug 5490, silently dies instead of throwing exception
            // now we just need an equivalent for simplepie and magpie.
            $data = xarModAPIFunc('headlines', 'user', 'process', 
                array('feedfile' => $feedfile, 'superrors' => true));
            break;
    } 

    if (!isset($data['feedcontent']) || empty($data['feedcontent'])) {
        // $data['warning'] = xarML('#(1) feed failed to load', $location);
        $data['warning'] = xarML('Feed failed to load');
    }
    // image handling included here for consistency
    if (!isset($data['image'])) $data['image'] = array();
    
    return $data;

} 
?>