<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * Checks for most recent update and updates DB if needed.
 *
 * @author J. Cox <niceguyeddie@xaraya.com>
 * @access private
 */
function headlines_schedulerapi_compare()
{
    // Security Check
    if(!xarSecurityCheck('OverviewHeadlines')) return;
    // get all headlines from module
    $links  = xarModAPIFunc('headlines', 'user', 'getall');

    if (empty($links)) return;

    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        // Check and see if a feed has been supplied to us.
        if (empty($link['url'])) {
            continue;
        }
        $feedfile = $link['url'];
        $links[$i] = xarModAPIFunc(
            'headlines', 'user', 'getparsed',
            array('feedfile' => $feedfile)
        );
        // Check and see if a valid feed has been supplied to us.
        if (!isset($links[$i]) || isset($links[$i]['warning'])) continue;
        // here we see if this feed has been updated by comparing the stored hash against the 
        // hash provided by the getparsed function, if they're different, we update the feed
        // with the new hash, and the time of the last item in the feed, or the current time
        if (isset($links[$i]['compare']) && ($link['string'] != $links[$i]['compare'])) {
            // call api function to update our feed item
            if (!xarModAPIFunc('headlines', 'user', 'update', array('hid' => $link['hid'], 'date' => $links[$i]['lastitem'], 'string' => $links[$i]['compare']))) return;
        }
    }


    return true;
}
?>