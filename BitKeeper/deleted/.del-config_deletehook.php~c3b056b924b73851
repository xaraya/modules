<?php
/**
 * deleteHook config for the hookbridge module.
 */
function hookbridge_adminapi_config_deletehook( $args ) 
{

    if (!xarVarFetch('itemtype',   'str', $itemtype,   '',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('authid',   'str', $authid,   '',     XARVAR_NOT_REQUIRED)) return;

    extract( $args );

    if ( isset( $authid ) && !empty($authid)  ) 
    {

        /*
         * The user confirmed the form. So save the results.
         */

        if (!xarSecConfirmAuthKey()) return;


        $data['hookenabled_delete']  = xarModGetVar('hookbridge', 'hookenabled_delete' );
        $data['hookfunctions_delete'] = unserialize(xarModGetVar('hookbridge', 'hookfunctions_delete' ));

        if (!xarVarFetch('hookenabled_delete',   'str', $hookenabled_delete,   '',     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('hookfunctions_delete',   'array', $hookfunctions_delete,   '',     XARVAR_NOT_REQUIRED)) return;

        if ( empty( $hookenabled_delete ) or !is_numeric( $hookenabled_delete ) ) 
        {
            $hookenabled_delete = 0;
        }

        xarModSetVar('hookbridge', 'hookenabled_delete', $hookenabled_delete );
        xarModSetVar('hookbridge', 'hookfunctions_delete', serialize($hookfunctions_delete) );

        

        /*
         * Set a status message
         */
        xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the deleteHook module settings!' ) );

        /*
         * Finished. Back to the sender!
         */
        return xarResponseRedirect(
            xarModURL('hookbridge','admin','config',array(
                                                            'itemtype' => $itemtype )
                                                          ));

    } // Save the changes


}
?>