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
 * get array of links and counts for publication types
 * @param $args['ptid'] optional publication type ID for which you *don't*
 *                      want a link (e.g. for the current publication type)
 * @param $args['all'] optional flag (1) if you want to include publication
 *                     types that don't have publications too (default 0)
 * @param $args['state'] array of requested status(es) for the publications
 * @param $args['func'] optional function to be called with the link
 * @param $args['count'] true (default) means counting the number of publications
 * @return array of array('pubtitle' => descr,
 *                        'pubid' => id,
 *                        'publink' => link,
 *                        'pubcount' => count)
 */
function publications_userapi_getpublinks($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($ptid)) {
        $ptid = null;
    }
    if (!isset($all)) {
        $all = 0;
    }
    if (!isset($func)) {
        $func = 'view';
    }
    if (!isset($typemod)) {
        $typemod = 'user';
    }
    if (!isset($count)) {
        $count = true;
    }
    if (!isset($state)) {
        $state = array(0);
    }
    if (!$count) {
        $all = 1;
    }

    // Get publication types
    $pubtypes = xarModAPIFunc('publications','user','getpubtypes');

    if ($count) {
        if (isset($state)) {
            $pubcount = xarModAPIFunc('publications','user','getpubcount',
                                     array('state' => $state));
        } else {
            $pubcount = xarModAPIFunc('publications','user','getpubcount');
        }
    }

    $publinks = array();
    $isfirst = 1;
    foreach ($pubtypes as $id => $pubtype) {
        if (!xarSecurityCheck('ViewPublications',0,'Publication',$id.':All:All:All')) {
            continue;
        }
        if ($all || (isset($pubcount[$id]) && $pubcount[$id] > 0)) {
             $item['pubtitle'] = $pubtype['description'];
             $item['pubid'] = $id;
             if (isset($ptid) && $ptid == $id) {
                 $item['publink'] = '';
             } else {
                 $item['publink'] = xarModURL('publications',$typemod,$func,array('ptid' => $id));
             }
             if ($count && isset($pubcount[$id])) {
                 $item['pubcount'] = $pubcount[$id];
             } else {
                 $item['pubcount'] = 0;
             }
             if ($isfirst) {
                 $isfirst = 0;
                 $item['pubjoin'] = '';
             } else {
                 $item['pubjoin'] = ' - ';
             }
             $publinks[] = $item;
        }
    }

    return $publinks;
}

?>
