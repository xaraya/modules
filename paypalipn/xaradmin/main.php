<?
/**
 * the main administration function
 *
 * @author  John Cox <niceguyeddie@xaraya.com>
 * @access  public
 * @param   no parameters
 * @return  true on success or void on falure
 * @throws  XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
*/
function paypalipn_admin_main()
{
	// Security Check
	if (!xarSecurityCheck('AdminPayPalIPN')) return;
	if (xarModGetVar('adminpanels', 'overview') == 0) {
		// Return the output
		return array();
	} else {
		xarResponseRedirect(xarModURL('paypalipn', 'admin', 'modifyconfig'));
	} 
	// success
	return true;
}
?>