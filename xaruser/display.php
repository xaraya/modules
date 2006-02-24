<?php
/**
 * Display a plan
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Display a plan
 *
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @since 22 Feb 2006
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  int $planid the plan id used for this itsp module
 * @return array
 */
function itsp_user_display($args)
{
    extract($args);

    if (!xarVarFetch('planid', 'id', $planid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    /* At this stage we check to see if we have been passed $objectid, the
     * generic item identifier.
     */
    if (!empty($objectid)) {
        $planid = $objectid;
    }
    /* Add the ITSP user menu */
    $data = xarModAPIFunc('itsp', 'user', 'menu');
    /* get the plan
     */
    $item = xarModAPIFunc('itsp',
        'user',
        'get_plan',
        array('planid' => $planid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

     /* Security check */
     if (!xarSecurityCheck('ReadITSPPlan',0,'Plan',"$planid:All:All")) {
         return $data;
     }

    /* Let any transformation hooks know that we want to transform some text.
     * You'll need to specify the item id, and an array containing the names of all
     * the pieces of text that you want to transform (e.g. for autolinks, wiki,
     * smilies, bbcode, ...).
     */
    $item['itemtype'] = 1;
    $item['transform'] = array('name');
    $item = xarModCallHooks('item',
        'transform',
        $planid,
        $item);
    // Fill in the details of the item.
    $data['name_value'] = $item['planname'];
    // $data['number_value'] = $item['number'];
    $data['item'] = $item;
    $data['planid'] = $planid;

    // Get the planitems

    $planitems = xarModApiFunc('itsp','user','get_planitems',array('planid'=>$planid));

    foreach ($planitems as $planitem) {
        // Add read link
        $pitemid = $planitem['pitemid'];
        // get the planitem
        $pitem = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$pitemid));
        if (xarSecurityCheck('ReadITSPPlan', 0, 'Plan', "$planid:$pitemid:All")) {
            $pitem['link'] = xarModURL('itsp',
                'user',
                'display',
                array('pitemid' => $pitemid));
            /* Security check 2 - else only display the item name (or whatever is
             * appropriate for your module)
             */
        } else {
            $pitem['link'] = '';
        }
        /* Clean up the item text before display */
        $pitem['pitemname'] = xarVarPrepForDisplay($pitem['pitemname']);
        $pitem['pitemdesc'] = xarVarPrepForDisplay($pitem['pitemdesc']);
        $pitem['mincredit'] = xarVarPrepForDisplay($pitem['mincredit']);
        /* Add this item to the list of items to be displayed */
        $data['planitems'][] = $pitem;
    }


   // $data['planitems'] = $planitems;
  //  $data['is_bold'] = xarModGetVar('itsp', 'bold');
    /* Note : module variables can also be specified directly in the
     * blocklayout template by using &xar-mod-<modname>-<varname>;
     * Note that you could also pass on the $item variable, and specify
     * the labels directly in the blocklayout template. But make sure you
     * use the <xar:ml>, <xar:mlstring> or <xar:mlkey> tags then, so that
     * labels can be translated for other languages...
     * Save the currently displayed item ID in a temporary variable cache
     * for any blocks that might be interested (e.g. the Others block)
     * You should use this -instead of globals- if you want to make
     * information available elsewhere in the processing of this page request
     */
    xarVarSetCached('Blocks.itsp', 'planid', $planid);
    /* Let any hooks know that we are displaying an item.
     */
    $item['returnurl'] = xarModURL('itsp',
        'user',
        'display',
       array('planid' => $planid));
    $hooks = xarModCallHooks('item',
        'display',
        $planid,
        $item);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    /* Once again, we are changing the name of the title for better
     * Search engine capability.
     */
    xarTplSetPageTitle(xarVarPrepForDisplay($item['planname']));
    /* Return the template variables defined in this function */
    return $data;
}
?>