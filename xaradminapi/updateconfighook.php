<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Categories module development team
 */
/**
 * update configuration for a module - hook for ('module','updateconfig','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return bool
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function smilies_adminapi_updateconfighook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'admin', 'updateconfighook', 'smilies');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // @todo check in $extrainfo (is it worth it?)
    if (!xarVarFetch('skiptags',        'str:1',    $skiptags,        '',         XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('image_folder',    'str',      $image_folder,    '',  XARVAR_NOT_REQUIRED)) return; 

    $seentags = array();
    if (!empty($skiptags)) {
      // TODO: make this list complete
      $alltags = array('div','p','b','a','blockquote','code','table','tr','td','thead','th','tfoot','span','textarea','input','label','fieldset','form','legend');
      // strip any spaces from input
      $skiptags = str_replace(' ', '', $skiptags);
      $tagstoskip = explode(',', $skiptags);
      foreach ($tagstoskip as $htmltag) {
        // skip invalid tags
        if (!in_array($htmltag, $alltags)) continue;
        $seentags[$htmltag] = 1;
      }
    }
    if (!empty($seentags)) {
      $seentags = array_keys($seentags);
    }

    $itemtype = 0;
    if (isset($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    }

    xarModSetVar($modname, 'skiptags.' . $itemtype, serialize($seentags));
    xarModSetVar($modname, 'image_folder.' . $itemtype, $image_folder);

    return $extrainfo;
}
?>
