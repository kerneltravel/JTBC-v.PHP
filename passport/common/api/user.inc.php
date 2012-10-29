<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function ap_check_isuser($username)
{
  global $conn;
  $tdatabase = mm_cndatabase(USER_FOLDER);
  $tidfield = mm_cnidfield(USER_FOLDER);
  $tfpre = mm_cnfpre(USER_FOLDER);
  $tusername = ii_get_safecode($username);
  $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'username') . "='$tusername'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    if ($trs[ii_cfnames($tfpre, 'lock')] == 1) $treturn = 2;
    else $treturn = 1;
  }
  else $treturn = 0;
  return $treturn;
}

function ap_check_userlogin()
{
  if (!ii_isnull($_SESSION[APP_NAME . 'username'])) return true;
  else
  {
    $tusername = ii_get_safecode($_COOKIE[APP_NAME . 'user']['username']);
    $tpassword = ii_get_safecode($_COOKIE[APP_NAME . 'user']['password']);
    if (!ii_isnull($tusername))
    {
      if (ap_check_username($tusername, $tpassword))
      {
        session_register(APP_NAME . 'username');
        $_SESSION[APP_NAME . 'username'] = $tusername;
        return true;
      }
      else return false;
    }
    else return false;
  }
}

function ap_check_username($username, $password)
{
  global $conn;
  $tdatabase = mm_cndatabase(USER_FOLDER);
  $tidfield = mm_cnidfield(USER_FOLDER);
  $tfpre = mm_cnfpre(USER_FOLDER);
  $tusername = ii_get_safecode($username);
  $tpassword = ii_get_safecode($password);
  $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'username') . "='$tusername' and " . ii_cfnames($tfpre, 'password') . "='$tpassword' and " . ii_cfnames($tfpre, 'lock') . "=0";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tsqlstr = "update $tdatabase set " . ii_cfnames($tfpre, 'pretime') . "=" . ii_cfnames($tfpre, 'lasttime') . "," . ii_cfnames($tfpre, 'lasttime') . "='" . ii_now() . "' where " . ii_cfnames($tfpre, 'username') . "='$tusername'";
    $trs = ii_conn_query($tsqlstr, $conn);
    return true;
  }
  else return false;
}

function ap_count_user_message($username)
{
  global $conn;
  $tusername = ii_get_safecode($username);
  $tdatabase = mm_cndatabase(USER_FOLDER . '.message');
  $tidfield = mm_cnidfield(USER_FOLDER . '.message');
  $tfpre = mm_cnfpre(USER_FOLDER . '.message');
  $tsqlstr = "select count($tidfield) from $tdatabase where " . ii_cfnames($tfpre, 'recipients') . "='$tusername' and " . ii_cfnames($tfpre, 'read') . "=0";
  $trs = @ii_conn_query($tsqlstr, $conn);
  $trs = @ii_conn_fetch_array($trs);
  if ($trs) return $trs[0];
  else return 0;
}

function ap_get_userinfo($strers, $username)
{
  global $conn;
  $tstrers = ii_get_safecode($strers);
  $tusername = ii_get_safecode($username);
  $tdatabase = mm_cndatabase(USER_FOLDER);
  $tidfield = mm_cnidfield(USER_FOLDER);
  $tfpre = mm_cnfpre(USER_FOLDER);
  $tsqlstr = "select " . ii_cfnames($tfpre, $tstrers) . " from $tdatabase where " . ii_cfnames($tfpre, 'username') . "='$tusername'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs) return $trs[ii_cfnames($tfpre, $tstrers)];
}

function ap_get_userface($face, $face_u, $face_url)
{
  global $global_images_route;
  $tface = ii_get_num($face);
  $tface_u = ii_get_num($face_u);
  $tface_url = strtolower(ii_htmlencode($face_url));
  if ($tface_u == 1)
  {
    if (ii_left($tface_url, 4) == 'http') return $tface_url;
    else return '';
  }
  else return $global_images_route . 'face/' . $face . '.gif';
}

function ap_sel_usergroup($name, $type, $value)
{
  return ii_show_xmlinfo_select('global.' . USER_FOLDER . ':sel_group.all', $value, $name . ':' . $type);
}

