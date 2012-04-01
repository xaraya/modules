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
 * Get pages relative to a given page
 *
 */

function publications_userapi_getrelativepages($args)
{
    if (empty($args['itemid'])) $args['itemid'] = 0;
    if (empty($args['scope'])) $args['scope'] = 'siblings';
    if (empty($args['sort'])) $args['sort'] = 0;

    // Make sure we have the base translation id
    if (!empty($args['itemid'])) {
        $args['itemid'] = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $args['itemid'], 'locale' => xarModVars::get('publications', 'defaultlanguage')));
    }

    $xartable = xarDB::getTables();
    sys::import('xaraya.structures.query');
    $q = new Query();
    $q->addtable($xartable['publications'],'p');
    
    switch ($args['scope']) {
        case 'descendants':
            $q->addtable($xartable['publications'],'root');
            $q->eq('root.id', $args['itemid']);
            $q->le('root.leftpage_id','expr:p.leftpage_id');
            $q->ge('root.rightpage_id','expr:p.rightpage_id');
        /*
            $q->eq('member.id', $args['itemid']);
            $q->addtable($xartable['publications'],'member');
            $q->addtable($xartable['publications'],'root');
            $q->le('root.leftpage_id','expr:member.leftpage_id');
            $q->ge('root.rightpage_id','expr:member.rightpage_id');
            $q->gt('p.leftpage_id','expr:root.leftpage_id');
            $q->lt('p.rightpage_id','expr:root.rightpage_id');
            */
        break;
        case 'children': 
            $q->eq('p.parentpage_id', $args['itemid']);
        break;
        case 'siblings':
            $q->addtable($xartable['publications'],'p1');
            $q->join('p.parentpage_id', 'p1.parentpage_id');
            $q->eq('p1.id', $args['itemid']);
        break;
    }
    if (!empty($args['itemtype'])) $q->eq('p.pubtype_id', $args['itemtype']);
    $q->gt('p.state', 2);
    $q->addfield('p.id AS id');
    $q->addfield('p.name AS name');
    $q->addfield('p.title AS title');
    $q->addfield('p.description AS description');
    $q->addfield('p.summary AS summary');
    
    // We can force alpha sorting, or else sort according to tree position
    if($args['sort']) {
        $q->setorder('p.title');
    } else {
        $q->setorder('p.leftpage_id');
    }
//    $q->qecho();
    $q->run();
    $result = $q->output();
    
    // If we are looking for translations rather than base documents, then find what translations are available and substitute them
    // CHECKME: is there a better way?
    // If there is no translation the base document remains. Is this desired outcome?
    
    if (!empty($result) && xarModVars::get('publications', 'defaultlanguage') != xarUserGetNavigationLocale()) {
        $indexedresult = array();
        foreach ($result as $v) $indexedresult[$v['id']] = $v;
        $ids = array_keys($indexedresult);
        
        $q = new Query();
        $q->addtable($xartable['publications']);
        $q->addfield('id');
        $q->addfield('parent_id');
        $q->addfield('name');
        $q->addfield('title');
        $q->addfield('description');
        $q->addfield('summary');
        $q->in('parent_id',$ids);
        $q->run();
        foreach ($q->output() as $row) $indexedresult[$row['parent_id']] = $row;
        $result = $indexedresult;
    }
    return $result;
}
?>