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

/**
 * Initialise the block
 */
function commerce_categoriesblock_init()
{
    return array(
        'content_text' => '',
        'content_type' => 'text',
        'expire' => 0,
        'hide_empty' => true,
        'custom_format' => '',
        'hide_errors' => true,
        'start_date' => '',
        'end_date' => ''
    );
}

/**
 * Get information on the block ($blockinfo array)
 */
function commerce_categoriesblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => 'commerce_categories_update',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true,
        'notes' => "content_type can be 'text', 'html', 'php' or 'data'"
    );
}

/**
 * Display function
 * @param $blockinfo array
 * @returns $blockinfo array
 */
function commerce_categoriesblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

    include_once 'modules/xen/xarclasses/xenquery.php';
    $xartables = xarDBGetTables();

    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $currentlang = xarModAPIFunc('commerce','user','get_language',array('locale' => $data['language']));

    $box_content='';

  // include needed functions
//  require_once(DIR_FS_INC . 'xtc_show_category.inc.php');
//  require_once(DIR_FS_INC . 'xtc_has_category_subcategories.inc.php');
//  require_once(DIR_FS_INC . 'xtc_count_products_in_category.inc.php');


    $categories_string = '';

    $q = new xenQuery('SELECT');
    $q->addtable($xartables['commerce_categories'],'c');
    $q->addtable($xartables['commerce_categories_description'],'cd');
    $q->addfields(array('c.categories_id', 'cd.categories_name', 'c.parent_id'));
    $q->eq('c.categories_status', 1);
    $q->eq('c.parent_id', 0);
    $q->eq('cd.language_id', $currentlang['id']);
    $q->join('c.categories_id', 'cd.categories_id');
    $q->setorder('sort_order');
    $q->addorder('cd.categories_name');
    if(!$q->run()) return;

    foreach ($q->output() as $categories)  {
        $foo[$categories['categories_id']] = array(
                                            'name' => $categories['categories_name'],
                                            'parent' => $categories['parent_id'],
                                            'level' => 0,
                                            'path' => $categories['categories_id'],
                                            'next_id' => false);

        if (isset($prev_id)) {
            $foo[$prev_id]['next_id'] = $categories['categories_id'];
        }

        $prev_id = $categories['categories_id'];

        if (!isset($first_element)) {
            $first_element = $categories['categories_id'];
        }
    }
    if (!isset($first_element)) $first_element = 0;

  //------------------------
  if (isset($cPath)) {
    $new_path = '';
    $id = split('_', $cPath);
    reset($id);
    while (list($key, $value) = each($id)) {
      unset($prev_id);
      unset($first_id);
      $categories_query = new xenQuery("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.parent_id = '" . $value . "' and c.categories_id = cd.categories_id and cd.language_id='" . $_SESSION['languages_id'] ."' order by sort_order, cd.categories_name");
      $category_check = $categories_query->getrows();
      if ($category_check > 0) {
        $new_path .= $value;
      $q = new xenQuery();
      if(!$q->run()) return;
        while ($row = $q->output()) {
          $foo[$row['categories_id']] = array(
                                              'name' => $row['categories_name'],
                                              'parent' => $row['parent_id'],
                                              'level' => $key+1,
                                              'path' => $new_path . '_' . $row['categories_id'],
                                              'next_id' => false);

          if (isset($prev_id)) {
            $foo[$prev_id]['next_id'] = $row['categories_id'];
          }

          $prev_id = $row['categories_id'];

          if (!isset($first_id)) {
            $first_id = $row['categories_id'];
          }

          $last_id = $row['categories_id'];
        }
        $foo[$last_id]['next_id'] = $foo[$value]['next_id'];
        $foo[$value]['next_id'] = $first_id;
        $new_path .= '_';
      } else {
        break;
      }
    }
  }
  xarModAPIFunc('commerce','user','show_category', array('base' => $first_element));

/*     $box_smarty->assign('BOX_TITLE', BOX_HEADING_CATEGORIES);
    $box_smarty->assign('BOX_CONTENT', $categories_string);
         // set cache ID
  if (USE_CACHE=='false') {
  $box_smarty->caching = 0;
  $box_categories= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_categories.html');
  } else {
  $box_smarty->caching = 1;
  $box_smarty->cache_lifetime=CACHE_LIFETIME;
  $box_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$_GET['cPath'];
  $box_categories= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_categories.html',$cache_id);
  }
*/
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>