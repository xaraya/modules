<?php
/**
 * View a list of items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * View a list of items
 *
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 *
 * @author the Example module development team
 * @param none
 * @return array $data array with all information for the template
 */
function example_user_view()
{
    /* Get parameters from whatever input we need. All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed. Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = xarModAPIFunc('example', 'user', 'menu');
    /* Prepare the variable that will hold some status message if necessary */
    $data['status'] = '';
    /* Prepare the array variable that will hold all items for display */
    $data['items'] = array();
    /* Specify some other variables for use in the function template */
    $data['pager'] = '';
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('ViewExample')) return;
    /* Lets get the UID of the current user to check for overridden defaults */
    $uid = xarUserGetVar('uid');
    /* The API function is called. The arguments to the function are passed in
     * as their own arguments array.
     * Security check 1 - the getall() function only returns items for which the
     * the user has at least OVERVIEW access.
     */
    $items = xarModAPIFunc('example',
        'user',
        'getall',
        array('startnum' => $startnum,
              'numitems' => xarModGetUserVar('example',
              'itemsperpage',
              $uid)));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    /* TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
     * Loop through each item and display it.
     */
    foreach ($items as $item) {
        /* Let any transformation hooks know that we want to transform some text
         * You'll need to specify the item id, and an array containing all the
         * pieces of text that you want to transform (e.g. for autolinks, wiki,
         * smilies, bbcode, ...).
         * Note : for your module, you might not want to call transformation
         * hooks in this overview list, but only in the display of the details
         * in the display() function.
         * list($item['name']) = xarModCallHooks('item',
         * 'transform',
         * $item['exid'],
         * array($item['name']));
         * Security check 2 - if the user has read access to the item, show a
         * link to display the details of the item
         */
        if (xarSecurityCheck('ReadExample', 0, 'Item', "$item[name]:All:$item[exid]")) {
            $item['link'] = xarModURL('example',
                'user',
                'display',
                array('exid' => $item['exid']));
            /* Security check 2 - else only display the item name (or whatever is
             * appropriate for your module). We do this by setting the link empty, and
             * have a check on this in the template.
             */
        } else {
            $item['link'] = '';
        }
        /* Clean up the item text before display */
        $item['name'] = xarVarPrepForDisplay($item['name']);
        /* Add this item to the list of items to be displayed */
        $data['items'][] = $item;
    }
    /* TODO: how to integrate cat ids in pager (automatically) when needed ???
     * Call the xarTPL helper function to produce a pager in case of there
     * being many items to display.
     *
     * Note that this function includes another user API function -countitems-. The
     * function returns a simple count of the total number of items in the item
     * table so that the pager function can do its job properly
     */
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('example', 'user', 'countitems'),
        xarModURL('example', 'user', 'view', array('startnum' => '%%')),
        xarModGetUserVar('example', 'itemsperpage', $uid));

    /* We are changing the name of the page to raise
     * better search engine compatibility.
     */
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Examples')));
    /* Return the template variables defined in this function */
    return $data;

    /* Note : instead of using the $data variable, you could also specify
     * the different template variables directly in your return statement :
     *
     * return array('menu' => ...,
     * 'items' => ...,
     * 'pager' => ...,
     * ... => ...);
     */
}
?>