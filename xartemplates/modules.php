<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003-4 Xaraya
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function commerce_admin_modules($args)
{
    sys::import('modules.xen.xarclasses.xenquery');
    $xartables = xarDBGetTables();

    if(!xarVarFetch('set',   'str',  $set, "", XARVAR_NOT_REQUIRED)) {return;}
    switch ($set) {
        case 'shipping':
            $module_type = 'shipping';
            $module_directory = 'modules/commerce/xarincludes/modules/shipping/';
            $module_key = 'MODULE_SHIPPING_INSTALLED';
            $data['heading_title'] = xarML('Shipping Modules');
            break;
        case 'ordertotal':
            $module_type = 'order_total';
            $module_directory = 'modules/commerce/xarincludes/modules/order_total/';
            $module_key = 'MODULE_ORDER_TOTAL_INSTALLED';
            $data['heading_title'] = xarML('Order Total Modules');
            break;
        case 'payment':
        default:
            $module_type = 'payment';
            $module_directory = 'modules/commerce/xarincludes/modules/payment/';
            $module_key = 'MODULE_PAYMENT_INSTALLED';
            $data['heading_title'] = xarML('Payment Modules');
            break;
    }

    if(!xarVarFetch('action',   'str',  $action, "", XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('module',   'str',  $module, "", XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('configuration',   'array',  $configuration, array(), XARVAR_NOT_REQUIRED)) {return;}
    switch ($action) {
        case 'save':
            while (list($key, $value) = each($configuration)) {
                $q = new xenQuery('UPDATE',$xartable['commerce_configuration']);
                $q->addfield('configuration_value',$value);
                $q->eq('configuration_key',$key);
            }
            xarRedirectResponse(xarModURL('commerce','admin','modules',array('set=' => $set,'module' => $module)));
            break;
        case 'install':
        case 'remove':
//TODO: check if this needs to be anything but .php
//$file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
            $class = basename($module);
            if (file_exists($module_directory . $class . '.php')) {
                include($module_directory . $class . $file_extension);
                $module = new $class;
                if ($action == 'install') {
                    $module->install();
                } elseif ($action == 'remove') {
                    $module->remove();
                }
            }
            xarRedirectResponse(xarModURL('commerce','admin','modules',array('set=' => $set,'module' => $class)));
            break;
    }
  $directory_array = array();
  if ($dir = @dir($module_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir($module_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == '.php') {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  $installed_modules = array();
    for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) {

        $class = substr($file, 0, strrpos($file, '.'));
        if (class_exists($class)) {
            $module = new $class;
            if ($module->check() > 0) {
                if ($module->sort_order > 0) {
                    $installed_modules[$module->sort_order] = $file;
                } else {
                    $installed_modules[] = $file;
                }
            }

        if(!xarVarFetch('module',   'str',  $module, '', XARVAR_NOT_REQUIRED)) {return;}
    echo "ss".var_dump($module);exit;
        if (((!$module) || ($module == $class)) && (!$mInfo)) {
            $module_info = array('code' => $module->code,
            'title' => $module->title,
            'description' => $module->description,
            'status' => $module->check());
            $module_keys = $module->keys();

            $keys_extra = array();
            for ($j = 0, $k = sizeof($module_keys); $j < $k; $j++) {
                $q = new xenQuery('SELECT',$xartable['commerce_configuration']);
                $q->addfields('configuration_key','configuration_value', 'use_function', 'set_function');
                $q->eq('configuration_key',$module_keys[$j]);
                if(!$q->run()) return;
                $key_value = $q->row();
                if ($key_value['configuration_key'] !='')
                    $keys_extra[$module_keys[$j]]['title'] = constant(strtoupper($key_value['configuration_key'] .'_TITLE'));
                $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
                if ($key_value['configuration_key'] !='')
                    $keys_extra[$module_keys[$j]]['description'] = constant(strtoupper($key_value['configuration_key'] .'_DESC'));
                $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
                $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
            }
            $module_info['keys'] = $keys_extra;
            $mInfo = new objectInfo($module_info);
        }

        if ( (is_object($mInfo)) && ($class == $mInfo->code) ) {

            if ($module->check() > 0) {
                $data['urlrow'] = '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin','modules',array('set=' => $set,'module' => $class,'action' => 'edit'))
                 . '\'">';
            } else {
                $data['urlrow'] = '<tr class="dataTableRowSelected">';
            }
        } else {
            $data['urlrow'] = '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' .
            xarModURL('commerce','admin','modules',array('set=' => $set,'module' => $class))
            . '\'">';
        }

        }
    }

    ksort($installed_modules);
    $q = new xenQuery('SELECT',$xartables['commerce_configuration'],array('configuration_value'));
    $q->eq('configuration_key',$module_keys[$j]);
    if(!$q->run()) return;
    $check = $q->output();
    if (!empty($check)) {
        if ($check['configuration_value'] != implode(';', $installed_modules)) {
            $q = new xenQuery('UPDATE',$xartables['commerce_configuration']);
            $q->addfield('configuration_value',implode(';', $installed_modules));
            $q->addfield('last_modified',now());
            $q->eq('configuration_key',$key);
            if(!$q->run()) return;
        }
    } else {
            $q = new xenQuery('INSERT',$xartables['commerce_configuration']);
            $q->addfield('configuration_key',implode(';', $installed_modules));
            $q->addfield('configuration_value',implode(';', $installed_modules));
            $q->addfield('configuration_group_id',6);
            $q->addfield('sort_order',0);
            $q->addfield('date_added',now());
            if(!$q->run()) return;
    }
/*
?>
              <tr>
                <td colspan="3" class="smallText"><?php echo TEXT_MODULE_DIRECTORY . ' ' . $module_directory; ?></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'edit':
      $keys = '';
      reset($mInfo->keys);
      while (list($key, $value) = each($mInfo->keys)) {
     // if($value['description']!='_DESC' && $value['title']!='_TITLE'){
        $keys .= '<b>' . $value['title'] . '</b><br>' .  $value['description'].'<br>';
    //  }
        if ($value['set_function']) {
          eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
        } else {
          $keys .= xtc_draw_input_field('configuration[' . $key . ']', $value['value']);
        }
        $keys .= '<br><br>';
      }
      $keys = substr($keys, 0, strrpos($keys, '<br><br>'));

      $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');

      $contents = array('form' => xtc_draw_form('modules', FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&action=save'));
      $contents[] = array('text' => $keys);
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    default:
      $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');

      if ($mInfo->status == '1') {
        $keys = '';
        reset($mInfo->keys);
        while (list(, $value) = each($mInfo->keys)) {
          $keys .= '<b>' . $value['title'] . '</b><br>';
          if ($value['use_function']) {
            $use_function = $value['use_function'];
            if (ereg('->', $use_function)) {
              $class_method = explode('->', $use_function);
              if (!is_object(${$class_method[0]})) {
                include(DIR_WS_CLASSES . $class_method[0] . '.php');
                ${$class_method[0]} = new $class_method[0]();
              }
              $keys .= xarModAPIFunc('commerce','admin','call_function',array(
                                        'function' => $class_method[1],
                                        'parameter' => $value['value'],
                                        'object' => ${$class_method[0]})
                                    );
            } else {
              $keys .= xarModAPIFunc('commerce','admin','call_function',array(
                                        'function' => $use_function,
                                        'parameter' => $value['value'])
                                    );
            }
          } else {
          if(strlen($value['value']) > 30) {
          $keys .=  substr($value['value'],0,30) . ' ...';
          } else {
            $keys .=  $value['value'];
            }
          }
          $keys .= '<br><br>';
        }
        $keys = substr($keys, 0, strrpos($keys, '<br><br>'));

        $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=remove') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_module_remove.gif'),'alt' => IMAGE_MODULE_REMOVE);
</a> <a href="' . xarModURL('commerce','admin',(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&action=edit') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT);
        </a>');
        $contents[] = array('text' => '<br>' . $mInfo->description);
        $contents[] = array('text' => '<br>' . $keys);
      } else {
        $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=install') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_module_install.gif'),'alt' => IMAGE_MODULE_INSTALL)        </a>');
        $contents[] = array('text' => '<br>' . $mInfo->description);
      }
      break;
  }

  if ( (xarModAPIFunc('commerce','user','not_null',array('arg' => $heading))) && (xarModAPIFunc('commerce','user','not_null',array('arg' => $contents))) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
  */
    return $data;
}
?>