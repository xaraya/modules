<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Given an itemid, get the publication type
 * CHECKME: use get in place of this function?
 */
function publications_userapi_getpubtypeaccess($args)
{
    if (empty($args['name']))
        throw new MissingParameterException('name');
        
    sys::import('xaraya.structures.query');
    $xartables = xarDB::getTables();
    $q = new Query('SELECT',$xartables['publications_types']);
    $q->addfield('access');
    $q->eq('name',$args['name']);
    if (!$q->run()) return;
    $result = $q->row();
    if (empty($result)) return "a:0:{}";
    return $result['access'];
}

?>
