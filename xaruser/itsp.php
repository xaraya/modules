<?php
/**
 * Display the ITSP for one user
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Display the user's ITSP
 *
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 *
 * @author the ITSP module development team
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['itspid'] the item id used for this itsp module
 */
function itsp_user_itsp($args)
{
    // Quick one
    if(!xarSecurityCheck('ViewITSP')) return;
    extract($args);

    if (!xarVarFetch('itspid',   'id', $itspid,   NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    /* At this stage we check to see if we have been passed $objectid, the
     * generic item identifier.
     */
    if (!empty($objectid)) {
        $itspid = $objectid;
    }
    /* Add the ITSP user menu */
    $data = xarModAPIFunc('itsp', 'user', 'menu');


    // We have a valid ITSP?
    /* The API function is called. The arguments to the function are passed in
     * as their own arguments array.
     * Security check 1 - the get() function will fail if the user does not
     * have at least READ access to this item (also see below).
     */

    if (empty($itspid)) {
        $item = xarModAPIFunc('itsp',
                          'user',
                          'get_itspid',
                          array('userid' => xarUserGetVar('uid')));
    } else {

        // The user API function is called to get the ITSP
        $item = xarModAPIFunc('itsp',
                              'user',
                              'get_itspid',
                              array('itspid' => $itspid));
    }

    // First see if there is an id to get.
    if (empty($item)) {
        $data['itspid'] = $itspid;
        xarTplSetPageTitle(xarML('Individual Training and Supervision Plan'));

        return $data;
    }

     $item['itemtype'] = 2;
     /* Security check 2 - if your API function does *not* check for the
     * appropriate access rights, or if for some reason you require higher
     * access than READ for this function, you *must* check this here !
     * if (!xarSecurityCheck('CommentITSP',0,'Item',"$item[name]:All:$item[exid]")) {
     * return $data;
     *}
     */

    /* Let any transformation hooks know that we want to transform some text.
     * You'll need to specify the item id, and an array containing the names of all
     * the pieces of text that you want to transform (e.g. for autolinks, wiki,
     * smilies, bbcode, ...).

    $item['transform'] = array('name');
    $item = xarModCallHooks('item','transform', $itspid, $item);
    // Fill in the details of the item.
    $data['name_value'] = $item['name'];
    $data['number_value'] = $item['number'];
*/
    $data['itspid'] = $itspid;

    //$data['is_bold'] = xarModGetVar('itsp', 'bold');

    xarVarSetCached('Blocks.itsp', 'itspid', $itspid);
    /* Let any hooks know that we are displaying an item.
     */
    $item['returnurl'] = xarModURL('itsp',
        'user',
        'display',
       array('itspid' => $itspid));
    $hooks = xarModCallHooks('item',
        'display',
        $itspid,
        $item);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    /* Once again, we are changing the name of the title for better
     * Search engine capability.
     */
    xarTplSetPageTitle(xarVarPrepForDisplay($item['itspid']));
    /* Return the template variables defined in this function */
    return $data;
}
?>