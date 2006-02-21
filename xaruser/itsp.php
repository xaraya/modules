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

    $uid = xarUserGetVar('uid');
    /* The API function is called.  The arguments to the function are passed in
     * as their own arguments array.
     * Security check 1 - the get() function will fail if the user does not
     * have at least READ access to this item (also see below).
     */
    $item = xarModAPIFunc('itsp',
        'user',
        'get',
        array('itspid' => $itspid, 'userid' => $uid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* If your module deals with different types of items, you should specify the item type
     * here, before calling any hooks
     */
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