<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
function labaffiliate_admin_create($args)
{
    extract($args);

    if (!xarVarFetch('program_name', 'str:1', $program_name, $program_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('programurl', 'str:1', $programurl, $programurl, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('affiliateurl', 'str:1', $affiliateurl, $affiliateurl, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('details', 'str:1', $details, $details, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('marketing_copy', 'str:1', $marketing_copy, $marketing_copy, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, $status, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    // Argument check
    $invalid = array();
    if (empty($program_name) || !is_string($program_name)) {
        $invalid[] = 'Program name';
        $program_name = '';
    }
    if (empty($programurl) || !is_string($programurl)) {
        $invalid[] = 'Program URL';
        $programurl = '';
    }

    if (empty($affiliateurl) || !is_string($affiliateurl)) {
        $invalid[] = 'Affiliate URL';
        $affiliateurl = '';
    }

    if (empty($details) || !is_string($details)) {
        $invalid[] = 'Details';
        $details = '';
    }

    if (empty($marketing_copy) || !is_string($marketing_copy)) {
        $invalid[] = 'Marketing Copy';
        $marketing_copy = '';
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        /* If we get here, we have encountered errors.
         * Send the user back to the admin_new form
         * call the admin_new function and return the template vars
         */
        return xarModFunc('labaffiliate', 'admin', 'new',
                          array('program_name' => $program_name,
							'programurl' => $programurl,
							'affiliateurl' => $affiliateurl,
							'details' => $details,
							'marketing_copy' => $marketing_copy,
                            'status' => $status,
							'invalid' => $invalid));
    }

    if (!xarSecConfirmAuthKey()) return;

    $programid = xarModAPIFunc('labaffiliate',
                          'admin',
                          'create',
                          array('program_name' => $program_name,
							'programurl' => $programurl,
							'affiliateurl' => $affiliateurl,
							'details' => $details,
							'marketing_copy' => $marketing_copy,
                            'status' => $status));

    if (!isset($programid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $allItems = xarModApiFunc('labaffiliate','user','getall');
    $newItemPosition = 0;
    foreach($allItems as $pos => $info) { 
        if($info['programid']==$programid) {  $newItemPosition = $pos; break; }
    }
    xarResponseRedirect(xarModURL('labaffiliate', 'admin', 'view',array('startnum' => $newItemPosition)));
    
    /* Return true, in this case */
    return true;
}

?>