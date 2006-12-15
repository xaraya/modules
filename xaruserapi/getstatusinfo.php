<?php
/**
 * Get status information
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author MichelV
 */
/**
 * Get status information
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param none
 * @return array with status info data
 */
function itsp_userapi_getstatusinfo()
{
        $statusoptions=array();
        $statusoptions[0] = xarML('Added'); // Standard status
        $statusoptions[1] = xarML('In progress'); // After first change of student
        $statusoptions[2] = xarML('Supervisor requested'); // Supervisor should approve
        $statusoptions[3] = xarML('Approved and Updated'); // Updated after approval
        $statusoptions[4] = xarML('Submitted'); // Sent to the office, closed for editing
        $statusoptions[5] = xarML('Approved');
        $statusoptions[6] = xarML('Certificate Requested');
        $statusoptions[7] = xarML('Closed');

        return $statusoptions;
}
?>