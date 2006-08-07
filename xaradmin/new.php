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
function messages_admin_new( $args )
{
    extract($args);

    if (!xarVarFetch('itemtype', 'int',    $itemtype, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cancel',   'str:1:', $cancel, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('preview',  'str:1:', $preview, null, XARVAR_NOT_REQUIRED)) return;

    /*
     * Return to the itemtype's view page if
     *  -> If the user decided to cancel the action
     *  -> There is no itemtype ( will go to main view )
     */
    if (!empty($cancel) or empty($itemtype)) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL('messages', 'admin', 'view',array('itemtype' => $itemtype )));
    }

    // These function is called under different contexts.
    // 1. first time ( authid is not set )
    // 2. preview    ( authid is set, preview is set )
    // 3. Submit     ( authid is set )
    if (isset($authid) ) {

        // Confirm the authorization key
        if (!xarSecConfirmAuthKey()) return;

        if (empty($preview)) {
            switch( $itemtype ) {
                case 1:
                    return xarModAPIFunc('messages', 'admin', 'create', $args );

                default:
                    // TODO // Add statusmessage
                    xarResponseRedirect(
                        xarModURL('messages', 'admin', 'view' ));
            }
        }
    }

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc('messages', 'admin', 'new', $args );

        default:
            // TODO // Add statusmessage
            xarResponseRedirect(
                xarModURL('messages', 'admin', 'view' ));
    }
}

?>