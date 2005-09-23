<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * The main administration function
 */
function tinymce_admin_overview()
{
    if (!xarSecurityCheck('AdminTinyMCE')) return;

    $data=array();
    $data['ddflushurl']=xarModURL('dynamicdata','admin','modifyconfig');
    return xarTplModule('tinymce', 'admin', 'main', $data,'main');
}

?>