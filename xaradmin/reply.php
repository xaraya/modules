<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_admin_reply( $args )
{
    extract( $args );
    if (!xarVarFetch('itemid',   'id', $itemid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'int',    $itemtype, 0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cancel',   'str:1:', $cancel, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('preview',  'str:1:', $preview, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subject',  'str:1:', $subject, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from_id',  'isset', $from_id, null, XARVAR_NOT_REQUIRED)) return;

    /*
     * Return to the itemtype's view page if
     *  -> If the user decided to cancel the action
     *  -> There is no itemid to modify
     *  -> There is no itemtype ( will go to main view )
     */
    if ( !empty($cancel) or empty($itemid) or empty($itemtype) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL('messages', 'admin', 'view', array('itemtype' => $itemtype )));
    }

    // check if authid is set.
    if ( isset( $authid ) ) {

        // Confirm the authorization key
        if (!xarSecConfirmAuthKey()) return;

        // Check if a preview is wished
        if ( !isset( $preview ) ) {

            switch( $itemtype ) {
                case 1:
                    return xarModAPIFunc('messages', 'admin', 'update', $args );

                default:
                    // TODO // Add statusmessage
                    xarResponseRedirect(
                        xarModURL('messages', 'admin', 'view' ));
            }
        }
    }

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc('messages', 'admin', 'modify', $args );

        default:
            // TODO // Add statusmessage
            xarResponseRedirect(
                xarModURL('messages', 'admin', 'view' ));
    }

}

?>