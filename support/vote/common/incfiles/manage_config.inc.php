<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
jtbc_cms_admin_init();
$nsearch = 'topic,id';
$ncontrol = 'select,lock';
$njspath = 'common/js/';

function pp_manage_navigation()
{
  return ii_ireplace('manage.navigation', 'tpl');
}

function pp_vote_type($strers)
{
  if ($strers == 0) return 'radio';
  else return 'checkbox';
}

function jtbc_cms_admin_manage_adddisp()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $ngenre, $nlng;
  $tbackurl = $_GET['backurl'];
  $ttopic = ii_cstr($_POST['topic']);
  $tcount = ii_get_num($_POST['count']);
  if ($tcount <= 0) mm_client_alert(ii_itake('manage.add_count_error', 'lng'), $tbackurl);
  if (!(ii_isnull($ttopic)))
  {
    $tsqlstr = "insert into $ndatabase (
    " . ii_cfname('topic') . ",
    " . ii_cfname('type') . ",
    " . ii_cfname('count') . ",
    " . ii_cfname('starttime') . ",
    " . ii_cfname('endtime') . ",
    " . ii_cfname('lock') . ",
    " . ii_cfname('time') . "
    ) values (
    '" . ii_left($ttopic, 50) . "',
    " . ii_get_num($_POST['type']) . ",
    $tcount,
    '" . ii_get_date($_POST['starttime']) . "',
    '" . ii_get_date($_POST['endtime']) . "',
    " . ii_get_num($_POST['lock']) . ",
    '" . ii_now() . "'
    )";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs)
    {
      $upfid = ii_conn_insert_id();
      $tdatabase = mm_cndatabase(ii_cvgenre($ngenre), 'data');
      $tidfield = mm_cnidfield(ii_cvgenre($ngenre), 'data');
      $tfpre = mm_cnfpre(ii_cvgenre($ngenre), 'data');
      for ($i = 1; $i <= $tcount; $i ++)
      {
        $tsqlstr = "insert into $tdatabase (
        " . ii_cfnames($tfpre, 'topic') . ",
        " . ii_cfnames($tfpre, 'count') . ",
        " . ii_cfnames($tfpre, 'fid') . ",
        " . ii_cfnames($tfpre, 'vid') . "
        ) values (
        '" . ii_left(ii_cstr($_POST['option' . $i]), 50) . "',
        " . ii_get_num($_POST['count' . $i]) . ",
        $upfid,
        $i
        )";
        ii_conn_query($tsqlstr, $conn);
      }
      jtbc_cms_admin_msg(ii_itake('global.lng_public.add_succeed', 'lng'), $tbackurl, 1);
    }
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.add_failed', 'lng'), $tbackurl, 1);
  }
  else
  {
    jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
  }
}

