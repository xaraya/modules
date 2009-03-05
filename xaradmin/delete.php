<?php
/**
 * Delete an article in a newsgroup
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsgroups
 * @link http://xaraya.com/index.php/release/802.html
 * @author hb
 */
/**
 * Delete article in a newsgroup, Redirect to previous message
 *
 * @param string phase     'confirm'
 * @param string group     newsgroup
 * @param string from      From header or email
 * @param string messageid message-id
 * @param int    article   message number in group (optional)
 * @return true on success, or void on failure
 */

function newsgroups_admin_delete()
{
    if (!xarVarFetch('phase','str:1:100',$phase,'',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('group','str:1:',$group, '', XARVAR_GET_OR_POST, XARVAR_PREP_FOR_DISPLAY)) return;

    // Security Check
    if(!xarSecurityCheck('DeleteNewsGroups')) return;

    switch(strtolower($phase)) {

        case 'ask':
        default:
          /*
          TODO Confirm page
          */
            break;

        case 'confirmed':

            if (!xarSecurityCheck('DeleteNewsGroups')) return;

            if (!xarVarFetch('article', 'int', $articlenum, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('from','str:1:200',$from)) return;
            if (!xarVarFetch('messageid','str:1',$messageid)) return;
            if (!xarSecConfirmAuthKey()) return;

            if (!xarModAPIFunc('newsgroups','admin','delete',
                               array('group'      => $group,
                                      'from'      => $from,
                                      'messageid' => $messageid))
            ) {
                return false;
            }

            // Redirect to the article before the deleted one or to group
            if (is_numeric($articlenum)) {
                xarResponseRedirect(xarModURL('newsgroups', 'user', 'article',
                                              array('group'  => $group,
                                                    'article' => $articlenum - 1
                                                    )));
            } else {
              xarResponseRedirect(xarModURL('newsgroups', 'user', 'group',
                                            array('group' => $group)));
            }

            return true;
    }
    return $data;
}
?>
