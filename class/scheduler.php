<?php
/**
 * Pre-fetch pages for caching (executed by the scheduler module)
 *
 * @package modules\xarcachemanager
 * @subpackage xarcachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\CacheManager;

use xarObject;
use xarMod;
use xarServer;

class CacheScheduler extends xarObject
{
    public static function init(array $args = [])
    {
    }

    /**
     * This is a poor-man's alternative for using wget in a cron job :
     * wget -r -l 1 -w 2 -nd --delete-after -o /tmp/wget.log http://www.mysite.com/
     *
     * @author mikespub
     * @access private
     */
    public static function prefetch($args)
    {
        extract($args);
        $method = __METHOD__;
        $logs = [];
        $logs[] = "$method start";

        // default start page is the homepage
        if (empty($starturl)) {
            $starturl = xarServer::getBaseURL();
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
        $avoid = xarServer::getCurrentURL([], false);

        $level = 0;
        $seen = [];
        $todo = [$starturl];

        // breadth-first
        while ($level <= $maxlevel && count($todo) > 0) {
            $found = [];
            foreach ($todo as $url) {
                $seen[$url] = 1;

                $logs[] = "$method get $url";
                // get the current page
                $page = xarMod::apiFunc(
                    'base',
                    'user',
                    'getfile',
                    ['url' => $url]
                );
                if (empty($page)) {
                    continue;
                }

                // extract local links only (= default)
                $links = xarMod::apiFunc(
                    'base',
                    'user',
                    'extractlinks',
                    ['content' => $page]
                );
                foreach ($links as $link) {
                    $found[$link] = 1;
                }

                // wait a while before retrieving the next page
                if (!empty($wait)) {
                    sleep($wait);
                }
            }
            $todo = [];
            foreach (array_keys($found) as $link) {
                if (!isset($seen[$link]) && $link != $avoid) {
                    $todo[] = $link;
                }
            }
            $level++;
        }
        $logs[] = "$method stop";

        return implode("\n", $logs);
    }
}
