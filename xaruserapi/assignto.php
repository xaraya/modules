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
    Finds a Rep to assign this ticket to

    @author Brian McGilligan
    @param $args['cats'] - The Selected Cats.
    @return uid of rep ticket needs to be assigned to
*/
function helpdesk_userapi_assignto($args)
{
    extract($args);

    if( !xarModAPILoad('helpdesk') ){ return false; }

    // Get reps for further processing
    $reps = xarModAPIFunc('dynamicdata', 'user', 'getitems',
        array(
            'module' => 'helpdesk',
            'itemtype' => REPRESENTATIVE_ITEMTYPE
        )
    );

    $cats = xarModAPIFunc('categories', 'user', 'getlinks',
        array(
            'modid'    => xarModGetIdFromName('helpdesk'),
            'itemtype' => REPRESENTATIVE_ITEMTYPE,
            'cids'     => $cids
        )
    );

    $rids = array();
    if(!empty($cats)){
        foreach($cats as $cat){
            foreach($cat as $repid){
                $rids[] = $repid;
            }
        }
    }

    if(empty($rids[0])){ return 0; }

    mt_srand((double)microtime()*100);
    $index = mt_rand(0, (sizeof($rids) - 1));

    foreach($reps as $rep)
    {
        if( $rep['id'] == $rids[$index] ){ return $rep['name']; }
    }
    return 0;
}
?>
