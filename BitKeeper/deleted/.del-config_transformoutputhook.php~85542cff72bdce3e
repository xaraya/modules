<?php
/**
 * transformoutputHook config for the hookbridge module.
 */
function hookbridge_adminapi_config_transformoutputhook( $args ) 
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


        $data['hookenabled_transformoutput']  = xarModGetVar('hookbridge', 'hookenabled_transformoutput' );
        $data['hookfunctions_transformoutput'] = unserialize(xarModGetVar('hookbridge', 'hookfunctions_transformoutput' ));

        if (!xarVarFetch('hookenabled_transformoutput',   'str', $hookenabled_transformoutput,   '',     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('hookfunctions_transformoutput',   'array', $hookfunctions_transformoutput,   '',     XARVAR_NOT_REQUIRED)) return;

        if ( empty( $hookenabled_transformoutput ) or !is_numeric( $hookenabled_transformoutput ) ) 
        {
            $hookenabled_transformoutput = 0;
        }

        xarModSetVar('hookbridge', 'hookenabled_transformoutput', $hookenabled_transformoutput );
        xarModSetVar('hookbridge', 'hookfunctions_transformoutput', serialize($hookfunctions_transformoutput) );

        

        /*
         * Set a status message
         */
        xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the transformoutputHook module settings!' ) );

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