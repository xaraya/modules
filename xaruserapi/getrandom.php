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
 * Get Random Publication(s)
 *
 * Note : the following parameters are all optional
 * @author Michel Dalle <mikespub@xaraya.com>
 * @param int    $args['numitems'] number of publications to get
 * @param int    $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param array  $args['state'] array of requested status(es) for the publications
 * @param array  $args['cids'] array of category IDs for which to get publications (OR/AND)
 *                      (for all categories don?t set it)
 * @param bool   $args['andcids'] true means AND-ing categories listed in cids
 * @param array  $args['fields'] array with all the fields to return per article
 *                        Default list is : 'id','title','summary','owner',
 *                        'pubdate','pubtype_id','notes','state','body'
 *                        Optional fields : 'cids','author','counter','rating','dynamicdata'
 * @param string $args['locale'] language/locale (if not using multi-sites, categories etc.)
 * @param bool   $args['unique'] return unique results
 * @return array of publications, or false on failure
 */
function publications_userapi_getrandom($args)
{
    // 1. count the number of items that apply
    $count = xarModAPIFunc('publications','user','countitems',$args);
    if (empty($count)) {
        return array();
    }

    // 2. retrieve numitems random publications
    if (empty($args['numitems'])) {
        $numitems = 1;
    } else {
        $numitems = $args['numitems'];
    }

    $idlist = array();
    if (empty($args['unique'])) {
        $args['unique'] = false;
    } else {
        $args['unique'] = true;
    }

    $publications = array();
    mt_srand((double) microtime() * 1000000);

    if ($count <= $numitems) {
        unset($args['numitems']);
        // retrieve all publications and randomize the order
        $items = xarModAPIFunc('publications','user','getall',$args);
        $randomkeys = array_rand($items, $count);
        if (!is_array($randomkeys)) {
            $randomkeys = array($randomkeys);
        }
        foreach ($randomkeys as $key) {
            array_push($publications, $items[$key]);
        }
    } else {
        // retrieve numitems x 1 random article
        $args['numitems'] = 1;

        for ($i = 0; $i < $numitems; $i++) {
            $args['startnum'] = mt_rand(1, $count);

            if ($args['unique'] && in_array($args['startnum'], $idlist)) {
                $i--;
            } else {
                $idlist[] = $args['startnum'];
                $items = xarModAPIFunc('publications','user','getall',$args);
                if (empty($items)) break;
                array_push($publications, array_pop($items));
            }
        }
    }

    return $publications;
}

?>
