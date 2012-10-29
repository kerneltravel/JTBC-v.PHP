<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function jtbc_cms_module_list()
{
  global $variable;
  global $ngenre, $npagesize, $nlisttopx;
  global $nsearch_genre, $nsearch_field;
  $tshgenre = ii_get_safecode($_GET['genre']);
  $tshfield = ii_get_safecode($_GET['field']);
  if (!(ii_cinstr($nsearch_genre, $tshgenre, ',') && ii_cinstr($nsearch_field, $tshfield, ','))) mm_imessage(ii_itake('module.condition_error', 'lng'), -1);
  $tshkeyword = ii_get_safecode($_GET['keyword']);
  $toffset = ii_get_num($_GET['offset']);
  if (ii_isnull($tshkeyword)) mm_imessage(ii_itake('module.keyword_error', 'lng'), -1);
  $tshkeywords = explode(' ', $tshkeyword);
  if (count($tshkeywords) > 5) mm_imessage(ii_itake('module.complex_error', 'lng'), -1);
  $tbaseurl = ii_get_actual_route($tshgenre) . '/';
  $turltype = ii_get_num($variable[ii_cvgenre($tshgenre) . '.nurltype']);
  $tcreatefolder = $variable[ii_cvgenre($tshgenre) . '.ncreatefolder'];
  $tcreatefiletype = $variable[ii_cvgenre($tshgenre) . '.ncreatefiletype'];
  $tdatabase = $variable[ii_cvgenre($tshgenre) . '.ndatabase'];
  $tidfield = $variable[ii_cvgenre($tshgenre) . '.nidfield'];
  $tfpre = $variable[ii_cvgenre($tshgenre) . '.nfpre'];
  $font_red = ii_itake('global.tpl_config.font_red', 'tpl');
  $tmpstr = ii_itake('module.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'hidden') . "=0";
  foreach ($tshkeywords as $key => $val)
  {
    $tsqlstr .= " and " . ii_cfnames($tfpre, $tshfield) . " like '%" . $val . "%'";
  }
  $tsqlstr .= " order by " . ii_cfnames($tfpre, 'time') . " desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $tidfield;
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
      $tfshkeyword = '';
      $tmptstr = $tmpastr;
      if ($tshfield == 'topic') $tfshkeyword = str_replace('{$explain}', $tshkeyword, $font_red);
      $ttopic = ii_htmlencode($trs[ii_cfnames($tfpre, 'topic')]);
      $tmptstr = str_replace('{$topicstr}', $ttopic, $tmpastr);
      if (!ii_isnull($tfshkeyword)) $ttopic = str_replace($tshkeyword, $tfshkeyword, $ttopic);
      $tmptstr = str_replace('{$topic}', $ttopic, $tmptstr);
      $tmptstr = str_replace('{$time}', ii_get_date($trs[ii_cfnames($tfpre, 'time')]), $tmptstr);
      $tmptstr = str_replace('{$count}', ii_get_num($trs[ii_cfnames($tfpre, 'count')]), $tmptstr);
      $tmptstr = str_replace('{$id}', ii_get_num($trs[$tidfield]), $tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = str_replace('{$baseurl}', $tbaseurl, $tmpstr);
  $tmpstr = str_replace('{$urltype}', $turltype, $tmpstr);
  $tmpstr = str_replace('{$createfolder}', $tcreatefolder, $tmpstr);
  $tmpstr = str_replace('{$createfiletype}', $tcreatefiletype, $tmpstr);
  $tmpstr = str_replace('{$cpagestr}', $tcp -> get_pagestr(), $tmpstr);
  $tmpstr = str_replace('{$genre}', $ngenre, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function jtbc_cms_module_index()
{
  global $ngenre;
  $tmpstr = ii_itake('module.index', 'tpl');
  if (!ii_isnull($tmpstr)) return $tmpstr;
  else return jtbc_cms_module_list();
}

function jtbc_cms_module()
{
  switch($_GET['type'])
  {
    case 'list':
      return jtbc_cms_module_list();
      break;
    case 'index':
      return jtbc_cms_module_index();
      break;
    default:
      return jtbc_cms_module_index();
      break;
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
