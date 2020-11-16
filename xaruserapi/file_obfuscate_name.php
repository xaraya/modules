<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 *  Obscures the given filename for added security
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   <type>
 *  @return <type>
 */

function uploads_userapi_file_obfuscate_name($args)
{
    extract($args);

    if (!isset($fileName) || empty($fileName)) {
        return false;
    }
    $hash = crypt($fileName, substr(md5(time() . $fileName . getmypid()), 0, 2));
    $hash = substr(md5($hash), 0, 8) . time() . getmypid();

    return $hash;
}
