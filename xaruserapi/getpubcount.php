<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
        return xarCoreCache::getCached('Publications.PubCount',$statestring);
    }

    $pubcount = array();

    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    sys::import('xaraya.structures.query');
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
    xarCoreCache::setCached('Publications.PubCount',$statestring,$pubcount);
    return $pubcount;
}

?>
