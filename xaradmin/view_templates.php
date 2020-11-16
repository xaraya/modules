<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
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
function pubsub_admin_view_templates()
{
    // Security Check
    if (!xarSecurityCheck('AdminPubSub')) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'pubsub_templates'));

    $data['templates'] = array();
    // get the list of available templates
    $templates = xarMod::apiFunc('pubsub', 'user', 'getalltemplates');
    foreach ($templates as $id => $templatename) {
        $data['templates'][$id] = array('name' => $templatename,
                                        'view' => xarModURL(
                                            'pubsub',
                                            'admin',
                                            'view_templates',
                                            array('action' => 'display',
                                                                  'id' => $id)
                                        ),
                                        'edit' => xarModURL(
                                            'pubsub',
                                            'admin',
                                            'view_templates',
                                            array('action' => 'modify',
                                                                  'id' => $id)
                                        ),
                                        'delete' => xarModURL(
                                            'pubsub',
                                            'admin',
                                            'view_templates',
                                            array('action' => 'delete',
                                                                    'id' => $id)
                                        )
                                       );
    }
    $data['new'] = xarModURL(
        'pubsub',
        'admin',
        'view_templates',
        array('action' => 'new')
    );

    xarVarFetch('id', 'int', $id, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('action', 'str:1:', $action, '', XARVAR_NOT_REQUIRED);
    if (!empty($id) && !empty($action)) {
        $info = xarMod::apiFunc(
            'pubsub',
            'user',
            'gettemplate',
            array('id' => $id)
        );
        if (empty($info)) {
            return;
        }
        $data['id'] = $id;
        $data['name'] = $info['name'];
        $data['template'] = xarVarPrepForDisplay($info['template']);
    }
    switch ($action) {
        case 'display':
        // TODO: adapt if/when we support more template variables in runjob()
            $tplData = array('userid' => xarUser::getVar('id'),
                             'name' => xarUser::getVar('uname'),
                             'module' => 'example',
                             'itemtype' => 0,
                             'itemid' => 123,
                             'title' => xarML('This is an item title'),
                             'link' => xarModURL(
                                 'example',
                                 'user',
                                 'display',
                                 array('exid' => 123)
                             ));
            $preview = xarTplString($info['compiled'], $tplData);
            $data['preview'] = xarVarPrepHTMLDisplay($preview);
            $data['action'] = 'display';
            break;

        case 'new':
            $data['name'] = '';
        // TODO: adapt if/when we support more template variables in runjob()
            $templatevariables = array('#$userid#','#$name#','#$module#','#$itemtype#','#$itemid#','#$title#','#$link#');
            $data['template'] = join("<br/>\n", $templatevariables);
            $data['id'] = 0;
            $data['submitbutton'] = xarML('Create Template');
            $data['action'] = 'create';
            break;

        case 'create':
            if (!xarSecConfirmAuthKey()) {
                return;
            }
            if (!xarVarFetch('name', 'str:1:', $name)) {
                return;
            }
            if (!xarVarFetch('template', 'str:1:', $template)) {
                return;
            }
            if (!xarMod::apiFunc(
                'pubsub',
                'admin',
                'addtemplate',
                array('name' => $name,
                                     'template' => $template)
            )) {
                return;
            }
            xarController::redirect(xarModURL('pubsub', 'admin', 'view_templates'));
            return true;
            break;

        case 'modify':
            $data['submitbutton'] = xarML('Update Template');
            $data['action'] = 'update';
            break;

        case 'update':
            if (!xarSecConfirmAuthKey()) {
                return;
            }
            if (!xarVarFetch('name', 'str:1:', $name)) {
                return;
            }
            if (!xarVarFetch('template', 'str:1:', $template)) {
                return;
            }
            if (!xarMod::apiFunc(
                'pubsub',
                'admin',
                'updatetemplate',
                array('id' => $id,
                                     'name' => $name,
                                     'template' => $template)
            )) {
                return;
            }
            xarController::redirect(xarModURL('pubsub', 'admin', 'view_templates'));
            return true;
            break;

        case 'delete':
            $data['submitbutton'] = xarML('Delete Template');
            $data['action'] = 'confirm';
            break;

        case 'confirm':
            if (!xarSecConfirmAuthKey()) {
                return;
            }
            if (!xarMod::apiFunc(
                'pubsub',
                'admin',
                'deltemplate',
                array('id' => $id)
            )) {
                return;
            }
            xarController::redirect(xarModURL('pubsub', 'admin', 'view_templates'));
            return true;
            break;

        case 'recompile':
            if (!xarSecConfirmAuthKey()) {
                return;
            }
            foreach ($templates as $id => $templatename) {
                $info = xarMod::apiFunc('pubsub', 'user', 'gettemplate', array('id' => $id));
                if (empty($info)) {
                    continue;
                }
                if (!xarMod::apiFunc(
                    'pubsub',
                    'admin',
                    'updatetemplate',
                    $info
                )) {
                    return;
                }
            }
            xarController::redirect(xarModURL('pubsub', 'admin', 'view_templates'));
            return true;
            break;

        default:
            break;
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['recompile'] = xarModURL(
        'pubsub',
        'admin',
        'view_templates',
        array('action' => 'recompile',
                                         'authid' => $data['authid'])
    );

    return $data;
}
