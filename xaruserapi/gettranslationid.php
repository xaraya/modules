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

function publications_userapi_gettranslationid($args)
{
    if (!isset($args['id'])) throw new BadParameterException('id');
    
    sys::import('xaraya.structures.query');

    $xartable = xarDB::getTables();
    $q = new Query('SELECT',$xartable['publications']);
    $q->eq('locale',xarUserGetNavigationLocale());
    $c[] = $q->peq('id',$args['id']);
    $c[] = $q->peq('parent_id',$args['id']);
    $q->qor($c);
    if (!$q->run()) return $args['id'];
    $result = $q->row();
    if (empty($result)) return $args['id'];
    return $result['id']; 
}
?>