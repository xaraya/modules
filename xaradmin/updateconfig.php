<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * Update configuration
 */
function ratings_admin_updateconfig()
{
    // Get parameters
    if(!xarVarFetch('style',    'array', $style,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('seclevel', 'array', $seclevel, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('shownum', 'array', $shownum, NULL, XARVAR_NOT_REQUIRED)) {return;}

    // Confirm authorisation code
//    if (!xarSecConfirmAuthKey()) return;
    // Security Check
//    if (!xarSecurityCheck('AdminRatings')) return;

    $settings = array('default');
    
    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'ratings'));

    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarModAPIFunc($modname,'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                foreach ($value as $itemtype => $val) {
                    $settings[] = "$modname.$itemtype";
                }
            } else {
                $settings[] = $modname;
            }
        }
    }

	foreach($settings as $modname) {
		if($modname == 'default') {
			if(isset($style['default'])) {
				xarModSetVar('ratings','defaultstyle', $style['default']);
			}
			if(isset($seclevel['default'])) {
				xarModSetVar('ratings','seclevel', $seclevel['default']);
			}
			if(!isset($shownum['default']) || $shownum['default'] != 1) {
				xarModSetVar('ratings','shownum', 0);
			} else {
				xarModSetVar('ratings','shownum', 1);
			}
		} else {
			if(isset($style[$modname])) {
				xarModSetVar('ratings',"style.$modname", $style[$modname]);
			}
			if(isset($seclevel[$modname])) {
				xarModSetVar('ratings',"seclevel.$modname", $seclevel[$modname]);
			}
			if(!isset($shownum[$modname]) || $shownum[$modname] != 1) {
				xarModSetVar('ratings',"shownum.$modname", 0);
			} else {
				xarModSetVar('ratings',"shownum.$modname", 1);
			}
		}
	}

    xarResponseRedirect(xarModURL('ratings', 'admin', 'modifyconfig'));

    return true;
}

?>
