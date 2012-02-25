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
    extract($args);
    if (empty($args['itemid'])) $args['itemid'] = 0;
    if (empty($args['scope'])) $args['scope'] = 'siblings';

    $xartable = xarDB::getTables();
    sys::import('xaraya.structures.query');
    $q = new Query();
    $q->addtable($xartable['publications'],'p');
    
    switch ($args['scope']) {
        case 'children': 
            $q->eq('p.parent_id', $args['itemid']);
            $q->addfield('p.id');
            $q->addfield('p.name');
            $q->addfield('p.title');
            $q->addfield('p.description');
            $q->addfield('p.summary');
        break;
        case 'siblings':
            $q->addtable($xartable['publications'],'p1');
            $q->join('p.parent_id', 'p1.parent_id');
            $q->eq('p.id', $args['itemid']);
            $q->gt('p1.state', 2);
            $q->addfield('p1.id');
            $q->addfield('p1.name');
            $q->addfield('p1.title');
            $q->addfield('p1.description');
            $q->addfield('p1.summary');
        break;
    }
//    $q->qecho();
    $q->run();
    $result = $q->output();
    return $result;
}
?>