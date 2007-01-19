<?php
/**
 * Modify module's configuration
 *
 * @package modules
 * @copyright (C) 2006-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */
/**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author JpGraph module development team
 * @return array
 */
function jpgraph_admin_modifyconfig()
{
    /* Get parameters from whatever input we need.
     */
    if (!xarVarFetch('cachedirectory',  'str::', $cachedirectory, xarModGetVar('jpgraph', 'cachedirectory'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ttfdirectory',  'str::', $ttfdirectory, xarModGetVar('jpgraph', 'ttfdirectory'), XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('csimcachedirectory',  'str::', $csimcachedirectory, xarModGetVar('jpgraph', 'csimcachedirectory'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('csimcachehttpdirectory',  'str::', $csimcachehttpdirectory, xarModGetVar('jpgraph', 'csimcachehttpdirectory'), XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('itemsperpage', 'int',      $itemsperpage, xarModGetVar('jpgraph', 'itemsperpage'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls',    'checkbox', $shorturls,    false, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('usecache',     'checkbox', $usecache,     true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('readcache',    'checkbox', $readcache,    true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname',    'str:1:',   $aliasname,    '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('action',       'str:4:',   $action,       false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',  'checkbox', $modulealias,  false,XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('graphic_error',     'checkbox', $graphic_error,     true, XARVAR_NOT_REQUIRED)) return;

    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = array();

    /* common menu configuration */
    $data = xarModAPIFunc('jpgraph', 'admin', 'menu');

    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AdminJpGraph')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();

    /* Specify some values for display */

    $data['itemsvalue'] = xarModGetVar('jpgraph', 'itemsperpage');

    /* Note : if you don't plan on providing encode/decode functions for
     * short URLs (see xaruserapi.php), you should remove this from your
     * admin-modifyconfig.xd template.
     */
    $data['shorturlschecked'] = xarModGetVar('jpgraph', 'SupportShortURLs') ? true : false;

    /* If you plan to use alias names for you module then you can use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('jpgraph', 'useModuleAlias');
    $data['aliasname '] = xarModGetVar('jpgraph','aliasname');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'jpgraph',
                       array('module' => 'jpgraph'));
    if (empty($hooks)) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;

         /* You can use the output from individual hooks in your template too, e.g. with
         * $hooks['categories'], $hooks['dynamicdata'], $hooks['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }

    if($action == 'update') {


        if (!xarVarFetch('window_height', 'str:1:',   $window_height,    xarModGetVar('jpgraph', 'window_height'), XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('window_width', 'str:1:',   $window_width,    xarModGetVar('jpgraph', 'window_width'), XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('cachetimeout', 'int::',   $cachetimeout,    xarModGetVar('jpgraph', 'cachetimeout'), XARVAR_NOT_REQUIRED)) return;

        /* Confirm authorisation code. This checks that the form had a valid
         * authorisation code attached to it. If it did not then the function will
         * proceed no further as it is possible that this is an attempt at sending
         * in false data to the system
         */

        if (!xarSecConfirmAuthKey()) return;
        /* Update module variables. Note that the default values are set in
         * xarVarFetch when recieving the incoming values, so no extra processing
         * is needed when setting the variables here.
         */
        xarModSetVar('jpgraph', 'cachedirectory', $cachedirectory);
        xarModSetVar('jpgraph', 'csimcachedirectory', $csimcachedirectory);
        xarModSetVar('jpgraph', 'csimcachehttpdirectory', $csimcachehttpdirectory);

        xarModSetVar('jpgraph', 'ttfdirectory', $ttfdirectory);
        xarModSetVar('jpgraph', 'cachetimeout', $cachetimeout);

        xarModSetVar('jpgraph', 'usecache', $usecache);
        xarModSetVar('jpgraph', 'readcache', $readcache);
        xarModSetVar('jpgraph', 'window_width', $window_width);
        xarModSetVar('jpgraph', 'window_height', $window_height);

        xarModSetVar('jpgraph', 'graphic_error', $graphic_error);
        xarModSetVar('jpgraph', 'itemsperpage', $itemsperpage);
        xarModSetVar('jpgraph', 'SupportShortURLs', $shorturls);
        if (isset($aliasname) && trim($aliasname)<>'') {
            xarModSetVar('jpgraph', 'useModuleAlias', $modulealias);
        } else{
             xarModSetVar('jpgraph', 'useModuleAlias', 0);
        }
        $currentalias = xarModGetVar('jpgraph','aliasname');
        $newalias = trim($aliasname);
        /* Get rid of the spaces if any, it's easier here and use that as the alias*/
        if ( strpos($newalias,'_') === FALSE )
        {
            $newalias = str_replace(' ','_',$newalias);
        }
        $hasalias= xarModGetAlias($currentalias);
        $useAliasName= xarModGetVar('jpgraph','useModuleAlias');

        if (($useAliasName==1) && !empty($newalias)){
            /* we want to use an aliasname */
            /* First check for old alias and delete it */
            if (isset($hasalias) && ($hasalias =='jpgraph')){
                xarModDelAlias($currentalias,'jpgraph');
            }
            /* now set the new alias if it's a new one */
              xarModSetAlias($newalias,'jpgraph');
        }
        /* now set the alias modvar */
        xarModSetVar('jpgraph', 'aliasname', $newalias);

        xarModCallHooks('module','updateconfig','jpgraph',
                       array('module' => 'jpgraph'));

    }


    /* Return the template variables defined in this function */
    return $data;
}
?>
