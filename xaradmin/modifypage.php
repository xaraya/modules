<?php

/**
 * File: $Id$
 *
 * Modify or create a page
 *
 * @package Xaraya
 * @copyright (C) 2004-2009 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_modifypage($args)
{
    extract($args);

    if (!xarVar::fetch('creating', 'bool', $creating, true, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('pid', 'id', $pid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('ptid', 'id', $ptid, 0, xarVar::DONT_SET)) {
        return;
    }

    if (!xarVar::fetch('return_url', 'str:0:200', $return_url, '', xarVar::DONT_SET)) {
        return;
    }

    $data = [];

    $data['return_url'] = $return_url;

    // TODO: move this.
    $data['batch'] = 0;

    if (!empty($pid)) {
        // Editing an existing page.

        // Setting up necessary data.
        $data['pid'] = $pid;
        $data['page'] = xarMod::apiFunc(
            'xarpages',
            'user',
            'getpage',
            ['pid' => $pid]
        );

        // Check we have minimum privs to edit this page.
        if (!xarSecurity::check('EditXarpagesPage', 1, 'Page', $data['page']['name'] . ':' . $data['page']['pagetype']['name'])) {
            return;
        }

        // Check the level of access we have. Are we allowed to rename or delete this page?
        if (xarSecurity::check('DeleteXarpagesPage', 0, 'Page', $data['page']['name'] . ':' . $data['page']['pagetype']['name'])) {
            $data['delete_allowed'] = true;
        }

        $data['ptid'] = $data['page']['pagetype']['ptid'];

        // We need all pages, but with the current page tree pruned.
        $pages = xarMod::apiFunc(
            'xarpages',
            'user',
            'getpagestree',
            ['left_exclude' => [$data['page']['left'], $data['page']['right']]]
        );

        $data['func'] = 'modify';

        $hooks = xarModHooks::call(
            'item',
            'modify',
            $pid,
            [
                'module' => 'xarpages',
                'itemtype' => $data['page']['itemtype'],
                'itemid' => $data['page']['pid'],
            ]
        );
    } else {
        // Adding a new page

        // Check we are allowed to create pages.
        if (!xarSecurity::check('AddXarpagesPage', 1, 'Page', 'All')) {
            return;
        }

        if (!xarVar::fetch('ptid', 'id', $ptid, 0, xarVar::DONT_SET)) {
            return;
        }
        if (!xarVar::fetch('insertpoint', 'id', $insertpoint, 0, xarVar::DONT_SET)) {
            return;
        }
        if (!xarVar::fetch('position', 'str', $position, 'after', xarVar::DONT_SET)) {
            return;
        }

        // TODO: fix this batch stuff. When the batch flag is selected, we want to return
        // the user to the 'new page' screen after creating a page, but with the refernce
        // item and position/offset already set.
        // Perhaps we just need to set it to insert after (same level) the previously
        // created item?
        $data['insertpoint'] = $insertpoint; // Deprecated
        $data['refpid'] = $insertpoint;
        $data['position'] = $position;

        $data['func'] = 'create';
        $data['pid'] = null;
        $data['ptid'] = $ptid;

        if (empty($ptid)) {
            // The page type has not yet been chosen.
            // Get a list of page types.
            $pagetypes = xarMod::apiFunc(
                'xarpages',
                'user',
                'gettypes',
                ['key' => 'ptid']
            );

            // Check privileges of each page type: are we allowed to create
            // pages for each type? We may actually end up with no page types
            // but that depends on the permissions.
            foreach ($pagetypes as $key => $pagetype) {
                if (!xarSecurity::check('AddXarpagesPage', 0, 'Page', 'All' . ':' . $pagetype['name'])) {
                    unset($pagetypes[$key]);
                }
            }

            $data['pagetypes'] = $pagetypes;

            // Return to the template immediately so the page type can be selected.
            return $data;
        } else {
            // We have a page type, now let the user create a page.
            // TODO: if there are any templates for this page type, present
            // the user with a selection to chose from. For now, just take the
            // the first template (if any) available.
            $templates = xarMod::apiFunc(
                'xarpages',
                'user',
                'getpages',
                ['itemtype' => $ptid, 'status' => 'TEMPLATE']
            );
            if (count($templates) > 0) {
                $template = reset($templates);
            }

            // Get all pages.
            $pages = xarMod::apiFunc('xarpages', 'user', 'getpagestree');

            $hooks = xarModHooks::call(
                'item',
                'new',
                '',
                ['module' => 'xarpages', 'itemtype' => $ptid, 'itemid' => '']
            );

            // Default data for the page form.
            $data['page'] = [
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
                'pagetype' => xarMod::apiFunc('xarpages', 'user', 'gettype', ['ptid' => $ptid]),
            ];

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
            }
        }
    }

    // Clear out any empty hooks, and truncate the remainder.
    if (isset($hooks)) {
        foreach ($hooks as $key => $hook) {
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

    $modinfo = xarMod::getInfo(xarMod::getRegId('xarpages'));

    // Get lists of files in the various custom APIs.
    // Dynamicdata is a prerequisite for this module, so no need to check
    // whether it is available before using its API.
    $custom_apis = [];
    foreach (['encode', 'decode', 'func'] as $api) {
        $data['custom_apis'][$api] = xarMod::apiFunc(
            'xarpages',
            'user',
            'browse_files',
            [
                'module'=>'xarpages',
                'basedir'=>'xar'.$api.'api',
                'levels' => 1,
                'match_glob' => '*.php',
                'strip_re'=>'/.php$/',
            ]
        );
    }

    // Get the list of available page templates.
    // TODO: create a property for doing this, as it can get a bit complex, and
    // is often needed, especially in respect of images.
    // Start with the default templates.
    $template_list = [];

    // The template will be prefixed by 'page-' and then the name of the page type.
    $template_prefix = 'page-' . $data['page']['pagetype']['name'] . '-';

    $templates = xarMod::apiFunc(
        'xarpages',
        'user',
        'browse_files',
        [
            'module'=>'xarpages',
            'basedir'=>'xartemplates',
            'levels' => 1,
            'match_glob' => $template_prefix . '*.x[td]',
            'strip_re'=>'/^'.$template_prefix.'|.x[td]$/',
        ]
    );
    if (!empty($templates)) {
        foreach ($templates as $template) {
            $template_list[$template] = 'xarpages: ' . $template;
        }
    }

    // Loop through the themes, and fetch any templates there.
    $themes = xarMod::apiFunc('themes', 'admin', 'getlist', ['state' => xarTheme::STATE_ACTIVE]);
    foreach ($themes as $theme) {
        // Check for templates for this module in the theme.
        $templates = xarMod::apiFunc(
            'xarpages',
            'user',
            'browse_files',
            [
                // TODO: find a way to avoid messing around with directory assumptions here.
                // Idealy this module should not need to know anything about this file structure.
                'basedir' => 'themes/' . $theme['osdirectory'] . '/modules/' . $modinfo['directory'],
                'levels' => 1,
                'match_glob' => $template_prefix . '*.xt',
                'strip_re'=>'/^'.$template_prefix.'|.xt$/',
            ]
        );
        if (!empty($templates)) {
            foreach ($templates as $template) {
                $template_list[$template] = $theme['name'] . ': ' . $template;
            }
        }
    }

    $data['templates'] = $template_list;
    $data['themes'] = $themes;

    $data['statuses'] = xarMod::apiFunc('xarpages', 'user', 'getstatuses');

    // Return output (allows a different admin page per page type)
    if (!empty($data['page']['pagetype']['name'])) {
        $pagetype = $data['page']['pagetype']['name'];
    } else {
        $pagetype = null;
    }

    return xarTpl::module('xarpages', 'admin', 'modifypage', $data, $pagetype);
}
