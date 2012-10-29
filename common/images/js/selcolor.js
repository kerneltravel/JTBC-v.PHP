selcolor = {
  setobj: null,
  setcolor: function(ncolor)
  {
    document.getElementById("selcolor_DisColor").style.background = ncolor;
    document.getElementById("selcolor_HexColor").value = ncolor;
  },
  intocolor: function(ncolor)
  {
    if (ncolor == "") ncolor = "#000000";
    var ColorHex = new Array('00','33','66','99','CC','FF');
    var SpColorHex = new Array('FF0000','00FF00','0000FF','FFFF00','00FFFF','FF00FF');
    var ColorTable = '';
    for (i = 0; i < 2; i ++)
    {
      for (j = 0; j < 6; j ++)
      {
        ColorTable = ColorTable + '<tr height="12">';
        if (i == 0)
        {
          ColorTable = ColorTable + '<td width="11" style="background-color:#' + ColorHex[j] + ColorHex[j] + ColorHex[j] + '" onmouseover="selcolor.setcolor(\'#' + ColorHex[j] + ColorHex[j] + ColorHex[j] + '\')">';
        }
        else
        {
          ColorTable = ColorTable + '<td width="11" style="background-color:#' + SpColorHex[j] + '" onmouseover="selcolor.setcolor(\'#' + SpColorHex[j] + '\')">';
        }
        ColorTable = ColorTable + '<td width="1" style="background-color:#000000">';
        for (k = 0; k < 3; k ++)
        {
          for (l = 0; l < 6; l ++)
          {
            ColorTable = ColorTable + '<td width="11" style="background-color:#' + ColorHex[k+i*3] + ColorHex[l] + ColorHex[j] + '" onmouseover="selcolor.setcolor(\'#' + ColorHex[k+i*3] + ColorHex[l] + ColorHex[j] + '\')">';
          }
        }
      }
    }
    ColorTable = '<table width="233" cellspacing="0" cellpadding="0" style="border:#333333 1px solid">'
    + '<tr height="30"><td bgcolor="#ffffff">'
    + '<table cellpadding="0" cellspacing="1">'
    + '<tr><td width="3"><td><input type="text" id="selcolor_DisColor" size="6" style="border: #333333 1px solid; background:' + ncolor + '" disabled="disabled"></td>'
    + '<td width="3"><td><input type="text" id="selcolor_HexColor" size="7" style="border:inset 1px;font-family:Arial;" value="' + ncolor + '"></td></tr></table></td></table>'
    + '<table cellpadding="0" cellspacing="1" style="cursor:hand; border: #333333 1px solid; background: #000000" onclick="selcolor.clkcolor()">'
    + ColorTable + '</table>';
    document.getElementById("selcolor_panel").innerHTML = ColorTable;
  },
  colorpanel: function(nobj, ncolor, e)
  {
    if (!document.getElementById("selcolor_panel"))
    {
      var tpanel = document.createElement("div");
      tpanel.setAttribute("id", "selcolor_panel");
      tpanel.style.position = "absolute";
      tpanel.style.width = "253px";
      tpanel.style.height = "177px";
      document.body.appendChild(tpanel);
    }
    var curPosX, curPosY
    if(window.event)
    {
      curPosX = document.body.scrollLeft + event.x;
      curPosY = document.body.scrollTop + event.y;
    }
    else
    {
      curPosX = e.pageX;
      curPosY = e.pageY;
    }
    if (curPosX + 233 > document.body.clientWidth) {curPosX = curPosX - 233;}
    if (curPosY + 177 > document.body.clientHeight + document.body.scrollTop) {curPosY = curPosY - 177;}
    with(document.getElementById("selcolor_panel"))
    {
      style.display = "block";
      style.left = curPosX + "px";
      style.top = curPosY + "px";
    }
    selcolor.intocolor(ncolor);
    selcolor.setobj = nobj;
  },
  clkcolor: function()
  {
    var tobj = selcolor.setobj;
    if (tobj)
    {
      tobj.value = document.getElementById("selcolor_HexColor").value;
      document.getElementById("selcolor_panel").style.display = "none";
    }
  }
}