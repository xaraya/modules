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
    switch ($args['ptid']) {
        case 1 : $namestring = 'new'; break;    // news
        case 2 : $namestring = 'doc'; break;    // document
        case 3 : $namestring = 'rev'; break;    // review
        case 4 : $namestring = 'faq'; break;    // FAQ
        case 1 : $namestring = 'pic'; break;    // picture
        case 6 : $namestring = 'web'; break;    // web page
        case 7 : $namestring = 'quo'; break;    // quote
        case 8 : $namestring = 'dow'; break;    // download
        case 9 : $namestring = 'tra'; break;    // translation
        case 10: $namestring = 'gen'; break;    // generic
        case 11: $namestring = 'blo'; break;    // blog
        case 12: $namestring = 'cat'; break;    // catalogue
        case 13: $namestring = 'eve'; break;    // event
        default:
            $namestring = xarMod::apiFunc('publications','user','getsetting', array('ptid' => $args['ptid'], 'setting' => 'namestring'));
        break;
    }

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