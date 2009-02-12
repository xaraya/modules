<?php
/**
 * XProject Module - A simple task management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
function labaccounting_ledgertransactions_display($args)
{
    extract($args);
    if (!xarVarFetch('transactionid', 'id', $transactionid)) return;

    $data['ledgertransactions_objectid'] = xarModGetVar('labaccounting', 'ledgertransactions_objectid');

    if (!xarModAPILoad('labaccounting', 'user')) return;

    $data = xarModAPIFunc('labaccounting','admin','menu');
    $data['transactionid'] = $transactionid;

    $item = xarModAPIFunc('labaccounting',
                          'ledgers',
                          'get',
                          array('ledgerid' => $ledgerid));

    if (!isset($item)) return;
    
    list($item['title']) = xarModCallHooks('item',
                                         'transform',
                                         $item['ledgerid'],
                                         array($item['title']));
    
    $data['item'] = $item;
    $data['authid'] = xarSecGenAuthKey();
    $data['title'] = $item['title'];

    return $data;
}
?>