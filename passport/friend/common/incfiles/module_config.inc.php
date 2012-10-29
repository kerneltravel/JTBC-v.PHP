<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function jtbc_cms_module_adddisp()
{
  $tbackurl = $_GET['backurl'];
  $tname = ii_get_safecode($_POST['name']);
  if (!ap_check_isuser($tname)) mm_imessage(ii_itake('manage.add_error1', 'lng'), '-1');
  global $conn;
  global $friend_max, $nusername;
  global $ndatabase, $nidfield, $nfpre;
  $tsqlstr = "select count($nidfield) from $ndatabase where " . ii_cfname('username') . "='$nusername'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if (ii_get_num($trs[0]) >= ii_get_num($friend_max)) mm_imessage(ii_ireplace('manage.add_error2', 'lng'), '-1');
  else
  {
    $tsqlstr = "insert into $ndatabase (
    " . ii_cfname('name') . ",
    " . ii_cfname('username') . ",
    " . ii_cfname('time') . "
    ) values (
    '" . ii_left(ii_cstr($_POST['name']), 50) . "',
    '$nusername',
    '" . ii_now() . "'
    )";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs) mm_client_redirect($tbackurl);
    else mm_imessage(ii_itake('global.lng_public.sudd', 'lng'), '-1');
  }
}

function jtbc_cms_module_controldisp()
{
  global $nusername;
  global $ndatabase, $nidfield, $nfpre;
  $tbackurl = $_GET['backurl'];
  $tcid = ii_get_safecode($_POST['sel_ids']);
  $totsql = " and " . ii_cfname('username') . "='$nusername'";
  mm_dbase_delete($ndatabase, $nidfield, $tcid, $totsql);
  mm_client_redirect($tbackurl);
}

function jtbc_cms_module_action()
{
  switch($_GET['action'])
  {
    case 'add':
      jtbc_cms_module_adddisp();
      break;
    case 'control':
      jtbc_cms_module_controldisp();
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
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('username') . "='$nusername' order by " . ii_cfname('time') . " desc";
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

function jtbc_cms_module()
{
  switch(mm_ctype($_GET['type']))
  {
    case 'list':
      return jtbc_cms_module_list();
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
