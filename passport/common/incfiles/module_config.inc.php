<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function pp_check_isregister_close()
{
  global $ngenre, $variable;
  if (ii_get_num($variable[$ngenre . '.register_close']) == 1) mm_imessage(ii_itake('module.register_close', 'lng'), -1);
}

function pp_check_islostpassword_close()
{
  global $ngenre, $variable;
  if (ii_get_num($variable[$ngenre . '.lostpassword_close']) == 1) mm_imessage(ii_itake('module.lostpassword_close', 'lng'), -1);
}

function jtbc_cms_module_logindisp()
{
  $tbackurl = $_GET['backurl'];
  if (ii_isnull($tbackurl)) $tbackurl = ii_get_actual_route('./');
  mm_check_valcode($tbackurl);
  $tusername = ii_get_safecode($_POST['username']);
  $tpassword = md5(ii_get_safecode($_POST['password']));
  if (ap_check_username($tusername, $tpassword))
  {
    setcookie(APP_NAME . 'user[username]', $tusername, 0, COOKIES_PATH);
    setcookie(APP_NAME . 'user[password]', $tpassword, 0, COOKIES_PATH);
    session_register(APP_NAME . 'username');
    $_SESSION[APP_NAME . 'username'] = $tusername;
    mm_client_redirect($tbackurl);
  }
  else mm_imessage(ii_itake('global.lng_error.login', 'lng'), $tbackurl);
}

function jtbc_cms_module_logoutdisp()
{
  $tbackurl = $_GET['backurl'];
  if (ii_isnull($tbackurl)) $tbackurl = ii_get_actual_route('./');
  setcookie(APP_NAME . 'user[username]', '', 0, COOKIES_PATH);
  setcookie(APP_NAME . 'user[password]', '', 0, COOKIES_PATH);
  session_unregister(APP_NAME . 'username');
  mm_client_redirect($tbackurl);
}

function jtbc_cms_module_registerdisp()
{
  global $ctype;
  $ctype = 'register';
  global $Err;
  if (!mm_ck_valcode()) $Err[count($Err)] = ii_itake('global.lng_error.valcode', 'lng');
  $tckstr = 'username:' . ii_itake('config.username', 'lng') . ',password:' . ii_itake('config.password', 'lng') . ',email:' . ii_itake('config.email', 'lng') . ',city:' . ii_itake('config.city', 'lng') . ',sex:' . ii_itake('config.sex', 'lng') . ',old:' . ii_itake('config.old', 'lng');
  $tary = explode(',', $tckstr);
  foreach ($tary as $key => $val)
  {
    $tvalary = explode(':', $val);
    if (ii_isnull($_POST[$tvalary[0]])) $Err[count($Err)] = str_replace('[]', '[' . $tvalary[1] . ']', ii_itake('global.lng_error.insert_empty', 'lng'));
  }
  $tRegUserName = stripslashes($_POST['username']);
  $tRegLimit = '&,\',<,>,#,+,-,/,*,@,$,%,^,' . chr(32) . ',' . chr(9) . ',;';
  $tRegLimitAry = explode(',', $tRegLimit);
  foreach ($tRegLimitAry as $key => $val)
  {
    if (is_numeric(strpos($tRegUserName, $val)))
    {
      $Err[count($Err)] = ii_itake('module.insert_limit', 'lng');
      continue;
    }
  }
  if (ii_strlen($tRegUserName) < 2 || ii_strlen($tRegUserName) > 16) $Err[count($Err)] = ii_itake('module.insert_length', 'lng');
  if ($_POST['password'] != $_POST['cpassword']) $Err[count($Err)] = ii_itake('module.insert_checkout', 'lng');
  if (!ii_isvalidemail($_POST['email'])) $Err[count($Err)] = ii_itake('module.insert_email', 'lng');
  if (!is_array($Err))
  {
    global $conn;
    global $ndatabase, $nidfield, $nfpre;
    $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$tRegUserName'";
    $trs = ii_conn_query($tsqlstr, $conn);
    $trs = ii_conn_fetch_array($trs);
    if ($trs) $Err[count($Err)] = ii_itake('module.insert_exist', 'lng');
    else
    {
      $tsqlstr = "insert into $ndatabase (
      " . ii_cfname('username') . ",
      " . ii_cfname('password') . ",
      " . ii_cfname('email') . ",
      " . ii_cfname('city') . ",
      " . ii_cfname('sex') . ",
      " . ii_cfname('old') . ",
      " . ii_cfname('name') . ",
      " . ii_cfname('qq') . ",
      " . ii_cfname('msn') . ",
      " . ii_cfname('phone') . ",
      " . ii_cfname('homepage') . ",
      " . ii_cfname('code') . ",
      " . ii_cfname('address') . ",
      " . ii_cfname('time') . "
      ) values (
      '$tRegUserName',
      '" . ii_md5($_POST['password']) . "',
      '" . ii_left(ii_cstr($_POST['email']), 50) . "',
      '" . ii_left(ii_cstr($_POST['city']), 50) . "',
      '" . ii_left(ii_cstr($_POST['sex']), 50) . "',
      '" . ii_left(ii_cstr($_POST['old']), 50) . "',
      '" . ii_left(ii_cstr($_POST['name']), 50) . "',
      '" . ii_left(ii_cstr($_POST['qq']), 50) . "',
      '" . ii_left(ii_cstr($_POST['msn']), 50) . "',
      '" . ii_left(ii_cstr($_POST['phone']), 50) . "',
      '" . ii_left(ii_cstr($_POST['homepage']), 255) . "',
      '" . ii_left(ii_cstr($_POST['code']), 50) . "',
      '" . ii_left(ii_cstr($_POST['address']), 255) . "',
      '" . ii_now() . "'
      )";
      $trs = ii_conn_query($tsqlstr, $conn);
      if ($trs)
      {
        setcookie(APP_NAME . 'user[username]', $tRegUserName, 0, COOKIES_PATH);
        setcookie(APP_NAME . 'user[password]', md5($_POST['password']), 0, COOKIES_PATH);
        session_register(APP_NAME . 'username');
        $_SESSION[APP_NAME . 'username'] = $tRegUserName;
        header('location: ' . ii_get_actual_route('./'));
      }
      else mm_imessage(ii_itake('global.lng_public.sudd', 'lng'), '-1');
    }
  }
}

