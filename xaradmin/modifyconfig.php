<?php
/**
 * Mailer Module
 *
 * @package modules
 * @subpackage mailer module
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main configuration page for the mailer object
 *
 */

// Use this version of the modifyconfig file when creating utility modules

    function mailer_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurityCheck('AdminMailer')) return;
        if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'mailer_general', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'mailer', XARVAR_NOT_REQUIRED)) return;
        $hooks = xarModCallHooks('module', 'getconfig', 'mailer');
        if (!empty($hooks) && isset($hooks['tabs'])) {
            foreach ($hooks['tabs'] as $key => $row) {
                $configarea[$key]  = $row['configarea'];
                $configtitle[$key] = $row['configtitle'];
                $configcontent[$key] = $row['configcontent'];
            }
            array_multisort($configtitle, SORT_ASC, $hooks['tabs']);
        } else {
            $hooks['tabs'] = array();
        }

        $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'mailer'));
        $data['module_settings']->setFieldList('items_per_page, use_module_alias, enable_short_urls');
        $data['module_settings']->getItem();

        $regid = xarMod::getRegID($tabmodule);
        switch (strtolower($phase)) {
            case 'modify':
            default:
                switch ($data['tab']) {
                    case 'mailer_general':
                        break;
                    case 'tab2':
                        break;
                    case 'tab3':
                        break;
                    default:
                        break;
                }

                break;

            case 'update':
                // Confirm authorisation code
                if (!xarSecConfirmAuthKey()) return;
                if (!xarVarFetch('defaultmastertable',    'str',      $defaultmastertable, xarModVars::get('mailer', 'defaultmastertable'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultuserobject',    'str',      $defaultuserobject, xarModVars::get('mailer', 'defaultuserobject'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultmailobject',    'str',      $defaultmailobject, xarModVars::get('mailer', 'defaultmailobject'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultrecipientname',    'str',      $defaultrecipientname, xarModVars::get('mailer', 'defaultrecipientname'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultsendername',    'str',      $defaultsendername, xarModVars::get('mailer', 'defaultsendername'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultsenderaddress',    'str',      $defaultsenderaddress, xarModVars::get('mailer', 'defaultsenderaddress'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultlocale',    'str',      $defaultlocale, xarModVars::get('mailer', 'defaultlocale'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultredirect',    'checkbox',      $defaultredirect, xarModVars::get('mailer', 'defaultredirect'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultredirectaddress',    'str',      $defaultredirectaddress, xarModVars::get('mailer', 'defaultredirectaddress'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('savetodb',    'checkbox',      $savetodb, xarModVars::get('mailer', 'savetodb'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultheader_x_mailer', 'str', $defaultheader_x_mailer,  xarModVars::get('mailer', 'defaultheader_x_mailer'), XARVAR_NOT_REQUIRED)) return;
                
                $isvalid = $data['module_settings']->checkInput();
                if (!$isvalid) {
                    return xarTplModule('dynamicdata','admin','modifyconfig', $data);
                } else {
                    $itemid = $data['module_settings']->updateItem();
                }

                $modvars = array(
                                'defaultmastertable',
                                'defaultuserobject',
                                'defaultmailobject',
                                'defaultrecipientname',
                                'defaultsendername',
                                'defaultsenderaddress',
                                'defaultlocale',
                                'defaultredirect',
                                'defaultredirectaddress',
                                'savetodb',
                                'defaultheader_x_mailer'
                                );

                if ($data['tab'] == 'mailer_general') {
                    foreach ($modvars as $var) if (isset($$var)) xarModVars::set('mailer', $var, $$var);
                }
                foreach ($modvars as $var) if (isset($$var)) xarModItemVars::set('mailer', $var, $$var, $regid);

                xarResponse::redirect(xarModURL('mailer', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
                // Return
                return true;
                break;

        }
        $data['hooks'] = $hooks;
        $data['tabmodule'] = $tabmodule;
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>
