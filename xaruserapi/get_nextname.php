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
 
 */
/**
 * Get a default name for the current document
 *
 * @param $args['ptid'] int publication type ID (optional) OR
 * @param $args['name'] string publication type name (optional)
 * @return array(id => array('name' => name, 'description' => description)), or false on
 *         failure
 */
  
function publications_userapi_get_nextname($args)
{
    if (empty($args['ptid'])) return xarML('new_publication');
    
    // Get the namestring for this pubtype
    $namestring = xarMod::apiFunc('publications','user','getsetting', array('ptid' => $args['ptid'], 'setting' => 'namestring'));

    // Get the number of publications of this pubtype and increment by 1
    sys::import('xaraya.structures.query');
    $tables =& xarDB::getTables();
    $q = new Query('SELECT', $tables['publications']);
    $q->eq('pubtype_id', $args['ptid']);
    $q->addfield('COUNT(*)');
    $q->run();
    $count = $q->row();
    $count = (int)reset($count);
    $count++;
    
    // Put them together
    if (!empty($namestring)) $namestring .= "_";
    $namestring .= $count;
    return $namestring;
}

?>