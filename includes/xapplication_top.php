<?php
/* -----------------------------------------------------------------------------------------
   $Id: application_top.php,v 1.11 2003/12/14 13:11:47 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.273 2003/05/19); www.oscommerce.com
   (c) 2003  nextcommerce (application_top.php,v 1.54 2003/08/25); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  // start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

  // set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE);
//  error_reporting(E_ALL);

  // Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
  if (file_exists('includes/local/configure.php')) {
    include('includes/local/configure.php');
  } else {
    include('includes/configure.php');
  }



  // define the project version
  define('PROJECT_VERSION', 'XT-Commerce v1.0 Beta2');

  // set the type of request (secure or not)
  $request_type = (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

  // set php_self in the local scope
  $PHP_SELF = $_SERVER['PHP_SELF'];

  // include the list of project filenames
  require(DIR_WS_INCLUDES . 'filenames.php');

  // include the list of project database tables
  require(DIR_WS_INCLUDES . 'database_tables.php');

  // customization for the design layout
  define('BOX_WIDTH', 125); // how wide the boxes should be in pixels (default: 125)

  // Store DB-Querys in a Log File
  define('STORE_DB_TRANSACTIONS', 'false');

  // include used functions
  require_once(DIR_FS_INC . 'xtc_db_error.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_perform.inc.php');
  require_once(DIR_FS_INC . 'new xenQuery.inc.php');
  require_once(DIR_FS_INC . '$q->output().inc.php');
  require_once(DIR_FS_INC . 'xtc_db_insert_id.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_free_result.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_fetch_fields.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_input.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_prepare_input.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_top_level_domain.inc.php');
  require_once(DIR_FS_INC . 'xtc_update_whos_online.inc.php');

  require_once(DIR_FS_INC . 'xtc_expire_banners.inc.php');
  require_once(DIR_FS_INC . 'xtc_expire_specials.inc.php');
  require_once(DIR_FS_INC . 'xtc_parse_category_path.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_product_path.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_parent_categories.inc.php');
  require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_uprid.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');
  require_once(DIR_FS_INC . 'xtc_has_product_attributes.inc.php');
  require_once(DIR_FS_INC . 'xtc_image.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_products_attribute_price.inc.php');
  require_once(DIR_FS_INC . 'xtc_check_stock_attributes.inc.php');
  require_once(DIR_FS_INC . 'xtc_currency_exists.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_separator.inc.php');
  require_once(DIR_FS_INC . 'xtc_remove_non_numeric.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_ip_address.inc.php');
  require_once(DIR_FS_INC . 'xtc_setcookie.inc.php');

  // modification for new graduated system
  require_once(DIR_FS_INC . 'xtc_count_cart.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_qty.inc.php');

  // set the application parameters
  $configuration_query = new xenQuery('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
      $q = new xenQuery();
      $q->run();
  while ($configuration = $q->output()) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }

  // if gzip_compression is enabled, start to buffer the output
  if ( (GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && (PHP_VERSION >= '4') ) {
    if (($ini_zlib_output_compression = (int)ini_get('zlib.output_compression')) < 1) {
      ob_start('ob_gzhandler');
    } else {
      ini_set('zlib.output_compression_level', GZIP_LEVEL);
    }
  }

  // set the HTTP GET parameters manually if search_engine_friendly_urls is enabled
  if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
    if (strlen(getenv('PATH_INFO')) > 1) {
      $GET_array = array();
      $PHP_SELF = str_replace(getenv('PATH_INFO'), '', $PHP_SELF);
      $vars = explode('/', substr(getenv('PATH_INFO'), 1));
      for ($i=0, $n=sizeof($vars); $i<$n; $i++) {
        if (strpos($vars[$i], '[]')) {
          $GET_array[substr($vars[$i], 0, -2)][] = $vars[$i+1];
        } else {
          $_GET[$vars[$i]] = $vars[$i+1];
        }
        $i++;
      }

      if (sizeof($GET_array) > 0) {
        while (list($key, $value) = each($GET_array)) {
          $_GET[$key] = $value;
        }
      }
    }
  }

  // set the top level domains
  $http_domain = xtc_get_top_level_domain(HTTP_SERVER);
  $https_domain = xtc_get_top_level_domain(HTTPS_SERVER);
  $current_domain = (($request_type == 'NONSSL') ? $http_domain : $https_domain);

  // include cache functions if enabled
 // if (USE_CACHE == 'true') include(DIR_WS_FUNCTIONS . 'cache.php');

  // include shopping cart class
  require(DIR_WS_CLASSES . 'shopping_cart.php');

  // include navigation history class
  require(DIR_WS_CLASSES . 'navigation_history.php');

  // some code to solve compatibility issues
  require(DIR_WS_FUNCTIONS . 'compatibility.php');

  // define how the session functions will be used
  require(DIR_WS_FUNCTIONS . 'sessions.php');

  // set the session name and save path
  session_name('XTCsid');
  session_save_path(SESSION_WRITE_DIRECTORY);

  // set the session cookie parameters
   if (function_exists('session_set_cookie_params')) {
    session_set_cookie_params(0, '/', (xarModAPIFunc('commerce','user','not_null',array('arg' => $current_domain)) ? '.' . $current_domain : ''));
  } elseif (function_exists('ini_set')) {
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_domain', (xarModAPIFunc('commerce','user','not_null',array('arg' => $current_domain)) ? '.' . $current_domain : ''));
  }

  // set the session ID if it exists
  if (isset($_POST[session_name()])) {
    session_id($_POST[session_name()]);
  } elseif ( ($request_type == 'SSL') && isset($_GET[session_name()]) ) {
    session_id($_GET[session_name()]);
  }

  // start the session
  $session_started = false;
  if (SESSION_FORCE_COOKIE_USE == 'True') {
    xtc_setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*30, '/', $current_domain);

    if (isset($HTTP_COOKIE_VARS['cookie_test'])) {
      session_start();
      $session_started = true;
    }
  } elseif (SESSION_BLOCK_SPIDERS == 'True') {
    $user_agent = strtolower(getenv('HTTP_USER_AGENT'));
    $spider_flag = false;

    if (xarModAPIFunc('commerce','user','not_null',array('arg' => $user_agent))) {
      $spiders = file(DIR_WS_INCLUDES . 'spiders.txt');

      for ($i=0, $n=sizeof($spiders); $i<$n; $i++) {
        if (xarModAPIFunc('commerce','user','not_null',array('arg' => $spiders[$i]))) {
          if (is_integer(strpos($user_agent, trim($spiders[$i])))) {
            $spider_flag = true;
            break;
          }
        }
      }
    }

    if ($spider_flag == false) {
      session_start();
      $session_started = true;
    }
  } else {
    session_start();
    $session_started = true;
  }

  // verify the ssl_session_id if the feature is enabled
  if ( ($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true) ) {
    $ssl_session_id = getenv('SSL_SESSION_ID');
    if (!session_is_registered('SSL_SESSION_ID')) {
      $_SESSION['SESSION_SSL_ID'] = $ssl_session_id;
    }

    if ($_SESSION['SESSION_SSL_ID'] != $ssl_session_id) {
      session_destroy();
      xarRedirectResponse(xarModURL('commerce','user','ssl_check'));
    }
  }

  // verify the browser user agent if the feature is enabled
  if (SESSION_CHECK_USER_AGENT == 'True') {
    $http_user_agent = getenv('HTTP_USER_AGENT');
    if (!session_is_registered('SESSION_USER_AGENT')) {
      $_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
    }

    if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
      session_destroy();
      xarRedirectResponse(xarModURL('commerce','user','login'));
    }
  }

  // verify the IP address if the feature is enabled
  if (SESSION_CHECK_IP_ADDRESS == 'True') {
    $ip_address = xtc_get_ip_address();
    if (!isset($_SESSION['SESSION_IP_ADDRESS'])) {
      $_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
    }

    if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
      session_destroy();
      xarRedirectResponse(xarModURL('commerce','user','login'));
    }
  }

  // create the shopping cart & fix the cart if necesary
  if (!is_object($_SESSION['cart'])) {
    $_SESSION['cart'] = new shoppingCart;
  }

  // include currencies class and create an instance
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  // include the mail classes
  if (EMAIL_TRANSPORT == 'sendmail') include(DIR_WS_CLASSES . 'class.phpmailer.php');
  if (EMAIL_TRANSPORT == 'smtp') include(DIR_WS_CLASSES . 'class.smtp.php');

  // set the language
  if (!isset($_SESSION['language']) || isset($_GET['language'])) {

    include(DIR_WS_CLASSES . 'language.php');
    $lng = new language($_GET['language']);

    if (!isset($_GET['language'])) $lng->get_browser_language();

    $_SESSION['language'] = $lng->language['directory'];
    $_SESSION['languages_id'] = $lng->language['id'];
    $_SESSION['language_charset'] = $lng->language['language_charset'];
  }

  // include the language translations
  require(DIR_WS_LANGUAGES . $_SESSION['language'].'/'.$_SESSION['language'] . '.php');

  // currency
  if (!isset($_SESSION['currency']) || isset($_GET['currency']) || ( (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (LANGUAGE_CURRENCY != $_SESSION['currency']) ) ) {

    if (isset($_GET['currency'])) {
      if (!$_SESSION['currency'] = xtc_currency_exists($_GET['currency'])) $_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
    } else {
      $_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
    }
  }
  if (isset($_SESSION['currency']) && $_SESSION['currency'] == '') {
    $_SESSION['currency'] = DEFAULT_CURRENCY;
  }



  // Shopping cart actions
  if (isset($_GET['action'])) {
    // redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled
    if ($session_started == false) {
      xarRedirectResponse(xarModURL('commerce','user',(FILENAME_COOKIE_USAGE));
    }

    if (DISPLAY_CART == 'true') {
      $goto =  FILENAME_SHOPPING_CART;
      $parameters = array('action', 'cPath', 'products_id', 'pid');
    } else {
      $goto = basename($PHP_SELF);
      if ($_GET['action'] == 'buy_now') {
        $parameters = array('action', 'pid', 'products_id');
      } else {
        $parameters = array('action', 'pid');
      }
    }
    switch ($_GET['action']) {
      // customer wants to update the product quantity in their shopping cart
      case 'update_product':
        for ($i = 0, $n = sizeof($_POST['products_id']); $i < $n; $i++) {
          if (in_array($_POST['products_id'][$i], (is_array($_POST['cart_delete']) ? $_POST['cart_delete'] : array()))) {
            $_SESSION['cart']->remove($_POST['products_id'][$i]);
          } else {
            $attributes = ($_POST['id'][$_POST['products_id'][$i]]) ? $_POST['id'][$_POST['products_id'][$i]] : '';
            $_SESSION['cart']->add_cart($_POST['products_id'][$i], xtc_remove_non_numeric($_POST['cart_quantity'][$i]), $attributes, false);
          }
        }
        xarRedirectResponse(xarModURL('commerce','user',($goto, xtc_get_all_get_params($parameters)));
        break;
      // customer adds a product from the products page
      case 'add_product':
        if (isset($_POST['products_id']) && is_numeric($_POST['products_id'])) {
          $_SESSION['cart']->add_cart($_POST['products_id'], $_SESSION['cart']->get_quantity(xtc_get_uprid($_POST['products_id'], $_POST['id']))+$_POST['products_qty'], $_POST['id']);
        }
        xarRedirectResponse(xarModURL('commerce','user',($goto, xtc_get_all_get_params($parameters)));
        break;

            // customer wants to add a quickie to the cart (called from a box)
      case 'add_a_quickie' :  $quickie_query = new xenQuery("select products_id from " . TABLE_PRODUCTS . " where products_model = '" . $_POST['quickie'] . "'");
                              if (!$quickie_query->getrows())) {
                                $quickie_query = new xenQuery("select products_id from " . TABLE_PRODUCTS . " where products_model LIKE '%" . $_POST['quickie'] . "%'");
                              }
                              if ($quickie_query->getrows() != 1) {
                                xarRedirectResponse(xarModURL('commerce','user',(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords=' . $_POST['quickie'], 'NONSSL'));
                              }
      $q = new xenQuery();
      $q->run();
                              $quickie = $q->output();
                              if (xtc_has_product_attributes($quickie['products_id'])) {
                                xarRedirectResponse(xarModURL('commerce','user','product_info', 'products_id=' . $quickie['products_id'], 'NONSSL'));
                              } else {
                                $_SESSION['cart']->add_cart($quickie['products_id'], 1);
                                xarRedirectResponse(xarModURL('commerce','user',($goto, xtc_get_all_get_params(array('action')), 'NONSSL'));
                              }
                              break;

      // performed by the 'buy now' button in product listings and review page
      case 'buy_now':
        if (isset($_GET['BUYproducts_id'])) {
          if (xtc_has_product_attributes($_GET['BUYproducts_id'])) {
            xarRedirectResponse(xarModURL('commerce','user','product_info', 'products_id=' . $_GET['BUYproducts_id']));
          } else {
            $_SESSION['cart']->add_cart($_GET['BUYproducts_id'], $_SESSION['cart']->get_quantity($_GET['BUYproducts_id'])+1);
          }
        }
        xarRedirectResponse(xarModURL('commerce','user',($goto, xtc_get_all_get_params(array('action'))));
        break;
      case 'notify':
        if (isset($_SESSION['customer_id'])) {
          if (isset($_GET['products_id'])) {
            $notify = $_GET['products_id'];
          } elseif (isset($_GET['notify'])) {
            $notify = $_GET['notify'];
          } elseif (isset($_POST['notify'])) {
            $notify = $_POST['notify'];
          } else {
            xarRedirectResponse(xarModURL('commerce','user',(basename($PHP_SELF), xtc_get_all_get_params(array('action', 'notify'))));
          }
          if (!is_array($notify)) $notify = array($notify);
          for ($i = 0, $n = sizeof($notify); $i < $n; $i++) {
            $check_query = new xenQuery("select count(*) as count from " . TABLE_PRODUCTS_NOTIFICATIONS . " where products_id = '" . $notify[$i] . "' and customers_id = '" . $_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
            $check = $q->output();
            if ($check['count'] < 1) {
              new xenQuery("insert into " . TABLE_PRODUCTS_NOTIFICATIONS . " (products_id, customers_id, date_added) values ('" . $notify[$i] . "', '" . $_SESSION['customer_id'] . "', now())");
            }
          }
          xarRedirectResponse(xarModURL('commerce','user',(basename($PHP_SELF), xtc_get_all_get_params(array('action', 'notify'))));
        } else {
         //
          xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
        }
        break;
      case 'notify_remove':
        if (isset($_SESSION['customer_id']) && isset($_GET['products_id'])) {
          $check_query = new xenQuery("select count(*) as count from " . TABLE_PRODUCTS_NOTIFICATIONS . " where products_id = '" . $_GET['products_id'] . "' and customers_id = '" . $_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
          $check = $q->output();
          if ($check['count'] > 0) {
            new xenQuery("delete from " . TABLE_PRODUCTS_NOTIFICATIONS . " where products_id = '" . $_GET['products_id'] . "' and customers_id = '" . $_SESSION['customer_id'] . "'");
          }
          xarRedirectResponse(xarModURL('commerce','user',(basename($PHP_SELF), xtc_get_all_get_params(array('action'))));
        } else {

          xarRedirectResponse(xarModURL('commerce','user','login', '', 'SSL'));
        }
        break;
      case 'cust_order':
        if (isset($_SESSION['customer_id']) && isset($_GET['pid'])) {
          if (xtc_has_product_attributes($_GET['pid'])) {
            xarRedirectResponse(xarModURL('commerce','user','product_info', 'products_id=' . $_GET['pid']));
          } else {
            $_SESSION['cart']->add_cart($_GET['pid'], $_SESSION['cart']->get_quantity($_GET['pid'])+1);
          }
        }
        xarRedirectResponse(xarModURL('commerce','user',($goto, xtc_get_all_get_params($parameters)));
        break;
    }
  }

  // write customers status in session
  require(DIR_WS_INCLUDES . 'write_customers_status.php');

  // include the who's online functions
  xtc_update_whos_online();

  // split-page-results
  require(DIR_WS_CLASSES . 'split_page_results.php');

  // infobox
  require(DIR_WS_CLASSES . 'boxes.php');

  // auto activate and expire banners
  xarModAPIFunc('commerce','user','activate_banners');
  xtc_expire_banners();

  // auto expire special products
  xtc_expire_specials();

  // calculate category path
  if (isset($_GET['cPath'])) {
    $cPath = $_GET['cPath'];
  } elseif (isset($_GET['products_id']) && !isset($_GET['manufacturers_id'])) {
    $cPath = xtc_get_product_path($_GET['products_id']);
  } else {
    $cPath = '';
  }

  if (xarModAPIFunc('commerce','user','not_null',array('arg' => $cPath))) {
    $cPath_array = xtc_parse_category_path($cPath);
    $cPath = implode('_', $cPath_array);
    $current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
  } else {
    $current_category_id = 0;
  }

  // include the breadcrumb class and start the breadcrumb trail
  require(DIR_WS_CLASSES . 'breadcrumb.php');
  $breadcrumb = new breadcrumb;

  $breadcrumb->add(HEADER_TITLE_TOP, HTTP_SERVER);
  $breadcrumb->add(HEADER_TITLE_CATALOG, xarModURL('commerce','user','default'));

  // add category names or the manufacturer name to the breadcrumb trail
  if (isset($cPath_array)) {
    for ($i=0, $n=sizeof($cPath_array); $i<$n; $i++) {
      $categories_query = new xenQuery("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $cPath_array[$i] . "' and language_id='" . $_SESSION['languages_id'] . "'");
      if ($categories_query->getrows() > 0) {
        $categories = $categories_query$q->output();
        $breadcrumb->add($categories['categories_name'], xarModURL('commerce','user','default', 'cPath=' . implode('_', array_slice($cPath_array, 0, ($i+1)))));
      } else {
        break;
      }
    }
  } elseif (isset($_GET['manufacturers_id'])) {
    $manufacturers_query = new xenQuery("select manufacturers_name from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . $_GET['manufacturers_id'] . "'");
    $manufacturers = $q->output()($manufacturers_query);
    $breadcrumb->add($manufacturers['manufacturers_name'], xarModURL('commerce','user','default', 'manufacturers_id=' . $_GET['manufacturers_id']));
  }

  // add the products model to the breadcrumb trail
  if (isset($_GET['products_id'])) {
    $model_query = new xenQuery("select products_model from " . TABLE_PRODUCTS . " where products_id = '" . $_GET['products_id'] . "'");
    $model = $q->output()($model_query);
    $breadcrumb->add($model['products_model'], xarModURL('commerce','user','product_info', 'cPath=' . $cPath . '&products_id=' . $_GET['products_id']));
  }

  // initialize the message stack for output messages
  require(DIR_WS_CLASSES . 'message_stack.php');
  $messageStack = new messageStack;

  // set which precautions should be checked
  define('WARN_INSTALL_EXISTENCE', 'true');
  define('WARN_CONFIG_WRITEABLE', 'true');
  define('WARN_SESSION_DIRECTORY_NOT_WRITEABLE', 'false');
  define('WARN_SESSION_AUTO_START', 'true');
  define('WARN_DOWNLOAD_DIRECTORY_NOT_READABLE', 'true');

  // for tracking of customers
  $_SESSION['user_info'] = array();
  if (!$_SESSION['user_info']['user_ip']) {
    $_SESSION['user_info']['user_ip'] = $_SERVER['REMOTE_ADDR'];
//    $user_info['user_ip_date'] =  value will be in fact added when login ;
    $_SESSION['user_info']['user_host'] = gethostbyaddr( $_SERVER['REMOTE_ADDR'] );;
    $_SESSION['user_info']['advertiser'] = $_GET['ad'];
    $_SESSION['user_info']['referer_url'] = $_SERVER['HTTP_REFERER'];
  }

  // Include Template Engine
  require(DIR_WS_CLASSES . 'smarty/Smarty.class.php');

  if (isset($_SESSION['customer_id'])) {
  $account_type_query=new xenQuery("SELECT account_type FROM
                                    ".TABLE_CUSTOMERS."
                                    WHERE customers_id = '".$_SESSION['customer_id']."'");
  $account_type=$q->output()($account_type_query);
  $_SESSION['account_type']=$account_type['account_type'];
   } else {
   $_SESSION['account_type']='0';
   }

  // modification for nre graduated system
  unset($_SESSION['actual_content']);
  xtc_count_cart();

?>