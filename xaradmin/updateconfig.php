<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * Update configuration
 * @param int restricted
 * @param int useitemtype
 * @param array keywords (default = empty)
 * @return mixed. true on succes and redirect to URL
 */
function keywords_admin_updateconfig()
{

    if (!xarSecConfirmAuthKey()) return;
    if (!xarSecurityCheck('AdminKeywords')) return;

    xarVarFetch('restricted','int:0:1',$restricted, 0);
    xarVarFetch('useitemtype','int:0:1',$useitemtype, 0);
    xarVarFetch('keywords','isset',$keywords,'', XARVAR_DONT_SET);
    xarVarFetch('isalias','isset',$isalias,'', XARVAR_DONT_SET);
    xarVarFetch('showsort','isset',$showsort,'', XARVAR_DONT_SET);
    xarVarFetch('displaycolumns','isset',$displaycolumns,'', XARVAR_DONT_SET);
    xarVarFetch('delimiters','isset',$delimiters,'', XARVAR_DONT_SET);

    xarModSetVar('keywords','restricted',$restricted);
    xarModSetVar('keywords','useitemtype',$useitemtype);

    if (isset($keywords) && is_array($keywords)) {
        xarModAPIFunc('keywords',
                      'admin',
                      'resetlimited'
        );
        foreach ($keywords as $modname => $value) {
            if ($modname == 'default.0' || $modname == 'default') {
                $moduleid='0';
                $itemtype = '0';
            } else {
                $moduleitem = explode(".", $modname);
                $moduleid = xarModGetIDFromName($moduleitem[0],'module');
                if (isset($moduleitem[1]) && is_numeric($moduleitem[1])) {
                    $itemtype = $moduleitem[1];
                } else {
                    $itemtype = 0;
                }
            }
            if ($value <> '') {
                xarModAPIFunc('keywords', 'admin', 'limited',
                              array('moduleid' => $moduleid,
                                    'keyword'  => $value,
                                    'itemtype' => $itemtype)
                );
            }
        }
    }
    if (empty($isalias)) {
        xarModSetVar('keywords','SupportShortURLs',0);
    } else {
        xarModSetVar('keywords','SupportShortURLs',1);
    }
    if (empty($showsort)) {
        xarModSetVar('keywords','showsort',0);
    } else {
        xarModSetVar('keywords','showsort',1);
    }
    if (empty($displaycolumns)) {
        xarModSetVar('keywords','displaycolumns',2);
    } else {
        xarModSetVar('keywords','displaycolumns',$displaycolumns);
    }
    if (isset($delimiters)) {
        xarModSetVar('keywords','delimiters',trim($delimiters));
    }
    xarResponseRedirect(xarModURL('keywords', 'admin', 'modifyconfig'));
    return true;
}
?>