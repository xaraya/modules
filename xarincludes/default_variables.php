<?php
/**
 * AuthLDAP
 * 
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/50.html
 * @author Chris Dudley <miko@xaraya.com>
 * @author Richard Cave <rcave@xaraya.com>
 * @author Sylvain Beucler <beuc@beuc.net>
*/


$default_groups_variables
= Array('defaultgroup4groups_id' => 1,       // Xaraya group ID to attach LDAP groups to (1 = tree root)
      'ldap_groups_to_import' => '',         // Comma-separated list of groups to look for and import
      'group_name_attrname' => 'cn',         // Attribute name of the group's human-readable name
      'group_ref_attrname' => 'uniquegroup', // Attribute name that references children groups
      'group_ref_attrtype' => 'dn',          // Attribute type of children groups references
      'user_ref_attrname' => 'uniquemember', // Attribute name that references group members
      'user_ref_attrtype' => 'dn',           // Attribute type of member references
      'ldap_base4groups' => 'ou=memberlist,ou=groups,o=corp.com' // Base of group search
      );
?>