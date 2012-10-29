<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
jtbc_cms_admin_init();
$nsearch = 'username,id';
$ncontrol = 'select,lock,delete';

function pp_manage_navigation()
{
  return ii_ireplace('manage.navigation', 'tpl');
}

function jtbc_cms_admin_manage_adddisp()
{
  global $ngenre;
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tbackurl = $_GET['backurl'];
  $tusername = ii_left(ii_cstr($_POST['username']), 50);
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$tusername'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs) jtbc_cms_admin_msg(ii_itake('global.lng_public.exist', 'lng'), $tbackurl, 1);
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
    " . ii_cfname('emoney') . ",
    " . ii_cfname('integral') . ",
    " . ii_cfname('utype') . ",
    " . ii_cfname('lock') . ",
    " . ii_cfname('forum_admin') . ",
    " . ii_cfname('face') . ",
    " . ii_cfname('face_u') . ",
    " . ii_cfname('face_url') . ",
    " . ii_cfname('face_width') . ",
    " . ii_cfname('face_height') . ",
    " . ii_cfname('sign') . ",
    " . ii_cfname('time') . "
    ) values (
    '$tusername',
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
    " . ii_get_num($_POST['emoney']) . ",
    " . ii_get_num($_POST['integral']) . ",
    " . ii_get_num($_POST['utype']) . ",
    " . ii_get_num($_POST['lock']) . ",
    " . ii_get_num($_POST['forum_admin']) . ",
    " . ii_get_num($_POST['face']) . ",
    " . ii_get_num($_POST['face_u']) . ",
    '" . ii_left(ii_cstr($_POST['face_url']), 255) . "',
    " . ii_get_num($_POST['face_width']) . ",
    " . ii_get_num($_POST['face_height']) . ",
    '" . ii_left(ii_cstr($_POST['sign']), 255) . "',
    '" . ii_now() . "'
    )";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs) jtbc_cms_admin_msg(ii_itake('global.lng_public.add_succeed', 'lng'), $tbackurl, 1);
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.add_failed', 'lng'), $tbackurl, 1);
  }
}

function jtbc_cms_admin_manage_editdisp()
{
  global $ngenre;
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tbackurl = $_GET['backurl'];
  $tid = ii_get_num($_GET['id']);
  if (!($tid == 0))
  {
    $tsqlstr = "update $ndatabase set ";
    if (!ii_isnull($_POST['password'])) $tsqlstr .= ii_cfname('password') . "='" . ii_md5($_POST['password']) . "',";
    $tsqlstr .= ii_cfname('email') . "='" . ii_left(ii_cstr($_POST['email']), 50) . "',
    " . ii_cfname('city') . "='" . ii_left(ii_cstr($_POST['city']), 50) . "',
    " . ii_cfname('sex') . "='" . ii_left(ii_cstr($_POST['sex']), 50) . "',
    " . ii_cfname('old') . "='" . ii_left(ii_cstr($_POST['old']), 50) . "',
    " . ii_cfname('name') . "='" . ii_left(ii_cstr($_POST['name']), 50) . "',
    " . ii_cfname('qq') . "='" . ii_left(ii_cstr($_POST['qq']), 50) . "',
    " . ii_cfname('msn') . "='" . ii_left(ii_cstr($_POST['msn']), 50) . "',
    " . ii_cfname('phone') . "='" . ii_left(ii_cstr($_POST['phone']), 50) . "',
    " . ii_cfname('homepage') . "='" . ii_left(ii_cstr($_POST['homepage']), 255) . "',
    " . ii_cfname('code') . "='" . ii_left(ii_cstr($_POST['code']), 50) . "',
    " . ii_cfname('address') . "='" . ii_left(ii_cstr($_POST['address']), 255) . "',
    " . ii_cfname('emoney') . "=" . ii_get_num($_POST['emoney']) . ",
    " . ii_cfname('integral') . "=" . ii_get_num($_POST['integral']) . ",
    " . ii_cfname('utype') . "=" . ii_get_num($_POST['utype']) . ",
    " . ii_cfname('lock') . "=" . ii_get_num($_POST['lock']) . ",
    " . ii_cfname('forum_admin') . "=" . ii_get_num($_POST['forum_admin']) . ",
    " . ii_cfname('face') . "=" . ii_get_num($_POST['face']) . ",
    " . ii_cfname('face_u') . "=" . ii_get_num($_POST['face_u']) . ",
    " . ii_cfname('face_url') . "='" . ii_left(ii_cstr($_POST['face_url']), 255) . "',
    " . ii_cfname('face_width') . "=" . ii_get_num($_POST['face_width']) . ",
    " . ii_cfname('face_height') . "=" . ii_get_num($_POST['face_height']) . ",
    " . ii_cfname('sign') . "='" . ii_left(ii_cstr($_POST['sign']), 255) . "'
    where $nidfield=$tid";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs) jtbc_cms_admin_msg(ii_itake('global.lng_public.edit_succeed', 'lng'), $tbackurl, 1);
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.edit_failed', 'lng'), $tbackurl, 1);
  }
  else
  {
    jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
  }
}

