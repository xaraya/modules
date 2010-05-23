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
 
 * @author Marc Lutolf <mfl@netspan.ch>
 */
function publications_userapi_gettranslationid($args)
{
    if (!isset($args['id'])) throw new BadParameterException('id');
    
    sys::import('xaraya.structures.query');

    $xartable = xarDB::getTables();
    $q = new Query('SELECT',$xartable['publications']);
    $c[] = $q->peq('id',$args['id']);
    $c[] = $q->peq('parent_id',$args['id']);
    $q->qor($c);
    $q->eq('locale',xarUserGetNavigationLocale());
    if (!$q->run()) return $args['id'];
    $result = $q->row();
    if (empty($result)) return $args['id'];
    return $result['id']; 
}
?>