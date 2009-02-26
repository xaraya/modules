<?php
/**
 * isformantiboted function
 *
 * @package Xaraya modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Formantibot
 * @copyright (C) 2008 2skies.com
 * @link http://xarigami.com/project/formantibot
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