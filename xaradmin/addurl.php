<?php
/**
 * New URL Form
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
 */

/**
 * New URL Form
 *
 * @return array $data template array
 */
function window_admin_addurl($args)
{
    extract($args);

    if (!xarModAPIFunc('window', 'admin','addurl')) return;

    return true;
}
?>