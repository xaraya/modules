<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @return array containing the itemlink(s) for the item(s).
 */
function categories_userapi_getitemlinks($args)
{
    $itemlinks = array();
    $catlist = xarModAPIFunc('categories','user','getcatinfo',
                             array('cids' => $args['itemids']));
    if (!isset($catlist) || !is_array($catlist) || count($catlist) == 0) {
       return $itemlinks;
    }

    foreach ($args['itemids'] as $itemid) {
        if (!isset($catlist[$itemid])) continue;
        if ((trim($catlist[$itemid]['description']) =='') && (xarModGetVar('categories','usename')== TRUE)) {
           $catlist[$itemid]['description']=$catlist[$itemid]['name'];
        }
        $itemlinks[$itemid] = array('url'   => xarModURL('categories', 'user', 'main',
                                                         array('catid' => $itemid)),
                                            'title' => xarVarPrepForDisplay($catlist[$itemid]['name']),
                                            'label' => xarVarPrepForDisplay($catlist[$itemid]['description']));
    }
    return $itemlinks;
}

?>
