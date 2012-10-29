var preview_html;
preview_html = "<style>";
preview_html += "#preview_images {margin-right:auto;margin-left:auto;text-align:center;position:absolute;z-index:99999;border:1px solid #000;vertical-align:middle;Alpha(Opacity=80);padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;background:#FFFFFF;display:none;width:340;height:220;}";
preview_html += "#preview_images_top {text-align:right;position: relative; height:20}";
preview_html += "#preview_images_bottom {text-align:center;position: relative; }";
preview_html += "</style>";
preview_html += "<div id=\"preview_images\">";
preview_html += "<div id=\"preview_images_top\">";
preview_html += "<a href=\"javascript:preview_images_close();\" target=\"_self\">×</a>";
preview_html += "</div>";
preview_html += "<div id=\"preview_images_bottom\">";
preview_html += "</div>";
preview_html += "</div>";
document.write (preview_html);

function preview_images_close()
{
  get_id("preview_images").style.display = "none";
}

function preview_images(strurl, e)
{
  var curPosX, curPosY
  if(window.event){
    curPosX = document.body.scrollLeft + event.x;
    curPosY = document.body.scrollTop + event.y;
  } else{
    curPosX = e.pageX;
    curPosY = e.pageY;
  }
  if (curPosX + 340 > document.body.clientWidth) {curPosX = curPosX - 340;}
  if (curPosY + 220 > document.body.clientHeight + document.body.scrollTop) {curPosY = curPosY - 220;}
  with(get_id("preview_images"))
  {
    style.display = "block";
    style.left = curPosX + "px";
    style.top = curPosY + "px";
  }
  get_id("preview_images_bottom").innerHTML = "<img src=\"" + strurl + "\" border=\"0\" onload=\"if (this.width > 300)this.width = 300;if (this.height > 180)this.height = 180;\" alt=\"" + strurl + "\">";
}