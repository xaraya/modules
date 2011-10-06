<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */
function keywords_userapi_getkeywordhits($args)
{    
    sys::import('xaraya.structures.query');
   
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $q = new Query('SELECT');
    $q->addtable($xartable['keywords'],'k');
    $q->addfield('k.keyword AS keyword');
    $q->addfield('COUNT(k.id) AS count');
    
    if (xarModIsAvailable('hitcount')) {
        xarModAPILoad('hitcount');
        $xartable =& xarDB::getTables();
        $q->addtable($xartable['hitcount'],'h');
        $q->join('k.module_id','h.module_id');
        $q->join('k.itemtype','h.itemtype');
        $q->join('k.itemid','h.itemid');
        $q->addfield('SUM(h.hits) AS hits');
    }
    $q->addgroup('k.keyword');
    $q->addorder('k.keyword','ASC');
    $q->run();
    
    // Reorganize to an array where the keywords are keys
    $tags = array();
    foreach ($q->output() as $tag) $tags[$tag['keyword']] = $tag['hits'];
    
    return $tags;
}

?>
