<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function commerce_admin_module_newsletter()
{


  require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'class.phpmailer.php');
  require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');




  switch ($_GET['action']) {  // actions for datahandling

    case 'save': // save newsletter

     $newsletter_title=xtc_db_prepare_input($_POST['title']);
     $body=xtc_db_prepare_input($_POST['newsletter_body']);
     $id=xtc_db_prepare_input($_POST['ID']);

     $customers_status=xtc_get_customers_statuses();
     $rzp='';
     for ($i=0,$n=sizeof($customers_status);$i<$n; $i++) {

     if (xtc_db_prepare_input($_POST['status'][$i])=='yes') {
         if ($rzp!='') $rzp.=',';
     $rzp.=$customers_status[$i]['id'];
     }


     }
      if (xtc_db_prepare_input($_POST['status_all'])=='yes') $rzp.=',all';
     $error=false; // reset error flag

      if ($error == false) {

      $q->addfield('title'=> $newsletter_title);
                               $q->addfield('status','0');
                               $q->addfield('bc',$rzp);
                               $q->addfield('date'),'now()');
                               $q->addfield('body',$body);

   if ($id!='') {
   xtc_db_perform(TABLE_MODULE_NEWSLETTER, $sql_data_array, 'update', "newsletter_id = '" . $id . "'");
   // create temp table
   new xenQuery("DROP TABLE IF EXISTS module_newsletter_temp_".$id);
   new xenQuery("CREATE TABLE module_newsletter_temp_".$id."
                  (
                     id int(11) NOT NULL auto_increment,
                    customers_id int(11) NOT NULL default '0',
                    customers_status int(11) NOT NULL default '0',
                    customers_firstname varchar(64) NOT NULL default '',
                    customers_lastname varchar(64) NOT NULL default '',
                    customers_email_address text NOT NULL,
                    date datetime NOT NULL default '0000-00-00 00:00:00',
                    comment varchar(64) NOT NULL default '',
                    PRIMARY KEY  (id)
                    )");
   } else {
   xtc_db_perform(TABLE_MODULE_NEWSLETTER, $sql_data_array);
   // create temp table
   $id=xtc_db_insert_id();
   new xenQuery("DROP TABLE IF EXISTS module_newsletter_temp_".$id);
   new xenQuery("CREATE TABLE module_newsletter_temp_".$id."
                  (
                     id int(11) NOT NULL auto_increment,
                    customers_id int(11) NOT NULL default '0',
                    customers_status int(11) NOT NULL default '0',
                    customers_firstname varchar(64) NOT NULL default '',
                    customers_lastname varchar(64) NOT NULL default '',
                    customers_email_address text NOT NULL,
                    date datetime NOT NULL default '0000-00-00 00:00:00',
                    comment varchar(64) NOT NULL default '',
                    PRIMARY KEY  (id)
                    )");
   }

   // filling temp table with data!
   $flag='';
   if (!strpos($rzp,'all')) $flag='true';
   $rzp=str_replace(',all','',$rzp);
   $groups=explode(',',$rzp);
   $sql_data_array='';

   for ($i=0,$n=sizeof($groups);$i<$n;$i++) {

   $customers_query=new xenQuery("SELECT
                                  customers_id,
                                  customers_firstname,
                                  customers_lastname,
                                  customers_email_address
                                  FROM ".TABLE_CUSTOMERS."
                                  WHERE
                                  customers_status='".$groups[$i]."'");

      $q = new xenQuery();
      if(!$q->run()) return;
   while ($customers_data=$q->output()){
                               $q->addfield('customers_id',$customers_data['customers_id']);
                               $q->addfield('customers_status',$groups[$i]);
                               $q->addfield('customers_firstname',$customers_data['customers_firstname']);
                               $q->addfield('customers_lastname',$customers_data['customers_lastname']);
                               $q->addfield('customers_email_address',$customers_data['customers_email_address']);
                               $q->addfield('date','now()');


   xtc_db_perform('module_newsletter_temp_'.$id, $sql_data_array);
   }


   }

   xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER));
   }

   break;

   case 'delete':

   new xenQuery("DELETE FROM ".TABLE_MODULE_NEWSLETTER." WHERE   newsletter_id='".$_GET['ID']."'");
   xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER));

   break;

   case 'send':
   // max email package  -> should be in admin area!
   $package_size='30';
   xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER,'send=0,'.$package_size.'&ID='.$_GET['ID']));
   }

