<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function keywords_userapi_getkeywordhits($args)
{
    if (!isset($args['cloudtype'])) {
        $args['cloudtype'] = 3;
    }

    // Return nothing if we asked for hits and the hitcount module is not available
    if ($args['cloudtype'] == 1 && !xarMod::isAvailable('hitcount')) {
        return [];
    }

    sys::import('xaraya.structures.query');

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    $q = new Query('SELECT');
    $q->addtable($xartable['keywords_index'], 'i');
    $q->addtable($xartable['keywords'], 'k');
    $q->join('i.keyword_id', 'k.id');
    $q->addfield('k.keyword AS keyword');
    $q->addfield('COUNT(i.id) AS count');

    if ($args['cloudtype'] == 2) {
        xarMod::apiLoad('hitcount');
        $xartable =& xarDB::getTables();
        $q->addtable($xartable['hitcount'], 'h');
        $q->join('k.module_id', 'h.module_id');
        $q->join('k.itemtype', 'h.itemtype');
        $q->join('k.itemid', 'h.itemid');
        $q->addfield('SUM(h.hits) AS hits');
    }
    $q->addgroup('k.keyword');
    $q->addorder('k.keyword', 'ASC');
    $q->optimize = false;
    $q->run();
    $result = $q->output();

    // Reorganize to an array where the keywords are keys
    $tags = [];
    if ($args['cloudtype'] == 2) {
        foreach ($result as $tag) {
            $tags[$tag['keyword']] = $tag['hits'];
        }
    } else {
        foreach ($result as $tag) {
            $tags[$tag['keyword']] = $tag['count'];
        }
    }
    return $tags;
}
