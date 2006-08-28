<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * the main administration function
 *
 * @author Jim McDonald
 * @access public
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function ratings_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminRatings')) return;

        xarResponseRedirect(xarModURL('ratings', 'admin', 'modifyconfig'));

    // success
    return true;
}

?>