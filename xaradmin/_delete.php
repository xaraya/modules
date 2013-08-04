<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */

/**
 * Show comments deletion form
 *
 * This form allows one to delete comments for all hooked modules
 */
function comments_admin_delete( )
{
    if (!xarSecurityCheck('AdminComments')) return;
    if (!xarVarFetch('dtype', 'str:1:', $dtype)) return;
    $delete_args = array();

    if (!isset($dtype) || !eregi('^(all|module|object)$',$dtype)) {
        $msg = xarML('Invalid or Missing Parameter \'dtype\'');
        throw new BadParameterException($msg);
    } else {

        $delete_args['dtype'] = $dtype;
        $output['dtype'] = $dtype;

        switch (strtolower($dtype)) {
            case 'object':
                if (!xarVarFetch('objectid','int:1',$objectid)) return;

                if (!isset($objectid) || empty($objectid)) {
                    $msg = xarML('Invalid or Missing Parameter \'objectid\'');
                    throw new BadParameterException($msg);
                }
                $output['objectid'] = $objectid;
                $delete_args['objectid'] = $objectid;

            // if dtype == object, then fall through to
            // the module section below cuz we need both
            // the module id and the object id
            case 'module':
                if (!xarVarFetch('modid','int:1',$modid)) return;

                if (!isset($modid) || empty($modid)) {
                    $msg = xarML('Invalid or Missing Parameter \'modid\'');
                    throw new BadParameterException($msg);
                }
                if (!xarVarFetch('itemtype','int:1',$itemtype)) return;
                if (empty($itemtype)) {
                    $itemtype = 0;
                }
                $modinfo = xarModGetInfo($modid);
                $output['modname']    = $modinfo['name'];
                $delete_args['modid'] = $modid;
                $delete_args['itemtype'] = $itemtype;
                break;
            case 'all':
                $output['modname']    = '\'ALL MODULES\'';
                break;
            default:
                $msg = xarML('Invalid or Missing Parameter \'dtype\'');
                throw new Exception($msg);
        }
    }

    if (!xarVarFetch('submitted', 'str:1:', $submitted,'',XARVAR_NOT_REQUIRED)) return;
    // if we're gathering submitted info form the delete
    // confirmation then we are ok to check delete choice,
    // then delete in the manner specified (or not) and
    // then redirect to the Comment's Statistics page
    if (isset($submitted) && !empty($submitted)) {

        // Confirm authorisation code
        if (!xarSecConfirmAuthKey())
            return;

        if (!xarVarFetch('choice', 'str:1:', $choice)) return;

        // if choice isn't set or it has an incorrect value,
        // redirect back to the choice page
        if (!isset($choice) || !eregi('^(yes|no|true|false)$',$choice)) {
            xarController::redirect(xarModURL('comments','admin','delete',$delete_args));
        }

        if($choice == 'yes' || $choice == 'true') {

            if (!xarModAPILoad('comments','user')) {
                die ("COULDN'T LOAD API!!!");
            }
            $retval = TRUE;

            switch (strtolower($dtype)) {
                case 'module':
                    xarMod::apiFunc('comments','admin','delete_module_nodes',
                                   array('modid'=>$modid,
                                         'itemtype' => $itemtype));
                    break;
                case 'object':
                    xarMod::apiFunc('comments','admin','delete_object_nodes',
                                   array('modid'    => $modid,
                                         'itemtype' => $itemtype,
                                         'objectid' => $objectid));
                    break;
                case 'all':
                    $dbconn = xarDB::getConn();
                    $xartable = xarDB::getTables();

                    $sql = "DELETE
                              FROM  $xartable[comments]";

                    $result =& $dbconn->Execute($sql);

                    break;
                default:
                    $retval = FALSE;
            }

            if (!$retval) {
                $msg = xarML('Unable to delete comments!');
                throw new BadParameterException($msg);
            }
        } else {
            if ( isset($modid) )  {
                xarController::redirect(xarModURL('comments','admin','module_stats',
                                              array('modid' => $modid,
                                                    'itemtype' => empty($itemtype) ? null : $itemtype)));
            } else {
                xarController::redirect(xarModURL('comments','admin','stats'));
            }
        }

        if (isset($modid) && strtolower($dtype) == 'object') {
            xarController::redirect(xarModURL('comments','admin','module_stats',
                                          array('modid' => $modid,
                                                'itemtype' => empty($itemtype) ? null : $itemtype)));
        } else {
            xarController::redirect(xarModURL('comments','admin','stats'));
        }
    }
    // If we're here, then we haven't received authorization
    // to delete any comments yet - so here we ask for confirmation.
    $output['authid'] = xarSecGenAuthKey();
    $output['delete_url'] = xarModURL('comments','admin','delete',$delete_args);

    return $output;
}
?>
