<?php
/**
 * SiteContact Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * get array of links and counts for sitecontact form types
 * @param $args['scid'] optional form type ID for which you *don't*
 *                      want a link (for the default form)
 * @param $args['all'] optional flag (1) if you want to include publication
 *                     types that don't have articles too (default 0)
 * @param $args['func'] optional function to be called with the link
 * @param $args['count'] true (default) means counting the number of forms
  * @param $args['formids'] optional array of form type ID for which we want responses
 * @return array of array('sctypename' => sctypename,
 *                        'scid' => scid,
 *                        'sclink' => sclink,
 *                        'sccount' => sccount)
 */
function sitecontact_userapi_getitemlinks($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($scid)) {
        $scid = null;
    }
    if (!isset($all)) {
        $all = 0;
    }
    if (!isset($func)) {
        $func = 'view';
    }
    if (!isset($count)) {
        $count = true;
    }
    if (!$count) {
        $all = 1;
    }

    // Get form types

    $sctypes = xarModAPIFunc('sitecontact','user','getcontacttypes');

    if (isset($formids) && is_array($formids) && count($formids)>0) {
        $formids = array_flip($formids);
        $newsctypes = array();
        foreach ($sctypes as $sctype) {
           if (array_key_exists($sctype['scid'],$formids)) {
               $newsctypes[]=$sctype;
           }
       }
        $sctypes = $newsctypes;
    }
    
    if ($count) {
        if (isset($status)) {
            $typecount = xarModAPIFunc('sitecontact','user','countresponseitems',
                                     array('status' => $status));
        } else {
            $typecount = xarModAPIFunc('sitecontact','user','countresponseitems');
        }
    }

    $scformlinks = array();
    $isfirst = 1;
    foreach ($sctypes as $id => $sctype) {

        if (!xarSecurityCheck('ViewSiteContact',0,'ContactForm', "$sctype[scid]:All:All")) {
            continue;
        }
        if ($all || (isset($typecount[$sctype['scid']]) && $typecount[$sctype['scid']] > 0)) {
             $item['sctypename'] = $sctype['sctypename'];
             $item['scid'] = $sctype['scid'];
             if (isset($scid) && $scid == $id) {
                 $item['sclink'] = '';
             } else {
                 $item['sclink'] = xarModURL('sitecontact','admin',$func,array('scid' => $sctype['scid']));
             }
             if ($count && isset($typecount[$sctype['scid']])) {
                 $item['sccount'] = $typecount[$sctype['scid']];
             } else {
                 $item['sccount'] = 0;
             }
             $scformlinks[] = $item;
        }
    }

    return $scformlinks;
}

?>