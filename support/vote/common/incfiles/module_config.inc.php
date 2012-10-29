<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function jtbc_cms_module_votedisp()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $ngenre, $nusername;
  $tid = ii_get_num($_GET['id']);
  if ($_COOKIE[APP_NAME . 'vote'][strval($tid)] == 1) mm_client_alert(ii_itake('module.vote_failed', 'lng'), -1);
  $tvotes = $_POST['votes'];
  $tvotes = implode(',', $tvotes);
  if (ii_isnull($tvotes)) mm_client_alert(ii_itake('module.vote_error6', 'lng'), -1);
  $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    if ($trs[ii_cfname('lock')] == 1) mm_client_alert(ii_itake('module.vote_error2', 'lng'), -1);
    if (ii_datediff('d', $trs[ii_cfname('starttime')], ii_now()) < 0) mm_client_alert(ii_itake('module.vote_error3', 'lng'), -1);
    if (ii_datediff('d', $trs[ii_cfname('endtime')], ii_now()) > 0) mm_client_alert(ii_itake('module.vote_error4', 'lng'), -1);
    if ($trs[ii_cfname('type')] == 0)
    {
      $tvotes = ii_get_num($tvotes, 0);
      if ($tvotes == 0) mm_client_alert(ii_itake('module.vote_error0', 'lng'), -1);
    }
    else
    {
      if (!ii_cidary($tvotes)) mm_client_alert(ii_itake('module.vote_error0', 'lng'), -1);
    }
  }
  else mm_client_alert(ii_itake('module.vote_error1', 'lng'), -1);
  $tdatabase = mm_cndatabase(ii_cvgenre($ngenre), 'voter');
  $tidfield = mm_cnidfield(ii_cvgenre($ngenre), 'voter');
  $tfpre = mm_cnfpre(ii_cvgenre($ngenre), 'voter');
  $tuserip = ii_get_client_ip();
  $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'fid') . "=$tid and " . ii_cfnames($tfpre, 'ip') . "='$tuserip'";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs) mm_client_alert(ii_itake('module.vote_error5', 'lng'), -1);
  else
  {
    $tsqlstr = "insert into $tdatabase (" . ii_cfnames($tfpre, 'fid') . "," . ii_cfnames($tfpre, 'ip') . "," . ii_cfnames($tfpre, 'username') . "," . ii_cfnames($tfpre, 'data') . "," . ii_cfnames($tfpre, 'time') . ") values ($tid,'$tuserip','$nusername','$tvotes','" . ii_now() . "')";
    ii_conn_query($tsqlstr, $conn);
  }
  $tdatabase = mm_cndatabase(ii_cvgenre($ngenre), 'data');
  $tidfield = mm_cnidfield(ii_cvgenre($ngenre), 'data');
  $tfpre = mm_cnfpre(ii_cvgenre($ngenre), 'data');
  $tsqlstr = "update $tdatabase set " . ii_cfnames($tfpre, 'count') . "=" . ii_cfnames($tfpre, 'count') . "+1 where " . ii_cfnames($tfpre, 'fid') . "=$tid and $tidfield in ($tvotes)";
  $trs = ii_conn_query($tsqlstr, $conn);
  if ($trs)
  {
    setcookie(APP_NAME . 'vote[' . strval($tid) . ']', 1, time() + (60 * 60 * 24 * 365), '/');
    mm_client_alert(ii_itake('module.vote_succeed', 'lng'), -1);
  }
  else mm_client_alert(ii_itake('module.vote_error0', 'lng'), -1);
}

function jtbc_cms_module_action()
{
  switch($_GET['action'])
  {
    case 'vote':
      jtbc_cms_module_votedisp();
      break;
  }
}

jtbc_cms_module_action();

function jtbc_cms_module_view()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $ngenre;
  $tid = ii_get_num($_GET['id']);
  $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('module.view', 'tpl');
    $tmpstr = str_replace('{$vtopic}', ii_htmlencode($trs[ii_cfname('topic')]), $tmpstr);
    $tdatabase = mm_cndatabase(ii_cvgenre($ngenre), 'data');
    $tidfield = mm_cnidfield(ii_cvgenre($ngenre), 'data');
    $tfpre = mm_cnfpre(ii_cvgenre($ngenre), 'data');
    $ti = 0; $tacount = 0;
    $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'fid') . "=$tid order by " . ii_cfnames($tfpre, 'vid') . " asc";
    $trs = ii_conn_query($tsqlstr, $conn);
    while ($trow = ii_conn_fetch_array($trs))
    {
      $tary[$ti]['count'] = ii_get_num($trow[ii_cfnames($tfpre, 'count')]);
      $tary[$ti]['topic'] = ii_htmlencode($trow[ii_cfnames($tfpre, 'topic')]);
      $tacount = $tacount + ii_get_num($trow[ii_cfnames($tfpre, 'count')]);
      $ti += 1;
    }
    $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
    $tmprstr = '';
    for ($i = 0; $i <= (count($tary) - 1); $i ++)
    {
      $tmptstr = str_replace('{$topic}', $tary[$i]['topic'], $tmpastr);
      $tmptstr = str_replace('{$count}', $tary[$i]['count'], $tmptstr);
      $tmptstr = str_replace('{$per}', ii_cper($tary[$i]['count'], $tacount), $tmptstr);
      $tmprstr .= $tmptstr;
    }
    $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    return $tmpstr;
  }
}

function jtbc_cms_module()
{
  switch($_GET['type'])
  {
    case 'view':
      return jtbc_cms_module_view();
      break;
    default:
      return jtbc_cms_module_view();
      break;
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
