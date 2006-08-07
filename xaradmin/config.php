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
/**
 * Administration for the mybookmarks module.
 */
function messages_admin_config( $args )
{
    extract( $args );
    if (!xarVarFetch('cancel',    'str:1:', $cancel, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype',  'int',    $itemtype, 0, XARVAR_NOT_REQUIRED)) return;
    // check if the user selected cancel
    if ( !empty( $cancel ) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL('messages','admin','config',array('itemtype' => $itemtype )));
        }

    switch( $itemtype ) {
        case 1:
            return xarModAPIFunc('messages','messages','config',$args );

        default:
            return messages_adminpriv_config( $args );
    }
}

/**
 * Administration for the mybookmarks module.
 */
function messages_adminpriv_config( $args )
{
    extract( $args );
    if (!xarVarFetch('itemtype',  'int',    $itemtype, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'int:0:1', $shorturls, 0, XARVAR_NOT_REQUIRED)) return;
    if ( isset( $authid ) ) {
        /* The user confirmed the form. So save the results.         */

        if (!xarSecConfirmAuthKey()) return;
        
        if ( empty( $supportshorturls ) or !is_numeric( $supportshorturls ) ) {
            $supportshorturls = 0;
        }

        xarModSetVar('messages','SupportShortURLs',$supportshorturls );

        /*
         * Finished. Back to the sender!
         */
        xarResponseRedirect(
            xarModURL('messages','admin','config', array('itemtype' => $itemtype )));

    } // Save the changes

    $data['common']['menu_label'] = 'Configure';
    $data['common']['menu']       = messages_adminpriv_configmenu();

    /*
     * Populate the rest of the template
     */
    $data['action']     = xarModURL('messages','admin','config' );
    $data['authid']     = xarSecGenAuthKey();
    $data['supportshorturls']   = xarModGetVar('messages', 'SupportShortURLs');
    return $data;

}

function messages_adminpriv_configmenu()
{
    /*
     * Build the configuration submenu
     */
    $menu = array(
        array(
            'label' =>  'Config',
            'url'   =>  xarModURL('messages','admin','config' )));

    $menu[] = array(
            'label' =>  'Messages',
            'url'   =>  xarModURL('messages','admin','config',array( 'itemtype' => '1' )));


    return $menu;

}
?>