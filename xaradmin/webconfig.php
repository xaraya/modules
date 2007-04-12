<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author Andrea Moro
 */
/**
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @author Andrea Moro
 * @access public
 * @param no $ parameters
 * @return true on success or void on failure
 * @throws no exceptions
 * @todo nothing
 */
function sharecontent_admin_webconfig()
{
    // Security Check
    if (!xarSecurityCheck('Adminsharecontent')) return;

    $websites = xarModAPIFunc('sharecontent', 'user', 'get');
	if (!$data['enablemail']=xarModGetVar('sharecontent','enablemail')) {
	    $data['enablemail']=0;
	}
	if (!$data['maxemails']=xarModGetVar('sharecontent','maxemails')) {
	    $data['maxemails']=0;
	}
	if (!$data['htmlmail']=xarModGetVar('sharecontent','htmlmail')) {
	    $data['htmlmail']=0;
	}

    $data['websites'] = $websites;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
