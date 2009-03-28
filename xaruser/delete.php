<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * delete item
 */
function publications_user_delete()
{
    $return = xarModURL('publications', 'user','view',array('ptid' => xarModVars::get('publications', 'defaultpubtype')));
    if(!xarVarFetch('confirmed',  'int', $confirmed,  0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('idlist',     'str', $idlist,     NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('returnurl',  'str', $data['returnurl'],  $return,  XARVAR_NOT_REQUIRED)) {return;}

    if (empty($idlist)) xarResponseRedirect($data['returnurl']);

    $ids = explode(',',trim($idlist,','));
    $data['idlist'] = $idlist;
    if (is_array($ids)) {
        $data['lang_title'] = xarML("Delete Publications");
    } else {
        $ids = array($ids);
        $data['lang_title'] = xarML("Delete Publication");
    }

    if (!$confirmed) {
        $data['authid'] = xarSecGenAuthKey();
        
        // TODO: abstract this to run on DD
        sys::import('xaraya.structures.query');
        $table = xarDB::getTables();
        $q = new Query('SELECT', $table['publications']);
        $q->in('id',$ids);
        $q->addfield('id');
        $q->addfield('name');
        if (!$q->run()) return false;
        $data['items'] = $q->output();
        
        return $data;
    } else {
        if (!xarSecConfirmAuthKey()) return;
        if (!xarModAPIFunc('publications', 'admin', 'delete',
                         array('itemid' => $ids,
                               'deletetype' => 0))) 
            return;
        xarResponseRedirect($data['returnurl']);
    }

    return true;
}

?>
