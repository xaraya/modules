<?php
/**
 * XProject Module main admin function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
 
function xproject_admin_main()
{
    xarResponseRedirect(xarModURL('xproject','admin','view'));
	return;
}

?>