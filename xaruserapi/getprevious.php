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
 
 * @author mikespub
 */
/**
 * get previous article
 * Note : the following parameters are all optional (except id and ptid)
 *
 * @param $args['id'] the article ID we want to have the previous article of
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param $args['sort'] sort order ('date','title','hits','rating',...)
 * @param $args['owner'] the ID of the author
 * @param $args['state'] array of requested status(es) for the publications
 * @param $args['enddate'] publications published before enddate
 *                         (unix timestamp format)
 * @return array of article fields, or false on failure
 */
function publications_userapi_getprevious($args)
{
    // Get arguments from argument array
    extract($args);

    // Optional argument
    if (empty($sort)) {
        $sort = 'date';
    }
    if (!isset($state)) {
        // frontpage or approved
        $state = array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED);
    }

    // Default fields in publications (for now)
    $fields = array('id','title');

    // Security Check
    if (!xarSecurityCheck('ViewPublications')) return;

    // Database information
    $dbconn = xarDB::getConn();

    // Get the field names and LEFT JOIN ... ON ... parts from publications
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the publications-specific columns too now
    $publicationsdef = xarModAPIFunc('publications','user','leftjoin',$args);

    // Create the query base query
    $query = "SELECT $publicationsdef[id], $publicationsdef[title], $publicationsdef[pubtype_id], $publicationsdef[owner]
                FROM $publicationsdef[table] WHERE ";

    // we rely on leftjoin() to create the necessary publications clauses now
    if (!empty($publicationsdef['where'])) {
        $query .= " $publicationsdef[where] AND ";
    }

    // Get the current article
    $current = xarModAPIFunc('publications','user','get',array('id' => $id));

    // Create the ORDER BY part
    switch($sort) {
    case 'title':
        $query .= $publicationsdef['title'] . ' < ' . $dbconn->qstr($current['title']) . ' ORDER BY ' . $publicationsdef['title'] . ' DESC, ' . $publicationsdef['id'] . ' DESC';
        break;
    case 'id':
        $query .= $publicationsdef['id'] . ' < ' . $current['id'] . ' ORDER BY ' . $publicationsdef['id'] . ' DESC';
        break;
    case 'date':
    default:
        $query .= $publicationsdef['pubdate'] . ' < ' . $dbconn->qstr($current['pubdate']) . ' ORDER BY ' . $publicationsdef['pubdate'] . ' DESC, ' . $publicationsdef['id'] . ' DESC';
    }


    // Run the query - finally :-)
    $result =& $dbconn->SelectLimit($query, 1, 0);

    if (!$result) return;

    $item = array();
    list($item['id'],$item['title'],$item['pubtype_id'],$item['owner']) = $result->fields;

    $result->Close();

    // TODO: grab categories & check against them too

    // check security - don't generate an exception here
    if (!xarSecurityCheck('ViewPublications',0,'Publication',"$item[pubtype_id]:All:$item[owner]:$item[id]")) {
        return array();
    }

    return $item;
}

?>
