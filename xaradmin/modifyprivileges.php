<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * Modify privileges
 *
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_admin_modifyprivileges()
{
    if (!xarVarFetch('func', 'str', $data['page'],  'main', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get the admin edit menu
    $data['menu'] = xarModApiFunc('newsletter', 'admin', 'configmenu');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // See if the newsletter groups have already been created
    $data['creategroups'] = xarModGetVar('newsletter', 'creategroups');

    $publisherGroup = xarModGetVar('newsletter', 'publisher');
    $editorGroup = xarModGetVar('newsletter', 'editor');
    $writerGroup = xarModGetVar('newsletter', 'writer');

    if (!$data['creategroups']) {
        // Labels for creating groups
        $data['creategroupbutton'] =  xarVarPrepForDisplay(xarML('Create Groups'));

        $data['publishergrouplabel'] = xarVarPrepForDisplay(xarML($publisherGroup));
        $data['editorgrouplabel'] = xarVarPrepForDisplay(xarML($editorGroup));
        $data['writergrouplabel'] = xarVarPrepForDisplay(xarML($writerGroup));

        // See Everyone group exists to set defaultgroup
        if( xarFindRole("Everybody"))
            $data['defaultgroup'] = 'Everybody';
        else
            $data['defaultgroup'] = '';

        // Get the list of groups
        if (!$groupRoles = xarGetGroups()) return; // throw back

        $i=0;
        while (list($key,$group) = each($groupRoles)) {
            // Check to see if this is an newsletter group
            if ($group['name'] != $publisherGroup &&
                $group['name'] != $editorGroup &&
                $group['name'] != $writerGroup) {

                $groups[$i]['name'] = xarVarPrepForDisplay($group['name']);
                $i++;
            }
        }
        sort($groups);

        // Put in a "don't create" group
        $groups[++$i]['name'] = "**Don't Create**";

        $data['groups'] = $groups;
    }

    // Specify privileges
    $data['publisherlabel'] = $publisherGroup;
    $data['editorlabel'] = $editorGroup;
    $data['writerlabel'] = $writerGroup;

    // Specify buttons
    $data['updateprivbutton'] = xarVarPrepForDisplay(xarML('Update Privileges'));

    // Get masks
    $masks = new xarMasks();
    $nwsltrMasks = $masks->getmasks('newsletter');

    // reverse sort masks so highest level on top
    rsort($nwsltrMasks);

    $data['masks'] = array();
    for($idx = 0; $idx < count($nwsltrMasks); $idx++) {
        $data['masks'][$idx]['name'] = $nwsltrMasks[$idx]->name;
        $data['masks'][$idx]['level'] = $nwsltrMasks[$idx]->level;
    }

    $data['publisherMask'] = xarModGetVar('newsletter', 'publishermask');
    $data['editorMask'] = xarModGetVar('newsletter', 'editormask');
    $data['writerMask'] = xarModGetVar('newsletter', 'writermask');
    // Would you ever need hooks here??
    // Set hooks
    $hooks = xarModCallHooks('module',
                             'modifyprivileges',
                             'newsletter',
                             array('module' => 'newsletter'));

    if (empty($hooks) || !is_string($hooks)) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }

    // Return the template variables defined in this function
    return $data;
}

?>
