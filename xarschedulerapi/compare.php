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
    $links  = xarModAPIFunc('headlines', 'user', 'getall');
    foreach ($links as $link){
        // We need to grab the current url right now for the string and the date
        // Get the feed file (from cache or from the remote site)
        $filedata = xarModAPIFunc('base', 'user', 'getfile',
                                  array('url'       =>  $link['url'],
                                        'cached'    =>  false));

        $compare['string']     = md5($filedata);
        $compare['date']       = time();

        if ($compare['string'] != $link['string']){
            // Get datbase setup
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $headlinestable = $xartable['headlines'];

            // Update the link
            $query = "UPDATE $headlinestable
                      SET xar_string   = ?,
                          xar_date     = ?
                      WHERE xar_hid     = ?";
            $bindvars = array($compare['string'], $compare['date'], $link['hid']);
            $result =& $dbconn->Execute($query,$bindvars);
            if (!$result) return;

            // Require the xmlParser class
            require_once('modules/base/xarclass/xmlParser.php');

            // Require the feedParser class
            require_once('modules/base/xarclass/feedParser.php');

            // Now that the compare is done, we need to actually update the list.
            // Create a need feedParser object
            $p = new feedParser();

            // Tell feedParser to parse the data
            $info = $p->parseFeed($filedata);

            if (empty($info['warning'])){
                foreach ($info as $content){
                $content = array_slice($content, 0, 1);
                     foreach ($content as $newline){
                            if(is_array($newline)) {
                                if (isset($newline['description'])){
                                    $description = $newline['description'];
                                } else {
                                    $description = '';
                                }
                                if (isset($newline['title'])){
                                    $title = $newline['title'];
                                } else {
                                    $title = '';
                                }
                                if (isset($newline['link'])){
                                    $link = $newline['link'];
                                } else {
                                    $link = '';
                                }

                                $imports[] = array('title' => $title, 'link' => $link, 'description' => $description);
                        }
                    }
                }
                // E_ALL Check for an empty array or insert latest search.
                $oldsearch = xarModGetVar('headlines', 'rsscloud');
                // Now comes the fun part.  Updating our modvar with the lastes stuff.  Probably needs a var to capture the number of items to store.
                foreach ($imports as $import){
                    $content = array();
                    // A little more complicated than the first search.  We need to get what's out
                    // there first so we can process it.
                    $oldsearch = unserialize($oldsearch);
                    $searchitems = array();
                    // Similar to what we are doing to display, only we are just creating a single
                    // entity of the old search terms.
                    $searchlines = explode("LINESPLIT", $oldsearch);
                    foreach ($searchlines as $searchline) {
                        $link = explode('|', $searchline);
                        $content[] .= $link[0] . '|' . $link[1] . '|' . $link[2];
                    }
                    // Now we are just processing the new search terms.
                    $content[] .= $import['title'] . '|' . $import['link'] . '|' . $info['channel']['title'];
                    // While we are in a readible array, we might as well pop it now.
                    $searchnum = count($content);
                    if ($searchnum >= 10) {
                        $dropsearch = array_shift($content);
                    }
                    $newsearch = implode("LINESPLIT", $content);
                    $newsearch = serialize($newsearch);
                    xarModSetVar('headlines', 'rsscloud', $newsearch);
                }
            }
        }
    }
    return true;
}
?>