/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 */

/*=============================================================================
  Some utility functions
=============================================================================*/

function getPositionOfEvent(evt)
{
  if (evt.pageX)
    return {
             left: evt.pageX,
             top: evt.pageY 
           };
  else if (evt.clientX) {
	//modified by CvdS for konquerer... clientLeft/-top doesn't exist
	return {
             left: evt.clientX + document.body.scrollLeft - (document.body.clientLeft ? document.body.clientLeft : 0),
             top:  evt.clientY + document.body.scrollTop  - (document.body.clientTop ? document.body.clientTop : 0)
           };
  }

  alert("Unable to get position of event");
  return { left: 0, top: 0 };
};

/*=============================================================================
  PS Menu navigation handling
=============================================================================*/

var psmenu =
{
  closeDelay: 800,
  currentMenuDivElement: null,
  currentCancelCount: 0,
  currentListener: null
};

//-[ Open/close menu ]---------------------------------------------------------

psmenu.openMenu = function(listener, menuDivElement, pos)
{
  if (psmenu.isOpen())
  {
    psmenu.closeCurrentMenu();
  }
  else
  {
    menuDivElement.style.visibility = "visible";
	//modified by CvdS for konquerer... it requires 'px' postfix
    menuDivElement.style.left = pos.left + 'px';
    menuDivElement.style.top = pos.top + 'px';

    psmenu.currentListener = listener;
  }
}


psmenu.closeMenu = function(menuDivElement)
{
  psmenu.cancelDelayedCloseMenu();

  menuDivElement.style.visibility = "hidden";
  psmenu.currentMenuDivElement = null;

  psmenu.currentListener.menuClosed();
}


psmenu.closeCurrentMenu = function()
{
  if (psmenu.currentMenuDivElement != null)
    psmenu.closeMenu(psmenu.currentMenuDivElement);
}


psmenu.delayedCloseMenu = function(menuDivElement)
{
  psmenu.currentMenuDivElement = menuDivElement;
  ++psmenu.currentCancelCount;
  setTimeout( "if (psmenu.currentCancelCount==" + psmenu.currentCancelCount + ") psmenu.closeCurrentMenu();", psmenu.closeDelay );
}


psmenu.cancelDelayedCloseMenu = function()
{
  psmenu.currentMenuDivElement = null;
  clearTimeout();
}


psmenu.isOpen = function()
{
  return psmenu.currentMenuDivElement != null;
}

//-[ Event handlers ]----------------------------------------------------------

psmenu.onMouseOutDiv = function(menuDivElement)
{
  psmenu.delayedCloseMenu(menuDivElement);
}


psmenu.onMouseOver = function(rowElement)
{
  rowElement.className = "psmenu-menuItemOn";
  psmenu.cancelDelayedCloseMenu();
}


psmenu.onMouseOut = function(rowElement)
{
  rowElement.className = "psmenu-menuItem";
}


psmenu.onMouseDown = function(rowElement)
{
  var menuDivElement = psmenu.getParentDivElement(rowElement);

  psmenu.currentListener.itemSelected(menuDivElement.id, rowElement.rowIndex);
  psmenu.closeMenu(menuDivElement);
}


//-[ DOM navigation ]----------------------------------------------------------

psmenu.getParentDivElement = function(rowElement)
{
  var element = rowElement.parentNode;
  while (element.tagName.toUpperCase() != "DIV")
    element = element.parentNode;

  return element;
}