function ap_send_user_message($topic, $content, $addresser, $recipients)
{
  global $conn;
  $taddresser = ii_get_safecode($addresser);
  $trecipients = ii_get_safecode($recipients);
  $tdatabase = mm_cndatabase(USER_FOLDER . '.message');
  $tidfield = mm_cnidfield(USER_FOLDER . '.message');
  $tfpre = mm_cnfpre(USER_FOLDER . '.message');
  $tsqlstr = "insert into $tdatabase (
  " . ii_cfnames($tfpre, 'topic') . ",
  " . ii_cfnames($tfpre, 'content') . ",
  " . ii_cfnames($tfpre, 'time') . ",
  " . ii_cfnames($tfpre, 'len') . ",
  " . ii_cfnames($tfpre, 'addresser') . ",
  " . ii_cfnames($tfpre, 'recipients') . "
  ) values (
  '" . ii_left($topic, 50) . "',
  '" . ii_left($content, 1000) . "',
  '" . ii_now() . "',
  " . strlen(ii_left($topic, 1000)) . ",
  '$taddresser',
  '$trecipients'
  )";
  $trs = ii_conn_query($tsqlstr, $conn);
  if ($trs) return true;
  else return false;
}

function ap_user_login($tpl = '')
{
  global $nvalidate;
  if (!ii_isnull($tpl)) $tmpstr = @ii_itake($tpl, 'tpl');
  if (ii_isnull($tmpstr)) $tmpstr = ii_itake('global.' . USER_FOLDER . ':api.login', 'tpl');
  if (!ii_isnull($tmpstr))
  {
    $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
    $tmpary = explode('{@@}', $tmpastr);
    if (count($tmpary) != 2) return;
    if (!ap_check_userlogin()) $tmprstr = $tmpary[0];
    else $tmprstr = $tmpary[1];
    $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    $tmpstr = mm_cvalhtml($tmpstr, $nvalidate, '{@recurrence_valcode}');
    return $tmpstr;
  }
}

function ap_user_data_member_side()
{
  $tmpstr = ii_itake('global.' . USER_FOLDER . ':module.data_member_side', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tmpary = ii_itake('global.' . USER_FOLDER . ':member_menu.all', 'lng', 1);
  if (is_array($tmpary))
  {
    foreach($tmpary as $key => $val)
    {
      $tmptstr = str_replace('{$href}', $key, $tmpastr);
      $tmptstr = str_replace('{$explain}', $val, $tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function ap_user_islogin($url = '')
{
  global $nurl;
  if (ii_isnull($url)) $turl = ii_get_actual_route(USER_FOLDER) . '?type=login&backurl=' . urlencode($nurl);
  else $turl = ii_get_actual_route(USER_FOLDER) . '?type=login&backurl=' . urlencode($url);
  if (!ap_check_userlogin()) mm_imessage(ii_itake('global.' . USER_FOLDER . ':api.nologin', 'lng'), $turl);
}

function ap_user_init()
{
  global $nusername;
  if (ap_check_userlogin()) $nusername = ii_get_safecode($_SESSION[APP_NAME . 'username']);
}

function ap_update_userproperty($strers, $value, $type, $username)
{
  global $conn;
  $tstrers = ii_get_safecode($strers);
  $tusername = ii_get_safecode($username);
  $tdatabase = mm_cndatabase(USER_FOLDER);
  $tidfield = mm_cnidfield(USER_FOLDER);
  $tfpre = mm_cnfpre(USER_FOLDER);
  switch($type)
  {
    case 0:
      $tvalue = ii_get_num($value);
      $tsqlstr = "update $tdatabase set " . ii_cfnames($tfpre, $tstrers) . "=" . ii_cfnames($tfpre, $tstrers) . "+$tvalue where " . ii_cfnames($tfpre, 'username') . "='$tusername'";
      break;
    case 1:
      $tvalue = ii_get_num($value);
      $tsqlstr = "update $tdatabase set " . ii_cfnames($tfpre, $tstrers) . "=$tvalue where " . ii_cfnames($tfpre, 'username') . "='$tusername'";
      break;
    case 2:
      $tvalue = ii_get_safecode($value);
      $tsqlstr = "update $tdatabase set " . ii_cfnames($tfpre, $tstrers) . "='$tvalue' where " . ii_cfnames($tfpre, 'username') . "='$tusername'";
      break;
  }
  if (!ii_isnull($tsqlstr)) return ii_conn_query($tsqlstr, $conn);
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
