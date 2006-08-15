<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Original Author of file: Andrea Moro
 * Purpose of file: Show latest posted comments
 * initialise block
 */
function comments_latestcommentsblock_init()
{
    // Initial values when the block is created.
    return array(
                 'howmany' => 5,
                 'modid' => array('all'),
                 'addauthor' => 1,
                 'addmodule' => 0,
                 'addcomment' => 20,
                 'addobject' => 1,
                 'adddate' => 'on',
                 'adddaysep' => 'on',
                 'truncate' => 18,
                 'addprevious' => 'on'
                );
}

/**
 * get information on block
 */
function comments_latestcommentsblock_info()
{
    // Values
    return array('text_type' => 'latestcomments',
                 'module' => 'comments',
                 'text_type_long' => 'Show Latest Comments',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}

/**
 * display block
 */
function comments_latestcommentsblock_display($blockinfo)
{
    // Security check
// TODO: use some blocks mask & instance for security check
    if (!xarSecurityCheck('Comments-Read')) {
        return;
    }

    if (empty($blockinfo['content'])) {
        return '';
    }

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    $vars['block_is_calling']=1;
    $vars['first']=1;
    $vars['order']='DESC';


    $blockinfo['content']=xarModFunc('comments', 'user', 'displayall', $vars) ;

    return $blockinfo;
}


/**
 * modify block settings
 */
function comments_latestcommentsblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // get the list of modules+itemtypes that comments is hooked to
    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'comments'));

    $modlist = array();
    $modlist['all'] = xarML('All');
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc($modname,'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                foreach ($value as $itemtype => $val) {
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                    }
                    $modlist["$modname.$itemtype"] = ucwords($modname) . ' - ' . $type;
                }
            } else {
                $modlist[$modname] = ucwords($modname);
                // allow selecting individual item types here too (if available)
                if (!empty($mytypes) && count($mytypes) > 0) {
                    foreach ($mytypes as $itemtype => $mytype) {
                        if (!isset($mytype['label'])) continue;
                        $modlist["$modname.$itemtype"] = ucwords($modname) . ' - ' . $mytype['label'];
                    }
                }
            }
        }
    }

    // Send content to template
    $output = xarTplBlock('comments','latestcommentsblockadmin',
                          array(
                                'howmany' => $vars['howmany'],
                                'modid' => $vars['modid'],
                                'modlist' => $modlist,
                                'addauthor' => $vars['addauthor'],
                                'addmodule' => $vars['addmodule'],
                                'addcomment' => $vars['addcomment'],
                                'addobject' => $vars['addobject'],
                                'adddate' => $vars['adddate'],
                                'adddaysep' => $vars['adddaysep'],
                                'truncate' => $vars['truncate'],
                                'addprevious' => $vars['addprevious']
                                ));

    // Return output
    return $output;
}

/**
 * update block settings
 */
function comments_latestcommentsblock_update($blockinfo)
{
    $vars = array();
    if (!xarVarFetch('howmany', 'int:1:', $vars['howmany'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modid', 'isset', $vars['modid'],  array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pubtypeid', 'isset', $vars['pubtypeid'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('addauthor', 'isset', $vars['addauthor'], '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('addmodule', 'isset', $vars['addmodule'], '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('addcomment', 'isset', $vars['addcomment'], '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('addobject', 'isset', $vars['addobject'], '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('adddate', 'checkbox', $vars['adddate'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('adddaysep', 'checkbox', $vars['adddaysep'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('truncate', 'int:1:', $vars['truncate'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('addprevious', 'checkbox', $vars['addprevious'], 0, XARVAR_NOT_REQUIRED)) return;

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}
?>