function jtbc_cms_admin_manage_editdisp()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $ngenre;
  $tid = ii_get_num($_GET['id']);
  $tbackurl = $_GET['backurl'];
  $ttopic = ii_cstr($_POST['topic']);
  $tcount = ii_get_num($_POST['count']);
  if (!(ii_isnull($ttopic)))
  {
    $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
    $trs = ii_conn_query($tsqlstr, $conn);
    $trs = ii_conn_fetch_array($trs);
    if ($trs)
    {
      $tycount = ii_get_num($trs[ii_cfname('count')]);
      $tsqlstr = "update $ndatabase set
      " . ii_cfname('topic') . "='" . ii_left($ttopic, 50) . "',
      " . ii_cfname('type') . "=" . ii_get_num($_POST['type']) . ",
      " . ii_cfname('count') . "=$tcount,
      " . ii_cfname('starttime') . "='" . ii_get_date($_POST['starttime']) . "',
      " . ii_cfname('endtime') . "='" . ii_get_date($_POST['endtime']) . "',
      " . ii_cfname('lock') . "=" . ii_get_num($_POST['lock']) . ",
      " . ii_cfname('time') . "='" . ii_get_date($_POST['time']) . "'
      where $nidfield=$tid";
      $trs = ii_conn_query($tsqlstr, $conn);
      if ($trs)
      {
        $tdatabase = mm_cndatabase(ii_cvgenre($ngenre), 'data');
        $tidfield = mm_cnidfield(ii_cvgenre($ngenre), 'data');
        $tfpre = mm_cnfpre(ii_cvgenre($ngenre), 'data');
        for($i = 1; $i <= $tcount; $i ++)
        {
          $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'fid') . "=$tid and " . ii_cfnames($tfpre, 'vid') . "=$i";
          $trs = ii_conn_query($tsqlstr, $conn);
          $trs = ii_conn_fetch_array($trs);
          if ($trs) $tsqlstr2 = "update $tdatabase set " . ii_cfnames($tfpre, 'topic') . "='" . ii_left(ii_cstr($_POST['option' . $i]), 50) . "'," . ii_cfnames($tfpre, 'count') . "=" . ii_get_num($_POST['count' . $i]) . " where " . ii_cfnames($tfpre, 'fid') . "=$tid and " . ii_cfnames($tfpre, 'vid') . "=$i";
          else $tsqlstr2 = "insert into $tdatabase (" . ii_cfnames($tfpre, 'topic') . "," . ii_cfnames($tfpre, 'count') . "," . ii_cfnames($tfpre, 'fid') . "," . ii_cfnames($tfpre, 'vid') . ") values ('" . ii_left(ii_cstr($_POST['option' . $i]), 50) . "'," . ii_get_num($_POST['count' . $i]) . ",$tid,$i)";
          ii_conn_query($tsqlstr2, $conn);
        }
        if ($tycount > $tcount)
        {
          $tmyvid = '';
          for($i = ($tcount + 1); $i <= $tycount; $i ++)
          {
            $tmyvid .= $i . ',';
          }
          if (!ii_isnull($tmyvid))
          {
            $tmyvid = ii_left($tmyvid, strlen($tmyvid) - 1);
            mm_dbase_delete($tdatabase, ii_cfnames($tfpre, 'vid'), $tmyvid);
          }
        }
        jtbc_cms_admin_msg(ii_itake('global.lng_public.edit_succeed', 'lng'), $tbackurl, 1);
      }
      else jtbc_cms_admin_msg(ii_itake('global.lng_public.edit_failed', 'lng'), $tbackurl, 1);
    }
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
  }
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_manage_createjsdisp()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre;
  global $ngenre, $njspath;
  $tjsname = stripslashes(ii_cstr($_POST['jsname']));
  if (ii_isnull($tjsname)) $tjsname = 'noname';
  $tjstpl = ii_cstr($_POST['jstpl']);
  $tid = ii_get_num($_GET['id']);
  $tbackurl = $_GET['backurl'];
  $tmpstr = ii_itake('vote.' . $tjstpl, 'tpl');
  if (!ii_isnull($tmpstr))
  {
    $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
    $trs = ii_conn_query($tsqlstr, $conn);
    $trs = ii_conn_fetch_array($trs);
    if ($trs)
    {
      $tmpstr = str_replace('{$vtopic}', ii_htmlencode($trs[ii_cfname('topic')]), $tmpstr);
      $tmpstr = str_replace('{$type}', ii_htmlencode($trs[ii_cfname('type')]), $tmpstr);
      $tmpstr = str_replace('{$vid}', $trs[$nidfield], $tmpstr);
    }
    $ndatabase = mm_cndatabase(ii_cvgenre($ngenre), 'data');
    $nidfield = mm_cnidfield(ii_cvgenre($ngenre), 'data');
    $nfpre = mm_cnfpre(ii_cvgenre($ngenre), 'data');
    $tsqlstr = "select * from $ndatabase where " . ii_cfname('fid') . "=$tid order by " . ii_cfname('vid') . " asc";
    $tmpastr = ii_ctemplate($tmpstr, '{@}');
    $tmprstr = '';
    $trs = ii_conn_query($tsqlstr, $conn);
    while ($trow = ii_conn_fetch_array($trs))
    {
      $tmptstr = $tmpastr;
      foreach ($trow as $key => $val)
      {
        $tkey = ii_get_lrstr($key, '_', 'rightr');
        $GLOBALS['RS_' . $tkey] = $val;
        $tmptstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmptstr);
      }
      $tmptstr = str_replace('{$id}', $trow[$nidfield], $tmptstr);
      $tmprstr .= $tmptstr;
    }
    $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    $tmpstr = ii_encode_newline($tmpstr);
    $toutstr = '';
    $tary = explode(CRLF, $tmpstr);
    foreach($tary as $key => $val)
    {
      if (!ii_isnull($val)) $toutstr .= 'document.write(\'' . addslashes($val) . '\');' . CRLF;
    }
    if (file_put_contents($njspath . $tjsname . '.js', $toutstr)) jtbc_cms_admin_msg(ii_itake('global.lng_public.succeed', 'lng'), $tbackurl, 1);
    else jtbc_cms_admin_msg(ii_itake('global.lng_public.failed', 'lng'), $tbackurl, 1);
  }
  else jtbc_cms_admin_msg(ii_itake('global.lng_public.sudd', 'lng'), $tbackurl, 1);
}

