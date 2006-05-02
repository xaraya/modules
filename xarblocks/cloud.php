<?php
/**
 * Displays an RSS Display.
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
 * @author RevJim (revjim.net), John Cox
 * @todo Make the admin selectable number of headlines work.
 * @todo show search and image of rss site
 */
/**
 * Block init - holds security.
 */
function headlines_cloudblock_init()
{
    return array(
        'rssurl' => '',
        'maxitems' => 5,
        'showdescriptions' => false
    );
}
/**
 * Block info array
 */
function headlines_cloudblock_info()
{
    return array(
        'text_type' => 'RSS Cloud',
        'text_type_long' => 'RSS Cloud',
        'module' => 'headlines',
        'func_update' => 'headlines_cloudblock_insert',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}
/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function headlines_cloudblock_display($blockinfo)
{
    // In order to have the list up to date, we need to call the var again.
    // Otherwise the search term is one off of the searches.
    $search = xarModGetVar('headlines', 'rsscloud');
    $feedcontent=array();
    if (empty($search)){
        $insert['title'] = 'None Configured';
        $insert['link'] = 'None Configured';
        $insert['channel']  = 'None Configured';
        //We are simply throwing something into the modvar so we don't get ugly errors.
        // This is really only run once.  TODO, throw this in the init and upgrade for
        // The search to remove this processing.
        $firstsearch = $insert['title'] . '|' . $insert['link'] . '|' . $insert['channel'];
        $firstsearch = serialize($firstsearch);
        xarModSetVar('headlines', 'rsscloud', $firstsearch);
    } else {
        // Lets Prep It All For Display Now.
        $search = unserialize($search);
        $searchitems = array();
        if (!empty($search)) {
            $searchlines = explode("LINESPLIT", $search);
            foreach ($searchlines as $searchline) {
                $link = explode('|', $searchline);
                $title = xarVarPrepForDisplay($link[0]);
                $url = xarVarPrepForDisplay($link[1]);
                $channel  = xarVarPrepForDisplay($link[2]);
                $feedcontent[] = array('title' => $title, 'url' => $url, 'channel' => $channel);
            }
        }
    }
    $blockinfo['content'] = array(
        'feedcontent'  => $feedcontent
    );
    return $blockinfo;
}
?>