function jtbc_cms_admin_manage_action()
{
  global $ndatabase, $nidfield, $nfpre, $ncontrol;
  switch($_GET['action'])
  {
    case 'add':
      jtbc_cms_admin_manage_adddisp();
      break;
    case 'edit':
      jtbc_cms_admin_manage_editdisp();
      break;
    case 'delete':
      jtbc_cms_admin_deletedisp($ndatabase, $nidfield);
      break;
    case 'control':
      jtbc_cms_admin_controldisp($ndatabase, $nidfield, $nfpre, $ncontrol);
      break;
  }
}

function jtbc_cms_admin_manage_add()
{
  $tmpstr = ii_ireplace('manage.add', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage_edit()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tid = ii_get_num($_GET['id']);
  $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('manage.edit', 'tpl');
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

function jtbc_cms_admin_manage_list()
{
  global $conn;
  global $ngenre, $npagesize;
  global $ndatabase, $nidfield, $nfpre;
  $toffset = ii_get_num($_GET['offset']);
  $search_field = ii_get_safecode($_GET['field']);
  $search_keyword = ii_get_safecode($_GET['keyword']);
  $tmpstr = ii_itake('manage.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tary = ii_itake('sel_group.all', 'sel', 1);
  if (is_array($tary))
  {
    foreach ($tary as $key => $val)
    {
      $thspan = 'group' . $key;
      $tmptstr = str_replace('{$topic}', $val, $tmpastr);
      $tmptstr = str_replace('{$ahref}', '?keyword=' . $key . '&field=utype&hspan=' . $thspan, $tmptstr);
      $tmptstr = str_replace('{$hspan}', $thspan, $tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmprstr = '';
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_idb}');
  $tsqlstr = "select * from $ndatabase where $nidfield>0";
  if ($search_field == 'username') $tsqlstr .= " and " . ii_cfname('username') . " like '%" . $search_keyword . "%'";
  if ($search_field == 'lock') $tsqlstr .= " and " . ii_cfname('lock') . "=" . ii_get_num($search_keyword);
  if ($search_field == 'utype') $tsqlstr .= " and " . ii_cfname('utype') . "=" . ii_get_num($search_keyword);
  if ($search_field == 'id') $tsqlstr .= " and $nidfield=" . ii_get_num($search_keyword);
  $tsqlstr .= " order by $ndatabase." . ii_cfname('time') . " desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $nidfield;
  $tcp -> sqlstr = $tsqlstr;
  $tcp -> offset = $toffset;
  $tcp -> pagesize = $npagesize;
  $tcp -> init();
  $trsary = $tcp -> get_rs_array();
  $font_disabled = ii_itake('global.tpl_config.font_disabled', 'tpl');
  if (is_array($trsary))
  {
    foreach($trsary as $trs)
    {
      $tusername = ii_htmlencode($trs[ii_cfname('username')]);
      if ($trs[ii_cfname('lock')] == 1) $tusername = str_replace('{$explain}', $tusername, $font_disabled);
      $tmptstr = str_replace('{$username}', $tusername, $tmpastr);
      $tmptstr = str_replace('{$usernamestr}', ii_encode_scripts(ii_htmlencode($trs[ii_cfname('username')])), $tmptstr);
      $tmptstr = str_replace('{$email}', ii_htmlencode($trs[ii_cfname('email')]), $tmptstr);
      $tmptstr = str_replace('{$sex}', ii_itake('global.sel_sex.' . ii_get_num($trs[ii_cfname('sex')]), 'sel'), $tmptstr);
      $tmptstr = str_replace('{$old}', ii_htmlencode($trs[ii_cfname('old')]), $tmptstr);
      $tmptstr = str_replace('{$time}', ii_get_date($trs[ii_cfname('time')]), $tmptstr);
      $tmptstr = str_replace('{$group}', ii_itake('sel_group.' . ii_get_num($trs[ii_cfname('utype')]), 'sel'), $tmptstr);
      $tmptstr = str_replace('{$id}', ii_get_num($trs[$nidfield]), $tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace('{$cpagestr}', $tcp -> get_pagestr(), $tmpstr);
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function jtbc_cms_admin_manage()
{
  switch($_GET['type'])
  {
    case 'add':
      return jtbc_cms_admin_manage_add();
      break;
    case 'edit':
      return jtbc_cms_admin_manage_edit();
      break;
    case 'list':
      return jtbc_cms_admin_manage_list();
      break;
    default:
      return jtbc_cms_admin_manage_list();
      break;
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
