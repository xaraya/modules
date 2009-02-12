<?php
/**
 * Dossier Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
function dossier_user_addfriend($args)
{
    if (!xarVarFetch('contactid', 'id', $contactid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('friendid', 'id', $friendid)) return;
    if (!xarVarFetch('featured', 'int::', $featured, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str:1:', $private, $private, XARVAR_NOT_REQUIRED)) return;

    extract($args);
    
    if($contactid == 0) $contactid = xarModAPIFunc('dossier','user','mycontactid');

    if(empty($returnurl)) $returnurl = xarModURL('dossier', 'user', 'main');

    if (!xarModAPIFunc('dossier',
                        'friendslist',
                        'create',
                        array('contactid' 	    => $contactid,
                            'friendid'	    => $friendid,
                            'featured'	    => $featured,
                            'private'	    => $private))) return;

    xarSessionSetVar('statusmsg', xarMLByKey('CONTACTCREATED'));
    
    xarResponseRedirect($returnurl);

    return true;
}

?>
