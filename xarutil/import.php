<?php
/**
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Import a content type
 */
function content_util_import($args) {

    if(!xarSecurityCheck('AdminContentTypes')) return;

    xarTplSetPageTemplateName('admin');

    if(!xarVarFetch('xml',        'isset', $xml,         NULL,  XARVAR_DONT_SET)) {return;} 

    extract($args);

    $data['authid'] = xarSecGenAuthKey();

    if (!empty($xml)) {
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        
      
        $import = xarMod::apiFunc('content','util','import', array('xml' => $xml));
        
        if ($import) {
            xarResponse::redirect(xarModURL('content', 'admin', 'modifycontenttype', array('itemid' => $import['objectid'], 'ctype' => $import['name'])));
        }
    }

    return $data;

}

?>