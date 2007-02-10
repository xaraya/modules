<meta name="generator" content="(c) by <?php echo PROJECT_VERSION; ?> , http://www.xt-commerce.com">
<?php include(DIR_WS_MODULES.FILENAME_METATAGS); ?>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="<?php echo 'templates/'.CURRENT_TEMPLATE.'/stylesheet.css'; ?>">
<script type="text/javascript"><!--
var selected;

function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}

function selectRowEffect(object, buttonSelect) {
  if (!selected) {
    if (document.getElementById) {
      selected = document.getElementById('defaultSelected');
    } else {
      selected = document.all['defaultSelected'];
    }
  }

  if (selected) selected.className = 'moduleRow';
  object.className = 'moduleRowSelected';
  selected = object;

// one button is not an array
  if (document.checkout_payment.payment[0]) {
    document.checkout_payment.payment[buttonSelect].checked=true;
  } else {
    document.checkout_payment.payment.checked=true;
  }
}

function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}
function checkBox(object) {
  document.account_newsletter.elements[object].checked = !document.account_newsletter.elements[object].checked;
}
function popupImageWindow(url) {
  window.open(url,'popupImageWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
<?php
// require theme based javascript
require('templates/'.CURRENT_TEMPLATE.'/javascript/general.js.php');

if (strstr($PHP_SELF, FILENAME_CHECKOUT_PAYMENT)) {
 echo $payment_modules->javascript_validation();
}

if (strstr($PHP_SELF, FILENAME_CREATE_ACCOUNT)) {
require('includes/form_check.js.php');
}

if (strstr($PHP_SELF, FILENAME_CREATE_GUEST_ACCOUNT )) {
require('includes/form_check.js.php');
}
if (strstr($PHP_SELF, FILENAME_ACCOUNT_PASSWORD )) {
require('includes/form_check.js.php');
}
if (strstr($PHP_SELF, FILENAME_ACCOUNT_EDIT )) {
require('includes/form_check.js.php');
}
if (strstr($PHP_SELF, FILENAME_ADDRESS_BOOK_PROCES )) {
  if (isset($_GET['delete']) == false) {
    include('includes/form_check.js.php');
  }
  }
if (strstr($PHP_SELF, FILENAME_CHECKOUT_SHIPPING_ADDRESS )or strstr($PHP_SELF,FILENAME_CHECKOUT_PAYMENT_ADDRESS)) {
require('includes/form_check.js.php');
?>
<script type="text/javascript"><!--
function check_form_optional(form_name) {
  var form = form_name;

  var firstname = form.elements['firstname'].value;
  var lastname = form.elements['lastname'].value;
  var street_address = form.elements['street_address'].value;

  if (firstname == '' && lastname == '' && street_address == '') {
    return true;
  } else {
    return check_form(form_name);
  }
}
//--></script>
<?php
}

?>
<?php
if (strstr($PHP_SELF, FILENAME_ADVANCED_SEARCH )) {
?>
<script type="text/javascript" src="includes/general.js"></script>
<script type="text/javascript"><!--
function check_form() {
  var error_message = "<?php echo JS_ERROR; ?>";
  var error_found = false;
  var error_field;
  var keywords = document.advanced_search.keywords.value;
  var dfrom = document.advanced_search.dfrom.value;
  var dto = document.advanced_search.dto.value;
  var pfrom = document.advanced_search.pfrom.value;
  var pto = document.advanced_search.pto.value;
  var pfrom_float;
  var pto_float;

  if ( ((keywords == '') || (keywords.length < 1)) && ((dfrom == '') || (dfrom == '<?php echo DOB_FORMAT_STRING; ?>') || (dfrom.length < 1)) && ((dto == '') || (dto == '<?php echo DOB_FORMAT_STRING; ?>') || (dto.length < 1)) && ((pfrom == '') || (pfrom.length < 1)) && ((pto == '') || (pto.length < 1)) ) {
    error_message = error_message + "<?php echo JS_AT_LEAST_ONE_INPUT; ?>";
    error_field = document.advanced_search.keywords;
    error_found = true;
  }

  if ((dfrom.length > 0) && (dfrom != '<?php echo DOB_FORMAT_STRING; ?>')) {
    if (!IsValidDate(dfrom, '<?php echo DOB_FORMAT_STRING; ?>')) {
      error_message = error_message + "<?php echo JS_INVALID_FROM_DATE; ?>";
      error_field = document.advanced_search.dfrom;
      error_found = true;
    }
  }

  if ((dto.length > 0) && (dto != '<?php echo DOB_FORMAT_STRING; ?>')) {
    if (!IsValidDate(dto, '<?php echo DOB_FORMAT_STRING; ?>')) {
      error_message = error_message + "<?php echo JS_INVALID_TO_DATE; ?>";
      error_field = document.advanced_search.dto;
      error_found = true;
    }
  }

  if ((dfrom.length > 0) && (dfrom != '<?php echo DOB_FORMAT_STRING; ?>') && (IsValidDate(dfrom, '<?php echo DOB_FORMAT_STRING; ?>')) && (dto.length > 0) && (dto != '<?php echo DOB_FORMAT_STRING; ?>') && (IsValidDate(dto, '<?php echo DOB_FORMAT_STRING; ?>'))) {
    if (!CheckDateRange(document.advanced_search.dfrom, document.advanced_search.dto)) {
      error_message = error_message + "<?php echo JS_TO_DATE_LESS_THAN_FROM_DATE; ?>";
      error_field = document.advanced_search.dto;
      error_found = true;
    }
  }

  if (pfrom.length > 0) {
    pfrom_float = parseFloat(pfrom);
    if (isNaN(pfrom_float)) {
      error_message = error_message + "<?php echo JS_PRICE_FROM_MUST_BE_NUM; ?>";
      error_field = document.advanced_search.pfrom;
      error_found = true;
    }
  } else {
    pfrom_float = 0;
  }

  if (pto.length > 0) {
    pto_float = parseFloat(pto);
    if (isNaN(pto_float)) {
      error_message = error_message + "<?php echo JS_PRICE_TO_MUST_BE_NUM; ?>";
      error_field = document.advanced_search.pto;
      error_found = true;
    }
  } else {
    pto_float = 0;
  }

  if ( (pfrom.length > 0) && (pto.length > 0) ) {
    if ( (!isNaN(pfrom_float)) && (!isNaN(pto_float)) && (pto_float < pfrom_float) ) {
      error_message = error_message + "<?php echo JS_PRICE_TO_LESS_THAN_PRICE_FROM; ?>";
      error_field = document.advanced_search.pto;
      error_found = true;
    }
  }

  if (error_found == true) {
    alert(error_message);
    error_field.focus();
    return false;
  } else {
    RemoveFormatString(document.advanced_search.dfrom, "<?php echo DOB_FORMAT_STRING; ?>");
    RemoveFormatString(document.advanced_search.dto, "<?php echo DOB_FORMAT_STRING; ?>");
    return true;
  }
}

function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=280,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
<?php
}

if (strstr($PHP_SELF, FILENAME_PRODUCT_REVIEWS_WRITE )) {
?>


<script type="text/javascript"><!--
function checkForm() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var review = document.product_reviews_write.review.value;

  if (review.length < <?php echo REVIEW_TEXT_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_REVIEW_TEXT; ?>";
    error = 1;
  }

  if ((document.product_reviews_write.rating[0].checked) || (document.product_reviews_write.rating[1].checked) || (document.product_reviews_write.rating[2].checked) || (document.product_reviews_write.rating[3].checked) || (document.product_reviews_write.rating[4].checked)) {
  } else {
    error_message = error_message + "<?php echo JS_REVIEW_RATING; ?>";
    error = 1;
  }

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}
//--></script>
<?php
}
if (strstr($PHP_SELF, FILENAME_POPUP_IMAGE )) {
?>

<script type="text/javascript"><!--
var i=0;
function resize() {
  if (navigator.appName == 'Netscape') i=40;
  if (document.images[0]) window.resizeTo(document.images[0].width +30, document.images[0].height+60-i);
  self.focus();
}
//--></script>
<?php
}
?>

</head>
<?php
if (strstr($PHP_SELF, FILENAME_POPUP_IMAGE )) {
echo '<body onload="resize();"> ';
} else {
echo '<body>';
}

  // include needed functions
  require_once('inc/xtc_output_warning.inc.php');
  require_once('inc/xtc_image.inc.php');
  require_once('inc/xtc_draw_separator.inc.php');

  // check if the 'install' directory exists, and warn of its existence
  if (WARN_INSTALL_EXISTENCE == 'true') {
    if (file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/xtc_installer')) {
      xtc_output_warning(WARNING_INSTALL_DIRECTORY_EXISTS);
    }
  }

  // check if the configure.php file is writeable
  if (WARN_CONFIG_WRITEABLE == 'true') {
    if ( (file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php')) && (is_writeable(dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php')) ) {
      xtc_output_warning(WARNING_CONFIG_FILE_WRITEABLE);
    }
  }

  // check if the session folder is writeable
  if (WARN_SESSION_DIRECTORY_NOT_WRITEABLE == 'true') {
    if (STORE_SESSIONS == '') {
      if (!is_dir(xtc_session_save_path())) {
        xtc_output_warning(WARNING_SESSION_DIRECTORY_NON_EXISTENT);
      } elseif (!is_writeable(xtc_session_save_path())) {
        xtc_output_warning(WARNING_SESSION_DIRECTORY_NOT_WRITEABLE);
      }
    }
  }

  // check session.auto_start is disabled
  if ( (function_exists('ini_get')) && (WARN_SESSION_AUTO_START == 'true') ) {
    if (ini_get('session.auto_start') == '1') {
      xtc_output_warning(WARNING_SESSION_AUTO_START);
    }
  }

  if ( (WARN_DOWNLOAD_DIRECTORY_NOT_READABLE == 'true') && (DOWNLOAD_ENABLED == 'true') ) {
    if (!is_dir(DIR_FS_DOWNLOAD)) {
      xtc_output_warning(WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT);
    }
  }


$data['navtrail'] = $breadcrumb->trail(' &raquo; ');
if (isset($_SESSION['customer_id'])) {

$data['logoff'] = xarModURL('commerce','user',(FILENAME_LOGOFF, '', 'SSL');
}
if ( $_SESSION['account_type']=='0') {
$data['account'] = xarModURL('commerce','user',(FILENAME_ACCOUNT, '', 'SSL');
}
$data['cart'] = xarModURL('commerce','user','shopping_cart', '', 'SSL');
$data['checkout'] = xarModURL('commerce','user',(FILENAME_CHECKOUT_SHIPPING, '', 'SSL');



  if (isset($_GET['error_message']) && xarModAPIFunc('commerce','user','not_null',array('arg' => $_GET['error_message']))) {

$data['error'] = '
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="headerError">
        <td class="headerError">'. htmlspecialchars(urldecode($_GET['error_message'])).'</td>
      </tr>
    </table>';

  }

  if (isset($_GET['info_message']) && xarModAPIFunc('commerce','user','not_null',array('arg' => $_GET['info_message']))) {

$data['error'] = '
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="headerInfo">
        <td class="headerInfo">'.htmlspecialchars($_GET['info_message']).'</td>
      </tr>
    </table>';

  }
?>