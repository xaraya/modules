<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * Modify the pubsub templates
 *
 * @author mikespub
 * @access public
 * @param none $ parameters
 * @return bool true on success or void on failure
 * @throws no exceptions
 */
function pubsub_admin_modifytemplates()
{
    // Security Check
    if (!xarSecurityCheck('AdminPubSub')) return;

    $data = array();

    $data['templates'] = array();
    // get the list of available templates
    $templates = xarModAPIFunc('pubsub','admin','getalltemplates');
    foreach ($templates as $id => $templatename) {
        $data['templates'][$id] = array('name' => $templatename,
                                        'view' => xarModURL('pubsub','admin','modifytemplates',
                                                            array('action' => 'display',
                                                                  'templateid' => $id)),
                                        'edit' => xarModURL('pubsub','admin','modifytemplates',
                                                            array('action' => 'modify',
                                                                  'templateid' => $id)),
                                        'delete' => xarModURL('pubsub','admin','modifytemplates',
                                                              array('action' => 'delete',
                                                                    'templateid' => $id))
                                       );
    }
    $data['new'] = xarModURL('pubsub','admin','modifytemplates',
                             array('action' => 'new'));

    xarVarFetch('templateid','int',$templateid,0, XARVAR_NOT_REQUIRED);
    xarVarFetch('action','str:1:',$action,'', XARVAR_NOT_REQUIRED);
    if (!empty($templateid) && !empty($action)) {
        $info = xarModAPIFunc('pubsub','admin','gettemplate',
                              array('templateid' => $templateid));
        if (empty($info)) return;
        $data['templateid'] = $templateid;
        $data['name'] = $info['name'];
        $data['template'] = xarVarPrepForDisplay($info['template']);
    }
    switch ($action) {
        case 'display':
        // TODO: adapt if/when we support more template variables in runjob()
            $tplData = array('userid' => xarUserGetVar('uid'),
                             'name' => xarUserGetVar('uname'),
                             'module' => 'example',
                             'itemtype' => 0,
                             'itemid' => 123,
                             'title' => xarML('This is an item title'),
                             'link' => xarModURL('example','user','display',
                                                 array('exid' => 123)));
            $preview = xarTplString($info['compiled'],$tplData);
            $data['preview'] = xarVarPrepHTMLDisplay($preview);
            $data['action'] = 'display';
            break;

        case 'new':
            $data['name'] = '';
        // TODO: adapt if/when we support more template variables in runjob()
            $templatevariables = array('#$userid#','#$name#','#$module#','#$itemtype#','#$itemid#','#$title#','#$link#');
            $data['template'] = join("<br/>\n",$templatevariables);
            $data['templateid'] = 0;
            $data['submitbutton'] = xarML('Create Template');
            $data['action'] = 'create';
            break;

        case 'create':
            if (!xarSecConfirmAuthKey()) return;
            if (!xarVarFetch('name','str:1:',$name)) return;
            if (!xarVarFetch('template','str:1:',$template)) return;
            if (!xarModAPIFunc('pubsub','admin','addtemplate',
                               array('name' => $name,
                                     'template' => $template))) {
                return;
            }
            xarResponseRedirect(xarModURL('pubsub', 'admin', 'modifytemplates'));
            return true;
            break;

        case 'modify':
            $data['submitbutton'] = xarML('Update Template');
            $data['action'] = 'update';
            break;

        case 'update':
            if (!xarSecConfirmAuthKey()) return;
            if (!xarVarFetch('name','str:1:',$name)) return;
            if (!xarVarFetch('template','str:1:',$template)) return;
            if (!xarModAPIFunc('pubsub','admin','updatetemplate',
                               array('templateid' => $templateid,
                                     'name' => $name,
                                     'template' => $template))) {
                return;
            }
            xarResponseRedirect(xarModURL('pubsub', 'admin', 'modifytemplates'));
            return true;
            break;

        case 'delete':
            $data['submitbutton'] = xarML('Delete Template');
            $data['action'] = 'confirm';
            break;

        case 'confirm':
            if (!xarSecConfirmAuthKey()) return;
            if (!xarModAPIFunc('pubsub','admin','deltemplate',
                               array('templateid' => $templateid))) {
                return;
            }
            xarResponseRedirect(xarModURL('pubsub', 'admin', 'modifytemplates'));
            return true;
            break;

        case 'recompile':
            if (!xarSecConfirmAuthKey()) return;
            foreach ($templates as $id => $templatename) {
                $info = xarModAPIFunc('pubsub','admin','gettemplate',
                                      array('templateid' => $id));
                if (empty($info)) continue;
                if (!xarModAPIFunc('pubsub','admin','updatetemplate',
                                   $info)) return;
            }
            xarResponseRedirect(xarModURL('pubsub', 'admin', 'modifytemplates'));
            return true;
            break;

        default:
            break;
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['recompile'] = xarModURL('pubsub','admin','modifytemplates',
                                   array('action' => 'recompile',
                                         'authid' => $data['authid']));

    return $data;
}

?>
