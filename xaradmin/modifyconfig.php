<?php
/**
 * File: $Id:
 * 
 * Standard function to modify configuration parameters
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search
 * @author Jo Dalle Nogare
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function search_admin_modifyconfig()
{ 
    //$data = xarModAPIFunc('search', 'admin', 'menu');
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
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
