<?php
/**
 * updateconfigHook config for the hookbridge module.
 */
function hookbridge_adminapi_config_updateconfighook( $args ) 
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


        $data['hookenabled_updateconfig']  = xarModGetVar('hookbridge', 'hookenabled_updateconfig' );
        $data['hookfunctions_updateconfig'] = unserialize(xarModGetVar('hookbridge', 'hookfunctions_updateconfig' ));

        if (!xarVarFetch('hookenabled_updateconfig',   'str', $hookenabled_updateconfig,   '',     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('hookfunctions_updateconfig',   'array', $hookfunctions_updateconfig,   '',     XARVAR_NOT_REQUIRED)) return;

        if ( empty( $hookenabled_updateconfig ) or !is_numeric( $hookenabled_updateconfig ) ) 
        {
            $hookenabled_updateconfig = 0;
        }

        xarModSetVar('hookbridge', 'hookenabled_updateconfig', $hookenabled_updateconfig );
        xarModSetVar('hookbridge', 'hookfunctions_updateconfig', serialize($hookfunctions_updateconfig) );

        

        /*
         * Set a status message
         */
        xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the updateconfigHook module settings!' ) );

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