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
/**
 * get previous publication
 * Note : the following parameters are all optional (except id and ptid)
 *
 * @param $args['id'] the publication ID we want to have the previous publication of
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param $args['sort'] sort order ('date','title','hits','rating',...)
 * @param $args['owner'] the ID of the author
 * @param $args['state'] array of requested status(es) for the publications
 * @param $args['enddate'] publications published before enddate
 *                         (unix timestamp format)
 * @return array of publication fields, or false on failure
 */
function publications_userapi_getprevious($args)
{
    // Security check
    if (!xarSecurityCheck('ViewPublications')) return;

    // Get arguments from argument array
    extract($args);

    // Optional argument
    if (empty($ptid)) $ptid = xarModVars::get('publications', 'defaultpubtype');
    if (empty($sort)) $sort = 'date';
    if (!isset($state)) {
        // frontpage or approved or placeholder
        xarMod::load('publications');
        $state = array(PUBLICATIONS_STATE_ACTIVE,PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_PLACEHOLDER);
    }

    // Default fields in publications (for now)
    $fields = array('id','name','title');

    // Create the query
    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
    $q = new Query('SELECT', $tables['publications']);
    $q->addfield('id');
    $q->addfield('name');
    $q->addfield('title');
    $q->addfield('pubtype_id');
    $q->in('state', $state);
    
    // Get the current article
    $current = xarMod::apiFunc('publications','user','get',array('id' => $id));

    // Add the ordering
    switch($sort) {
    case 'tree':
        $q->lt('rightpage_id', (int)$current['leftpage_id']);
        $q->setorder('rightpage_id', 'DESC');
        break;
    case 'id':
        $q->eq('pubtype_id', $ptid);
        $q->lt('id', (int)$current['id']);
        $q->setorder('id', 'DESC');
        break;
    case 'name':
        $q->eq('pubtype_id', $ptid);
        $q->lt('name', $current['name']);
        $q->setorder('name', 'DESC');
    case 'title':
        $q->eq('pubtype_id', $ptid);
        $q->lt('title', $current['title']);
        $q->setorder('title', 'DESC');
        break;
    case 'date':
    default:
        $q->eq('pubtype_id', $ptid);
        $q->lt('start_date', (int)$current['start_date']);
        $q->setorder('start_date', 'DESC');
    }

    // We only want a single row
    $q->setrowstodo(1);
    
    // Run the query
    $q->run();
    return $q->row();

    // TODO: grab categories & check against them too

    // check security - don't generate an exception here
    if (!xarSecurityCheck('ViewPublications',0,'Publication',"$item[pubtype_id]:All:$item[owner]:$item[id]")) {
        return array();
    }

    return $item;
}

?>