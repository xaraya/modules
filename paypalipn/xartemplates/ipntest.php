<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

  <html>
      <head>
          <title>PayPal IPN Script Testing Environment</title>
          <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
          <meta name="keywords" content="Free, PayPal, IPN, IPN Test, IPN Test Environment, IPN Testing, Instant Payment Notification">
          <meta name="description" content="Free PayPal IPN Testing Environment">
          <link rel="stylesheet" type="text/css" href="site.css">
      </head>

 <body bgcolor="#FFFFFF">
 <div align="center">
         <script language="JavaScript" type="text/javascript">

			var ipncount=0;

	function postIPN() {
  	if ( (document.ipntest.ipnscript.value=="http://www.yourdomain.com/ipnscript.php.pl.cgi.asp")
        ||(document.ipntest.ipnscript.value=="") ) {
                     alert("You MUST enter the URL of your IPN script !!!");
                     return false;
			} else {
  	if (ipncount == 0) {
  	  document.forms.ipntest.submit();
		ipncount++;
		return true;
			} else {
                     alert("Patience, we are waiting for your script to reply !!!");
                     return false;
			}}}

			var keycount=0;

	function resendIPN() {

  	if (document.reipntest.key.value=="") {
                     alert("You MUST enter your IPN Resend Key !!!");
                     return false;
			} else {
  	if (keycount == 0) {
  	  document.forms.reipntest.submit();
		keycount++;
		return true;
			} else {
                     alert("Patience, we are waiting for your script to reply !!!");
                     return false;
			}}}

	function dipSwitch(dip) {
		document.ipntest.dipswitch.value = dip;
		document.reipntest.dipswitch.value = dip;
			}

	function ipnVersion(v) {
		if (v == 1.6) {
		document.ipntest.payment_status[0].disabled = false;
		document.ipntest.parent_txn_id.disabled = false;
		document.ipntest.reason_code[3].disabled = false;
		document.ipntest.notify_version.value = "1.6";
                     alert("IPN Version 1.6 (development)!");
			}
		else {
		document.ipntest.payment_status[0].disabled = true;
		document.ipntest.parent_txn_id.disabled = true;
		document.ipntest.reason_code[3].disabled = true;
		document.ipntest.notify_version.value = "1.5";
                     alert("IPN Version 1.5 (current)!");

}}

         </script>

  <table width="90%" cellpadding="0" cellspacing="0" border="0" align="center" class="tableborder2">

      <tr>
          <td bgcolor="#FFFFFF" align="left" class="tableborder3"> &nbsp; <a href="/testing/ipntest.php?mode=_currency-usd" onMouseOver="window.status='USD Mode'; return true;" onMouseOut="window.status=''; return true;"><img src="usd.gif" border="0" alt="USD Mode"></a> <a href="/testing/ipntest.php?mode=_currency-cad" onMouseOver="window.status='CAD Mode'; return true;" onMouseOut="window.status=''; return true;"><img src="cad.gif" border="0" alt="CAD Mode"></a> <a href="/testing/ipntest.php?mode=_currency-gbp" onMouseOver="window.status='GBP Mode'; return true;" onMouseOut="window.status=''; return true;"><img src="gbp.gif" border="0" alt="GBP Mode"></a> <a href="/testing/ipntest.php?mode=_currency-eur" onMouseOver="window.status='EUR Mode'; return true;" onMouseOut="window.status=''; return true;"><img src="eur.gif" border="0" alt="EUR Mode"></a> <a href="/testing/ipntest.php?mode=_currency-jpy" onMouseOver="window.status='JPY Mode'; return true;" onMouseOut="window.status=''; return true;"><img src="jpy.gif" border="0" alt="JPY Mode"></a> <a href="/testing/ipntest.php?mode=_fetch-stats" onMouseOver="window.status='Live Statistics'; return true;" onMouseOut="window.status=''; return true;"><img src="stats.gif" border="0" alt="Live Statistics"></a> <a href="/testing/ipntest.php?mode=_fetch-help" onMouseOver="window.status='Help'; return true;" onMouseOut="window.status=''; return true;"><img src="help.gif" border="0" alt="Help"></a></td>
          <td bgcolor="#FFFFFF" align="right"class="tableborder3"><font class="site_link"><a href="http://www.paypalipn.com" target="_blank" class="site_link" onMouseOver="window.status='www.paypalipn.com'; return true;" onMouseOut="window.status=''; return true;">www.paypalipn.com</a> | <a href="http://www.paypaldev.org" target="_blank" class="site_link" onMouseOver="window.status='www.paypaldev.org'; return true;" onMouseOut="window.status=''; return true;">www.paypaldev.org</a></font> &nbsp; </td>
      </tr>

      <tr>
          <td colspan="2" align="center" height="20" width="90%" bgcolor="#FFFFFF" class="tableborder3"><font class="title_blk"><br><a href="http://www.profitpal.co.uk" target="_blank" onMouseOver="window.status='ProfitPal&trade;'; return true;" onMouseOut="window.status=''; return true;"><img src="profit_logo.gif" width="117" height="35" alt="ProfitPal&trade; - coming soon" border="0"></a>&nbsp; &nbsp; &nbsp; <u>Instant Payment Notification - Script Testing Environment</u><br><br></font></td>
      </tr>
  </table>

  <table width="90%" cellpadding="0" cellspacing="0" border="0" align="center" class="tableborder">
      <tr>
          <td align="center" height="20" width="90%" bgcolor="#FFFFFF" class="tableborder4"><br><input type="button" name="Non SSL" value="Non SSL" class="form_buttons2" onClick="window.location.href='http://www.eliteweaver.co.uk/testing/ipntest.php'"> <input type="button" name="SSL Secure" value="SSL Secure" class="form_buttons2" onClick="window.location.href='https://www.eliteweaver.co.uk/testing/ipntest.php'"> <input type="button" name="Free PHP Handler 2.0" value="Free PHP Handler 2.0" class="form_buttons2" onClick="window.location.href='/downloads/ew_php-idh-2_0.zip'"> <input type="button" name="Free ASP Handler 1.4" value="Free ASP Handler 1.4" class="form_buttons2" onClick="window.location.href='/downloads/ew_asp-idh-1_4.zip'"><br><br></td>
      </tr>
  </table>

 <div align="center">
  <table width="90%"cellspacing="0" cellpadding="3" border="0" bgcolor="#F0F0F0" class="tableborder2">

      <tr>
          <td colspan="2" class="tabletitle" bgcolor="#336699">&nbsp; IPN Handler & Result</td>
      </tr>

