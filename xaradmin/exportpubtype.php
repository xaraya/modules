<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * export the definition of a publication type (+ map it to a pseudo-DD format)
 */
function publications_admin_exportpubtype($args)
{
    extract($args);

    // Get parameters
    if (!xarVarFetch('ptid','isset', $ptid, NULL, XARVAR_DONT_SET)) {return;}

    if (!xarSecurityCheck('AdminPublications')) return;

    $pubtypes = xarModAPIFunc('publications','user','getpubtypes');

    if (empty($ptid) || empty($pubtypes[$ptid])) {
        $msg = xarML('Invalid publication type #(1)',
                     xarVarPrepForDisplay($ptid));
        throw new BadParameterException(null,$msg);
    }
    $pubtype = $pubtypes[$ptid];

    // Initialise the template variables
    $data = array();
    $data['descr'] = $pubtype['description'];

// TODO: migrate pubtype definitions + merge with DD export later on

    // Start the dynamic object definition (cfr. DD export)
    $data['xml'] = '<object name="' . $pubtype['name'] . '">
  <label>' . xarVarPrepForDisplay($pubtype['description']) . '</label>
  <moduleid>' . xarModGetIDFromName('publications') . '</moduleid>
  <itemtype>' . $ptid . '</itemtype>
  <urlparam>id</urlparam>
  <maxid>0</maxid>
  <config>
';

    // Get the article settings for this pubtype
    $settings = xarModVars::get('publications','settings.'.$ptid);
    $unsettings = unserialize($settings);
    unset($unsettings['cids']);
    unset($unsettings['number_of_categories']);

    foreach ($unsettings as $key => $val) {
        if (!isset($val)) continue;
        $data['xml'] .= "    <$key>$val</$key>\n";
    }

    // Check if we're using this as an alias for short URLs
    if (xarModGetAlias($pubtype['name']) == 'publications') {
        $isalias = 1;
    } else {
        $isalias = 0;
    }

    $data['xml'] .= '  </config>
  <isalias>' . $isalias . '</isalias>
  <properties>
    <property name="id">
      <id>1</id>
      <label>Publication ID</label>
      <type>itemid</type>
      <default></default>
      <source>xar_publications.id</source>
      <status>1</status>
    </property>
    <property name="pubtype_id">
      <id>2</id>
      <label>Publication Type</label>
      <type>itemtype</type>
      <default>1</default>
      <source>xar_publications.pubtype_id</source>
      <status>1</status>
    </property>
';

    // Configurable fields for publications
    $fields = array('title','summary','body','notes','owner','pubdate','status');
    $id = 3;
    foreach ($fields as $field) {
        $specs = $pubtype['config'][$field];
        if (empty($specs['label'])) {
            $specs['label'] = ucwords($field);
            $status = 0;
        } elseif ($field == 'body') {
            $status = 2;
        } else {
            $status = 1;
        }
        if (empty($specs['input'])) {
            $specs['input'] = 0;
        } else {
            $specs['input'] = 1;
        }
        if (!isset($specs['validation'])) {
            $specs['validation'] = '';
        }
        $data['xml'] .= '    <property name="' . $field . '">
      <id>' . $id . '</id>
      <label>' . $specs['label'] . '</label>
      <type>' . $specs['format'] . '</type>
      <default></default>
      <source>xar_publications.' . $field . '</source>
      <input>' . $specs['input'] . '</input>
      <status>' . $status . '</status>
      <validation>' . xarVarPrepForDisplay($specs['validation']) . '</validation>
    </property>
';
        // $specs['type'] = fixed for publications fields + unused in DD
        $id++;
    }

    // Retrieve any dynamic object for this pubtype, or create a dummy one
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('name'     => $pubtype['name'],
                                   'label'    => $pubtype['description'],
                                   'moduleid' => xarModGetIDFromName('publications'),
                                   'itemtype' => $ptid,
                                   'urlparam' => 'id',
                                   'isalias'  => $isalias,
                                   //'config'   => $settings));
                                   'config'   => $unsettings));

    if (isset($object) && count($object->properties) > 0) {
        $proptypes = DataPropertyMaster::getPropertyTypes();
        $prefix = xarDB::getPrefix();
        $prefix .= '_';
        $keys = array('id','label','type','default','source','status','order','validation');

        foreach (array_keys($object->properties) as $name) {
            $info = array();
            foreach ($keys as $key) {
                if (isset($object->properties[$name]->$key)) {
                    $info[$key] = $object->properties[$name]->$key;
                } else {
                    $info[$key] = '';
                }
            }
            // replace numeric property type with text version
            if (isset($proptypes[$info['type']])) {
                $info['type'] = $proptypes[$info['type']]['name'];
            }
            // replace local table prefix with default xar_* one
            $info['source'] = preg_replace("/^$prefix/",'xar_',$info['source']);

            $data['xml'] .= '    <property name="' . $name . '">
      <id>' . $info['id'] . '</id>
      <label>' . $info['label'] . '</label>
      <type>' . $info['type'] . '</type>
      <default>' . $info['default'] . '</default>
      <source>' . $info['source'] . '</source>
      <status>' . $info['status'] . '</status>
      <order>' . $info['order'] . '</order>
      <validation>' . xarVarPrepForDisplay($info['validation']) . '</validation>
    </property>
';
        }
    }

    $data['xml'] .= "  </properties>
</object>\n";

/* // for migration to dynamic objects later

    // Reverse the properties list for nicer export
    $fields = array_reverse($fields);
    if (count($object->properties) > 0) {
        $object->properties = array_reverse($object->properties);
    }
    foreach ($fields as $field) {
        $specs = $pubtype['config'][$field];
        if (empty($specs['label'])) {
            $specs['label'] = ucwords($field);
            $status = 0;
        } elseif ($field == 'body') {
            $status = 2;
        } else {
            $status = 1;
        }
        $object->addProperty(array('name'   => $field,
                                   'label'  => $specs['label'],
                                   'status' => $status,
                                   'type'   => $specs['format'],
                                   'source' => 'xar_publications.'.$field));
    // these field specs have no equivalent in DD :
        // $specs['input'], // we'll guess this on import
        // $specs['type'], // unused/fixed for publications
    }

    // Predefined fields for publications
    $object->addProperty(array('name'   => 'pubtype_id',
                               'label'  => 'Publication Type',
                               'status' => 1,
                               'type'   => 'itemtype',
                               'default' => $ptid,
                               'source' => 'xar_publications.pubtype_id'));

    $object->addProperty(array('name'   => 'id',
                               'label'  => 'Publication ID',
                               'status' => 1,
                               'type'   => 'itemid',
                               'source' => 'xar_publications.id'));

    // Reverse the properties list for nicer export
    $object->properties = array_reverse($object->properties);

    // Export the (real or dummy) dynamic object
    $data['xml'] = xarModAPIFunc('dynamicdata','util','export',
                                 array('objectref' => & $object));
*/

    // Prepare the XML stuff for output in a textarea (for copy & paste)
    $data['xml'] = xarVarPrepForDisplay($data['xml']);

    // Return the template variables defined in this function
    return $data;
}

?>
