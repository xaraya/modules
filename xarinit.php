<?php

function vendors_init()
{

sys::import('modules.xen.xarclasses.xenquery');

# --------------------------------------------------------
#
# Set up tables
#
    $q = new xenQuery();
    $prefix = xarDBGetSiteTablePrefix();

$query = "DROP TABLE IF EXISTS " . $prefix . "_vendors_vendors";
if (!$q->run($query)) return;
$query = "CREATE TABLE " . $prefix . "_vendors_vendors (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(80) default 0 NOT NULL,
  address int(10) default NULL,
  contact varchar(35) default NULL,
  phone varchar(20) default NULL,
  fax varchar(20) default NULL,
  email varchar(100) default 0 NOT NULL,
  description text,
  notes text,
  terms int(10) default 0,
  taxincluded varchar(1) default NULL,
  externalcode char(35) default NULL,
  currencycode char(4) default NULL,
  state int(11) default 1,
  PRIMARY KEY  (id)
) TYPE=MyISAM";
if (!$q->run($query)) return;


# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewVendors','All','vendors','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadVendors','All','vendors','All','All','ACCESS_READ');
    xarRegisterMask('CommentVendors','All','vendors','All','All','ACCESS_COMMENT');
    xarRegisterMask('EditVendors','All','vendors','All','All','ACCESS_EDIT');
    xarRegisterMask('AddVendors','All','vendors','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteVendors','All','vendors','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminVendors','All','vendors','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('ViewVendors','All','vendors','All','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadVendors','All','vendors','All','All','ACCESS_READ');
    xarRegisterPrivilege('CommentVendors','All','vendors','All','All','ACCESS_COMMENT');
    xarRegisterPrivilege('EditVendors','All','vendors','All','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddVendors','All','vendors','All','All','ACCESS_ADD');
    xarRegisterPrivilege('DeleteVendors','All','vendors','All','All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminVendors','All','vendors','All','All','ACCESS_ADMIN');
    xarMakePrivilegeRoot('ViewVendors');
    xarMakePrivilegeRoot('ReadVendors');
    xarMakePrivilegeRoot('EditVendors');
    xarMakePrivilegeRoot('CommentVendors');
    xarMakePrivilegeRoot('AddVendors');
    xarMakePrivilegeRoot('DeleteVendors');
    xarMakePrivilegeRoot('AdminVendors');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModVars::set('vendors', 'itemsperpage', 20);

# --------------------------------------------------------
#
# Set up hooks
#
    // This is a GUI hook for the roles module that enhances the roles profile page
    if (!xarModRegisterHook('item', 'usermenu', 'GUI', 'vendors', 'user', 'usermenu')) return false;
    xarModAPIFunc('modules', 'admin', 'enablehooks', array('callerModName' => 'roles', 'hookModName' => 'vendors'));

    xarModRegisterHook('module', 'getconfig', 'API','vendors', 'admin', 'getconfighook');
    xarModAPIFunc('modules','admin','enablehooks',array('callerModName' => 'commerce', 'hookModName' => 'vendors'));

# --------------------------------------------------------
#
# Delete block details for this module (for now)
#
    $blocktypes = xarModAPIfunc(
        'blocks', 'user', 'getallblocktypes',
        array('module' => 'vendors')
    );

    // Delete block types.
    if (is_array($blocktypes) && !empty($blocktypes)) {
        foreach($blocktypes as $blocktype) {
            $result = xarModAPIfunc(
                'blocks', 'admin', 'delete_type', $blocktype
            );
        }
    }

# --------------------------------------------------------
#
# Register block types
#
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'vendors',
                'blockType' => 'manufacturer_info'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'vendors',
                'blockType' => 'manufacturers'))) return;

# --------------------------------------------------------
#
# Register block instances
#
// Put a manufacturers block in the 'right' blockgroup
/*    $type = xarModAPIFunc('blocks', 'user', 'getblocktype', array('module' => 'vendors', 'type'=>'manufacturers'));
    $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
    $bid = xarModAPIFunc('blocks','admin','create_instance',array('type' => $type['tid'],
                                                                  'name' => 'productsmanufacturers',
                                                                  'state' => 0,
                                                                  'groups' => array($rightgroup)));
*/
# --------------------------------------------------------
#
# Create objects
#

    $module = 'vendors';
    $objects = array(
                    'vendors_vendors'
                    );

    if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

# --------------------------------------------------------
#
# Create roles
#
    $role = xarFindRole('VendorGroup');
    if (empty($role)) {
        $parent = xarFindRole('Everybody');
        $new = array('name' => 'VendorGroup',
                     'itemtype' => ROLES_GROUPTYPE,
                     'parentid' => $parent->getID(),
                    );
        $uid = xarModAPIFunc('roles','admin','create',$new);
    }
    xarModVars::set('vendors','defaultgroup',$uid);

# --------------------------------------------------------
#
# Add this module to the list of installed commerce suite modules
#
    $modules = unserialize(xarModGetVar('commerce', 'ice_modules'));
    $info = xarModGetInfo(xarModGetIDFromName('vendors'));
    $modules[$info['name']] = $info['regid'];
    $result = xarModVars::set('commerce', 'ice_modules', serialize($modules));

    return true;
}

function vendors_upgrade()
{
    return true;
}

function vendors_delete()
{
# --------------------------------------------------------
#
# Purge all the roles created by this module
#
    $role = xarRoles::getRole(xarModVars::get('vendors','defaultgroup'));
    $descendants = $role->getDescendants();
    foreach ($descendants as $item)
        if (!$item->purge()) return;
    if (!$role->purge()) return;

# --------------------------------------------------------
#
# Remove this module from the list of commerce modules
#
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'vendors'));
}

?>
