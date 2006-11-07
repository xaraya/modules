<?php
/**
 * Standard function to view items
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage PHPlot Module
 * @link http://xaraya.com/index.php/release/818.html
 * @author PHPlot Module Development Team
 */

/**
 * Standard function to view items
 *
 * @author PHPlot module development team
 * @return array
 */
function phplot_admin_view()
{
    /* Get parameters from whatever input we need. All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed. Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that
     * will not hold in future versions of Xaraya
     */
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = xarModAPIFunc('phplot', 'admin', 'menu');
    /* Initialise the variable that will hold the items, so that the template
     * doesn't need to be adapted in case of errors
     */
    $data['items'] = array();

    /* Call the xarTPL helper function to produce a pager in case of there
     * being many items to display.
     *
     *
     * Note that this function includes another user API function. The
     * function returns a simple count of the total number of items in the item
     * table so that the pager function can do its job properly
     */
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('phplot', 'user', 'countitems'),
        xarModURL('phplot', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('phplot', 'itemsperpage'));
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('EditPHPlot')) return;
    /* The user API function is called. This takes the number of items
     * required and the first number in the list of all items, which we
     * obtained from the input and gets us the information on the appropriate
     * items.
     */
    $items = xarModAPIFunc('phplot',
                           'user',
                           'getall',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('phplot','itemsperpage')));
    /* Check for exceptions */
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Check individual permissions for Edit / Delete
     * Note : we could use a foreach ($items as $item) here as well, as
     * shown in xaruser.php, but as an phplot, we'll adapt the $items array
     * 'in place', and *then* pass the complete items array to $data
     */
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('EditPHPlot', 0, 'Item', "$item[name]:All:$item[exid]")) {
            $items[$i]['editurl'] = xarModURL('phplot',
                'admin',
                'modify',
                array('exid' => $item['exid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeletePHPlot', 0, 'Item', "$item[name]:All:$item[exid]")) {
            $items[$i]['deleteurl'] = xarModURL('phplot',
                'admin',
                'delete',
                array('exid' => $item['exid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
    }
    /* Add the array of items to the template variables */
    $data['items'] = $items;

    /* Return the template variables defined in this function */
    return $data;
    /* Note : instead of using the $data variable, you could also specify
     * the different template variables directly in your return statement :
     *
     * return array('items' => ...,
     * 'namelabel' => ...,
     *... => ...);
     */
}
?>