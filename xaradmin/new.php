<?php
/**
 * Add a new item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * add new item
 */
function labAccounting_admin_new()
{
    if(!xarVarFetch('itemtype', 'int', $itemtype,  NULL, XARVAR_DONT_SET)) {return;}
    
    // route appropriately, in case this isn't for itemtype 5
    switch($itemtype) {
        case 1:
        case 3:
            xarResponseRedirect(xarModURL('labaccounting', 'journals', 'new'));
            return true;
            break;
        case 2:
        case 4:
            xarResponseRedirect(xarModURL('labaccounting', 'ledgers', 'new'));
            return true;
            break;
    }        
    
    $data = xarModAPIFunc('labAccounting','admin','menu');
    
    $item['itemtype'] = $itemtype;

//    if (!xarSecurityCheck('AddDynExample')) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $data['object'] = xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'labaccounting',
                                        'itemtype' => $itemtype));

    $item = array();
    $item['module'] = 'labAccounting';
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // Return the template variables defined in this function
    return $data;
}

?>