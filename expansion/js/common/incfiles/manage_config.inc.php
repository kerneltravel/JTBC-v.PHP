<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
jtbc_cms_admin_init();
$nsearch = 'topic,id';
$ncontrol = 'select,delete';

function pp_manage_navigation()
{
  return ii_ireplace('manage.navigation', 'tpl');
}

function jtbc_cms_admin_manage_adddisp()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tbackurl = $_GET['backurl'];
  $tsqlstr = "insert into $ndatabase (
  " . ii_cfname('topic') . ",
  " . ii_cfname('content') . ",
  " . ii_cfname('retimevalue') . ",
  " . ii_cfname('retimetype') . ",
  " . ii_cfname('retime') . ",
  " . ii_cfname('time') . "
  ) values (
  '" . ii_left(ii_cstr($_POST['topic']), 50) . "',
  '" . ii_left(ii_cstr($_POST['content']), 100000) . "',
  " . ii_get_num($_POST['retimevalue']) . ",
  " . ii_get_num($_POST['retimetype']) . ",
  '" . ii_now() . "',
  '" . ii_get_date(ii_cstr($_POST['time'])) . "'
  )";
  $trs = ii_conn_query($tsqlstr, $conn);
  if ($trs) jtbc_cms_admin_msg(ii_itake('global.lng_public.add_succeed', 'lng'), $tbackurl, 1);
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.add_failed', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_manage_editdisp()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tid = ii_get_num($_GET['id']);
  $tbackurl = $_GET['backurl'];
  $tsqlstr = "update $ndatabase set
  " . ii_cfname('topic') . "='" . ii_left(ii_cstr($_POST['topic']), 50) . "',
  " . ii_cfname('content') . "='" . ii_left(ii_cstr($_POST['content']), 100000) . "',
  " . ii_cfname('retimevalue') . "=" . ii_get_num($_POST['retimevalue']) . ",
  " . ii_cfname('retimetype') . "=" . ii_get_num($_POST['retimetype']) . ",
  " . ii_cfname('retime') . "='" . ii_get_date(ii_cstr($_POST['retime'])) . "',
  " . ii_cfname('time') . "='" . ii_get_date(ii_cstr($_POST['time'])) . "'
  where $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  if ($trs) jtbc_cms_admin_msg(ii_itake('global.lng_public.edit_succeed', 'lng'), $tbackurl, 1);
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.edit_failed', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_manage_createjsdisp()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  $tid = ii_get_num($_GET['id']);
  $tbackurl = $_GET['backurl'];
  $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    global $njspath;
    $tjspath = $njspath . $trs[ii_cfname('topic')] . '.' . $tid . '.js';
    $tjscontent = pp_encode2js(ii_creplace($trs[ii_cfname('content')]));
    if (file_put_contents($tjspath, $tjscontent))
    {
      $tsqlstr = "update $ndatabase set " . ii_cfname('retime') . "='" . ii_now() . "'";
      @ii_conn_query($tsqlstr, $conn);
      jtbc_cms_admin_msg(ii_itake('global.lng_public.succeed', 'lng'), $tbackurl, 1);
    }
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.failed', 'lng'), $tbackurl, 1);

  }
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.failed', 'lng'), $tbackurl, 1);
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
    case 'createjs':
      jtbc_cms_admin_manage_createjsdisp();
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
  $tsqlstr = "select * from $ndatabase where $nidfield>0";
  if ($search_field == 'topic') $tsqlstr .= " and " . ii_cfname('topic') . " like '%" . $search_keyword . "%'";
  if ($search_field == 'id') $tsqlstr .= " and $nidfield=" . ii_get_num($search_keyword);
  $tsqlstr .= " order by " . ii_cfname('time') . " desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $nidfield;
  $tcp -> sqlstr = $tsqlstr;
  $tcp -> offset = $toffset;
  $tcp -> pagesize = $npagesize;
  $tcp -> init();
  $trsary = $tcp -> get_rs_array();
  if (!(ii_isnull($search_keyword)) && $search_field == 'topic') $font_red = ii_itake('global.tpl_config.font_red', 'tpl');
  if (is_array($trsary))
  {
    foreach($trsary as $trs)
    {
      $ttopic = ii_htmlencode($trs[ii_cfname('topic')]);
      if (isset($font_red))
      {
        $font_red = str_replace('{$explain}', $search_keyword, $font_red);
        $ttopic = str_replace($search_keyword, $font_red, $ttopic);
      }
      $tmptstr = str_replace('{$topic}', $ttopic, $tmpastr);
      $tmptstr = str_replace('{$topicstr}', ii_encode_scripts(ii_htmlencode($trs[ii_cfname('topic')])), $tmptstr);
      $tmptstr = str_replace('{$time}', ii_get_date($trs[ii_cfname('time')]), $tmptstr);
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
