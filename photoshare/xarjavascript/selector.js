/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 */

function selectorAdd1(id)
{
  var src = document.getElementById(id);
  var dst = document.getElementById(id+"1");

  selectorMove(src,dst);
}


function selectorRemove1(id)
{
  var src = document.getElementById(id+"1");
  var dst = document.getElementById(id);

  selectorMove(src,dst);
}


function selectorAdd2(id)
{
  var src = document.getElementById(id);
  var dst = document.getElementById(id+"2");

  selectorMove(src,dst);
}


function selectorRemove2(id)
{
  var src = document.getElementById(id+"2");
  var dst = document.getElementById(id);

  selectorMove(src,dst);
}


function selectorMove(src, dst)
{
  var srcSize = src.length;

  for (var i=0; i<srcSize; ++i)
  {
    if (src.options[i].selected)
    {
      var len = dst.length;
      dst.options[len] = new Option(src.options[i].text);
      dst.options[len].value = src.options[i].value;
    }
  }

  for (var i=srcSize-1; i>=0; --i)
  {
    if (src.options[i].selected)
      src.options[i] = null;
  }
}


function selectorSubmit(selectors)
{
  for (var i in selectors)
  {
    var selector = selectors[i];

    var src1 = document.getElementById(selector+"1");
    var result = selectorGetValue(src1);
    var hidden1 = document.getElementById(selector+"Value1");
    hidden1.value = result;

    var src2 = document.getElementById(selector+"2");
    var result = selectorGetValue(src2);
    var hidden2 = document.getElementById(selector+"Value2");
    hidden2.value = result;

  }
}


function selectorGetValue(src)
{
  var srcSize = src.length;
  var result = "";

  for (var i=0; i<srcSize; ++i)
  {
    if (i == 0)
      result += src.options[i].value;
    else
      result += ";" + src.options[i].value;
  }

  return result;
}
