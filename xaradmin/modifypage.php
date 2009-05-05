<?php

/**
 * File: $Id$
 *
 * Modify or create a page
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_modifypage($args)
{
    extract($args);

    if (!xarVarFetch('creating', 'bool', $creating, true, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarVarFetch('pid', 'id', $pid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('ptid', 'id', $ptid, 0, XARVAR_DONT_SET)) {return;}

    if (!xarVarFetch('return_url', 'str:0:200', $return_url, '', XARVAR_DONT_SET)) {return;}

    $data = array();

    $data['return_url'] = $return_url;

    // TODO: move this.
    $data['batch'] = 0;

    // Bring in the access property for security checks
    sys::import('modules.dynamicdata.class.properties.master');
    $accessproperty = DataPropertyMaster::getProperty(array('name' => 'access'));
    $accessproperty->module = 'xarpages';
    $accessproperty->component = 'Page';

    if (!empty($pid)) {
        // Editing an existing page.

        // Setting up necessary data.
        $data['pid'] = $pid;
        $data['page'] = xarModAPIFunc(
            'xarpages', 'user', 'getpage',
            array('pid' => $pid)
        );

        $thisinstance = $data['page']['name'] . ':' . $data['page']['pagetype']['name'];

        // Decide whether this page can be modified by the current user
        $args = array(
            'instance' => $thisinstance,
            'group' => $data['page']['info']['modify_access']['group'],
            'level' => $data['page']['info']['modify_access']['level'],
        );
        $data['allowaccess'] = $accessproperty->check($args);

        // Decide whether this page can be deleted by the current user
        $args = array(
            'instance' => $thisinstance,
            'group' => $data['page']['info']['delete_access']['group'],
            'level' => $data['page']['info']['delete_access']['level'],
        );
        $data['delete_allowed'] = $accessproperty->check($args);
        
        $data['ptid'] = $data['page']['pagetype']['id'];

        // We need all pages, but with the current page tree pruned.
        $pages = xarModAPIFunc(
            'xarpages', 'user', 'getpagestree',
            array('left_exclude' => array($data['page']['left'], $data['page']['right']))
        );

        $data['func'] = 'modify';

        $hooks = xarModCallHooks(
            'item', 'modify', $pid,
            array(
                'module' => 'xarpages',
                'itemtype' => $data['page']['itemtype'],
                'itemid' => $data['page']['pid']
            )
        );
    } else {
        // Adding a new page

        if (!xarVarFetch('ptid', 'id', $ptid, 0, XARVAR_DONT_SET)) {return;}
        if (!xarVarFetch('insertpoint', 'id', $insertpoint, 0, XARVAR_DONT_SET)) {return;}
        if (!xarVarFetch('position', 'str', $position, 'after', XARVAR_DONT_SET)) {return;}

        // TODO: fix this batch stuff. When the batch flag is selected, we want to return
        // the user to the 'new page' screen after creating a page, but with the refernce
        // item and position/offset already set.
        // Perhaps we just need to set it to insert after (same level) the previously
        // created item?
        $data['insertpoint'] = $insertpoint; // Deprecated
        $data['refpid'] = $insertpoint;
        $data['position'] = $position;

        $data['func'] = 'create';
        $data['pid'] = NULL;
        $data['ptid'] = $ptid;

        if (empty($ptid)) {
            // The page type has not yet been chosen.
            // Get a list of page types.
            $pagetypes = xarModAPIfunc(
                'xarpages', 'user', 'get_types',
                array('key' => 'id')
            );

            // Check privileges of each page type: are we allowed to create
            // pages for each type? We may actually end up with no page types
            // but that depends on the permissions.
            foreach($pagetypes as $key => $pagetype) {
                // Decide whether this block can be modified by the current user
                $args = array(
                    'instance' => 'All' . ':' . $pagetype['name'],
                    'group' => $pagetype['info']['add_access']['group'],
                    'level' => $pagetype['info']['add_access']['level'],
                );
                if(!$accessproperty->check($args)) unset($pagetypes[$key]);                
            }

            $data['pagetypes'] = $pagetypes;

            // Return to the template immediately so the page type can be selected.
            return $data;
        } else {
            // We have a page type, now let the user create a page.
            // TODO: if there are any templates for this page type, present
            // the user with a selection to chose from. For now, just take the
            // the first template (if any) available.
            $templates = xarModAPIfunc(
                'xarpages', 'user', 'getpages',
                array('itemtype' => $ptid, 'status' => 'TEMPLATE')
            );
            if (count($templates) > 0) {
                $template = reset($templates);
            }

            // Get all pages.
            $pages = xarModAPIFunc('xarpages', 'user', 'getpagestree');

            $hooks = xarModCallHooks(
                'item', 'new', '',
                array('module' => 'xarpages', 'itemtype' => $ptid, 'itemid' => 0)
            );

            // Default data for the page form.
            $data['page'] = array(
                'left' => 0,
                'right' => 0,
                'name'=>'',
                'desc'=>'',
                'encode_url' => '',
                'decode_url' => '',
                'function' => '',
                'theme' => '',
                'status' => 'ACTIVE',
                'alias' => 0,
                'template' => '',
                'page_template' => '',
                'info' => serialize(array()),
                'pagetype' => xarModAPIfunc('xarpages', 'user', 'get_type', array('ptid' => $ptid))
            );

            // If we have a template, then set a few values up to initialise the new page form.
            if (!empty($template)) {
                $data['page']['name'] = $template['name'];
                $data['page']['desc'] = $template['desc'];
                $data['page']['encode_url'] = $template['encode_url'];
                $data['page']['decode_url'] = $template['decode_url'];
                $data['page']['function'] = $template['function'];
                $data['page']['theme'] = $template['theme'];
                $data['page']['template'] = $template['template'];
                $data['page']['page_template'] = $template['page_template'];
                $data['page']['info'] = $template['info'];
            }
        }
    }

    // Clear out any empty hooks, and truncate the remainder.
    if (isset($hooks)) {
        foreach($hooks as $key => $hook) {
            if (trim($hook) == '') {
                unset($hooks[$key]);
            } else {
                $hooks[$key] = trim($hook);
            }
        }
        $data['hooks'] =& $hooks;
    }

    // Implode the names for each page into a path for display.
    foreach ($pages['pages'] as $key => $page) {
        $pages['pages'][$key]['slash_separated'] =  '/' . implode('/', $page['namepath']);
    }
    $data['pages'] = $pages['pages'];

    $modinfo = xarModGetInfo(xarMod::getRegID('xarpages'));

    // Get lists of files in the various custom APIs.
    // Dynamicdata is a prerequisite for this module, so no need to check
    // whether it is available before using its API.
    $custom_apis = array();
    foreach(array('encode', 'decode', 'func') as $api) {
        $data['custom_apis'][$api] = xarModAPIfunc(
            'xarpages', 'user', 'browse_files',
            array(
                'module'=>'xarpages',
                'basedir'=>'xar'.$api.'api',
                'levels' => 1,
                'match_glob' => '*.php',
                'strip_re'=>'/.php$/'
            )
        );
    }

    // Get the list of available page templates.
    // TODO: create a property for doing this, as it can get a bit complex, and
    // is often needed, especially in respect of images.
    // Start with the default templates.
    $template_list = array();

    // The template will be prefixed by 'page-' and then the name of the page type.
    $template_prefix = 'page-' . $data['page']['pagetype']['name'] . '-';

    $templates = xarModAPIfunc(
        'xarpages', 'user', 'browse_files',
        array(
            'module'=>'xarpages',
            'basedir'=>'xartemplates',
            'levels' => 1,
            'match_glob' => $template_prefix . '*.xd',
            'strip_re'=>'/^'.$template_prefix.'|.xd$/'
        )
    );
    if (!empty($templates)) {
        foreach($templates as $template) {
            $template_list[$template] = 'xarpages: ' . $template;
        }
    }

    // Loop through the themes, and fetch any templates there.
    $themes = xarModAPIfunc('themes', 'admin', 'getlist', array('state' => XARTHEME_STATE_ACTIVE));
    foreach($themes as $theme) {
        // Check for templates for this module in the theme.
        $templates = xarModAPIfunc(
            'xarpages', 'user', 'browse_files',
            array(
                // TODO: find a way to avoid messing around with directory assumptions here.
                // Idealy this module should not need to know anything about this file structure.
                'basedir' => 'themes/' . $theme['osdirectory'] . '/modules/' . $modinfo['directory'],
                'levels' => 1,
                'match_glob' => $template_prefix . '*.xt',
                'strip_re'=>'/^'.$template_prefix.'|.xt$/'
            )
        );
        if (!empty($templates)) {
            foreach($templates as $template) {
                    $template_list[$template] = $theme['name'] . ': ' . $template;
            }
        }
    }

    $data['templates'] = $template_list;
    $data['themes'] = $themes;

    $data['statuses'] = xarModAPIfunc('xarpages', 'user', 'getstatuses');

    // Return output (allows a different admin page per page type)
    if (!empty($data['page']['pagetype']['name'])) {
        $pagetype = $data['page']['pagetype']['name'];
    } else {
        $pagetype = NULL;
    }

    return xarTplModule('xarpages', 'admin', 'modifypage', $data, $pagetype);
}

?>