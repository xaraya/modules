<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Obscures the given filename for added security
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   <type>
 *  @returns <type>
 */

function uploads_userapi_file_obfuscate_name( $args )
{

    extract ($args);

    if (!isset($fileName) || empty($fileName)) {
        return FALSE;
    }
    $hash = crypt($fileName, substr(md5(time() . $fileName . getmypid()), 0, 2));
    $hash = substr(md5($hash), 0, 8) . time() . getmypid();

    return $hash;

}

?>