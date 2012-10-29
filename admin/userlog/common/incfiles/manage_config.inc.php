<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
$ncontrol = 'select,delete';
$nsearch = 'username,id';

function pp_manage_navigation()
{
  return ii_ireplace('manage.navigation', 'tpl');
}

function jtbc_cms_admin_manage_action()
{
  global $ndatabase, $nidfield, $nfpre, $ncontrol;
  switch($_GET['action'])
  {
    case 'delete':
      jtbc_cms_admin_deletedisp($ndatabase, $nidfield);
      break;
    case 'control':
      jtbc_cms_admin_controldisp($ndatabase, $nidfield, $nfpre, $ncontrol);
      break;
  }
}

function jtbc_cms_admin_manage_list()
{
  global $ndatabase, $nidfield, $nfpre, $npagesize;
  $toffset = ii_get_num($_GET['offset']);
  $search_field = ii_get_safecode($_GET['field']);
  $search_keyword = ii_get_safecode($_GET['keyword']);
  $tmpstr = ii_itake('manage.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $ndatabase where $nidfield>0";
  if ($search_field == 'username') $tsqlstr .= " and " . ii_cfname('name') . " like '%" . $search_keyword . "%'";
  if ($search_field == 'islogin') $tsqlstr .= " and " . ii_cfname('islogin') . "=" . ii_get_num($search_keyword);
  if ($search_field == 'id') $tsqlstr .= " and $nidfield=" . ii_get_num($search_keyword);
  $tsqlstr .= " order by $nidfield desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $nidfield;
  $tcp -> sqlstr = $tsqlstr;
  $tcp -> offset = $toffset;
  $tcp -> pagesize = $npagesize;
  $tcp -> init();
  $trsary = $tcp -> get_rs_array();
  $font_disabled = ii_itake('global.tpl_config.font_disabled', 'tpl');
  $tyesstr = ii_itake('global.lng_config.yes', 'lng');
  $tnostr = ii_itake('global.lng_config.no', 'lng');
  if (is_array($trsary))
  {
    foreach($trsary as $trs)
    {
      $tislogin = $tyesstr;
      $tusername = $trs[ii_cfname('name')];
      if ($trs[ii_cfname('islogin')] == 0)
      {
        $tusername = str_replace('{$explain}', $tusername, $font_disabled);
        $tislogin = $tnostr;
      }
      $tmptstr = str_replace('{$username}', $tusername, $tmpastr);
      $tmptstr = str_replace('{$usernamestr}', ii_encode_scripts(ii_htmlencode($trs[ii_cfname('name')])), $tmptstr);
      $tmptstr = str_replace('{$time}', ii_get_date($trs[ii_cfname('time')]), $tmptstr);
      $tmptstr = str_replace('{$ip}', $trs[ii_cfname('ip')], $tmptstr);
      $tmptstr = str_replace('{$islogin}', $tislogin, $tmptstr);
      $tmptstr = str_replace('{$id}', $trs[$nidfield], $tmptstr);
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