function jtbc_cms_module_lostpassworddisp()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tusername = ii_get_safecode($_POST['username']);
  $temail = ii_get_safecode($_POST['email']);
  $tname = ii_get_safecode($_POST['name']);
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$tusername' and " . ii_cfname('email') . "='$temail' and " . ii_cfname('name') . "='$tname'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tpassword = ii_random(8);
    $tmd5password = md5($tpassword);
    $ttopic = ii_itake('module.lostpassword_topic', 'lng');
    $ttopic = str_replace('[]', '[' . ii_itake('global.module.web_title', 'lng') . ']', $ttopic);
    $tbody = ii_itake('module.lostpassword_body', 'lng');
    $tbody = str_replace('[name]', ii_htmlencode($tname), $tbody);
    $tbody = str_replace('[username]', ii_htmlencode($tusername), $tbody);
    $tbody = str_replace('[password]', ii_htmlencode($tpassword), $tbody);
    if (mm_sendemail($temail, $ttopic, $tbody))
    {
      $tsqlstr = "update $ndatabase set " . ii_cfname('password') . "='$tmd5password' where " . ii_cfname('username') . "='$tusername'";
      ii_conn_query($tsqlstr, $conn);
      mm_imessage(ii_itake('module.lostpassword_emailok', 'lng'));
    }
    else mm_imessage(ii_itake('module.lostpassword_emailerror', 'lng'));
  }
  else mm_imessage(ii_itake('module.lostpassword_infoerror', 'lng'), -1);
}

