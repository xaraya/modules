<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_admin_view($args)
{
    if (!xarVarFetch('itemtype', 'int', $itemtype, 0,XARVAR_NOT_REQUIRED)) return;

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc('messages', 'admin', 'view' );


        default:
            return messages_admin_common('Main Page'); }
}

?>