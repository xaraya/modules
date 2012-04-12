<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function publications_userapi_gettranslationid($args)
{
    if (!isset($args['id'])) throw new BadParameterException('id');
    if (empty($args['id'])) return 0;
    
    // We can check on a full locale or just a partial one (excluding charset)
    if (empty($args['partiallocale'])) $args['partiallocale'] = 0;
    // We can look for a specific translation
    if (empty($args['locale'])) {
        $locale = xarUserGetNavigationLocale();
    } else {
        $locale = $args['locale'];
    }

    sys::import('xaraya.structures.query');
    
    if ($args['partiallocale']) {
        $parts = explode('.',$locale);
        $locale = $parts[0];
    }

    $xartable = xarDB::getTables();
    
    if (empty($args['locale'])) {
        // Return the id of the translation if it exists, or else the base document 
        $q = new Query('SELECT',$xartable['publications']);
        $q->addfield('id');
        $q->eq('locale',$locale);
        $c[] = $q->peq('id',$args['id']);
        $c[] = $q->peq('parent_id',$args['id']);
        $q->qor($c);
        if (!$q->run()) return $args['id'];
        $result = $q->row();
        if (empty($result)) return $args['id'];
        return $result['id']; 
    } elseif ($args['locale'] == xarUserGetNavigationLocale()) {
        // No need to look further
        return $args['id'];
    } elseif ($args['locale'] == xarModVars::get('publications', 'defaultlanguage')) {
        // Force getting the base document
        $q = new Query('SELECT',$xartable['publications']);
        $q->addfield('parent_id');
        $q->eq('id',$args['id']);
        if (!$q->run()) return $args['id'];
        $result = $q->row();
        if (empty($result)) return $args['id'];
        // If this was already the base document, return its ID
        if (empty($result['parent_id'])) return $args['id'];
        // Else return the parent ID
        return $result['parent_id']; 
    } else {
        // Force getting another translation
        $q = new Query('SELECT');
        $q->addtable($xartable['publications'],'p1');
        $q->addtable($xartable['publications'],'p2');
        $q->join('p2.parent_id','p1.parent_id');
        $q->addfield('p2.id');
        $q->eq('p2.locale',$locale);
        $q->eq('p1.id',$args['id']);
        if (!$q->run()) return $args['id'];
        $result = $q->row();
        if (empty($result)) return $args['id'];
        return $result['id']; 
    }


if (xarUserGetVar('uname') == 'random') {
    $xartable = xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($xartable['publications'],'p1');
    $q->addtable($xartable['publications'],'p2');
    $q->join('p2.id','p1.parent_id');
    $q->addfield('p1.id');
    $c[] = $q->peq('p1.id',$args['id']);
    $c[] = $q->peq('p1.parent_id',$args['id']);
    $c[] = $q->peq('p2.id',$args['id']);
    $q->qor($c);
    $d[] = $q->peq('p1.locale',$args['locale']);
    $d[] = $q->peq('p2.locale',$args['locale']);
    $q->qor($d);
    if (!$q->run()) return $args['id'];
    $q->qecho();
    $result = $q->row();
    if (empty($result)) return $args['id'];
    return $result['id']; 
    }
}
?>