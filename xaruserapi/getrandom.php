<?php

/**
 * get random article(s)
 * Note : the following parameters are all optional
 *
 * @param $args['numitems'] number of articles to get
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param $args['status'] array of requested status(es) for the articles
 * @param $args['cids'] array of category IDs for which to get articles (OR/AND)
 *                      (for all categories don´t set it)
 * @param $args['andcids'] true means AND-ing categories listed in cids
 * @param $args['fields'] array with all the fields to return per article
 *                        Default list is : 'aid','title','summary','authorid',
 *                        'pubdate','pubtypeid','notes','status','body'
 *                        Optional fields : 'cids','author','counter','rating','dynamicdata'
 * @param $args['language'] language/locale (if not using multi-sites, categories etc.)
 * @returns array
 * @return array of articles, or false on failure
 */
function articles_userapi_getrandom($args)
{
    // 1. count the number of items that apply
    $count = xarModAPIFunc('articles','user','countitems',$args);
    if (empty($count)) {
        return array();
    }

    // 2. retrieve numitems random articles
    if (empty($args['numitems'])) {
        $numitems = 1;
    } else {
        $numitems = $args['numitems'];
    }
    
    if (empty($args['unique'])) {
        $args['unique'] = false;
    }
    
    $articles = array();
    mt_srand((double) microtime() * 1000000);
    
    if ($count <= $numitems) {
        unset($args['numitems']);
        // retrieve all articles and randomize the order
        $items = xarModAPIFunc('articles','user','getall',$args);
        $randomkeys = array_rand($items, $count);
        if (!is_array($randomkeys)) {
            $randomkeys = array($randomkeys);
        }
        foreach ($randomkeys as $key) {
            array_push($articles, $items[$key]);
        }
    } else {
        // retrieve numitems x 1 random article
        $args['numitems'] = 1;
        for ($i = 0; $i < $numitems; $i++) {
            $args['startnum'] = mt_rand(1, $count);
            
            if ($args['unique']) {
                if (in_array($args['startnum'], $articles)) {
                    $i--;
                    continue;
                }
            }
            $items = xarModAPIFunc('articles','user','getall',$args);
            if (empty($items)) break;
            array_push($articles, array_pop($items));
        }
    }
    
    
    
    return $articles;
}

?>