<form name="ipntest" action="" method="post">

      <tr>
          <td class="form_std" align="right">IPN Handler:</td>
          <td><input type="text" maxlength="255" size="60" name="ipnscript" value="http://www.yourdomain.com/ipnscript.php.pl.cgi.asp" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right"></td>
          <td><font class="form_sml"><b>NOTE:</b> Please ensure your IPN Handler points to this script whilst testing!<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Extra: <b>www.eliteweaver.co.uk/cgi-bin/webscr</b> can also be used.....</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">IPN Result:</td>
          <td bgcolor="#DEDEDE"><input type="radio" name="ipnstatus" value="VERIFIED" class="form_objects" checked="checked"><font class="form_grn"> VERIFIED</font> <input type="radio" name="ipnstatus" value="INVALID" class="form_objects"><font class="form_red"> INVALID</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">Version:</td>
          <td bgcolor="#DEDEDE"><input type="radio" name="ipnversion" value="" class="form_objects" checked="checked" onClick="ipnVersion(1.5)"><font class="form_blu"> 1.5</font> <input type="radio" name="ipnversion" value="" class="form_objects" onClick="ipnVersion(1.6)"><font class="form_blu"> 1.6</font></td>
      </tr>

      <tr>
          <td colspan="2" class="tabletitle" bgcolor="#336699">&nbsp; IPN Standard Variables</td>
      </tr>

      <tr>
          <td class="form_std" align="right">receiver_email:</td>
          <td><input type="text" maxlength="150" size="30" name="receiver_email" value="paypal@yourdomain.com" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">receiver_id:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="20" name="receiver_id" value="9IBXD38T86GTY" class="form_objects"><font class="form_std"> <- Dynamic</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">business:</td>
          <td><input type="text" maxlength="150" size="30" name="business" value="paypal@yourdomain.com" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">item_name:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="item_name" value="Item Name" class="form_objects"></td>
      </tr>


      <tr>
          <td class="form_std" align="right">item_number:</td>
          <td><input type="text" maxlength="150" size="15" name="item_number" value="293" class="form_objects"><font class="form_std"> <- Dynamic</font></td>
      </tr>


      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">quantity:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="15" name="quantity" value="1" class="form_objects"></td>
      </tr>


      <tr>
          <td class="form_std" align="right">invoice:</td>
          <td><input type="text" maxlength="150" size="15" name="invoice" value="259965" class="form_objects"><font class="form_std"> <- Dynamic</font></td>
      </tr>


      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">custom:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="custom" value="Custom" class="form_objects"></td>
      </tr>


      <tr>
          <td class="form_std" align="right">option_name1:</td>
          <td><input type="text" maxlength="150" size="30" name="option_name1" value="Option" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">option_selection1:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="option_selection1" value="Selection" class="form_objects"></td>
      </tr>


      <tr>
          <td class="form_std" align="right">option_name2:</td>
          <td><input type="text" maxlength="150" size="30" name="option_name2" value="Option" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">option_selection2:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="option_selection2" value="Selection" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">num_cart_items:</td>
          <td><input type="text" maxlength="25" size="15" name="num_cart_items" value="0" class="form_objects"><font class="form_std"> <- Cart only</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">payment_status:</td>
          <td bgcolor="#DEDEDE"><input type="radio" name="payment_status" disabled value="Canceled_Reversal" class="form_objects" onClick="javascript:alert('Don\'t forget to specify a parent_txn_id !!!'); document.ipntest.reason_code[3].checked = true; parent.location='#parent_txn_id';"><font class="form_std"> Canceled_Reversal</font> <input type="radio" name="payment_status" value="Completed" class="form_objects" checked="checked" onClick="javascript: document.ipntest.reason_code[3].checked = false;"><font class="form_std"> Completed</font> <input type="radio" name="payment_status" value="Pending" class="form_objects" onClick="javascript: document.ipntest.reason_code[3].checked = false;"><font class="form_std"> Pending</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right"></td>
          <td bgcolor="#DEDEDE"><input type="radio" name="payment_status" value="Failed" class="form_objects" onClick="javascript: document.ipntest.reason_code[3].checked = false;"><font class="form_std"> Failed</font> <input type="radio" name="payment_status" value="Denied" class="form_objects" onClick="javascript: document.ipntest.reason_code[3].checked = false;"><font class="form_std"> Denied</font> <input type="radio" name="payment_status" value="Refunded" class="form_objects"><font class="form_std"> Refunded</font> <input type="radio" name="payment_status" value="Reversed" class="form_objects" onClick="javascript: document.ipntest.reason_code[3].checked = false;"><font class="form_std"> Reversed</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">pending_reason:</td>
          <td><input type="radio" name="pending_reason" value="echeck" class="form_objects"><font class="form_std"> echeck</font> <input type="radio" name="pending_reason" value="multi_currency" class="form_objects"><font class="form_std"> multi_currency</font> <input type="radio" name="pending_reason" value="intl" class="form_objects"><font class="form_std"> intl</font> <input type="radio" name="pending_reason" value="verify" class="form_objects"><font class="form_std"> verify</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right"></td>
          <td><input type="radio" name="pending_reason" value="address" class="form_objects"><font class="form_std"> address</font> <input type="radio" name="pending_reason" value="upgrade" class="form_objects"><font class="form_std"> upgrade</font> <input type="radio" name="pending_reason" value="unilateral" class="form_objects"><font class="form_std"> unilateral</font> <input type="radio" name="pending_reason" value="other" class="form_objects"><font class="form_std"> other</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">payment_date:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="payment_date" value="06:59:17 Mar 07, 2004 PDT" class="form_objects"><font class="form_std"> <- Real-Time</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">settle_amount:</td>
          <td><input type="text" maxlength="25" size="15" name="settle_amount" value="" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">settle_currency:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="15" name="settle_currency" value="" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">exchange_rate:</td>
          <td><input type="text" maxlength="25" size="15" name="exchange_rate" value="" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">payment_gross:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="15" name="payment_gross" value="24.99" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">payment_fee:</td>
          <td><input type="text" maxlength="25" size="15" name="payment_fee" value="1.02" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">mc_gross:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="15" name="mc_gross" value="24.99" class="form_objects"> </td>
      </tr>

      <tr>
          <td class="form_std" align="right">mc_fee:</td>
          <td><input type="text" maxlength="25" size="15" name="mc_fee" value="1.02" class="form_objects"> </td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">mc_currency:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="15" name="mc_currency" value="USD" class="form_objects"><font class="form_std"> <- Default</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">tax:</td>
          <td><input type="text" maxlength="25" size="15" name="tax" value="0.00" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">txn_id:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="25" name="txn_id" value="452AGKTJWBM2TL6MK" class="form_objects"><font class="form_std"> <- Dynamic</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">parent_txn_id:</td>
          <td><input type="text" maxlength="25" size="25" name="parent_txn_id" disabled value="" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">txn_type:</td>
          <td bgcolor="#DEDEDE"><input type="radio" name="txn_type" checked value="web_accept" class="form_objects"><font class="form_std"> web_accept</font> <input type="radio" name="txn_type"  value="cart" class="form_objects"><font class="form_std"> cart</font> <input type="radio" name="txn_type"  value="send_money" class="form_objects"><font class="form_std"> send_money</font> <input type="radio" name="txn_type" value="reversal" class="form_objects"><font class="form_std"> reversal</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">reason_code:</td>
          <td><input type="radio" name="reason_code" value="buyer_complaint" class="form_objects"><font class="form_std"> buyer_complaint</font> <input type="radio" name="reason_code" value="chargeback" class="form_objects"><font class="form_std"> chargeback</font> <input type="radio" name="reason_code" value="guarantee" class="form_objects"><font class="form_std"> guarantee</font> <input type="radio" name="reason_code" disabled value="refund" class="form_objects"><font class="form_std"> refund</font> <input type="radio" name="reason_code" value="other" class="form_objects"><font class="form_std"> other</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">for_auction:</td>
          <td bgcolor="#DEDEDE"><input type="radio" name="for_auction" value="true" class="form_objects"><font class="form_std"> true</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">auction_buyer_id:</td>
          <td><input type="text" maxlength="25" size="20" name="auction_buyer_id" value="" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">auction_close_date:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="auction_close_date" value="" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">auction_multi_item:</td>
          <td><input type="text" maxlength="25" size="15" name="auction_multi_item" value="" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">ebay_address_id:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="20" name="ebay_address_id" value="" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">memo:</td>
          <td><textarea name="memo" cols="50" rows="5" class="form_objects">PayPal Special Instructions/Note Field.</textarea></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">first_name:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="first_name" value="Thomas" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">last_name:</td>
          <td><input type="text" maxlength="150" size="30" name="last_name" value="Tester" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">address_street:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="address_street" value="21 Test Street" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">address_city:</td>
          <td><input type="text" maxlength="150" size="30" name="address_city" value="Testopia" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">address_state:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="address_state" value="Testville" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">address_zip:</td>
          <td><input type="text" maxlength="25" size="15" name="address_zip" value="123456" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">address_country:</td>
          <td bgcolor="#DEDEDE"><select name="address_country" value="" class="form_objects">
