<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * set an array of root categories
 *
 * @param int $args['ptid'] publication type ID
 * @param array $args['cids'] array of category ids for the root categories
 * @returns bool
 * @return true on success
 */
function articles_adminapi_setrootcats($args)
{
    extract($args);

    if (empty($ptid) || !is_numeric($ptid)) {
        $ptid = null;
    }

    if (empty($cids)) {
        $cids = array();
    } elseif (!is_array($cids)) {
        $newcids = array();
        if (is_numeric($cids)) {
            $newcids[] = intval($cids);
        }
        $cids = $newcids;
    }

    $numcids = count($cids);
    $cidstring = join(';', $cids);

    if (empty($ptid)) {
        xarModVars::set('articles','number_of_categories',$numcids);
        xarModVars::set('articles','mastercids',$cidstring);

        xarMod::apiFunc('categories','admin','setcatbases',
                      array('module' => 'articles',
                            'itemtype' => 0,
                            'cids' => $cids));
    } else {
        xarModVars::set('articles','number_of_categories.'.$ptid,$numcids);
        xarModVars::set('articles','mastercids.'.$ptid,$cidstring);

        xarMod::apiFunc('categories','admin','setcatbases',
                      array('module' => 'articles',
                            'itemtype' => $ptid,
                            'cids' => $cids));
    }

    return true;
}

?>