function jtbc_cms_module_manage_informationdisp()
{
  $tbackurl = $_GET['backurl'];
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $nusername;
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$nusername'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tsqlstr = "update $ndatabase set
    " . ii_cfname('email') . "='" . ii_left(ii_cstr($_POST['email']), 50) . "',
    " . ii_cfname('city') . "='" . ii_left(ii_cstr($_POST['city']), 50) . "',
    " . ii_cfname('sex') . "=" . ii_get_num($_POST['sex']) . ",
    " . ii_cfname('old') . "=" . ii_get_num($_POST['old']) . ",
    " . ii_cfname('name') . "='" . ii_left(ii_cstr($_POST['name']), 50) . "',
    " . ii_cfname('qq') . "=" . ii_get_num($_POST['qq']) . ",
    " . ii_cfname('msn') . "='" . ii_left(ii_cstr($_POST['msn']), 50) . "',
    " . ii_cfname('phone') . "='" . ii_left(ii_cstr($_POST['phone']), 50) . "',
    " . ii_cfname('homepage') . "='" . ii_left(ii_cstr($_POST['homepage']), 255) . "',
    " . ii_cfname('code') . "=" . ii_get_num($_POST['code']) . ",
    " . ii_cfname('address') . "='" . ii_left(ii_cstr($_POST['address']), 255) . "'
    where " . ii_cfname('username') . "='$nusername'";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs) mm_imessage(ii_itake('global.lng_public.edit_succeed', 'lng'), $tbackurl);
    else mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl);
  }
  else mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl);
}

function jtbc_cms_module_manage_passworddisp()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $nusername;
  $tbackurl = $_GET['backurl'];
  $tpassword = md5($_POST['password']);
  $tnpassword = md5($_POST['npassword']);
  $tncpassword = md5($_POST['ncpassword']);
  if ($tnpassword != $tncpassword) mm_imessage(ii_itake('module.insert_checkout', 'lng'), $tbackurl);
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$nusername' and " . ii_cfname('password') . "='$tpassword'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tsqlstr = "update $ndatabase set " . ii_cfname('password') . "='$tnpassword' where " . ii_cfname('username') . "='$nusername'";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs)
    {
      setcookie(APP_NAME . 'user[password]', $tnpassword, 0, COOKIES_PATH);
      mm_imessage(ii_itake('global.lng_public.edit_succeed', 'lng'), $tbackurl);
    }
    else mm_imessage(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl);
  }
  else mm_imessage(ii_itake('module.insert_password', 'lng'), $tbackurl);
}

function jtbc_cms_module_manage_usersetdisp()
{
  $tbackurl = $_GET['backurl'];
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $nusername;
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$nusername'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    global $face_width_max, $face_height_max;
    $tface_width = ii_get_num($_POST['face_width']);
    $tface_height = ii_get_num($_POST['face_height']);
    if ($tface_width > $face_width_max) $tface_width = $face_width_max;
    if ($tface_height > $face_height_max) $tface_height = $face_height_max;

    $tsqlstr = "update $ndatabase set
    " . ii_cfname('face') . "=" . ii_get_num($_POST['face']) . ",
    " . ii_cfname('face_u') . "=" . ii_get_num($_POST['face_u']) . ",
    " . ii_cfname('face_url') . "='" . ii_left(ii_cstr($_POST['face_url']), 255) . "',
    " . ii_cfname('face_width') . "=$tface_width,
    " . ii_cfname('face_height') . "=$tface_height,
    " . ii_cfname('sign') . "='" . ii_left(ii_cstr($_POST['sign']), 100) . "'
    where " . ii_cfname('username') . "='$nusername'";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs) mm_imessage(ii_itake('global.lng_public.edit_succeed', 'lng'), $tbackurl);
    else mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl);
  }
  else mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl);
}

function jtbc_cms_module_managedisp()
{
  switch($_GET['mtype'])
  {
    case 'information':
      jtbc_cms_module_manage_informationdisp();
      break;
    case 'password':
      jtbc_cms_module_manage_passworddisp();
      break;
    case 'userset':
      jtbc_cms_module_manage_usersetdisp();
      break;
  }
}

function jtbc_cms_module_action()
{
  switch($_GET['action'])
  {
    case 'login':
      jtbc_cms_module_logindisp();
      break;
    case 'logout':
      jtbc_cms_module_logoutdisp();
      break;
    case 'register':
      pp_check_isregister_close();
      return jtbc_cms_module_registerdisp();
      break;
    case 'lostpassword':
      pp_check_islostpassword_close();
      return jtbc_cms_module_lostpassworddisp();
      break;
    case 'manage':
      jtbc_cms_module_managedisp();
      break;
  }
}

