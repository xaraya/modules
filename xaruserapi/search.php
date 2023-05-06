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
 * @author Alberto Cazzaniga (Janez)
 */
/**
 * Perform the search
 * @return array with keys to keywords
 */
function keywords_userapi_search($args)
{
    if (!xarSecurity::check('ReadKeywords')) return;

    if (empty($args) || count($args) < 1)  return;

    extract($args);
    if($args['search'] == '') return;
    
    // If there is more than one keyword passed, separate them
    $words = xarMod::apiFunc('keywords', 'admin', 'separatekeywords', array('keywords' => $args['search']));

    // Get item
    sys::import('xaraya.structures.query');
    $tables =& xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($tables['keywords'], 'k');
    $q->addtable($tables['keywords_index'], 'i');
    $q->join('k.id', 'i.keyword_id');
    $q->addfield('k.keyword AS keyword');
    $q->addfield('i.module_id AS module_id');
    $q->addfield('i.itemtype AS itemtype');
    $q->addfield('i.itemid AS itemid');
    $q->addfield('COUNT(i.id) AS count');
    $a = array();
    foreach ($words as $word) {
        $a[] = $q->plike('keyword', "%" . $word . "%");
    }
    $q->qor($a);
    $q->setgroup('keyword');
    $q->addorder('keyword','ASC');
    $q->optimize = false;
    $q->run();
    $result = $q->output();

    return $result;

}
?>
