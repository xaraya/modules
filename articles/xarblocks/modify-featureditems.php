<?php
// File: featureditems.php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Err, well, this file was created by
// Jonn Beames, but it consist almost exlusively of code originally by
// Jim McDonald, MikeC, and Mike(of mikespub fame) taken from the
// topitems.php block of the articles module.  And Richard Cave gave me
// help with the multiselect box.
// Purpose of file: Featured Articles Block
// ----------------------------------------------------------------------

/**
 * modify block settings
 */

function articles_featureditemsblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['pubtypeid'])) {$vars['pubtypeid'] = '';}
    if (empty($vars['catfilter'])) {$vars['catfilter'] = '';}
    if (empty($vars['status'])) {$vars['status'] = array(3, 2);}
    if (empty($vars['itemlimit'])) {$vars['itemlimit'] = 10;}
    if (empty($vars['featuredaid'])) {$vars['featuredaid'] = 0;}
    if (empty($vars['alttitle'])) {$vars['alttitle'] = '';}
    if (empty($vars['showfeaturedsum'])) {$vars['showfeaturedsum'] = false;}
    if (empty($vars['moreitems'])) {$vars['moreitems'] = array();}
    if (empty($vars['toptype'])) {$vars['toptype'] = 'date';}
    if (empty($vars['showsummary'])) {$vars['showsummary'] = false;}
    if (empty($vars['linkpubtype'])) {$vars['linkpubtype'] = false;}

    if (!isset($vars['showvalue'])) {
        if ($vars['toptype'] == 'rating') {
            $vars['showvalue'] = false;
        } else {
            $vars['showvalue'] = true;
        }
    }

    $vars['fields'] = array('aid', 'title');

    if (!is_array($vars['status'])) {
        $statusarray = array($vars['status']);
    } else {
	    $statusarray = $vars['status'];
    }

    if(!empty($vars['catfilter'])) {
        $cidsarray = array($vars['catfilter']);
    } else {
        $cidsarray = array();
    }

    $vars['filtereditems'] = xarModAPIFunc(
        'articles', 'user', 'getall',
        array(
            'ptid'      => $vars['pubtypeid'],
            'cids'      => $cidsarray,
            'enddate'   => time(),
            'status'    => $statusarray,
            'fields'    => $vars['fields'],
            'sort'      => $vars['toptype'],
            'numitems'  => $vars['itemlimit']
        )
    );

    $vars['pubtypes'] = xarModAPIFunc('articles', 'user', 'getpubtypes');
    $vars['categorylist'] = xarModAPIFunc('categories', 'user', 'getcat');
    $vars['statusoptions'] = array(
        array('id' => '', 'name' => xarML('All Published')),
        array('id' => '3', 'name' => xarML('Frontpage')),
        array('id' => '2', 'name' => xarML('Approved'))
    );

    $vars['sortoptions'] = array(
        array('id' => 'hits', 'name' => xarML('Hit Count')),
        array('id' => 'rating', 'name' => xarML('Rating')),
        array('id' => 'date', 'name' => xarML('Date'))
    );

    //Put together the additional featured articles list
    for($idx=0; $idx < count($vars['filtereditems']); ++$idx) {
        $vars['filtereditems'][$idx]['selected'] = '';
        for($mx=0; $mx < count($vars['moreitems']); ++$mx) {
            if (($vars['moreitems'][$mx]) == ($vars['filtereditems'][$idx]['aid'])) {
                $vars['filtereditems'][$idx]['selected'] = 'selected';
            }
        }
    }
    $vars['morearticles'] = $vars['filtereditems'];
    $vars['blockid'] = $blockinfo['bid'];

    // Return output (template data)
    return $vars;
}

/**
 * update block settings
 */

function articles_featureditemsblock_update($blockinfo)
{
    // Make sure we retrieve the new pubtype from the configuration form.
    // TODO: use xarVarFetch()
    xarVarFetch('pubtypeid', 'id', $vars['pubtypeid'], 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('catfilter', 'id', $vars['catfilter'], 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('status', 'int:0:4', $vars['status'], NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemlimit', 'int:1:50', $vars['itemlimit'], 10, XARVAR_NOT_REQUIRED);
    xarVarFetch('toptype', 'enum:date:rating:hits', $vars['toptype'], 'date', XARVAR_NOT_REQUIRED);
    xarVarFetch('featuredaid', 'id', $vars['featuredaid'], 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('alttitle', 'str', $vars['alttitle'], '', XARVAR_NOT_REQUIRED);
    xarVarFetch('moreitems', 'list:id', $vars['moreitems'], NULL, XARVAR_NOT_REQUIRED);

    xarVarFetch('showfeaturedsum', 'checkbox', $vars['showfeaturedsum'], false, XARVAR_NOT_REQUIRED);
    xarVarFetch('showsummary', 'checkbox', $vars['showsummary'], false, XARVAR_NOT_REQUIRED);
    xarVarFetch('showvalue', 'checkbox', $vars['showvalue'], false, XARVAR_NOT_REQUIRED);
    xarVarFetch('linkpubtype', 'checkbox', $vars['linkpubtype'], false, XARVAR_NOT_REQUIRED);

    $blockinfo['content'] = serialize($vars);
    return $blockinfo;
}

?>