<?php
/**
 * transforminputHook config for the hookbridge module.
 */
function hookbridge_adminapi_config_transforminputhook( $args ) 
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


        $data['hookenabled_transforminput']  = xarModGetVar('hookbridge', 'hookenabled_transforminput' );
        $data['hookfunctions_transforminput'] = unserialize(xarModGetVar('hookbridge', 'hookfunctions_transforminput' ));

        if (!xarVarFetch('hookenabled_transforminput',   'str', $hookenabled_transforminput,   '',     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('hookfunctions_transforminput',   'array', $hookfunctions_transforminput,   '',     XARVAR_NOT_REQUIRED)) return;

        if ( empty( $hookenabled_transforminput ) or !is_numeric( $hookenabled_transforminput ) ) 
        {
            $hookenabled_transforminput = 0;
        }

        xarModSetVar('hookbridge', 'hookenabled_transforminput', $hookenabled_transforminput );
        xarModSetVar('hookbridge', 'hookfunctions_transforminput', serialize($hookfunctions_transforminput) );

        

        /*
         * Set a status message
         */
        xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the transforminputHook module settings!' ) );

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