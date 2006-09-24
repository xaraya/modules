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
 * Finds a Rep to assign this ticket to
 *
 *  @author Brian McGilligan
 *  @param $args['cids'] - The selected categories.
 *  @param $args['company'] - A possible selected compnay.
 *  @return array of uid that can be assigned to the cats and/or company
 */
function helpdesk_userapi_get_assignable_reps($args)
{
    extract($args);

    // Get reps for further processing
    $reps = xarModAPIFunc('dynamicdata', 'user', 'getitems',
        array(
            'module' => 'helpdesk',
            'itemtype' => REPRESENTATIVE_ITEMTYPE
        )
    );

    $repid_to_uid = array();
    foreach( $reps as $rep ){
        $repid_to_uid[$rep['id']] = $rep['name'];
    }

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
                $rids[] = $repid_to_uid[$repid];
            }
        }
    }

    if( !empty($company) ){
        foreach ($reps as $rep){
            if( in_array($company, $rep['companies']) ){
                $rids[] = $rep['name'];
            }
        }
    }

    // If no reps assign to a category or company them add all reps as they are
    // all assignable.
    if( empty($rids) ){
        foreach( $reps as $rep ){ $rids[] = $rep['name']; }
    }

    return $rids;
}
?>
