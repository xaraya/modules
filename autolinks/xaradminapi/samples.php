<?php

/**
 * Create sample data (autolink types, links, DD objects, properties, etc.)
 * @param action - 'create' or 'get': create samples or return the samples array.
 * @returns int
 * @return autolink ID on success, false on failure
 */
function autolinks_adminapi_samples($args)
{
    // If required, create default autolink type and update unlinked autolinks to point to it.
    // TODO: revisit error handling when a better upgrade model is available.
    // TODO: structure so each new sample autolink type can be added for each upgrade.

    extract($args);

    if (!isset($action)) {$action = 'noop';}

    $setuptypes = array(
        array(
            'type_name' => xarML('Standard autolink'),
            'template_name' => 'standard',
            'default' => true,
            'links' => array(
                array(
                    'name' => xarML('Xaraya home'),
                    'keyword' => 'xaraya[\'s]*',
                    'match_re' => '1',
                    'title' => 'Xaraya - Content Management System',
                    'url' => 'http://www.xaraya.com/',
                    'comment' => 'Xaraya home page.',
                    'enabled' => '1'
                )
            )
        ),
        array(
            'type_name' => xarML('Sample autolink type'),
            'template_name' => 'sample1',
            'type_desc' => xarML('URL in [square brackets] after the matched keyword. No DD fields are needed.')
        ),
        array(
            'type_name' => xarML('External'),
            'template_name' => 'external',
            'type_desc' => xarML('External URLs. Opens in an "external" window. These URLs are marked up with a "WWW" world icon.'
                .' To extend this, you can create a DD field named "icon" and enter a different icon file name.'),
            'dd_object' => array(
                array(
                    'name' => 'imgalt',
                    'label' => xarML('Image alt text'),
                    'type' => 2, // 'textbox'
                    'default' => ''
                ),
                array(
                    'name' => 'icon',
                    'label' => xarML('Icon File Name'),
                    'type' => 2, // 'textbox'
                    'default' => ''
                ),
                array(
                    'name' => 'target',
                    'label' => xarML('Target'),
                    'type' => 2, // 'textbox'
                    'default' => ''
                )
            ),
            'links' => array(
                array(
                    'name' => xarML('Any external HTTP URL'),
                    'keyword' => '(http://(?!demo.xaraya.com)[-.a-z]+/)[^\s.;?!]*',
                    'match_re' => '1',
                    'title' => 'Visit the site: $2',
                    'url' => '$1',
                    'comment' => 'Matches any URL to an external website home page. Does not match the current site (demo.xaraya.com in this example) - which is left up to other links to catch.',
                    'sample' => 'http://www.xaraya.com/ http://demo.xaraya.com/ http://xxx/abc/123',
                    'enabled' => '0',
                    'dd_properties' => array (
                        'imgalt' => '',
                        'icon' => 'icon_www.gif',
                        'target' => 'external'
                    )
                )
            ),
        ),
        array(
            'type_name' => xarML('Articles'),
            'template_name' => 'article',
            'dynamic_replace' => '1',
            'type_desc' => xarML('Various links for fetching articles links. Don\'t forget to hook in DD to view these links correctly. Change these examples to fit the way you want to use them in the site.'),
            'dd_object' => array(
                array(
                    'name' => 'aid',
                    'label' => xarML('Article ID'),
                    'type' => 2, // 'textbox'
                    'default' => '$2'
                ),
                array(
                    'name' => 'ptid',
                    'label' => xarML('Publication Type ID'),
                    'type' => 2, // 'textbox'
                    'default' => '$3'
                ),
                array(
                    'name' => 'text',
                    'label' => xarML('Alternative Text (optional)'),
                    'type' => 2, // 'textbox'
                    'default' => ''
                )
            ),
            'links' => array(
                array(
                    'name' => xarML('Article title by article ID'),
                    'keyword' => '\[article:title:aid:([\d]+)\]',
                    'match_re' => '1',
                    'title' => '',
                    'url' => 'display',
                    'comment' => 'Use format: [article:title:aid:<article-id>]',
                    'sample' => 'Valid article: &#91;article:title:aid:1] = [article:title:aid:1]; invalid: &#91;article:title:aid:9999] = [article:title:aid:9999]',
                    'enabled' => '0',
                    'dd_properties' => array (
                        'aid' => '$2',
                        'ptid' => '',
                        'text' => ''
                    )
                ),
                array(
                    'name' => xarML('readfirst'),
                    'keyword' => '\[readfirst:([\d]+)\]',
                    'match_re' => '1',
                    'title' => '',
                    'url' => 'readfirst',
                    'comment' => 'Use format: [readfirst:<article-id>]',
                    'sample' => '&#91;readfirst:1] = [readfirst:1]',
                    'enabled' => '0',
                    'dd_properties' => array (
                        'aid' => '$2',
                        'ptid' => '',
                        'text' => ''
                    )
                ),
                array(
                    'name' => xarML('readfirst2'),
                    'keyword' => '\[readfirst:([\d]+):([\d]+)\]',
                    'match_re' => '1',
                    'title' => '',
                    'url' => 'readfirst',
                    'comment' => 'Use format: [readfirst:<aid>:<ptid>]',
                    'sample' => '&#91;readfirst:1:2] = [readfirst:1:2]',
                    'enabled' => '0',
                    'dd_properties' => array (
                        'aid' => '$2',
                        'ptid' => '$3',
                        'text' => ''
                    )
                ),
                array(
                    'name' => xarML('readnext'),
                    'keyword' => '\[readnext:([\d]+)\]',
                    'match_re' => '1',
                    'title' => '',
                    'url' => 'readnext',
                    'comment' => 'Use format: [readnext:<article-id>]',
                    'sample' => '&#91;readnext:1] = [readnext:1]',
                    'enabled' => '0',
                    'dd_properties' => array (
                        'aid' => '$2',
                        'ptid' => '',
                        'text' => ''
                    )
                ),
                array(
                    'name' => xarML('readlast'),
                    'keyword' => '\[readlast:([\d]+)\]',
                    'match_re' => '1',
                    'title' => '',
                    'url' => 'readlast',
                    'comment' => 'Use format: [readfirst:<article-id>]',
                    'sample' => '&#91;readlast:1] = [readlast:1]',
                    'enabled' => '0',
                    'dd_properties' => array (
                        'aid' => '$2',
                        'ptid' => '',
                        'text' => ''
                    )
               ),
            )
        ),
        array(
            'type_name' => xarML('Generic Module'),
            'template_name' => 'generic',
            'dynamic_replace' => '0',
            'type_desc' => xarML('Allows generic links to module functions to be set up. DD fields are numerous and described in the template.'),
            'dd_object' => array(
                array(
                    'name' => 'module',
                    'label' => xarML('Module'),
                    'type' => 2, // 'textbox'
                    'default' => ''
                    // Also 'source', 'status', 'order' and 'validation'
                ),
                array(
                    'name' => 'type',
                    'label' => xarML('Module Type'),
                    'type' => 2, // 'textbox'
                    'default' => 'user'
                ),
                array(
                    'name' => 'func',
                    'label' => xarML('Function'),
                    'type' => 2, // 'textbox'
                    'default' => 'display'
                ),
                array(
                    'name' => 'text',
                    'label' => xarML('Link Text (optional)'),
                    'type' => 2, // 'textbox'
                    'default' => ''
                ),
                array(
                    'name' => 'idname',
                    'label' => xarML('ID1 Name'),
                    'type' => 2, // 'textbox'
                    'default' => 'id'
                ),
                array(
                    'name' => 'idvalue',
                    'label' => xarML('ID1 Value'),
                    'type' => 2, // 'textbox'
                    'default' => '$2'
                ),
                array(
                    'name' => 'idname2',
                    'label' => xarML('ID2 Name'),
                    'type' => 2, // 'textbox'
                    'default' => ''
                ),
                array(
                    'name' => 'idvalue2',
                    'label' => xarML('ID2 Value'),
                    'type' => 2, // 'textbox'
                    'default' => ''
                ),
                array(
                    'name' => 'target',
                    'label' => xarML('Target'),
                    'type' => 2, // 'textbox'
                    'default' => ''
                )
            ),
            'links' => array(
                array(
                    'name' => xarML('Uploads'),
                    'keyword' => '#ulid:([\d]+)#',
                    'match_re' => '1',
                    'title' => '',
                    'url' => '#',
                    'comment' => 'Use format: [ulid:<upload-id>]',
                    'sample' => '&#35;ulid:1# = #ulid:1#',
                    'enabled' => '0',
                    'dd_properties' => array (
                        'module' => 'uploads',
                        'type' => 'admin',
                        'func' => 'download',
                        'idname' => 'dlid',
                        'idvalue' => '$2'
                    )
                )
            )
        )
    );

    // Create some autolink types where they do not exist.

    if ($action == 'create') {
        // Security check
        if(!xarSecurityCheck('AdminAutolinks')) {return;}

        foreach ($setuptypes as $setuptype) {
            // Check if a type for that template exists.
            $links = xarModAPIfunc(
                'autolinks', 'user', 'getalltypes',
                array('template_name' => $setuptype['template_name'])
            );

            if (!$links) {
                // Unset the IDs from the previous type.
                unset($objectid);

                // Create the autolink type
                $tid = xarModAPIfunc('autolinks', 'admin', 'createtype', $setuptype);
                if ($tid) {
                    // Now if this is the default type, point existing links to it.
                    if (!empty($setuptype['default'])) {
                        // Scan the current autolinks for tids to be updated.
                        $links = xarModAPIfunc('autolinks', 'user', 'getall');
                        if (is_array($links)) {
                            foreach ($links as $lid => $link) {
                                if ($links['tid'] == 0 || $links['type_name'] == '') {
                                    // Update the tid in this link.
                                    $result = xarModAPIfunc('autolinks', 'admin', 'update',
                                        array('lid'=>$lid, 'tid'=>$tid));
                                }
                            }
                        }
                    }

                    // Fetch the item type back.
                    $type = xarModAPIfunc('autolinks', 'user', 'gettype', array('tid' => $tid));

                    // If there is a DD object to create, then do that.
                    if (isset($setuptype['dd_object'])) {
                        // Is DD active?
                        if (xarModIsAvailable('dynamicdata')) {
                            // Details of the object.
                            $newobject['moduleid'] = xarModGetIDFromName('autolinks');
                            $newobject['urlparam'] = 'lid';
                            $newobject['itemtype'] = $type['itemtype'];
                            $newobject['name'] = 'autolinks_' . $type['itemtype'];
                            $newobject['label'] = 'Autolinks_' . $type['itemtype'];

                            // Create the dynamic object.
                            $objectid = xarModAPIfunc(
                                'dynamicdata', 'admin', 'createobject', $newobject
                            );

                            if (xarExceptionMajor()) {
                                // Fudge over any errors that occur, for now.
                                xarExceptionHandled();
                            } else {
                                // Loop for each property to create.
                                $order = 1;
                                foreach ($setuptype['dd_object'] as $property) {
                                    // Create the property
                                    $property['objectid'] = $objectid;
                                    $property['moduleid'] = xarModGetIDFromName('autolinks');
                                    $property['itemtype'] = $type['itemtype'];
                                    $property['order'] = $order;
                                    $order += 1;
                                    $result = xarModAPIfunc(
                                        'dynamicdata', 'admin', 'createproperty', $property
                                    );
                                    if (xarExceptionMajor()) {return;} // Or handled.
                                }
                            }
                        }
                    }

                    // If there are example links to add, do them.
                    if (isset($setuptype['links'])) {
                        foreach ($setuptype['links'] as $samplelink) {
                            $samplelink['tid'] = $tid;
                            $lid = xarModAPIfunc('autolinks', 'admin', 'create', $samplelink);

                            if ($lid && isset($samplelink['dd_properties'])) {
                                // Get the link back.
                                $link = xarModAPIfunc('autolinks', 'user', 'get', array('lid' => $lid));

                                // There are dd properties to configure.
                                $result = xarModAPIfunc(
                                    'dynamicdata', 'admin', 'create',
                                    array(
                                        'itemid' => $lid,
                                        'itemtype' => $link['itemtype'],
                                        'modid' => xarModGetIDFromName('autolinks'),
                                        'values' => $samplelink['dd_properties']
                                    )
                                );

                                // If the link is non-dynamic, then recompile it now the DD
                                // property values are set.
                                if (!$type['dynamic_replace']) {
                                    $result = xarModAPIfunc(
                                        'autolinks', 'admin', 'update',
                                        array('lid' => $lid, 'enabled' => $link['enabled'])
                                    );
                                }
                            }
                        }
                    }
                } else {
                    if (xarExceptionMajor()) {
                        xarExceptionHandled();
                    }
                }
            }
        }
        return true;
    }

    if ($action == 'get') {
        return $setuptypes;
    }

    return false;
}

?>