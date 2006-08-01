<?php
/**
 * isformantiboted function
 *
 * @package Modules
 * @copyright (C) 2002-2006 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage formantibot
 * @link http://xaraya.com/index.php/release/761.html   
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 */

/**
 * Verifies whether a domain is formantiboted or not
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access public
 * @param string $domain Domain to verify for formantibot status
 * @returns bool True if formantiboted, false otherwise
 *
 */
function formantibot_user_image()
{

    include_once 'modules/formantibot/xarclass/secureimage.php';

    $secureImage = new securimage();
    $secureImage->display();

    exit();
}
?>
