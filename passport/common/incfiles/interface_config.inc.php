<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
header("cache-control: no-cache, must-revalidate");
header("pragma: no-cache");

function jtbc_cms_interface_login()
{
  ap_user_init();
  global $nusername, $nvalidate;
  if (ii_isnull($nusername))
  {
    $tmpstr = ii_ireplace('global.' . USER_FOLDER . ':api.jslogin_login', 'tpl');
    $tmpstr = mm_cvalhtml($tmpstr, $nvalidate, '{@recurrence_valcode}');
  }
  else $tmpstr = ii_ireplace('global.' . USER_FOLDER . ':api.jslogin_logined', 'tpl');
  echo $tmpstr;
}

function jtbc_cms_interface_nlogin()
{
  if (!mm_ck_valcode()) echo 'error1';
  else
  {
    $tusername = ii_unescape(ii_get_safecode($_GET['username']));
    $tpassword = md5(ii_unescape(ii_get_safecode($_GET['password'])));
    setcookie(APP_NAME . 'user[username]', $tusername, 0, COOKIES_PATH);
    setcookie(APP_NAME . 'user[password]', $tpassword, 0, COOKIES_PATH);
    if (ap_check_username($tusername, $tpassword)) echo 'ok';
    else echo 'error2';
  }
}

function jtbc_cms_interface_check_username()
{
  $tusername = ii_unescape(ii_get_safecode($_GET['username']));
  if (ap_check_isuser($tusername) == 0) echo '0';
  else echo '1';
}

function jtbc_cms_interface()
{
  switch($_GET['type'])
  {
    case 'login':
      return jtbc_cms_interface_login();
      break;
    case 'nlogin':
      return jtbc_cms_interface_nlogin();
      break;
    case 'check_username':
      return jtbc_cms_interface_check_username();
      break;
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
