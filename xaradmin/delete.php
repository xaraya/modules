<?php
/**
 * Delete an article in a newsgroup
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2009 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsgroups
 * @author hb
 */
/**
 * Delete article in a newsgroup, Redirect to previous message
 *
 * @param string $args['phase']     'confirm'
 * @param string $args['group']     newsgroup
 * @param string $args['from']      From header or email
 * @param string $args['messageid'] message-id
 * @param int    $args['article']   message number in group (optional)
 * @returns misc
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

            // if (!xarSecConfirmAuthKey()) return; // If delete is POSTed

            if (!xarModAPIFunc('newsgroups','admin','delete',
                               array('group'      => $group,
                                      'from'      => $from,
                                      'messageid' => $messageid
                                     ))) return;

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
