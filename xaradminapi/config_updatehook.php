<?php
/**
 * UpdateHook config for the hookbridge module.
 */
function hookbridge_adminapi_config_updatehook( $args ) 
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


        $data['hookenabled_update']   = xarModGetVar('hookbridge', 'hookenabled_update' );
        $data['hookfunctions_update'] = unserialize(xarModGetVar('hookbridge', 'hookfunctions_update' ));

        if (!xarVarFetch('hookenabled_update',   'str',    $hookenabled_update,   '',     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('hookfunctions_update',  'array', $hookfunctions_update, '',     XARVAR_NOT_REQUIRED)) return;

        if ( empty( $hookenabled_update ) or !is_numeric( $hookenabled_update ) ) 
        {
            $hookenabled_update = 0;
        }

        xarModSetVar('hookbridge', 'hookenabled_update', $hookenabled_update );
        xarModSetVar('hookbridge', 'hookfunctions_update', serialize($hookfunctions_update) );

        

        /*
         * Set a status message
         */
        xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the UpdateHook module settings!' ) );

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