<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_pages_incr($args)
{
    if(!xarVarFetch('pageid','int', $pageid, $pageid, XARVAR_NOT_REQUIRED)) {return;}

    extract($args);

    $pageinfo = xarModAPIFunc('xproject', 'pages', 'get', array('pageid' => $pageid));

    if(!xarModAPIFunc('xproject',
                    'pages',
                    'incr',
                    array('pageid' => $pageid))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Page Incremented'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'display', array('projectid' => $pageinfo['projectid'], 'mode' => "pages")));

    return true;
}

?>