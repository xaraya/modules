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
 * @author mikespub
 */
/**
 * display keywords entry
 *
 * @param $args['itemid'] item id of the keywords entry
 * @return array Item
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_user_cloud($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return;

//    $keywords = xarmodapifunc('keywords','user','getlist',array('count'=>'1'));
    
//    var_dump($keywords);exit;
    
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
    
    $tags = array();
    foreach ($q->output() as $tag) $tags[$tag['keyword']] = $tag['hits'];
    
    $data['tags'] = $tags;   
    return $data;
}

?>
