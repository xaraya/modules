<?php
/**
 * Standard function to modify configuration parameters
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search Module
 * @link http://xaraya.com/index.php/release/32.html
 * @author Search Module Development Team
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 * @author Jo Dalle Nogare
 * @return array
 */
function search_admin_modifyconfig()
{
    // Security check
    if (!xarSecurityCheck('AdminSearch')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    // Specify some labels and values for display
    $data['showLastSearches'] = xarModGetVar('search', 'showsearches') ? true : false;
    $data['searchitemvalue'] = xarModGetVar('search', 'searchestoshow');
    if (!isset($data['searchitemvalue'])) {
        $data['searchitemvalue']=0;
    }
    $data['itemsvalue'] = xarModGetVar('search', 'itemsperpage');
    if (!isset($data['itemsvalue'])) {
        $data['itemsvalue']=20;
    }
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));
    //$data['shorturlslabel'] = xarML('Enable short URLs?');
    //$data['shorturlschecked'] = xarModGetVar('search', 'SupportShortURLs') ? true : false;

    // Return the template variables defined in this function
    return $data;
}

?>
