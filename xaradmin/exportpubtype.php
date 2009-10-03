<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * export the definition of a publication type (+ map it to a pseudo-DD format)
 */
function articles_admin_exportpubtype($args)
{
    extract($args);

    // Get parameters
    if (!xarVarFetch('ptid','isset', $ptid, NULL, XARVAR_DONT_SET)) {return;}

    if (!xarSecurityCheck('AdminArticles')) return;

    $pubtypes = xarMod::apiFunc('articles','user','getpubtypes');

    if (empty($ptid) || empty($pubtypes[$ptid])) {
        $msg = xarML('Invalid publication type #(1)',
                     xarVarPrepForDisplay($ptid));
        throw new BadParameterException(null,$msg);
    }
    $pubtype = $pubtypes[$ptid];

    // Initialise the template variables
    $data = array();
    $data['descr'] = $pubtype['descr'];

// TODO: migrate pubtype definitions + merge with DD export later on

    // Start the dynamic object definition (cfr. DD export)
    $data['xml'] = '<object name="' . $pubtype['name'] . '">
  <label>' . xarVarPrepForDisplay($pubtype['descr']) . '</label>
  <module_id>' . xarMod::getRegId('articles') . '</module_id>
  <itemtype>' . $ptid . '</itemtype>
  <class>ArticleObject</class>
  <filepath>modules/articles/class/article.php</filepath>
  <urlparam>aid</urlparam>
  <maxid>0</maxid>
  <config>
';

    // Get the article settings for this pubtype
    $settings = xarModVars::get('articles','settings.'.$ptid);
    $unsettings = unserialize($settings);

    foreach ($unsettings as $key => $val) {
        if (!isset($val)) continue;
        $data['xml'] .= "    <$key>$val</$key>\n";
    }

    // Check if we're using this as an alias for short URLs
    if (xarModGetAlias($pubtype['name']) == 'articles') {
        $isalias = 1;
    } else {
        $isalias = 0;
    }

    sys::import('modules.dynamicdata.class.properties.master');

    $data['xml'] .= '  </config>
  <isalias>' . $isalias . '</isalias>
  <properties>
    <property name="aid">
      <id>1</id>
      <label>Article ID</label>
      <type>itemid</type>
      <defaultvalue></defaultvalue>
      <source>xar_articles.xar_aid</source>
      <status>' . (DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE + DataPropertyMaster::DD_INPUTSTATE_NOINPUT) . '</status>
      <seq>1</seq>
    </property>
    <property name="pubtypeid">
      <id>2</id>
      <label>Publication Type</label>
      <type>itemtype</type>
      <defaultvalue>'.$ptid.'</defaultvalue>
      <source>xar_articles.xar_pubtypeid</source>
      <status>' . (DataPropertyMaster::DD_DISPLAYSTATE_HIDDEN + DataPropertyMaster::DD_INPUTSTATE_NOINPUT) . '</status>
      <seq>2</seq>
    </property>
';

    // Configurable fields for articles
    $fields = array('title','summary','body','notes','authorid','pubdate','status');
    $id = 3;
    // FIXME: map status & input fields to DD state someday
    foreach ($fields as $field) {
        $specs = $pubtype['config'][$field];
        if (empty($specs['label'])) {
            $specs['label'] = ucwords($field);
            $status = DataPropertyMaster::DD_DISPLAYSTATE_DISABLED;
        } elseif ($field == 'body') {
            $status = DataPropertyMaster::DD_DISPLAYSTATE_DISPLAYONLY;
        } else {
            $status = DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE;
        }
        if (empty($specs['input'])) {
            $specs['input'] = 0;
            $status += DataPropertyMaster::DD_INPUTSTATE_NOINPUT;
        } else {
            $specs['input'] = 1;
            $status += DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY;
        }
        if (!isset($specs['validation'])) {
            $specs['validation'] = '';
        }
        $data['xml'] .= '    <property name="' . $field . '">
      <id>' . $id . '</id>
      <label>' . $specs['label'] . '</label>
      <type>' . $specs['format'] . '</type>
      <defaultvalue></defaultvalue>
      <source>xar_articles.xar_' . $field . '</source>
      <input>' . $specs['input'] . '</input>
      <status>' . $status . '</status>
      <seq>' . $id . '</seq>
      <configuration>' . $specs['validation'] . '</configuration>
    </property>
';
        // $specs['type'] = fixed for articles fields + unused in DD
        $id++;
    }

    // Retrieve any dynamic object for this pubtype, or create a dummy one
    $object = xarMod::apiFunc('dynamicdata','user','getobject',
                             array('name'     => $pubtype['name'],
                                   'label'    => $pubtype['descr'],
                                   'moduleid' => xarMod::getRegId('articles'),
                                   'itemtype' => $ptid,
                                   'urlparam' => 'aid',
                                   'isalias'  => $isalias,
                                   //'config'   => $settings));
                                   'config'   => $unsettings));

    if (isset($object) && count($object->properties) > 0) {
        $proptypes = DataPropertyMaster::getPropertyTypes();
        $prefix = xarDB::getPrefix();
        $prefix .= '_';
        $keys = array('id','label','type','defaultvalue','source','status','seq','configuration');

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

            // we've already got those properties :-)
            if (preg_match('/^xar_articles\./',$info['source'])) continue;

            $info['seq'] += $id;

            $data['xml'] .= '    <property name="' . $name . '">
      <id>' . $info['id'] . '</id>
      <label>' . $info['label'] . '</label>
      <type>' . $info['type'] . '</type>
      <defaultvalue>' . $info['defaultvalue'] . '</defaultvalue>
      <source>' . $info['source'] . '</source>
      <status>' . $info['status'] . '</status>
      <seq>' . $info['seq'] . '</seq>
      <configuration>' . $info['configuration'] . '</configuration>
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
    // FIXME: map status & input to DD state someday
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
                                   'source' => 'xar_articles.xar_'.$field));
    // these field specs have no equivalent in DD :
        // $specs['input'], // we'll guess this on import
        // $specs['type'], // unused/fixed for articles
    }

    // Predefined fields for articles
    $object->addProperty(array('name'   => 'pubtypeid',
                               'label'  => 'Publication Type',
                               'status' => 1,
                               'type'   => 'itemtype',
                               'default' => $ptid,
                               'source' => 'xar_articles.xar_pubtypeid'));

    $object->addProperty(array('name'   => 'aid',
                               'label'  => 'Article ID',
                               'status' => 1,
                               'type'   => 'itemid',
                               'source' => 'xar_articles.xar_aid'));

    // Reverse the properties list for nicer export
    $object->properties = array_reverse($object->properties);

    // Export the (real or dummy) dynamic object
    $data['xml'] = xarMod::apiFunc('dynamicdata','util','export',
                                 array('objectref' => & $object));
*/

    // Prepare the XML stuff for output in a textarea (for copy & paste)
    $data['xml'] = xarVarPrepForDisplay($data['xml']);

    // Return the template variables defined in this function
    return $data;
}

?>
