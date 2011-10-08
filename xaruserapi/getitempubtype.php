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
function publications_userapi_getitempubtype($args)
{
    if (empty($args['itemid']))
        throw new MissingParameterException('itemid');
        
    sys::import('xaraya.structures.query');
    $xartables = xarDB::getTables();
    $q = new Query('SELECT',$xartables['publications']);
    $q->addfield('pubtype_id');
    $q->eq('id',$args['itemid']);
    if (!$q->run()) return;
    $result = $q->row();
    if (empty($result)) return 0;
    return $result['pubtype_id'];
}

?>
