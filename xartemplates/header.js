/* -----------------------------------------------------------------------------------------
   $Id: general.js,v 1.1 2003/09/06 22:13:53 fanta2k Exp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.js,v 1.3 2003/02/10); www.oscommerce.com 
   (c) 2003	 nextcommerce (general.js,v 1.3 2003/08/13); www.nextcommerce.org 

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}
function checkBox(object) {
  document.account_newsletter.elements[object].checked = !document.account_newsletter.elements[object].checked;
}