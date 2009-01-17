<?php
/**
 * isformantiboted function
 *
 * @package Modules
 * @copyright (C) 2002-2009 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage formantibot
 * @link http://xaraya.com/index.php/release/761.html   
 */

/**
 * Verifies whether a domain is formantiboted or not
 *
 * @access public
 * @param string $domain Domain to verify for formantibot status
 * @returns bool True if formantiboted, false otherwise
 * @ required for when short URLs are off
 *
 */
function formantibot_user_main()
{

    include_once 'modules/formantibot/xarclass/secureimage.php';

    $secureImage = new securimage();
    $secureImage->display();

    exit();
}
?>