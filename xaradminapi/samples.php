<?php

/**
 * Create sample data (autolink types, links, DD objects, properties, etc.)
 * @param action - 'create' or 'get': create samples or return the samples array.
 * @return int autolink ID on success, false on failure
 * @todo convert this into a multi-lingual XML file and an import function
 */

function autolinks_adminapi_samples($args)
{
    // If required, create default autolink type and update unlinked autolinks to point to it.
    // TODO: revisit error handling when a better upgrade model is available.
    // TODO: structure so each new sample autolink type can be added for each upgrade.

    extract($args);

    if (!isset($action)) {$action = 'noop';}

    $setuptypes = array(
        'autolink-types' => array(
            'autolink-type:tid:1' => array(
                'type_name' => xarML('Standard autolink'),
                'template_name' => 'standard',
                'default' => true,
                'links' => array(
                    'link:lid:1' => array(
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
            'autolink-type:tid:2' => array(
                'type_name' => xarML('Sample autolink type'),
                'template_name' => 'sample1',
                'type_desc' => xarML('URL in [square brackets] after the matched keyword. No DD fields are needed.')
            ),
            'autolink-type:tid:3' => array(
                'type_name' => xarML('External'),
                'template_name' => 'external',
                'type_desc' => xarML('External URLs. Opens in an "external" window. These URLs are marked up with a "WWW" world icon.'
                    .' To extend this, you can create a DD field named "icon" and enter a different icon file name.'),
                'dd_object' => array(
                    'property:pid:1' => array(
                        'name' => 'imgalt',
                        'label' => xarML('Image alt text'),
                        'type' => 2, // 'textbox'
                        'default' => ''
                    ),
                    'property:pid:2' => array(
                        'name' => 'icon',
                        'label' => xarML('Icon File Name'),
                        'type' => 2, // 'textbox'
                        'default' => ''
                    ),
                    'property:pid:2' => array(
                        'name' => 'target',
                        'label' => xarML('Target'),
                        'type' => 2, // 'textbox'
                        'default' => ''
                    )
                ),
                'links' => array(
                    'link:lid:1' => array(
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
            'autolink-type:tid:4' => array(
                'type_name' => xarML('Articles'),
                'template_name' => 'article',
                'dynamic_replace' => '1',
                'type_desc' => xarML('Various links for fetching articles links. Don\'t forget to hook in DD to view these links correctly. Change these examples to fit the way you want to use them in the site.'),
                'dd_object' => array(
                    'property:pid:1' => array(
                        'name' => 'aid',
                        'label' => xarML('Article ID'),
                        'type' => 2, // 'textbox'
                        'default' => '$2'
                    ),
                    'property:pid:2' => array(
                        'name' => 'ptid',
                        'label' => xarML('Publication Type ID'),
                        'type' => 2, // 'textbox'
                        'default' => '$3'
                    ),
                    'property:pid:3' => array(
                        'name' => 'text',
                        'label' => xarML('Alternative Text (optional)'),
                        'type' => 2, // 'textbox'
                        'default' => ''
                    )
                ),
                'links' => array(
                    'link:lid:1' => array(
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
                    'link:lid:2' => array(
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
                    'link:lid:2' => array(
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
                    'link:lid:3' => array(
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
                    'link:lid:4' => array(
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

            'autolink-type:tid:5' => array(
                'type_name' => xarML('Generic Module'),
                'template_name' => 'generic',
                'dynamic_replace' => '0',
                'type_desc' => xarML('Allows generic links to module functions to be set up. DD fields are numerous and described in the template.'),
                'dd_object' => array(
                    'property:pid:1' => array(
                        'name' => 'modulename',
                        'label' => xarML('Module'),
                        'type' => 2, // 'textbox'
                        'default' => ''
                        // Also 'source', 'status', 'order' and 'validation'
                    ),
                    'property:pid:2' => array(
                        'name' => 'type',
                        'label' => xarML('Module Type'),
                        'type' => 2, // 'textbox'
                        'default' => 'user'
                    ),
                    'property:pid:3' => array(
                        'name' => 'func',
                        'label' => xarML('Function'),
                        'type' => 2, // 'textbox'
                        'default' => 'display'
                    ),
                    'property:pid:4' => array(
                        'name' => 'text',
                        'label' => xarML('Link Text (optional)'),
                        'type' => 2, // 'textbox'
                        'default' => ''
                    ),
                    'property:pid:5' => array(
                        'name' => 'idname',
                        'label' => xarML('ID1 Name'),
                        'type' => 2, // 'textbox'
                        'default' => 'id'
                    ),
                    'property:pid:6' => array(
                        'name' => 'idvalue',
                        'label' => xarML('ID1 Value'),
                        'type' => 2, // 'textbox'
                        'default' => '$2'
                    ),
                    'property:pid:7' => array(
                        'name' => 'idname2',
                        'label' => xarML('ID2 Name'),
                        'type' => 2, // 'textbox'
                        'default' => ''
                    ),
                    'property:pid:8' => array(
                        'name' => 'idvalue2',
                        'label' => xarML('ID2 Value'),
                        'type' => 2, // 'textbox'
                        'default' => ''
                    ),
                    'property:pid:9' => array(
                        'name' => 'target',
                        'label' => xarML('Target'),
                        'type' => 2, // 'textbox'
                        'default' => ''
                    )
                ),
                'links' => array(
                    'link:lid:9' => array(
                        'name' => xarML('uploads'),
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
            ),
            'autolink-type:tid:6' => array (
                'type_name' => xarML('Glossary Item'),
                'template_name' => 'glossary',
                'dynamic_replace' => '1',
                'type_desc' => xarML('Provides glossary links. The links will refresh the page with the glossary word passed in as an extra GET parameter. The URL is not used - set it to "blank" or "none".'),
                'dd_object' => array (
                    'property:pid:1' => array (
                        'name' => 'replace',
                        'label' => xarML('Replace Text'),
                        'type' => 2, // 'textbox'
                        'default' => '$1'
                        // Also 'source', 'status', 'order' and 'validation'
                    ),
                    'property:pid:2' => array (
                        'name' => 'term',
                        'label' => xarML('Glossary Term (for URL)'),
                        'type' => 2, // 'textbox'
                        'default' => '$1'
                        // Also 'source', 'status', 'order' and 'validation'
                    )
                ),
                'links' => array(
                    'link:lid:10' => array(
                        'name' => xarML('glossary'),
                        'keyword' => 'glossary',
                        'match_re' => '0',
                        'title' => '',
                        'url' => 'none',
                        'comment' => '',
                        'sample' => 'Link glossary item',
                        'enabled' => '0'
                    )
                )
            ),

            'autolink-type:tid:7' => array(
                'type_name' => xarML('External CSS'),
                'template_name' => 'externalcss',
                'dynamic_replace' => '0',
                'type_desc' => xarML('External URLs. These URLs are given an attribute "external".'
                    .' Your theme will need to provide the styling for these external links.'),
                'links' => array(
                    'link:lid:1' => array(
                        'name' => xarML('external_url'),
                        'keyword' => '(http://(?!demo.xaraya.com)[-.a-z]+/)[^\s.;?!]*',
                        'match_re' => '1',
                        'title' => 'Visit the site: $2',
                        'url' => '$1',
                        'comment' => 'Matches any URL to an external website home page. Does not match the current site (demo.xaraya.com in this example) - which is left up to other links to catch.',
                        'sample' => 'http://www.xaraya.com/ http://demo.xaraya.com/ http://xxx/abc/123',
                        'enabled' => '0'
                    )
                ),
            ),

            'autolink-type:tid:8' => array(
                'type_name' => xarML('Mailto'),
                'template_name' => 'mailto',
                'dynamic_replace' => '0',
                'type_desc' => xarML('E-mail links, that may be obfuscated.'),
                'links' => array(
                    'link:lid:1' => array(
                        'name' => xarML('mailto'),
                        'keyword' => 'mailto:(([A-Za-z._]+)@([A-Za-z._]+))',
                        'match_re' => '1',
                        'title' => 'Send an e-mail to $3',
                        'url' => '$3&#064;$4',
                        'comment' => 'Matches "mailto:x@y.z" strings',
                        'sample' => 'mailto:xaraya@example.com mailto:fred@com',
                        'enabled' => '0'
                    )
                ),
            )

        )
    );

    // Create some autolink types where they do not exist.

    if ($action == 'create') {
        // Security check
        if (!xarSecurityCheck('AdminAutolinks')) {return;}

        foreach ($setuptypes['autolink-types'] as $setuptype) {
            // Check if a type for that template exists.
            $links = xarModAPIfunc(
                'autolinks', 'user', 'getalltypes',
                array('template_name' => $setuptype['template_name'])
            );

            if (!$links) {
                // Unset the IDs from the previous type.
                unset($objectid);

                // Create the autolink type
                xarVarValidate('pre:lower', $setuptype['type_name']);
                $tid = xarModAPIfunc('autolinks', 'admin', 'createtype', $setuptype);
                if ($tid) {
                    // Now if this is the default type, point existing links to it.
                    if (!empty($setuptype['default'])) {
                        // Scan the current autolinks for tids to be updated.
                        $links = xarModAPIfunc('autolinks', 'user', 'getall');
                        if (is_array($links)) {
                            foreach ($links as $lid => $link) {
                                if ($link['tid'] == 0 || $link['type_name'] == '') {
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

                            if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {
                                // Fudge over any errors that occur, for now.
                                xarErrorHandled();
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
                                    if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {return;} // Or handled.
                                }
                            }
                        }
                    }

                    // If there are example links to add, do them.
                    if (isset($setuptype['links'])) {
                        foreach ($setuptype['links'] as $samplelink) {
                            $samplelink['tid'] = $tid;
                            xarVarValidate('pre:ftoken:lower', $samplelink['name']);
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
                    if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {
                        xarErrorHandled();
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