<option value=Anguilla>Anguilla
<option value=Argentina>Argentina
<option value=Australia>Australia
<option value=Austria>Austria
<option value=Belgium>Belgium
<option value=Brazil>Brazil
<option value=Canada>Canada
<option value=Chile>Chile
<option value=China>China
<option value=Costa Rica>Costa Rica
<option value=Denmark>Denmark
<option value=Dominican Republic>Dominican Republic
<option value=Finland>Finland
<option value=France>France
<option value=Germany>Germany
<option value=Greece>Greece
<option value=Hong Kong>Hong Kong
<option value=Iceland>Iceland
<option value=India>India
<option value=Ireland>Ireland
<option value=Israel>Israel
<option value=Italy>Italy
<option value=Jamaica>Jamaica
<option value=Japan>Japan
<option value=Luxembourg>Luxembourg
<option value=Mexico>Mexico
<option value=Netherlands>Netherlands
<option value=New Zealand>New Zealand	
<option value=Norway>Norway
<option value=Portugal>Portugal
<option value=Singapore>Singapore
<option value=South Africa>South Africa
<option value=South Korea>South Korea
<option value=Spain>Spain
<option value=Sweden>Sweden
<option value=Switzerland>Switzerland
<option value=Taiwan>Taiwan
<option value=United Kingdom>United Kingdom
<option selected value=United States>United States</td>
  </select>
      </tr>

      <tr>
          <td class="form_std" align="right">address_status:</td>
          <td><input type="radio" name="address_status" value="confirmed" class="form_objects" checked="checked"><font class="form_std"> confirmed</font> <input type="radio" name="address_status" value="unconfirmed" class="form_objects"><font class="form_std"> unconfirmed</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">address_owner:</td>
          <td bgcolor="#DEDEDE"><input type="radio" name="address_owner" value="1" class="form_objects" checked="checked"><font class="form_std"> 1</font> <input type="radio" name="address_owner" value="0" class="form_objects"><font class="form_std"> null</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">payer_email:</td>
          <td><input type="text" maxlength="150" size="30" name="payer_email" value="paypal@theirdomain.com" class="form_objects"><font class="form_std"> </font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">paypal_address_id:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="20" name="paypal_address_id" value="3UOSPFVESZ6NR" class="form_objects"><font class="form_std"> <- Dynamic</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">payer_id:</td>
          <td><input type="text" maxlength="25" size="20" name="payer_id" value="V59LDJ9546Y1U" class="form_objects"><font class="form_std"> <- Dynamic</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">payer_business_name:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="75" size="30" name="payer_business_name" value="Test Company Ltd." class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">payer_status:</td>
          <td><input type="radio" name="payer_status" value="verified" class="form_objects" checked="checked"><font class="form_std"> verified</font> <input type="radio" name="payer_status" value="unverified" class="form_objects"><font class="form_std"> unverified</font> <input type="radio" name="payer_status" value="intl_verified" class="form_objects"><font class="form_std"> intl_verified</font> <input type="radio" name="payer_status" value="intl_unverified" class="form_objects"><font class="form_std"> intl_unverified</font> </td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">payment_type:</td>
          <td bgcolor="#DEDEDE"><input type="radio" name="payment_type" value="echeck" class="form_objects"><font class="form_std"> echeck</font> <input type="radio" name="payment_type" value="instant" class="form_objects" checked="checked"><font class="form_std"> instant</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">notify_version:</td>
          <td><input type="text" maxlength="25" size="15" name="notify_version" value="1.5" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">verify_sign:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="50" name="verify_sign" value="Ce91D9wAxOIPN6FXoFuzhyTjUG4HuVBACF9fDeRVzdxW-Pr01W73lrSq" class="form_objects"><font class="form_std"> <- Dynamic</font></td>
      </tr>

      <tr>
          <td colspan="2" class="tabletitle" bgcolor="#336699">&nbsp; IPN Subscription Variables</td>
      </tr>

      <tr>
          <td class="form_std" align="right">txn_type:</td>
          <td><input type="radio" name="txn_type" value="subscr_signup" class="form_objects"><font class="form_std"> subscr_signup</font> <input type="radio" name="txn_type" value="subscr_cancel" class="form_objects"><font class="form_std"> subscr_cancel</font> <input type="radio" name="txn_type" value="subscr_failed" class="form_objects"><font class="form_std"> subscr_failed</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right"></td>
          <td><input type="radio" name="txn_type"  value="subscr_payment" class="form_objects"><font class="form_std"> subscr_payment</font> <input type="radio" name="txn_type" value="subscr_eot" class="form_objects"><font class="form_std"> subscr_eot</font> <input type="radio" name="txn_type" value="subscr_modify" class="form_objects"><font class="form_std"> subscr_modify</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">subscr_date:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="subscr_date" value="06:59:17 Mar 07, 2004 PDT" class="form_objects"><font class="form_std"> <- Real-Time</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">subscr_effective:</td>
          <td><input type="text" maxlength="150" size="30" name="subscr_effective" value="06:59:17 Mar 07, 2004 PDT" class="form_objects"><font class="form_std"> <- Real-Time</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">period1:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="15" name="period1" value="7 d" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">period2:</td>
          <td><input type="text" maxlength="25" size="15" name="period2" value="3 w" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">period3:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="15" name="period3" value="11 m" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">amount1:</td>
          <td><input type="text" maxlength="25" size="15" name="amount1" value="1.99" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">amount2:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="15" name="amount2" value="6.49" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">amount3:</td>
          <td><input type="text" maxlength="25" size="15" name="amount3" value="19.99" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">mc_amount1:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="15" name="mc_amount1" value="1.99" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">mc_amount2:</td>
          <td><input type="text" maxlength="25" size="15" name="mc_amount2" value="6.49" class="form_objects"></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">mc_amount3:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="15" name="mc_amount3" value="19.99" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">recurring:</td>
          <td><input type="radio" name="recurring" value="1" class="form_objects" checked="checked"><font class="form_std"> 1 = yes</font> <input type="radio" name="recurring" value="0" class="form_objects"><font class="form_std"> 0 = no</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">reattempt:</td>
          <td bgcolor="#DEDEDE"><input type="radio" name="reattempt" value="1" class="form_objects" checked="checked"><font class="form_std"> 1 = yes</font> <input type="radio" name="reattempt" value="0" class="form_objects"><font class="form_std"> 0 = no</font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">retry_at:</td>
          <td><input type="text" maxlength="150" size="30" name="retry_at" value="06:59:17 Mar 14, 2004 PDT" class="form_objects"><font class="form_std"> <- Real-Time + 1 Week</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">recur_times:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="25" size="15" name="recur_times" value="0" class="form_objects"></td>
      </tr>

      <tr>
          <td class="form_std" align="right">username:</td>
          <td><input type="text" maxlength="150" size="30" name="username" value="qTlQGWNn" class="form_objects"><font class="form_std"> <- Dynamic</font></td>
      </tr>

      <tr>
          <td bgcolor="#DEDEDE" class="form_std" align="right">password:</td>
          <td bgcolor="#DEDEDE"><input type="text" maxlength="150" size="30" name="password" value="mbyPG5ftnr.M2" class="form_objects"><font class="form_std"> <- Dynamic hash <font class="form_sml">(Unencrypted = mbfVWMxD)</font></font></td>
      </tr>

      <tr>
          <td class="form_std" align="right">subscr_id:</td>
          <td><input type="text" maxlength="150" size="30" name="subscr_id" value="S-60IAZ4CWAYEQI0HF6" class="form_objects"><font class="form_std"> <- Dynamic</font></td>
      </tr>
  </table>

  <table width="90%" cellspacing="0" cellpadding="3" bgcolor="#DEDEDE" border="0" class="tableborder2">
      <tr>
          <td colspan="2" class="tabletitle" bgcolor="#336699">&nbsp; Form Actions</td>
      </tr>

      <tr>
          <td height="35" class="form_std" align=center>Automatic: <input type="radio" name="dip" checked value="" class="form_objects" onClick="dipSwitch(1)"> Manual: <input type="radio" name="dip" value="" class="form_objects" onClick="dipSwitch(2)"></td>
      </tr>

      <tr>
          <td height="35" class="form_std" align=center><input type="hidden" name="action" value="simulateIPN"><input type="hidden" name="dipswitch" value="1"><input type="button" name="action" value="Submit IPN" class="form_buttons" onClick=postIPN();> <input type="reset" name="reset" value="Reset Form" class="form_buttons"></td>
      </tr>
