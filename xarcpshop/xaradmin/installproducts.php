<?php
/**
 * File: $Id:
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
function xarcpshop_admin_installproducts()
{

    if (!xarSecurityCheck('EditxarCPShop')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
 
    xarDBLoadTableMaintenanceAPI();
    $cptypestable = $xartable['cptypes'];


    $query = "DELETE FROM $cptypestable";

    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;

$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (111, 'Organic Cotton T-Shirt', 'Our 100% USDA certified organic cotton t-shirt from American Apparel is made in the USA. This is a fitted tee and sizes run small compared to other t-shirts.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (112, 'Value T-Shirt', 'Our 100% cotton, Hanes Heavyweight is preshrunk, durable and guaranteed.  100% ComfortSoft<sup>TM</sup> cotton yarn, 5.5 oz.  Double-needle sleeve and bottom hems.  Taped shoulder-to-shoulder.  Densely knit.  Our printing is better than ever — full of detail and color — and exceptionally fade resistant wash after wash.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (113, 'Ringer T-shirt', 'Our women’s ringer tees from Hyp are made of 100% fine cotton jersey. Tees feature contrasting neck and cuff trim as well as contrasting stitching.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (114, 'Women\'s Tank Top', 'Our Hanes Silver for Her tank top is perfect for those hot summer days. Made of 100% combed ring spun cotton with 1x1 rib, this tank has a tapered body for a close and fashionable fit. Neck and armhole are carefully shaped to conceal a bra. 6.1 oz.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (115, 'Men\'s Sleeveless Tee', 'Our 100% cotton sleeveless tee from Anvil is perfect for those hot summer days. Tees feature a double needle hemmed armhole. Preshrunk. 6.1oz.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (117, 'Junior Fleece Shorts', 'Our fleece shorts from American Apparel are short and snug - the perfect accessory for a comfortable but sexy look. Functional drawstring at waist offers comfort and mobility.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (70, 'Kids T-Shirt', 'The most comfortable t-shirt ever! Our 100% cotton, Hanes Authentic Tagless T-Shirt is preshrunk, durable and guaranteed. \r\n<ul>\r\n<li>6.1 oz. fabric - luxuriously soft 100% open ended cotton jersey \r\n</li><li>Double-needle coverseamed neck \r\n</li><li>Taped shoulder-to-shoulder \r\n</li><li>Double needle sleeve and bottom hems\r\n</li></ul>\r\n<p>Our printing is better than ever — full of detail and color — and exceptionally fade resistant wash after wash.</p>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (58, 'Small Framed Print', 'Prints are perfect for the home or office.\r\nAll prints are custom manufactured using archival inks and acid-free\r\npaper. Framed prints are matted and framed in a stylish black frame\r\nwith plexiglass cover. Frames include complete backing. Frame size: 19\"\r\nx 11\"')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (59, 'Large Framed Print', 'Prints are perfect for the home or office.\r\nAll prints are custom manufactured using archival inks and acid-free\r\npaper. Framed prints are matted and framed in a stylish black frame\r\nwith plexiglass cover. Frames include complete backing. Frame size: 15\"\r\nx 19\"')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (82, 'Framed Panel Print', 'Prints are perfect for the home or office.\r\nAll prints are custom manufactured using archival inks and acid-free\r\npaper. Framed prints are matted and framed in a stylish black frame\r\nwith plexiglass cover. Frames include complete backing. Frame size: 13\"\r\nx 16\"')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (118, 'Button', 'Express yourself with your own customized button. Wear it with pride!\r\n<ul><li>2.25 inch diameter</li>\r\n<li>Metal shell</li>\r\n<li>Mylar/UV protecting cover</li>\r\n<li>Pinned metal back</li></ul>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (119, 'Magnet', 'Express yourself with our 2.25 inch button magnets.\r\n<ul><li>2.25 inch diameter</li>\r\n<li>Metal shell</li>\r\n<li>Flat magnetic back</li>\r\n<li>Mylar/UV protecting cover</li></ul>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (120, '2.25\" Button (10 pack)', 'Express yourself with your own customized button. Wear it with pride!\r\n<ul><li>2.25 inch diameter</li>\r\n<li>Metal shell</li>\r\n<li>Mylar/UV protecting cover</li>\r\n<li>Pinned metal back</li>\r\n<li>10 pack</li></ul>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (121, '2.25\" Button (100 pack)', 'Express yourself with your own customized button. Wear it with pride!\r\n<ul><li>2.25 inch diameter</li>\r\n<li>Metal shell</li>\r\n<li>Mylar/UV protecting cover</li>\r\n<li>Pinned metal back</li>\r\n<li>100 pack</li></ul>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (122, '2.25\" Magnet (10 pack)', 'Express yourself with our 2.25 inch button magnets.\r\n<ul><li>2.25 inch diameter</li>\r\n<li>Metal shell</li>\r\n<li>Flat magnetic back</li>\r\n<li>Mylar/UV protecting cover</li>\r\n<li>10 pack</li></ul>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (123, '2.25\" Magnet (100 pack)', 'Express yourself with our 2.25 inch button magnets.\r\n<ul><li>2.25 inch diameter</li>\r\n<li>Metal shell</li>\r\n<li>Flat magnetic back</li>\r\n<li>Mylar/UV protecting cover</li>\r\n<li>100 pack</li></ul>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (106, 'Fitted T-Shirt', 'Forget everything you know about t-shirts.\r\nBuilt to be lightweight and comfortable, with a tighter knit for a\r\nsofter feel, this tee is truly stylish. A tailored cut for a more\r\nmodern fit keeps you comfortably in fashion (Size up for looser fit). <ul><li>100% combed ring spun cotton</li>\r\n<li>Tailored fit</li>\r\n<li>Made in the USA</li>\r\n<li>Double needled sleeves and hems</li></ul>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (107, 'Yellow T-Shirt', 'Our 100% cotton, Hanes Authentic Tagless T-shirt is preshrunk, durable and\r\nguaranteed.\r\n<ul><li>6.1 oz 100% open-end cotton jersey</li>\r\n<li>Double-needle coverseamed neck</li>\r\n<li>Taped shoulder-to-shoulder</li>\r\n<li>Double needle sleeve and bottom hems</li>\r\n<li>Preshrunk to minimize shrinkage</li></ul>\r\n<p>Our printing is better than ever — full of detail and color — and exceptionally fade resistant wash after wash.</p>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (108, 'Green T-Shirt', 'The most comfortable t-shirt ever! Our 100% cotton, Hanes Beefy-T is preshrunk, durable and guaranteed.\r\n<ul><li>6.1 oz. fabric - luxuriously soft 100% cotton ring-spun yarn</li>\r\n<li>Double-needle coverseamed neck</li>\r\n<li>Taped shoulder-to-shoulder</li>\r\n<li>Double needle sleeve and bottom hems</li></ul>\r\n<p>Our printing is better than ever — full of detail and color — and exceptionally fade resistant wash after wash.</p>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (109, 'Women\'s Pink T-Shirt', 'The most comfortable women\'s t-shirt ever! Our 100% cotton, Hanes Her Way-T is preshrunk, durable and guaranteed. \r\n<ul><li>5.6 oz. fabric - luxuriously soft 100% open-end yarn </li>\r\n<li>Stylish ½\" ribbed collar  </li>\r\n<li>Double needle sleeve and bottom hems</li></ul>\r\nOur printing is better than ever - full of detail and color - and exceptionally fade resistant wash after wash.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (110, 'Dog T-Shirt', 'Our cool doggy tees will make your dog the envy of the neighborhood.  \r\n<ul><li>Made of 100% ring spun cotton.  5.8oz.  1x1 rib.</li>\r\n<li>Black ringer accent on sleeves and collar.</li>\r\n<li>Three sizes to choose from. Please review our <a href=\"javascript:launchHelp(\'/cp/help/help_sizechart.aspx#110\',\'height=400,width=430,scrollbars=yes\')\">size charts</a> to find the perfect size for your pooch.</li></ul>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (102, 'Jr. Raglan', 'Our Jr. Raglan is body contoured and baby\r\nsoft. Made of 100% superfine combed cotton baby rib, this raglan\r\nprovides the perfect look for any season.\r\n<ul><li>100% combed cotton baby rib</li>\r\n<li>Double-needle hemmed trim</li>\r\n<li>1/2 binding trim on neck</li></ul>\r\nOur printing is better than ever - full of detail and color - and exceptionally fade resistant wash after wash.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (103, 'Jr. Hoodie', 'Our Jr. Hoodie is cut to fit and made to\r\nturn heads. Made of baby soft 100% superfine combed cotton fleece, this\r\nhoodie will heat up any room.\r\n<ul><li>Prewashed 100% Combed Ring Spun Cotton California Fleece</li>\r\n<li>3/8\" binding on hood and pockets</li>\r\n<li>2 ½\" set cuff and 3\" bottom band</li></ul>\r\nOur printing is better than ever - full of detail and color - and exceptionally fade resistant wash after wash.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (93, 'Audio CD', '· Audio CD<br>· Number Of Discs: 1')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (94, 'Data CD', '· Data CD<br>· Number Of Discs: 1')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (38, 'Baseball Jersey', 'Our 100% Cotton Baseball Jerseys are popular with both men and women. Choose either Red, Blue or Black sleeves.\r\n<ul><li>100% Preshrunk Cotton\r\n</li><li>5.9 oz Jersey (Tubular)\r\n</li><li>3/4 Raglan Sleeves\r\n</li><li>Double Needle Sleeves </li></ul>\r\n<p>Our printing is better than ever — full of detail and color — and exceptionally fade resistant wash after wash.</p>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (2, 'White T-Shirt', 'The most comfortable t-shirt ever! Our 100% cotton, Hanes Authentic Tagless T-Shirt is preshrunk, durable and guaranteed. \r\n<ul>\r\n<li>6.1 oz. fabric - luxuriously soft 100% open ended cotton jersey \r\n</li><li>Double-needle coverseamed neck \r\n</li><li>Taped shoulder-to-shoulder \r\n</li><li>Double needle sleeve and bottom hems\r\n</li></ul>\r\n<p>Our printing is better than ever — full of detail and color — and exceptionally fade resistant wash after wash.</p>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (7, 'Ash Grey T-Shirt', 'The most comfortable t-shirt ever! Our 100% cotton, Hanes Authentic Tagless T-Shirt is preshrunk, durable and guaranteed. \r\n<ul>\r\n<li>6.1 oz. fabric - luxuriously soft 100% open ended cotton jersey \r\n</li><li>Double-needle coverseamed neck \r\n</li><li>Taped shoulder-to-shoulder \r\n</li><li>Double needle sleeve and bottom hems\r\n</li></ul>\r\n<p>Our printing is better than ever — full of detail and color — and exceptionally fade resistant wash after wash.</p>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (45, 'Golf Shirt', 'The perfect casual wear for the office, our\r\nAnvil golf shirts are made of 100% preshrunk, heavyweight cotton. Soft\r\nfashion knit collar and rib sleeve bands. Two woodtone buttons on a\r\nclean finished placket with 1/4\" reinforced box. Double needle bottom\r\nhem.\r\n<ul><li>5.6 oz preshrunk heavyweight cotton</li>\r\n<li>Knit collar</li>\r\n<li>Banded sleeves</li>\r\n<li>Two woodtone buttons</li></ul>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (5, 'Long Sleeve T-Shirt', 'The most comfortable t-shirt ever! Our 100% cotton, Hanes Beefy-T is preshrunk, durable and guaranteed.  \r\n<ul><li>6.1 oz. fabric — luxuriously soft 100% cotton ring-spun yarn </li>\r\n<li>Double-needle coverseamed neck </li>\r\n<li>Taped shoulder-to-shoulder </li>\r\n<li>Double needle sleeve and bottom hems </li></ul>\r\n<p>Our printing is better than ever — full of detail and color — and exceptionally fade resistant wash after wash.</p>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (6, 'Jr. Baby Doll T-Shirt', 'Our super soft 100% combed cotton, ribbed\r\nbaby doll T-shirt from American Apparel will keep you in style. Special\r\ndetails include 1/2\" binding on neck and sleeve with 1\" bottom hem. <p>Our printing is better than ever — full of detail and color — and incredibly fade resistant wash after wash.</p>')";
   if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (89, 'Women\'s T-Shirt', 'The most comfortable women\'s t-shirt ever! Our 100% cotton, Hanes Her Way-T is preshrunk, durable and guaranteed. \r\n<ul><li>5.6 oz. fabric - luxuriously soft 100% open-end yarn </li>\r\n<li>Stylish ½\" ribbed collar  </li>\r\n<li>Double needle sleeve and bottom hems</li></ul>\r\nOur printing is better than ever - full of detail and color - and exceptionally fade resistant wash after wash.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (96, 'Jr. Spaghetti Tank', 'Get ready for warm weather in this stylish\r\nspaghetti tank. Made of soft 100% superfine combed cotton baby rib,\r\nthis tank provides the perfect silhouette for summer.\r\n<ul><li>100% combed cotton baby rib </li>\r\n<li>Double-needle hemmed trim</li> \r\n<li>½ binding trim on straps and neck </li></ul>\r\nOur printing is better than ever - full of detail and color - and exceptionally fade resistant wash after wash.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (23, 'Hooded Sweatshirt', 'Stay warm with our ash grey Hanes Ultimate\r\nCotton Pullover Hood. Constructed with a heavyweight 90/10\r\ncotton/polyester blend. Thick (but not bulky), comfortable, durable and\r\nguaranteed.\r\n<ul><li>10 oz. patented PrintPro® fabric in a 90/10 cotton/polyester blend </li>\r\n<li>Double needle stitched armholes and waistband</li>\r\n<li>Double ply drawstring hood and hand warming pouch pocket </li>\r\n<li>Spandex ribbed cuffs and waistband </li>\r\n<li>Preshrunk to minimize shrinkage</li></ul>\r\nOur printing is better than ever - full of detail and color - and exceptionally fade resistant wash after wash.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (4, 'Sweatshirt', 'Stay warm with our ash grey Hanes\r\nHeavyweight 50/50 cotton/polyester sweatshirts. Thick (but not bulky),\r\ncomfortable, durable and guaranteed. <ul><li>9 oz. patented PrintPro® fabric in a 50/50 cotton/polyester blend </li>\r\n<li>Double-needle coverseamed colret, armholes and waistband </li>\r\n<li>Spandex trim in the neck, cuffs and waistband </li>\r\n<li>Preshrunk to minimize shrinkage</li></ul>\r\n<p>Our printing is better than ever — full of detail and color — and exceptionally fade resistant wash after wash.</p>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (15, 'Boxer Shorts', 'Enjoy the comfort of our roomy 100% cotton,\r\nopen fly boxers from Robinson Apparel. Great for underwear or\r\nnightwear. All printing is full of detail and color — and exceptionally\r\nfade resistant wash after wash. Elastic waist band.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (80, 'Classic Thong', 'These thongs are made for strutting! Perfect\r\nfor any type of low rise pants, these panties are made to fit low.\r\nCombed cotton baby rib with an exclusive high end woven trim makes\r\nthese panties look and feel like no other. <ul><li>100% combed cotton baby rib</li>\r\n<li>Double-needle hemmed trim</li>\r\n<li>Super soft high end woven elastic trim</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (87, 'Camisole', 'Perfect for letting the woman in your life\r\nknow she\'s appreciated, this stylish girly camisole is made of soft\r\n100% fine baby rib cotton.\r\n<ul><li>100% combed cotton baby rib </li>\r\n<li>Double-needle hemmed trim</li> \r\n<li>Super soft high end woven elastic trim</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (69, 'Infant/Toddler T-Shirt', 'Our 100% cotton kids t-shirts will look great on your little ones.\r\n<ul><li>100% cotton jersey knit t-shirt.</li>\r\n<li>Shoulder to shoulder taping.</li>\r\n<li>Ribbed crewneck.</li>\r\n<li>Double-needle hemmed sleeves and bottom.</li>\r\n<li>5.5 oz</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (72, 'Infant Creeper', '<ul><li>100% cotton jersey knit creeper with crewneck.</li>\r\n<li>Double-needle hemmed sleeves and bottom.</li>\r\n<li>Double-needle ribbed binding on legs.</li>\r\n<li>Three snap bottom.</li>\r\n<li>5.5 oz</li></ul>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (73, 'Bib', '100% cotton bib with sturdy velcro closure. One size fits all up to 36 months.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (1, 'Large Mug', 'Our 15oz ceramic mug will keep your favorite beverage hot. Large handle for easy grasping. Dishwasher and microwave safe. Printing is full of color and detail. Guaranteed.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (0, 'Mug', 'Our 11oz ceramic mug will keep your favorite beverage hot. Large handle for easy grasping. Dishwasher and microwave safe. Printing is full of color and detail. Guaranteed.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (47, 'Stainless Steel Travel Mug', 'Now you can take your coffee \"to go\" with this 15 oz stainless steel travel mug. Seal in the steam and keep your coffee fresh even longer with the sealable plastic sip top. Easy grip handle. Fits in most car cup holders. Dishwasher safe. 6\" tall.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (11, 'Stein', '22oz ceramic stein with gold trim.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (37, 'Tile Coaster', 'Our 4.25\" x 4.25\" ceramic tile coasters add color to any room. 1/6 inch thick. Felt pads protect your tables and countertops. Dishwasher safe. Not for use with abrasive cups and mugs.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (86, 'Tile Box', 'This tile box is perfect for stylishly\r\nstoring knick-knacks, jewelry, or any precious keepsake. Measuring 5\r\n1/4\" sq. x 2 1/8\" with a 4 1/4\" tile this versatile hinged box is made\r\nof authentic lacquered Alderwood.\r\n<ul><li>Made of solid lacquered Alderwood</li>\r\n<li>Measures 5 1/4\" sq. x  2 1/8\"  with a 4 1/4\" tile and hinged lid</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (3, 'Mousepad', 'Our durable cloth top mousepads will keep your mouse rolling in style. Rubber backing keeps the mousepad from sliding. Machine washable. 9.25\" x 7.75\"')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (49, 'Wall Clock', 'Decorate any room in your home or office with our 10 inch wall clock. Black plastic case. Made in the USA. Requires 1 AA battery (included).')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (26, 'Teddy Bear', 'Our 11 inch bear is a great companion for\r\nthose cold winter mornings. Quality construction, soft fur, a festive\r\nred ribbon, and a white raglan tee will make him the envy of all your\r\nstuffed animals.\r\n<ul><li>Soft plush fur</li>\r\n<li>11 inches tall</li>\r\n<li>Red bow and t-shirt included</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (64, 'Lunchbox', 'Our retro silver lunchbox brings back memories of childhood with modern day functionality. The image is applied on to clear permanent adhesive vinyl. Measuring 8\" x 4\" x 7\" it\'s perfect for lunch or anything else you want to fill it with. Clasp closure and quality construction will make you the envy of everyone on the schoolyard. Get yours today!')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (42, 'BBQ Apron', 'Useful in the kitchen or at the BBQ, our medium length aprons will help keep spills and splatters off your clothes. Includes neck ties and extra long waist ties. Two center stitched bottom compartment pouches allow you to keep cooking tools and recipes handy. 35% Cotton / 65% polyester blend, twill fabric. Machine washable and guaranteed.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (40, 'Flying Disc', 'Our 9 ¼ inch flying discs are perfect for playing in the yard or park. Imprinted with full size weatherproof decal.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (9, 'Baseball Cap', 'Great for year round wear, our all-weather microfiber caps are light weight, stylish and water resistant. Pre-curved bill. Velcro strap back. One size fits all.')";
   if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (65, 'Black Cap', 'Our 100% cotton caps are great year round. Low profile. Adjustable Velcro closure. Matching embroidered eyelets. One size fits all.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (101, 'Trucker Hat', 'Get ready to cruise the urban jungle in our vintage trucker hat. \r\n<ul><li>Foam front</li>\r\n<li>Plastic mesh backing</li>\r\n<li>One size fits all snap backing</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (41, 'Visor', 'Great for year round wear, our all-weather unstructured microfiber caps are light weight, stylish and water resistant. Low crown with pre-curved bill. Velcro strap back. One size fits all.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (51, 'Sticker (Oval)', 'Our stickers are printed on 4mil vinyl using water and UV resistant inks – meaning no fading in the sun or bleeding in the rain.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (50, 'Sticker (Rectangular)', 'Our stickers are printed on 4mil vinyl using water and UV resistant inks – meaning no fading in the sun or bleeding in the rain.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (90, 'Sticker (Bumper)', 'Tell the world how you feel! Our bumper\r\nstickers are perfect for expressing yourself while cruising down the\r\nhighway or just for posting on the wall. Made of durable vinyl and\r\nmeasuring a generous 10\" x 3\" these stickers are made for adding style\r\nto any surface.\r\n<ul><li>Printed on 4mil vinyl using water and UV resistant inks - means no fading in the sun or bleeding in the rain.</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (75, 'Wall Calendar', 'Don\'t forget mom\'s birthday - keep track of\r\nimportant dates with our 2005 twelve-month calendar. Our high quality\r\ncalendars are printed on glossy 100 lb text weight paper. They measure\r\n8.5\" x 11\" when folded, and 11\" x 17\" when hanging on your wall. <ul><li>Full bleed dynamic color</li>\r\n<li>100 lb text weight high gloss paper</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (74, 'Calendar Print', 'Don\'t miss an important date ever again! Our\r\nhigh quality one page wall calendars are printed on glossy, 10 point\r\npaper and measure 11\" x 17\". <ul><li>Full bleed dynamic color</li>\r\n<li>Glossy, 10 point paper</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (100, 'Journal', 'Record your thoughts, or keep track of your daily to-do\'s with this trusty journal.   \r\n<ul><li>Measures 5\" x 8\"</li>\r\n<li>Filled with 80 sheets of premium 60lb book weight unlined paper</li>\r\n<li>10 point glossy front cover</li>\r\n<li>16 mil textured back poly cover in black</li>\r\n<li>Wire-o bound</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (78, 'Greeting Cards (Package of 6)', 'Our high quality cards are printed on 10pt\r\nsingle-side coated glossy paper. Each card measures 5\" x 7\" when\r\nfolded. Six cards per package. <ul><li>Full bleed dynamic color</li><li>10 point one side coated glossy paper</li><li>Package of six cards</li><li>Envelopes included</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (77, 'Postcards (Package of 8)', 'These high quality postcards are printed on\r\nglossy, 10 pt paper, and come in a package of eight. The cards measure\r\na standard 6\" x 4\" and are ready to send updates and greetings\r\nworldwide.\r\n<ul><li>Full Bleed dynamic color</li>\r\n<li>Sturdy 10 pt glossy paper</li>\r\n<li>Package of eight</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (53, 'Small Poster', 'Perfect for dressing up any wall. Our high-quality poster is printed on heavyweight 7 mil semi-gloss paper using superior dye inks.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (54, 'Large Poster', 'Perfect for dressing up any wall. Our high-quality poster is printed on heavyweight 7 mil semi-gloss paper using superior dye inks.')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (92, 'Mini Poster Print', 'Like a poster -- only smaller! Our high\r\nquality one page prints are printed on glossy, 10 point paper and\r\nmeasure 11\" x 17\". <ul><li>Full bleed dynamic color</li>\r\n<li>Glossy, 10 point paper</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (52, 'License Plate Frame', 'Upgrade your car with our classy, chrome license plate frame. UV and water resistant. Fits most cars. 12 x 6')";    if (empty($sql)) return;
  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (17, 'Tote Bag', 'Our 100% cotton canvas tote bags have plenty\r\nof room to carry everything you need when you are on the go. They\r\ninclude a bottom gusset and extra long handles for easy carrying. <ul><li>10 oz heavyweight natural canvas fabric </li>\r\n<li>Full side and bottom gusset </li>\r\n<li>28\" reinforced self-fabric handles </li>\r\n<li>Machine washable </li>\r\n<li>Measures 15\" x 18\" x 6\"</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO $cptypestable (xar_prodtypeid, xar_prodtype, xar_description) VALUES (18, 'Messenger Bag', 'Great for school or the office, our colorful messenger bags are spacious and laptop/PDA friendly.  \r\n<ul><li>One front adjustable clasp closure.</li>\r\n<li>Main compartment has inside slip pocket.</li>\r\n<li>Front panel has zipper compartment.</li>\r\n<li>Adjustable 2\" shoulder strap.</li>\r\n<li>600 Denier Polyester</li>\r\n<li>Size: 14 1/2\" x 12\" x 5\"</li>')";
    if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
$sql="INSERT INTO {$cptypestable} (xar_prodtypeid, xar_prodtype, xar_description) VALUES (97, 'Book', 'Paperback book')";
   if (empty($sql)) return;  $result = &$dbconn->Execute($sql); if (!$result) return;
    xarResponseRedirect(xarModURL('xarcpshop', 'admin', 'prodtypes'));
return true;

}

?>
