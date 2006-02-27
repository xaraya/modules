<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_adminapi_config( $args )
{

    $data = messages_admin_common( 'messages Configuration' );

    list( $itemtype, $authid ) = xarVarCleanFromInput( 'itemtype', 'authid' );
    extract( $args );

    if ( isset( $authid ) ) {

        /*
         * The user confirmed the form. So save the results.
         */

        if (!xarSecConfirmAuthKey()) return;

        $itemsperpage = xarVarCleanFromInput( 'itemsperpage' );

        if ( empty( $itemsperpage ) or !is_numeric( $itemsperpage ) ) {
            $itemsperpage = 10;
        }

        xarModSetVar(
            'messages'
            ,'itemsperpage.' . $itemtype
            ,$itemsperpage );


        /*
         * call the hook 'module:updateconfig:GUI'
         */
        $args = array(
            'module'        =>  'messages'
            ,'itemtype'     =>  $itemtype );
        $data['hooks'] = xarModCallHooks(
            'module'
            ,'updateconfig'
            ,'messages'
            ,$args
            ,'messages' );

        /*
         * Finished. Back to the sender!
         */
        xarResponseRedirect(
            xarModURL(
                'messages'
                ,'admin'
                ,'config'
                ,array(
                    'itemtype' => $itemtype )));

    } // Save the changes


    /*
     * call the hook 'module:modifyconfig:GUI'
     */
    $args = array(
        'module'        =>  'messages'
        ,'itemtype'     =>  $itemtype );
    $data['hooks'] = xarModCallHooks(
        'module'
        ,'modifyconfig'
        ,'messages'
        ,$args
        ,'messages' );



    $data['itemtype']       = $itemtype;
    $data['itemtype_label'] = $itemtype;
    $data['itemsperpage']   = xarModGetVar(
        'messages'
        ,'itemsperpage.' . $itemtype );


    /*
     * Populate the rest of the template
     */
    $data['common']['menu_label'] = 'Configure';
    $data['common']['menu']       = messages_adminpriv_configmenu();
    $data['action']     = xarModURL( 'messages', 'admin', 'config' );
    $data['authid']     = xarSecGenAuthKey();
    $data['_bl_template'] = 'messages';
    return $data;
}

?>
