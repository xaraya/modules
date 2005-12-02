function boolToggle(obj)
{
    stat = true;
    if (obj.selectedIndex == 0 || obj.selectedIndex == 3) {
        stat = false;
    }

    items = document.searchform.elements['bools[]'];

    for (i = 0; i < items.length; i++) {
        if (stat == true) items[i].selectedIndex = 0;
        items[i].disabled = stat;

    }

}