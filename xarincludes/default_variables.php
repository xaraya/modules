<?php
$default_groups_variables
= Array('defaultgroup4groups_id' => 1,         // Xaraya group ID to attach LDAP groups to (1 = tree root)
      'ldap_groups_to_import' => '',         // Comma-separated list of groups to look for and import
      'group_name_attrname' => 'cn',         // Attribute name of the group's human-readable name
      'group_ref_attrname' => 'uniquegroup', // Attribute name that references children groups
      'group_ref_attrtype' => 'dn',          // Attribute type of children groups references
      'user_ref_attrname' => 'uniquemember', // Attribute name that references group members
      'user_ref_attrtype' => 'dn',           // Attribute type of member references
      'ldap_base4groups' => 'ou=memberlist,ou=groups,o=corp.com' // Base of group search
      );
?>
