var loader_html;
var loader_id;
var loader_pos = 0;
var loader_dir = 2;
var loader_len = 0;

loader_html = "<style>";
loader_html += "#loader_container {text-align:center;position:absolute;width:100%;left: 0;z-index: 1;display:none;}";
loader_html += "#loader {font-family:Tahoma, Helvetica, sans;font-size:11.5px;color:#000000;background-color:#FFFFFF;padding:10px 0 16px 0;margin:0 auto;display:block;width:130px;border:1px solid #FF0000;text-align:left;z-index:2;}";
loader_html += "#progress {height:5px;font-size:1px;width:1px;position:relative;top:1px;left:0px;background-color: #FF0000;}";
loader_html += "#loader_bg {background-color: #FFE6BF;position:relative;top:8px;left:8px;height:7px;width:113px;font-size:1px;}";
loader_html += "</style>";
loader_html += "<div id=\"loader_container\">";
loader_html += "<div id=\"loader\">";
loader_html += "<div id=\"loader_text\" align=\"center\"></div>";
loader_html += "<div id=\"loader_bg\"><div id=\"progress\"></div></div>";
loader_html += "</div>";
loader_html += "</div>";
document.write(loader_html);

function loader_animate()
{
    var loader_elem = document.getElementById('progress');
    if (loader_elem != null) {
    if (loader_pos == 0) loader_len += loader_dir;
    if (loader_len>32 || loader_pos>79) loader_pos += loader_dir;
    if (loader_pos>79) loader_len -= loader_dir;
    if (loader_pos>79 && loader_len==0) loader_pos = 0;
    loader_elem.style.left = loader_pos;
    loader_elem.style.width = loader_len;
    }
}

function loader_remove()
{
  this.clearInterval(loader_id);
  get_id("loader_container").style.display = "none";
}

function loader_show(strers)
{
  loader_id = setInterval(loader_animate, 20);
  get_id("loader_container").style.top = document.body.scrollTop + 100;
  get_id("loader_container").style.display = "block";
  get_id("loader_text").innerHTML = strers;
}