<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function jtbc_cms_module_senddisp()
{
  global $ctype;
  $ctype = 'send';
  global $message_max;
  global $Err;
  $tbackurl = $_GET['backurl'];
  $trecipients = ii_get_safecode($_POST['recipients']);
  $tuserstate = ap_check_isuser($trecipients);
  if ($tuserstate == 0) $Err[count($Err)] = ii_itake('manage.recipients_error', 'lng');
  if ($tuserstate == 2) $Err[count($Err)] = ii_itake('manage.recipients_error2', 'lng');
  if (ap_count_user_message($trecipients) >= $message_max) $Err[count($Err)] = ii_ireplace('manage.recipients_error_max', 'lng');
  $tckstr = 'topic:' . ii_itake('global.lng_config.topic', 'lng') . ',content:' . ii_itake('global.lng_config.content', 'lng');
  $tary = explode(',', $tckstr);
  foreach ($tary as $key => $val)
  {
    $tvalary = explode(':', $val);
    if (ii_isnull($_POST[$tvalary[0]])) $Err[count($Err)] = str_replace('[]', '[' . $tvalary[1] . ']', ii_itake('global.lng_error.insert_empty', 'lng'));
  }
  if (!is_array($Err))
  {
    global $conn;
    global $ndatabase, $nidfield, $nfpre;
    global $nusername;
    $tsqlstr = "insert into $ndatabase (
    " . ii_cfname('topic') . ",
    " . ii_cfname('content') . ",
    " . ii_cfname('time') . ",
    " . ii_cfname('len') . ",
    " . ii_cfname('addresser') . ",
    " . ii_cfname('recipients') . "
    ) values (
    '" . ii_left(ii_cstr($_POST['topic']), 50) . "',
    '" . ii_left(ii_cstr($_POST['content']), 1000) . "',
    '" . ii_now() . "',
    " . strlen(ii_left(ii_cstr($_POST['content']), 1000)) . ",
    '$nusername',
    '$trecipients'
    )";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs) mm_imessage(ii_itake('manage.send_succeed', 'lng'), $tbackurl);
    else mm_imessage(ii_itake('global.lng_public.sudd', 'lng'), '-1');
  }
}

function jtbc_cms_module_controldisp()
{
  global $nusername;
  global $ndatabase, $nidfield, $nfpre;
  $tbackurl = $_GET['backurl'];
  $tcid = ii_get_safecode($_POST['sel_ids']);
  $totsql = " and " . ii_cfname('recipients') . "='$nusername'";
  mm_dbase_delete($ndatabase, $nidfield, $tcid, $totsql);
  mm_client_redirect($tbackurl);
}

function jtbc_cms_module_deletedisp()
{
  $tbackurl = $_GET['backurl'];
  global $nusername;
  global $ndatabase, $nidfield, $nfpre;
  $tcid = ii_get_num($_GET['id']);
  $totsql = " and " . ii_cfname('recipients') . "='$nusername'";
  mm_dbase_delete($ndatabase, $nidfield, $tcid, $totsql);
  mm_client_redirect($tbackurl);
}

function jtbc_cms_module_action()
{
  switch($_GET['action'])
  {
    case 'send':
      jtbc_cms_module_senddisp();
      break;
    case 'control':
      jtbc_cms_module_controldisp();
      break;
    case 'delete':
      jtbc_cms_module_deletedisp();
      break;
  }
}

function jtbc_cms_module_list()
{
  global $conn;
  global $nusername, $nlisttopx, $npagesize;
  global $ndatabase, $nidfield, $nfpre;
  $toffset = ii_get_num($_GET['offset']);
  $tmpstr = ii_itake('module.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('recipients') . "='$nusername' order by " . ii_cfname('time') . " desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $nidfield;
  $tcp -> pagesize = $npagesize;
  $tcp -> rslimit = $nlisttopx;
  $tcp -> sqlstr = $tsqlstr;
  $tcp -> offset = $toffset;
  $tcp -> init();
  $trsary = $tcp -> get_rs_array();
  if (is_array($trsary))
  {
    foreach($trsary as $trs)
    {
      $tmptstr = $tmpastr;
      foreach ($trs as $key => $val)
      {
        $tkey = ii_get_lrstr($key, '_', 'rightr');
        $GLOBALS['RS_' . $tkey] = $val;
        $tmptstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmptstr);
      }
      $tmptstr = str_replace('{$id}', $trs[$nidfield], $tmptstr);
      $tmptstr = ii_creplace($tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = str_replace('{$cpagestr}', $tcp -> get_pagestr(), $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function jtbc_cms_module_listb()
{
  global $conn;
  global $nusername, $nlisttopx, $npagesize;
  global $ndatabase, $nidfield, $nfpre;
  $toffset = ii_get_num($_GET['offset']);
  $tmpstr = ii_itake('module.listb', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('addresser') . "='$nusername' order by " . ii_cfname('time') . " desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $nidfield;
  $tcp -> pagesize = $npagesize;
  $tcp -> rslimit = $nlisttopx;
  $tcp -> sqlstr = $tsqlstr;
  $tcp -> offset = $toffset;
  $tcp -> init();
  $trsary = $tcp -> get_rs_array();
  if (is_array($trsary))
  {
    foreach($trsary as $trs)
    {
      $tmptstr = $tmpastr;
      foreach ($trs as $key => $val)
      {
        $tkey = ii_get_lrstr($key, '_', 'rightr');
        $GLOBALS['RS_' . $tkey] = $val;
        $tmptstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmptstr);
      }
      $tmptstr = str_replace('{$id}', $trs[$nidfield], $tmptstr);
      $tmptstr = ii_creplace($tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = str_replace('{$cpagestr}', $tcp -> get_pagestr(), $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function jtbc_cms_module_send()
{
  return ii_ireplace('module.send', 'tpl');
}

function jtbc_cms_module_detail()
{
  global $conn;
  global $nusername;
  global $ndatabase, $nidfield, $nfpre;
  $tid = ii_get_num($_GET['id']);
  $tsqlstr = "select * from $ndatabase where (" . ii_cfname('recipients') . "='$nusername' or " . ii_cfname('addresser') . "='$nusername') and $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    if ($trs[ii_cfname('recipients')] == $nusername && $trs[ii_cfname('read')] == 0)
    {
      $tsqlstr = "update $ndatabase set " . ii_cfname('read') . "=1 where $nidfield=$tid";
      ii_conn_query($tsqlstr, $conn);
    }
    $tmpstr = ii_itake('module.detail', 'tpl');
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
  else mm_imessage(ii_itake('global.lng_public.sudd', 'lng'));
}

function jtbc_cms_module()
{
  switch(mm_ctype($_GET['type']))
  {
    case 'list':
      return jtbc_cms_module_list();
      break;
    case 'listb':
      return jtbc_cms_module_listb();
      break;
    case 'send':
      return jtbc_cms_module_send();
      break;
    case 'detail':
      return jtbc_cms_module_detail();
      break;
    default:
      return jtbc_cms_module_list();
      break;
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
