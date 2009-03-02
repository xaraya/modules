From: Georg Schnattinger
Newsgroups: xaraya.public-dev
Subject: [Xaraya_public-dev] Special Modules
Date: Tue, 02 Sep 2003 11:09:18 +0200

Helllo!

Lately I finished on a special module: a subitems module.
I don't think Xaraya is able to save subitems to items. I try to explain:
For example, if you want to have a pubtype "cooking recipe". This 
pubtype sould be to store the ingredients of the meal, thus there would 
be required a list of input fields: ingredient1, quantity1, 
pic1(extended info :-) ) , ingredient2, quantity2, pic2,...
The disadvantage of this is the high number of fields to ensure, that 
there your users aren't going against the limit. Furthermore there are 
always displayed all input fields. And of  course it's not a very nice 
way to do it.
As I didn't know how to do this better, I had a dream about a module, 
which can be hooked from any other module and which is able to display / 
provide editing / etc. for subitems to an item. I had a dream about a 
table, which saves the information, which DD-Object stores the subitems 
of "module" / "itemtype" combination and another table containing which 
DD-Itemids belong to "module" / "itemtype" / "itemid" in this DD-Object 
(1 to n Relation). I had the dream to share my module with the community.
Now, the module is here, and all dreams got true. If you didn't get my 
technical information, don't worry. You can download the module as zip 
file from my webspace: http://test.ice-clanpage.com/subitems.zip.

because there isn't any documentation yet, I give you a short briefing 
of usage:
- install module
- goto dynamicdata admin area, new object, create a dynamic data object 
, which should hold the subitem information (you must have one field 
with the ItemID, default 0, display only) -> remember the objectid, edit 
label (used for display) and name (used for custom default template)
- goto subitems module, admin area -> add link -> type in objectid (from 
previsously created dd-object), module name, itemtype
- goto module modules, hooks -> subitems -> enable the hook for the 
module /itemtype combination, you created the link for
-> now it should work (tested with cusom pubtype)

missing things with this module:
- permissions
- cannot hook without itemtype (I think, but for articles pubtype, this 
isn't a problem)
- etc.

Feedback is very welcome.

--Georg