</form>

<form name="reipntest" action="" method="post">
      <tr>
          <td bgcolor="#DEDEDE" height="35" class="form_std" align=center><font class="form_std">Resend Key: </font> <input type="text" maxlength="20" size="12" name="key" value="" class="form_objects"> <input type="hidden" name="dipswitch" value="1"><input type="hidden" name="action" value="resendIPN"><input type="button" name="action" value="Go!" class="form_buttons" onClick=resendIPN();></td>
      </tr>
</form>

      <tr>
          <td colspan="2" class="tabletitle" bgcolor="#336699" align="right">EW &nbsp;</td>
      </tr>
  </table>

  <table width="90%" cellpadding="0" cellspacing="0" border="0" align="center" class="tableborder">

      <tr>
          <td bgcolor="#FFFFFF" align="center"><a href="mailto:ipn@eliteweaver.co.uk?subject=IPN Script Testing Environment" class="footer_link" onMouseOver="window.status='PayPal IPN Script Testing Environment &copy; 2002, 2003 - EliteWeaver UK - All rights reserved.'; return true;" onMouseOut="window.status=''; return true;">PayPal IPN Script Testing Environment &copy; 2002, 2003 - EliteWeaver UK - All rights reserved.</a></td>
          <td bgcolor="#FFFFFF" align="right"><a href="http://www.paypal.com/cgi-bin/webscr?cmd=p/pdn/about-board-outside" target="_blank" onMouseOver="window.status='PDN Advisory Board Member'; return true;" onMouseOut="window.status=''; return true;"><img src="pdn_logo.gif" border="0" alt="PDN Advisory Board Member"></a></td>
      </tr>
  </table>
 </div>
 </body>
  </html>
