<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
    Gets categories
    @author Brian McGilligan
*/
function helpdesk_userapi_cats($args)
{
    extract($args);

    $cats = xarModAPIFunc('categories', 'user', 'getchildren',
                          array('cids' => $cids)
                         );

    if(!$cats) { return; }

    $l = "";
    if(!empty($cats) && is_array($cats)){
        foreach($cats as $cat){
            if($cat['cid'] == $tcid){ return $cat['name']; }
            $temp = helpdesk_userapi_cats(array('cids' => $cat['cid'],
                                                'tcid' => $tcid));
            if($temp){ $l = $cat['name'] . "->" . $temp; }
        }
    }
    return $l;
}
?>
