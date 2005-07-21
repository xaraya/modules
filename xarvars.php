<?php
/*/
 * shopping/xarvars.php 1.00 July 25th 2003 jared_rich@excite.com
 *
 * Shopping Module Variable Initialization File
 *
 * copyright (C) 2003 by Jared Rich
 * license GPL <http://www.gnu.org/licenses/gpl.html>
 * author: Jared Rich
/*/

// Policy Vars
xarModSetVar('shopping', 'spolicy', "The administrator of this website has not defined a shipping policy");
xarModSetVar('shopping', 'rpolicy', "The administrator of this website has not defined a return policy");

// General Config Vars
xarModSetVar('shopping', 'itemsperpage', 50);
xarModSetVar('shopping', 'useritemsperpage', 10);
xarModSetVar('shopping', 'lowstock', 5);
xarModSetVar('shopping', 'recosperpage', 50);
xarModSetVar('shopping', 'userrecosperpage', 9);
xarModSetVar('shopping', 'acceptpaypal', false);
xarModSetVar('shopping', 'acceptcredit', false);
xarModSetVar('shopping', 'acceptbill', true);
xarModSetVar('shopping', 'userecommendations', true);
xarModSetVar('shopping', 'displaylowstock', true);

// Mail Send/Don't Send Vars
xarModSetVar('shopping', 'sendsubmit', true);
xarModSetVar('shopping', 'sendreview', false);
xarModSetVar('shopping', 'sendpaid', true);
xarModSetVar('shopping', 'sendship', true);
xarModSetVar('shopping', 'sendmod', true);
xarModSetVar('shopping', 'senddel', true);
xarModSetVar('shopping', 'adminsendsubmit', true);
xarModSetVar('shopping', 'adminsendmod', true);
xarModSetVar('shopping', 'adminsenddel', true);

// Mail Messages Vars
xarModSetVar('shopping', 'mailsubmittitle', "Your order from %%sitename%%");
xarModSetVar('shopping', 'mailsubmit',
"Thank you for your recent order with %%sitename%%. Your order number is %%ordernumber%%.  Below are the details of this order.

%%orderdetails%%

You can modify or cancel this order until it has been paid for.
Please visit %%modurl%% or %%deleteurl%% respectively to do so.

Thank You,
%%admin%%");
xarModSetVar('shopping', 'mailreviewtitle', "Your order from %%sitename%% has been reviewed");
xarModSetVar('shopping', 'mailreview',
"Your order from %%sitename%% (%%ordernumber%%) has been reviewed. Once we receive payment, your order will be shipped.

You can modify or cancel this order until it has been paid for.
Please visit %%modurl%% or %%deleteurl%% respectively to do so.

Thank You,
%%admin%%");
xarModSetVar('shopping', 'mailpaidtitle', "Your order from %%sitename%% has been paid for");
xarModSetVar('shopping', 'mailpaid',
"Your order from %%sitename%% (%%ordernumber%%) has been paid for.  You can no longer modify or cancel this order. It will be shipping soon.

Thank You,
%%admin%%");
xarModSetVar('shopping', 'mailshiptitle', "Your order from %%sitename%% has shipped");
xarModSetVar('shopping', 'mailship',
"Your order from %%sitename%% (%%ordernumber%%) has been shipped.  This completes the transaction.

Thank You,
%%admin%%");
xarModSetVar('shopping', 'mailmodtitle', "Your order from %%sitename%% has been modified");
xarModSetVar('shopping', 'mailmod',
"Your order from %%sitename%% (%%ordernumber%%) has been modified.  Below are the new details of this order.

%%orderdetails%%

You can modify or cancel this order until it has been paid for.
Please visit %%modurl%% or %%deleteurl%% respectively to do so.

Thank You,
%%admin%%");
xarModSetVar('shopping', 'maildeltitle', "Your order from %%sitename%% has been canceled");
xarModSetVar('shopping', 'maildel',
"Your order from %%sitename%% (%%ordernumber%%) has been cancelled.

Thank You,
%%admin%%");
xarModSetVar('shopping', 'adminmailsubmittitle', "Order Submitted by %%username%%");
xarModSetVar('shopping', 'adminmailsubmit',
"Order number %%ordernumber%% was submitted by %%username%% on %%orderdate%%
Details:

%%orderdetails%%

Please review this order ASAP");
xarModSetVar('shopping', 'adminmailmodtitle', "Order Modified by %%username%%");
xarModSetVar('shopping', 'adminmailmod',
"Order number %%ordernumber%% was modified by %%username%% on %%orderdate%%
New details:

%%orderdetails%%

Please review this order ASAP");
xarModSetVar('shopping', 'adminmaildeltitle', "Order Canceled by %%username%%");
xarModSetVar('shopping', 'adminmaildel',
"Order number %%ordernumber%% was canceled by %%username%% on %%orderdate%%");
?>
