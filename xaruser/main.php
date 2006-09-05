<?php
/**
 * Main User Function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Marc Lutolf
 * @author Yassen Yotov (CyberOto)
 * @author  Shawn McKenzie (AbraCadaver)
 */

/**
 * External page entry point
 *
 * @return  data on success or void on falure
 * @throws  XAR_SYSTEM_EXCEPTION, 'NOT_ALLOWED'
*/
function window_user_main($args)
{
     // Security check
    if(!xarSecurityCheck('ViewWindow')) return;
    extract($args);
    if (!xarVarFetch('page', 'str', $page, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str', $title, xarML('External Application'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('height', 'str', $height, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('width', 'str', $width, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('auto_resize', 'str', $auto_resize, NULL, XARVAR_NOT_REQUIRED)) return;

    $data=array('page'=> $page,
                'title'=> $title,
                'height' => $height,
                'width' => $width,
                'auto_resize'=>$auto_resize);
    return xarResponseRedirect(xarModURL('window', 'user', 'display', $data));

    return true;
}
?>