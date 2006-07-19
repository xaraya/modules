<?php
/**
 * Pre-fetch pages for caching (executed by the scheduler module)
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * This is a poor-man's alternative for using wget in a cron job :
 * wget -r -l 1 -w 2 -nd --delete-after -o /tmp/wget.log http://www.mysite.com/
 *
 * @author mikespub
 * @access private
 */
function xarcachemanager_schedulerapi_prefetch($args)
{
    extract($args);

    // default start page is the homepage
    if (empty($starturl)) {
        $starturl = xarServerGetBaseURL();
    }
    // default is go 1 level deep
    if (!isset($maxlevel)) {
        $maxlevel = 1;
    }
    // default is wait 2 seconds
    if (!isset($wait)) {
        $wait = 2;
    }
    // avoid the current page just in case...
    $avoid = xarServerGetCurrentURL(array(), false);

    $level = 0;
    $seen = array();
    $todo = array($starturl);

    // breadth-first
    while ($level <= $maxlevel && count($todo) > 0) {
        $found = array();
        foreach ($todo as $url) {
            $seen[$url] = 1;

            // get the current page
            $page = xarModAPIFunc('base','user','getfile',
                                  array('url' => $url));
            if (empty($page)) continue;

            // extract local links only (= default)
            $links = xarModAPIFunc('base','user','extractlinks',
                                   array('content' => $page));
            foreach ($links as $link) {
                $found[$link] = 1;
            }

            // wait a while before retrieving the next page
            if (!empty($wait)) {
                sleep($wait);
            }
        }
        $todo = array();
        foreach (array_keys($found) as $link) {
            if (!isset($seen[$link]) && $link != $avoid) {
                $todo[] = $link;
            }
        }
        $level++;
    }

    return true;
}

?>
