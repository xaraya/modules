<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * get entries for a module item
 *
 * @param int $args['modid'] module id
 * @param int $args['itemtype'] item type
 * @param int $args['itemid'] item id
 * @param int $args['numitems'] number of entries to retrieve (optional)
 * @param int $args['startnum'] starting number (optional)
 * @return array of keywords
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @todo This is so similar to getitems, that they could be merged. It is only the format of the results that differs.
 */
function keywords_userapi_getwords($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return;

    extract($args);

    if (!isset($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid #(1)', 'module id');
        throw new Exception($msg);
        return;
    }
    if (!isset($itemid) || !is_numeric($itemid)) {
        $msg = xarML('Invalid #(1)', 'item id');
        throw new Exception($msg);
        return;
    }

    $table =& xarDB::getTables();
    $q = new Query('SELECT', $table['keywords_index']);
    $q->addtable($table['keywords'], 'k');
    $q->addtable($table['keywords_index'], 'i');
    $q->join('k.keyword_id', 'i.id');
    $q->addfield('k.id AS id');
    $q->addfield('i.keyword AS keyword');
    $q->eq('k.module_id', $modid);
    $q->eq('k.itemid', $itemid);
    if (!empty($itemtype)) {
        if (is_array($itemtype)) {
            $q->in('k.itemtype', $itemtype);
        } else {
            $q->eq('k.itemtype', (int)$itemtype);
        }
    }
    $q->addorder('keyword', 'ASC');
//    $q->qecho();
    $q->run();
    $words = $q->output();

    return $words;
}

?>