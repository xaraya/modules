<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * get the number of publications per publication type
 * @param $args['state'] array of requested status(es) for the publications
 * @return array array(id => count), or false on failure
 */
function publications_userapi_getpubcount($args)
{
    if (!empty($args['state'])) {
        $statestring = 'all';
    } else if (is_array($args['state'])) {
        sort($args['state']);
        $statestring = join('+',$args['state']);
    } else {
        $statestring = $args['state'];
    }
    
    if (xarVarIsCached('Publications.PubCount',$statestring)) {
        return xarVarGetCached('Publications.PubCount',$statestring);
    }

    $pubcount = array();

    $dbconn = xarDB::getConn();
    $tables = xarDB::getTables();
    $q = new Query('SELECT', $tables['publications']);
    $q->addfield('pubtype_id');
    $q->addfield('COUNT(state) AS count');
    $q->addgroup('pubtype_id');
    if (!empty($args['state'])) {
    } else if (is_array($args['state'])) {
        $q->in('state', $args['state']);
    } else {
        $q->eq('state', $args['state']);
    }
//    $q->qecho();
    if (!$q->run()) return;
    $pubcount = array();
    foreach ($q->output() as $key => $value) $pubcount[$value['pubtype_id']] = $value['count'];
    xarVarSetCached('Publications.PubCount',$statestring,$pubcount);
    return $pubcount;
}

?>
