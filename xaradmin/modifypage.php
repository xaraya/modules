<?php

/**
 * Update or create a page.
 * TODO: when creating a page of a specific type, allow pages of status 'template'
 * to be pulled in to provide default population of the page.
 */

function xarpages_admin_modifypage()
{
    if (!xarVarFetch('creating', 'bool', $creating, true, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarVarFetch('pid', 'id', $pid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('ptid', 'id', $ptid, 0, XARVAR_DONT_SET)) {return;}

    $data = array();

    // TODO: move this.
    $data['batch'] = 0;

    if (!empty($pid)) {
        // Editing an existing page.

        // Setting up necessary data.
        $data['pid'] = $pid;
        $data['page'] = xarModAPIFunc(
            'xarpages', 'user', 'getpage',
            array('pid' => $pid)
        );

        // Check we have minimum privs to edit this page.
        if (!xarSecurityCheck('EditPage', 1, 'Page', $data['page']['name'] . ':' . $data['page']['pagetype']['name'])) {
            return;
        }

        // Check the level of access we have. Are we allowed to rename or delete this page?
        if (xarSecurityCheck('DeletePage', 0, 'Page', $data['page']['name'] . ':' . $data['page']['pagetype']['name'])) {
            $data['delete_allowed'] = true;
        }
    
        $data['ptid'] = $data['page']['pagetype']['ptid'];

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

        // Check we are allowed to create pages.
        if (!xarSecurityCheck('AddPage', 1, 'Page', 'All')) {
            return;
        }

        if (!xarVarFetch('ptid', 'id', $ptid, 0, XARVAR_DONT_SET)) {return;}
        if (!xarVarFetch('insertpoint', 'id', $insertpoint, 0, XARVAR_DONT_SET)) {return;}
        if (!xarVarFetch('position', 'str', $position, 'after', XARVAR_DONT_SET)) {return;}

        // TODO: fix this batch stuff. When the batch flag is selected, we want to return
        // the user to the 'new page' screen after creating a page, but with the refernce
        // item and position/offset already set.
        // Perhaps we just need to set it to insert after (same level) the previously
        // created item?
        $data['insertpoint'] = $insertpoint;
        $data['position'] = $position;

        $data['func'] = 'create';
        $data['pid'] = NULL;
        $data['ptid'] = $ptid;

        if (empty($ptid)) {
            // The page type has not yet been chosen.
            // Get a list of page types.
            $pagetypes = xarModAPIfunc(
                'xarpages', 'user', 'gettypes',
                array('key' => 'ptid')
            );
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
                array('module' => 'xarpages', 'itemtype' => $ptid, 'itemid' => '')
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
                'pagetype' => xarModAPIfunc('xarpages', 'user', 'gettype', array('ptid' => $ptid))
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

    $modinfo = xarModGetInfo(xarModGetIDFromName('xarpages')); 

    // Get lists of files in the various custom APIs.
    // Dynamicdata is a prerequisite for this module, so no need to check
    // whether it is available before using its API.
    $custom_apis = array();
    foreach(array('encode', 'decode', 'func') as $api) {
        $custom_apis[$api] = xarModAPIfunc(
            'dynamicdata', 'admin', 'browse',
            array(
                'basedir' => 'modules/' . $modinfo['directory'] . '/xar' . $api . 'api',
                'filetype' => 'php'
            )
        );
        foreach($custom_apis[$api] as $key => $value) {
            $custom_apis[$api][$key] = preg_replace('/.php$/', '', $value);
        }
    }
    $data['custom_apis'] = $custom_apis;

    // Get the list of available page templates.
    // TODO: create a property for doing this, as it can get a bit complex, and
    // is often needed, especially in respect of images.
    // Start with the default templates.
    $template_list = array();

    // The template will be prefixed by 'page-' and then the name of the page type.
    $template_prefix = 'page-' . $data['page']['pagetype']['name'] . '-';

    $templates = xarModAPIfunc(
        'dynamicdata', 'admin', 'browse',
        array(
            'basedir' => 'modules/' . $modinfo['directory'] . '/xartemplates',
            'filetype' => 'xd'
        )
    );
    foreach($templates as $template) {
        if (preg_match('/^' . $template_prefix . '/', $template)) {
            $root = preg_replace(array('/^' . $template_prefix . '/', '/[.]xd$/'), '', $template);
            $template_list[$root] = 'xarpages: ' . $root;
        }
    }
    // Loop through the themes, and fetch any templates there.
    $themes = xarModAPIfunc('themes', 'admin', 'getlist', array('state' => XARTHEME_STATE_ACTIVE));
    foreach($themes as $theme) {
        // Check for templates for this module in the theme.
        $templates = xarModAPIfunc(
            'dynamicdata', 'admin', 'browse',
            array(
                'basedir' => 'themes/' . $theme['osdirectory'] . '/modules/' . $modinfo['directory'],
                'filetype' => 'xt'
            )
        );
        foreach($templates as $template) {
            if (preg_match('/^' . $template_prefix . '/', $template)) {
                $root = preg_replace(array('/^' . $template_prefix . '/', '/[.]xt$/'), '', $template);
                $template_list[$root] = $theme['name'] . ': ' . $root;
            }
        }
    }

    $data['templates'] = $template_list;
    $data['themes'] = $themes;

    $data['statuses'] = xarModAPIfunc('xarpages', 'user', 'getstatuses');

    // Return output
    return $data;
}

?>