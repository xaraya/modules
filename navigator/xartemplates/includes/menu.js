/* Concept gleaned from: http://www.howtocreate.co.uk/tutorials/testMenu.html
   original credit goes to:  */

function navigator_branch_showSubMenu(id) {

    var list = document.getElementById(id);

    //change display of child
    for( var x = 0; list.childNodes[x]; x++ ){
        if( list.childNodes[x].tagName == 'UL' ) {
            list.childNodes[x].className += (list.childNodes[x].className?' ':'') + 'xar-navigator-show-submenu';
        }
        if( list.childNodes[x].tagName == 'A' ) {
            list.childNodes[x].className += (list.childNodes[x].className?' ':'') + 'xar-navigator-anchor-hilight';
        }
    }
}

function navigator_branch_hideSubMenu(id) {

    var list = document.getElementById(id);

    //change display of child
    for( var x = 0; list.childNodes[x]; x++ ){
        if( list.childNodes[x].tagName == 'UL' ) {
            list.childNodes[x].className = list.childNodes[x].className.replace(/ ?xar-navigator-show-submenu$/,'');
        }
        if( list.childNodes[x].tagName == 'A' ) {
            list.childNodes[x].className = list.childNodes[x].className.replace(/ ?xar-navigator-anchor-hilight$/,'');
        }
    }
}