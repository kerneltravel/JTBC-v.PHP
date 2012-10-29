<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function jtbc_cms_module_adddisp()
{
  jtbc_cms_web_noout();
  global $variable, $ngenre;
  global $conn, $nusername;
  global $ndatabase, $nidfield, $nfpre;
  $tbackurl = $_GET['backurl'];
  $tkeyword = ii_get_safecode($_GET['keyword']);
  $tfid = ii_get_num($_GET['fid']);
  $tauthor = $nusername;
  if (ii_isnull($tauthor)) $tauthor = ii_itake('config.df_username', 'lng');
  $tcontent = ii_left(ii_cstr($_POST['content']), 500);
  if (!ii_isnull($tcontent) && $tfid != 0)
  {
    $tuserip = ii_get_client_ip();
    $ttimeout = ii_get_num($variable[ii_cvgenre($ngenre) . '.timeout'], -1);
    if ($ttimeout != -1)
    {
      $tsqlstr = "select * from $ndatabase where " . ii_cfname('authorip') . "='$tuserip' order by " . ii_cfname('time') . " desc limit 0,1";
      $trs = ii_conn_query($tsqlstr, $conn);
      $trs = ii_conn_fetch_array($trs);
      if ($trs)
      {
        if (ii_datediff('s', $trs[ii_cfname('time')], ii_now()) <= $ttimeout) mm_imessage(ii_itake('module.error0', 'lng'), '-1');
      }
    }
    $tsqlstr = "insert into $ndatabase (
    " . ii_cfname('author') . ",
    " . ii_cfname('authorip') . ",
    " . ii_cfname('content') . ",
    " . ii_cfname('time') . ",
    " . ii_cfname('keyword') . ",
    " . ii_cfname('fid') . "
    ) values (
    '$tauthor',
    '$tuserip',
    '$tcontent',
    '" . ii_now() . "',
    '$tkeyword',
    $tfid
    )";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs)
    {
      if (!ii_isnull($tbackurl)) mm_client_redirect($tbackurl);
      else mm_client_redirect('?type=list&keyword=' . urlencode($tkeyword) . '&fid=' . $tfid);
    }
    else mm_imessage(ii_itake('global.lng_public.sudd', 'lng'), '-1');
  }
  else mm_imessage(ii_itake('module.error1', 'lng'), '-1');
}

function jtbc_cms_module_action()
{
  switch($_GET['action'])
  {
    case 'add':
      jtbc_cms_module_adddisp();
      break;
  }
}

function jtbc_cms_module_list()
{
  global $conn;
  global $nlisttopx, $npagesize;
  global $ndatabase, $nidfield, $nfpre;
  $tfid = ii_get_num($_GET['fid']);
  $tkeyword = ii_get_safecode($_GET['keyword']);
  $toffset = ii_get_num($_GET['offset']);
  $tmpstr = ii_itake('module.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('keyword') . "='$tkeyword' and " . ii_cfname('hidden') . "=0 and " . ii_cfname('fid') . "=$tfid order by " . ii_cfname('time') . " desc";
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
