<?php
/**
 * CreateHook config for the hookbridge module.
 */
function hookbridge_adminapi_config_createhook( $args ) 
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


        $data['hookenabled_create']  = xarModGetVar('hookbridge', 'hookenabled_create' );
        $data['hookfunctions_create'] = unserialize(xarModGetVar('hookbridge', 'hookfunctions_create' ));

        if (!xarVarFetch('hookenabled_create',   'str', $hookenabled_create,   '',     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('hookfunctions_create',   'array', $hookfunctions_create,   '',     XARVAR_NOT_REQUIRED)) return;

        if ( empty( $hookenabled_create ) or !is_numeric( $hookenabled_create ) ) 
        {
            $hookenabled_create = 0;
        }

        xarModSetVar('hookbridge', 'hookenabled_create', $hookenabled_create );
        xarModSetVar('hookbridge', 'hookfunctions_create', serialize($hookfunctions_create) );

        

        /*
         * Set a status message
         */
        xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the CreateHook module settings!' ) );

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