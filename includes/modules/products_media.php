<?php
/* -----------------------------------------------------------------------------------------
   $Id: products_media.php,v 1.7 2003/12/30 09:02:31 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2003  nextcommerce (products_media.php,v 1.8 2003/08/25); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
//$module_smarty= new Smarty;
$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
$module_content=array();
$filename='';

// check if allowed to see
require_once(DIR_FS_INC.'xtc_in_array.inc.php');
$check_query=new xenQuery("SELECT DISTINCT
                products_id
                FROM ".TABLE_PRODUCTS_CONTENT."
                WHERE languages_id='".$_SESSION['languages_id']."'");
//$check_data=$q->output();

$check_data=array();
$i='0';
      $q = new xenQuery();
      if(!$q->run()) return;
while ($content_data=$q->output()) {
 $check_data[$i]=$content_data['products_id'];
 $i++;
}
if (xtc_in_array($_GET['products_id'],$check_data)) {
// get content data

require_once(DIR_FS_INC.'xtc_filesize.inc.php');


//get download
$content_query=new xenQuery("SELECT
                content_id,
                content_name,
                content_link,
                content_file,
                content_read,
                file_comment
                FROM ".TABLE_PRODUCTS_CONTENT."
                WHERE
                products_id='".$_GET['products_id']."' AND
                languages_id='".$_SESSION['languages_id']."'");


      $q = new xenQuery();
      if(!$q->run()) return;
    while ($content_data=$q->output()) {
    $filename='';
    if ($content_data['content_link']!='')  {

    $icon= xtc_image(xarTplGetImage(DIR_WS_CATALOG.'admin/images/icons/icon_link.gif');
    } else {
    $icon= xtc_image(xarTplGetImage(DIR_WS_CATALOG.'admin/images/icons/icon_'.str_replace('.','',strstr($content_data['content_file'],'.')).'.gif');
    }



    if ($content_data['content_link']!='')  $filename= '<a href="'.$content_data['content_link'].'" target="new">';
    $filename.=  $content_data['content_name'];
    if ($content_data['content_link']!='') $filename.= '</a>';

    if ($content_data['content_link']=='') {
    if (eregi('.html',$content_data['content_file'])
    or eregi('.htm',$content_data['content_file'])
    or eregi('.txt',$content_data['content_file'])
    or eregi('.bmp',$content_data['content_file'])
    or eregi('.jpg',$content_data['content_file'])
    or eregi('.gif',$content_data['content_file'])
    or eregi('.png',$content_data['content_file'])
    or eregi('.tif',$content_data['content_file'])
    )
    {


     $button = '<a style="cursor:hand" onclick="javascript:window.open(\''.xarModURL('commerce','user',(FILENAME_MEDIA_CONTENT,'coID='.$content_data['content_id']).'\', \'popup\', \'toolbar=0, width=640, height=600\')">
     xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_view.gif'),'alt' => TEXT_VIEW).'
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_view.gif'),'alt' => TEXT_VIEW);
     </a>';

    } else {

    $button= '<a href="'.xarModURL('commerce','user',('media/products/'.$content_data['content_file']).'">'.
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_view.gif'),'alt' => TEXT_DOWNLOAD)    </a>';

    }
    }
$module_content[]=array(
            'ICON' => $icon,
            'FILENAME' => $filename,
            'DESCRIPTION' => $content_data['file_comment'],
            'FILESIZE' => xtc_filesize($content_data['content_file']),
            'BUTTON' => $button,
            'HITS' => $content_data['content_read']);
    }

  $module_smarty->assign('language', $_SESSION['language']);
  $module_smarty->assign('module_content',$module_content);
  // set cache ID
  if (USE_CACHE=='false') {
  $module_smarty->caching = 0;
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/products_media.html');
  } else {
  $module_smarty->caching = 1;
  $module_smarty->cache_lifetime=CACHE_LIFETIME;
  $module_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$_GET['products_id'].$_SESSION['customers_status']['customers_status_name'];
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/products_media.html',$cache_id);
  }
  $info_smarty->assign('MODULE_products_media',$module);
}
?>