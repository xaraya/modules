<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    $count = xarMod::apiFunc('publications', 'user', 'countitems', $args);
    if (empty($count)) {
        return [];
    }

    // 2. retrieve numitems random publications
    if (empty($args['numitems'])) {
        $numitems = 1;
    } else {
        $numitems = $args['numitems'];
    }

    $idlist = [];
    if (empty($args['unique'])) {
        $args['unique'] = false;
    } else {
        $args['unique'] = true;
    }

    $publications = [];
    mt_srand((float) microtime() * 1000000);

    if ($count <= $numitems) {
        unset($args['numitems']);
        // retrieve all publications and randomize the order
        $items = xarMod::apiFunc('publications', 'user', 'getall', $args);
        $randomkeys = array_rand($items, $count);
        if (!is_array($randomkeys)) {
            $randomkeys = [$randomkeys];
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
                $items = xarMod::apiFunc('publications', 'user', 'getall', $args);
                if (empty($items)) {
                    break;
                }
                array_push($publications, array_pop($items));
            }
        }
    }

    return $publications;
}
