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

    if (!empty($pid)) {
        // Editing an existing page.

        // Setting up necessary data.
        $data['pid'] = $pid;
        $data['page'] = xarModAPIFunc(
            'xarpages', 'user', 'getpage',
            array('pid' => $pid)
        );

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
        if (empty($hooks)) {
            $data['hooks'] = '';
        } elseif (is_array($hooks)) {
            $data['hooks'] = trim(join('', $hooks));
        }
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
        $data['insertpoint'] = $insertpoint;
        $data['position'] = $position;

        if (empty($ptid)) {
            // The page type has not yet been chosen.
            // Get a list of page types.
            $pagetypes = xarModAPIfunc(
                'xarpages', 'user', 'gettypes',
                array('key' => 'ptid')
            );
            $data['pagetypes'] = $pagetypes;
        } else {
            // We have a page type, now let the user create a page.

            // Get all pages.
            $pages = xarModAPIFunc('xarpages', 'user', 'getpagestree');

            $info = array();
            $info['module'] = 'xarpages';
            $info['itemtype'] = $ptid;
            $info['itemid'] = '';
            $hooks = xarModCallHooks('item', 'new', '', $info);
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = trim(join('', $hooks));
            }

            // Default data for the page form.
            $data['page'] = array(
                'left' => 0,
                'right' => 0,
                'name'=>'',
                'desc'=>'',
                'encode_url' => '',
                'decode_url' => '',
                'function' => '',
                'status' => 'ACTIVE',
                'pagetype' => xarModAPIfunc('xarpages', 'user', 'gettype', array('ptid' => $ptid))
            );
        }

        $data['func'] = 'create';
        $data['pid'] = NULL;
        $data['ptid'] = $ptid;
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
    $templates = xarModAPIfunc(
        'dynamicdata', 'admin', 'browse',
        array(
            'basedir' => 'modules/' . $modinfo['directory'] . '/xartemplates',
            'filetype' => 'xd'
        )
    );
    foreach($templates as $template) {
        if (preg_match('/^page-/', $template)) {
            $root = preg_replace(array('/^page-/', '/[.]xd$/'), '', $template);
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
            if (preg_match('/^page-/', $template)) {
                $root = preg_replace(array('/^page-/', '/[.]xt$/'), '', $template);
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