// action for sending mails!

if ($_GET['send']) {

$limits=explode(',',$_GET['send']);
$limit_low = $limits['0'];
$limit_up = $limits['1'];



     $limit_query=new xenQuery("SELECT count(*) as count
                                FROM module_newsletter_temp_".$_GET['ID']."
                                ");
      $q = new xenQuery();
      if(!$q->run()) return;
     $limit_data=$q->output();



 // select emailrange from db

    $email_query=new xenQuery("SELECT
                               customers_firstname,
                               customers_lastname,
                               customers_email_address,
                               id
                               FROM  module_newsletter_temp_".$_GET['ID']."
                               LIMIT ".$limit_low.",".$limit_up);

     $email_data=array();
      $q = new xenQuery();
      if(!$q->run()) return;
 while ($email_query_data=$q->output()) {

 $email_data[]=array('id' => $email_query_data['id'],
                      'firstname'=>$email_query_data['customers_firstname'],
                        'lastname'=>$email_query_data['customers_lastname'],
                        'email'=>$email_query_data['customers_email_address']);
 }

 // ok lets send the mails in package of 30 mails, to prevent php timeout
 $package_size='30';
 $break='0';
 if ($limit_data['count']<$limit_up) {
     $limit_up=$limit_data['count'];
     $break='1';
 }
 $max_runtime=$limit_up-$limit_low;
  $newsletters_query=new xenQuery("SELECT
                                   title,
                                    body,
                                    bc,
                                    cc
                                  FROM ".TABLE_MODULE_NEWSLETTER."
                                  WHERE  newsletter_id='".$_GET['ID']."'");
      $q = new xenQuery();
      if(!$q->run()) return;
 $newsletters_data=$q->output();
 xtc_php_mail(EMAIL_SUPPORT_ADDRESS,EMAIL_SUPPORT_NAME,$newsletters_data['cc'],'' , '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', $newsletters_data['title'],$newsletters_data['body'] , $newsletters_data['body']);
 for ($i=1;$i<=$max_runtime;$i++)
 {
  // mail
  xtc_php_mail(EMAIL_SUPPORT_ADDRESS,EMAIL_SUPPORT_NAME,$email_data[$i-1]['email'] ,$email_data[$i-1]['lastname'] . ' ' . $email_data[$i-1]['firstname'] , $newsletters_data['cc'], EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', $newsletters_data['title'],$newsletters_data['body'] , $newsletters_data['body']);
  new xenQuery("UPDATE module_newsletter_temp_".$_GET['ID']." SET comment='send' WHERE id='".$email_data[$i-1]['id']."'");

 }
 if ($break=='1') {
     // finished

          $limit1_query=new xenQuery("SELECT count(*) as count
                                FROM module_newsletter_temp_".$_GET['ID']."
                                WHERE comment='send'");
      $q = new xenQuery();
      if(!$q->run()) return;
     $limit1_data=$q->output();

     if ($limit1_data['count']-$limit_data['count']<=0)
     {
     new xenQuery("UPDATE ".TABLE_MODULE_NEWSLETTER." SET status='1' WHERE newsletter_id='".$_GET['ID']."'");
     xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER));
     } else {
     echo '<b>'.$limit1_data['count'].'<b> emails send<br>';
     echo '<b>'.$limit1_data['count']-$limit_data['count'].'<b> emails left';
     }


 } else {
 $limit_low=$limit_up+1;
 $limit_up=$limit_low+$package_size;
 xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER,'send='.$limit_low.','.$$limit_up.'&ID='.$_GET['ID']));
 }


}



 if ($_GET['send'])
 {
 ?>

      <tr><td>
      Sending
      </td></tr>
<?php
}
?>

      <tr>
        <td><table width="100%" border="0">
          <tr>
            <td>
 <?php

 // Default seite
switch ($_GET['action']) {

    default:

 // Get Customers Groups
 $customer_group_query=new xenQuery("SELECT
                                     customers_status_name,
                                     customers_status_id,
                                     customers_status_image
                                     FROM ".TABLE_CUSTOMERS_STATUS."
                                     WHERE
                                     language_id='".$_SESSION['languages_id']."'");
 $customer_group=array();
      $q = new xenQuery();
      if(!$q->run()) return;
 while ($customer_group_data=$q->output() {

      // get single users
     $group_query=new xenQuery("SELECT count(*) as count
                                FROM ".TABLE_CUSTOMERS."
                                WHERE customers_newsletter='1' and
                                customers_status='".$customer_group_data['customers_status_id']."'");
      $q = new xenQuery();
      if(!$q->run()) return;
     $group_data=$q->output();


 $customer_group[]=array('ID'=>$customer_group_data['customers_status_id'],
                          'NAME'=>$customer_group_data['customers_status_name'],
                          'IMAGE'=>$customer_group_data['customers_status_image'],
                          'USERS'=>$group_data['count']);


 }

 ?>
<br>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr class="dataTableHeadingRow">
          <td class="dataTableHeadingContent" width="150" ><?php echo TITLE_CUSTOMERS; ?></td>
          <td class="dataTableHeadingContent"  ><?php echo TITLE_STK; ?></td>
        </tr>

        <?php
for ($i=0,$n=sizeof($customer_group); $i<$n; $i++) {
?>
        <tr>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" valign="middle" align="left"><?php echo xtc_image(xarTplGetImage(DIR_WS_ICONS . $customer_group[$i]['IMAGE']), ''); ?><?php echo $customer_group[$i]['NAME']; ?></td>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left"><?php echo $customer_group[$i]['USERS']; ?></td>
        </tr>
        <?php
}
?>
      </table></td>
    <td width="30%" align="right" valign="top""><?php
    echo '<a href="'.xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER,'action=new').'">'.
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_new_newsletter.gif'));
    </a>';


    ?></td>
  </tr>
</table>
 <br>
 <?php

 // get data for newsletter overwiev

 $newsletters_query=new xenQuery("SELECT
                                   newsletter_id,date,title
                                  FROM ".TABLE_MODULE_NEWSLETTER."
                                  WHERE status='0'");
 $news_data=array();
      $q = new xenQuery();
      if(!$q->run()) return;
 while ($newsletters_data=$q->output()) {

 $news_data[]=array(    'id' => $newsletters_data['newsletter_id'],
                        'date'=>$newsletters_data['date'],
                        'title'=>$newsletters_data['title']);
 }

?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" width="30" ><?php echo TITLE_DATE; ?></td>
          <td class="dataTableHeadingContent" width="80%" ><?php echo TITLE_NOT_SEND; ?></td>
          <td class="dataTableHeadingContent"  >.</td>
        </tr>
<?php
for ($i=0,$n=sizeof($news_data); $i<$n; $i++) {
if ($news_data[$i]['id']!='') {
?>
        <tr>
        <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left"><?php echo $news_data[$i]['date']; ?></td>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" valign="middle" align="left"><?php echo xtc_image(xarTplGetImage(DIR_WS_CATALOG.'images/icons/arrow.gif'); ?><a href="<?php echo xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER,'ID='.$news_data[$i]['id']); ?>"><b><?php echo $news_data[$i]['title']; ?></b></a></td>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left">






          </td>
        </tr>
 <?php

if ($_GET['ID']!='' && $_GET['ID']==$news_data[$i]['id']) {

$total_query=new xenQuery("SELECT
                           count(*) as count
                           FROM module_newsletter_temp_".$_GET['ID']."");
      $q = new xenQuery();
      if(!$q->run()) return;
$total_data=$q->output();
?>
<tr>
<td class="dataTableContent_products" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left"></td>
<td colspan="2" class="dataTableContent_products" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left"><?php echo TEXT_SEND_TO.$total_data['count']; ?></td>
</tr>
<td class="dataTableContent" valign="top" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left">
  <a href="<?php echo xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER,'action=delete&ID='.$news_data[$i]['id']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
  <?php
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif'),'' => '',''=>'','parameters' => 'Delete','','','style="cursor:hand" onClick="return confirm(\''.DELETE_ENTRY.'\')"');
</a><br>';
  ?>
<a href="<?php echo xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER,'action=edit&ID='.$news_data[$i]['id']); ?>">
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_edit.gif'),'' => '',''=>'','parameters' => 'Edit','','','style="cursor:hand" onClick="return confirm(\''.DELETE_ENTRY.'\')"');
</a>'; ?>
<a href="<?php echo xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER,'action=send&ID='.$news_data[$i]['id']); ?>"><br><br><hr noshade>
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_send.gif'),'' => '',''=>'','parameters' => 'style="cursor:hand" onClick="return confirm(\''.DELETE_ENTRY.'\')"');
</a>'; ?>

</td>
<td colspan="2" class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left">
<?php

 // get data
    $newsletters_query=new xenQuery("SELECT
                                   title,body,cc,bc
                                  FROM ".TABLE_MODULE_NEWSLETTER."
                                  WHERE newsletter_id='".$_GET['ID']."'");
      $q = new xenQuery();
      if(!$q->run()) return;
   $newsletters_data=$q->output();

echo TEXT_TITLE.$newsletters_data['title'].'<br>';

     $customers_status=xtc_get_customers_statuses();
     for ($i=0,$n=sizeof($customers_status);$i<$n; $i++) {

     $newsletters_data['bc']=str_replace($customers_status[$i]['id'],$customers_status[$i]['text'],$newsletters_data['bc']);

     }

echo TEXT_TO.$newsletters_data['bc'].'<br>';
echo TEXT_CC.$newsletters_data['cc'].'<br><br>'.TEXT_PREVIEW;
echo '<table style="border-color: #cccccc; border: 1px solid;" width="100%"><tr><td>'.$newsletters_data['body'].'</td></tr></table>';
?>
</td></tr>
<?php
}
?>

<?php
}
}


?>
</table>
<br><br>
<?php
 $newsletters_query=new xenQuery("SELECT
                                   newsletter_id,date,title
                                  FROM ".TABLE_MODULE_NEWSLETTER."
                                  WHERE status='1'");
 $news_data=array();
      $q = new xenQuery();
      if(!$q->run()) return;
 while ($newsletters_data=$q->output()) {

 $news_data[]=array(    'id' => $newsletters_data['newsletter_id'],
                        'date'=>$newsletters_data['date'],
                        'title'=>$newsletters_data['title']);
 }

?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr class="dataTableHeadingRow">
          <td class="dataTableHeadingContent" width="80%" ><?php echo TITLE_SEND; ?></td>
          <td class="dataTableHeadingContent"><?php echo TITLE_ACTION; ?></td>
        </tr>
<?php
for ($i=0,$n=sizeof($news_data); $i<$n; $i++) {
if ($news_data[$i]['id']!='') {
?>
        <tr>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" valign="middle" align="left"><?php echo $news_data[$i]['date'].'    '; ?><b><?php echo $news_data[$i]['title']; ?></b></td>
          <td class="dataTableContent" style="border-bottom: 1px solid; border-color: #f1f1f1;" align="left">

  <a href="<?php echo xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER,'action=delete&ID='.$news_data[$i]['id']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
  <?php
  echo xtc_image(xarTplGetImage(DIR_WS_ICONS.'delete.gif'),'Delete','','','style="cursor:hand" onClick="return confirm(\''.DELETE_ENTRY.'\')"').'  '.TEXT_DELETE.'</a>&#160;&#160;';
  ?>
           <a href="<?php echo xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER,'action=edit_products_content&coID='.$content_array[$ii]['id']); ?>">
<?php echo xtc_image(xarTplGetImage(DIR_WS_ICONS.'icon_edit.gif'),'Edit','','','style="cursor:hand" onClick="return confirm(\''.DELETE_ENTRY.'\')"').'  '.TEXT_EDIT.'</a>'; ?>






          </td>
        </tr>
<?php
}
}


?>
</table>

<?php


  break;       // end default page

  case 'edit':

   $newsletters_query=new xenQuery("SELECT
                                   title,body,cc
                                  FROM ".TABLE_MODULE_NEWSLETTER."
                                  WHERE newsletter_id='".$_GET['ID']."'");
      $q = new xenQuery();
      if(!$q->run()) return;
   $newsletters_data=$q->output();

  case 'safe':
  case 'new':  // action for NEW newsletter!

$customers_status=xtc_get_customers_statuses();


  echo xtc_draw_form('edit_newsletter',FILENAME_MODULE_NEWSLETTER,'action=save','post','enctype="multipart/form-data"').
  <input type="hidden" name="ID" value="#$_GET['ID']#">


  <br><br>
 <table class="main" width="100%" border="0">
   </tr>
      <tr>
      <td width="10%"><?php echo TEXT_TITLE; ?></td>
      <td width="90%"><?php echo xtc_draw_textarea_field('title', 'soft', '100%', '3',$newsletters_data['title']); ?></td>
   </tr>
            <tr>
      <td width="10%"><?php echo TEXT_TO; ?></td>
      <td width="90%"><?php
for ($i=0,$n=sizeof($customers_status);$i<$n; $i++) {

     $group_query=new xenQuery("SELECT count(*) as count
                                FROM ".TABLE_CUSTOMERS."
                                WHERE customers_newsletter='1' and
                                customers_status='".$customers_status[$i]['id']."'");
      $q = new xenQuery();
      if(!$q->run()) return;
     $group_data=$q->output();

     $group_query=new xenQuery("SELECT count(*) as count
                                FROM ".TABLE_CUSTOMERS."
                                WHERE
                                customers_status='".$customers_status[$i]['id']."'");
      $q = new xenQuery();
      if(!$q->run()) return;
     $group_data_all=$q->output();

echo xtc_draw_checkbox_field('status['.$i.']', 'yes',true).' '.$customers_status[$i]['text'].'  <i>(<b>'.$group_data['count'].'</b>'.TEXT_USERS.$group_data_all['count'].TEXT_CUSTOMERS.'<br>';

}
echo xtc_draw_checkbox_field('status_all', 'yes',false).' <b>'.TEXT_NEWSLETTER_ONLY.'</b>';

       ?></td>
   </tr>
         <tr>
      <td width="10%"><?php echo TEXT_CC; ?></td>
      <td width="90%"><?php

       echo xtc_draw_textarea_field('cc', 'soft', '100%', '3',$newsletters_data['cc']); ?></td>
   </tr>
      </tr>
      <tr>
      <td width="10%" valign="top"><?php echo TEXT_BODY; ?></td>
      <td width="90%"><?php
$sw = new SPAW_Wysiwyg(
              $control_name='newsletter_body', // control's name
              $value=stripslashes($newsletters_data['body']),                  // initial value
              $lang='',                   // language
              $mode = 'own',                 // toolbar mode
              $theme='default',                  // theme (skin)
              $width='100%',              // width
              $height='800px',            // height
              $css_stylesheet='',         // css stylesheet file for content
              $dropdown_data=''           // data for dropdowns (style, font, etc.)
            );


$sw->show();

        ?></td>
   </tr>
   </table>
   <a href="<?php echo xarModURL('commerce','admin',(FILENAME_MODULE_NEWSLETTER); ?>"><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_back.gif'),'alt' => IMAGE_NEW_BACK);; ?></a>
   <right><?php echo <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_save.gif')#" border="0" alt=IMAGE_SAVE>; ?></right>
  </form>
  <?php

  break;
} // end switch
}
?>