function jtbc_cms_admin_manage_deletedisp()
{
  global $ndatabase, $nidfield, $nfpre;
  global $ngenre;
  $tbackurl = $_GET['backurl'];
  $tid = ii_get_num($_GET['id']);
  mm_dbase_delete($ndatabase, $nidfield, $tid);
  $tdatabase = mm_cndatabase(ii_cvgenre($ngenre), 'data');
  $tidfield = mm_cnidfield(ii_cvgenre($ngenre), 'data');
  $tfpre = mm_cnfpre(ii_cvgenre($ngenre), 'data');
  mm_dbase_delete($tdatabase, ii_cfnames($tfpre, 'fid'), $tid);
  $tdatabase = mm_cndatabase(ii_cvgenre($ngenre), 'voter');
  $tidfield = mm_cnidfield(ii_cvgenre($ngenre), 'voter');
  $tfpre = mm_cnfpre(ii_cvgenre($ngenre), 'voter');
  mm_dbase_delete($tdatabase, ii_cfnames($tfpre, 'fid'), $tid);
  mm_client_redirect($tbackurl);
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
      jtbc_cms_admin_manage_deletedisp();
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
  global $ngenre;
  $tid = ii_get_num($_GET['id']);
  $tdatabase = mm_cndatabase(ii_cvgenre($ngenre), 'data');
  $tidfield = mm_cnidfield(ii_cvgenre($ngenre), 'data');
  $tfpre = mm_cnfpre(ii_cvgenre($ngenre), 'data');
  $tmpstr = ii_itake('manage.edit', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'fid') . "=" . $tid . " order by " . ii_cfnames($tfpre, 'vid') . " asc";
  $trs = ii_conn_query($tsqlstr, $conn);
  while ($trow = ii_conn_fetch_array($trs))
  {
    $tmptstr = str_replace('{$topic}', ii_htmlencode($trow[ii_cfnames($tfpre, 'topic')]), $tmpastr);
    $tmptstr = str_replace('{$count}', ii_get_num($trow[ii_cfnames($tfpre, 'count')], 0), $tmptstr);
    $tmptstr = str_replace('{$vid}', ii_get_num($trow[ii_cfnames($tfpre, 'vid')], 0), $tmptstr);
    $tmprstr .= $tmptstr;
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
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
  else
  {
    mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), -1);
  }
}

function jtbc_cms_admin_manage_list()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre, $npagesize;
  $toffset = ii_get_num($_GET['offset']);
  $search_field = ii_get_safecode($_GET['field']);
  $search_keyword = ii_get_safecode($_GET['keyword']);
  $tmpstr = ii_itake('manage.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $ndatabase where $nidfield>0";
  if ($search_field == 'topic') $tsqlstr .= " and " . ii_cfname('topic') . " like '%" . $search_keyword . "%'";
  if ($search_field == 'id') $tsqlstr .= " and $nidfield=" . ii_get_num($search_keyword);
  $tsqlstr .= " order by $ndatabase." . ii_cfname('time') . " desc";
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
      $tmptstr = str_replace('{$type}', ii_get_num($trs[ii_cfname('type')]), $tmptstr);
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
