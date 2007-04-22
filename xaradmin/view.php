<?php
/**
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage bkview
 * @author Johnny Robeson
*/

/**
 * view repositories
 */
function bkview_admin_view()
{
    // Security check
    if (!xarSecurityCheck('AdminAllRepositories')) return;
    
    $data['items'] = array();
  
    $items = xarModAPIFunc('bkview', 'user', 'getall',array());
    //if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    // TODO: Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $items[$i]['editurl'] = xarModURL('bkview','admin','modify',
                                              array('repoid' => $item['repoid']));
        $items[$i]['edittitle'] = xarML('Edit');
        $items[$i]['deleteurl'] = xarModURL('bkview',    'admin','delete',
                                                array('repoid' => $item['repoid']));
        $items[$i]['deletetitle'] = xarML('Delete');
    }
    
    // Add the array of items to the template variables
    $data['items'] = $items;
    $data['pageinfo']=xarML('View registered repositories');
    return $data;
}
?>