function jtbc_cms_module_login()
{
  global $nvalidate;
  $tmpstr = ii_ireplace('module.login', 'tpl');
  $tmpstr = mm_cvalhtml($tmpstr, $nvalidate, '{@recurrence_valcode}');
  return $tmpstr;
}

function jtbc_cms_module_user_detail()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tusername = ii_get_safecode($_GET['username']);
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$tusername'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('module.user_detail', 'tpl');
    foreach ($trs as $key => $val)
    {
      $tkey = ii_get_lrstr($key, '_', 'rightr');
      $GLOBALS['RS_' . $tkey] = $val;
      $tmpstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmpstr);
    }
    $tmpstr = str_replace('{$id}', $trs[$nidfield], $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    return $tmpstr;
  }
  else mm_client_alert(ii_itake('global.lng_public.not_exist', 'lng'), -1);
}

function jtbc_cms_module_lostpassword()
{
  return ii_ireplace('module.lostpassword', 'tpl');
}

function jtbc_cms_module_register()
{
  global $nvalidate;
  $tmpstr = ii_itake('module.register', 'tpl');
  $tmpstr = mm_cvalhtml($tmpstr, $nvalidate, '{@recurrence_valcode}');
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function jtbc_cms_module_manage_member()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $nusername;
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$nusername'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('module.manage_member', 'tpl');
    foreach ($trs as $key => $val)
    {
      $tkey = ii_get_lrstr($key, '_', 'rightr');
      $GLOBALS['RS_' . $tkey] = $val;
      $tmpstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmpstr);
    }
    $tmpstr = str_replace('{$id}', $trs[$nidfield], $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    return $tmpstr;
  }
  else mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), -1);
}

function jtbc_cms_module_manage_information()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $nusername;
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$nusername'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('module.manage_information', 'tpl');
    foreach ($trs as $key => $val)
    {
      $tkey = ii_get_lrstr($key, '_', 'rightr');
      $GLOBALS['RS_' . $tkey] = $val;
      $tmpstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmpstr);
    }
    $tmpstr = str_replace('{$id}', $trs[$nidfield], $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    return $tmpstr;
  }
  else mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), -1);
}

function jtbc_cms_module_manage_password()
{
  return ii_ireplace('module.manage_password', 'tpl');
}

function jtbc_cms_module_manage_userset()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $nusername;
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$nusername'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('module.manage_userset', 'tpl');
    foreach ($trs as $key => $val)
    {
      $tkey = ii_get_lrstr($key, '_', 'rightr');
      $GLOBALS['RS_' . $tkey] = $val;
      $tmpstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmpstr);
    }
    $tmpstr = str_replace('{$id}', $trs[$nidfield], $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    return $tmpstr;
  }
  else mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), -1);
}

function jtbc_cms_module_manage()
{
  switch(mm_ctype($_GET['mtype'], 1))
  {
    case 'member':
      return jtbc_cms_module_manage_member();
      break;
    case 'information':
      return jtbc_cms_module_manage_information();
      break;
    case 'password':
      return jtbc_cms_module_manage_password();
      break;
    case 'userset':
      return jtbc_cms_module_manage_userset();
      break;
    default:
      return jtbc_cms_module_manage_member();
      break;
  }
}

function jtbc_cms_module_premise()
{
  return ii_ireplace('module.premise', 'tpl');
}

function jtbc_cms_module()
{
  switch(mm_ctype($_GET['type']))
  {
    case 'login':
      return jtbc_cms_module_login();
      break;
    case 'user_detail':
      return jtbc_cms_module_user_detail();
      break;
    case 'lostpassword':
      pp_check_islostpassword_close();
      return jtbc_cms_module_lostpassword();
      break;
    case 'register':
      pp_check_isregister_close();
      return jtbc_cms_module_register();
      break;
    case 'manage':
      ap_user_islogin();
      return jtbc_cms_module_manage();
      break;
    default:
      return jtbc_cms_module_premise();
      break;
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
