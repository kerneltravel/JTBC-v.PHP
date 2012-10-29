<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
define('AP_SUPPORT_REVIEW_GENRE', 'support/review');

function ap_review_output_note($keyword, $fid, $topx)
{
  global $conn;
  $tkeyword = ii_get_safecode($keyword);
  $tfid = ii_get_num($fid);
  $ttopx = ii_get_num($topx);
  if ($ttopx == 0) $ttopx = 5;
  $tdatabase = mm_cndatabase(ii_cvgenre(AP_SUPPORT_REVIEW_GENRE));
  $tidfield = mm_cnidfield(ii_cvgenre(AP_SUPPORT_REVIEW_GENRE));
  $tfpre = mm_cnfpre(ii_cvgenre(AP_SUPPORT_REVIEW_GENRE));
  $tsqlstr = "select * from $tdatabase where " . ii_cfnames($tfpre, 'keyword') . "='$tkeyword' and " . ii_cfnames($tfpre, 'fid') . "=$tfid order by " . ii_cfnames($tfpre, 'time') . " desc limit 0,$ttopx";
  $trs = ii_conn_query($tsqlstr, $conn);
  $tmpstr = ii_itake('global.' . AP_SUPPORT_REVIEW_GENRE . ':api.output_note', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  while ($trow = ii_conn_fetch_array($trs))
  {
    $tmptstr = $tmpastr;
    foreach ($trow as $key => $val)
    {
      $tkey = ii_get_lrstr($key, '_', 'rightr');
      $GLOBALS['RS_' . $tkey] = $val;
      $tmptstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmptstr);
    }
    $tmptstr = ii_creplace($tmptstr);
    $tmprstr .= $tmptstr;
  }
  $tmpstr = str_replace(JTBC_CINFO, $tmprstr, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function ap_review_input_form($keyword, $fid)
{
  $tmpstr = ii_itake('global.' . AP_SUPPORT_REVIEW_GENRE . ':api.input_form', 'tpl');
  $tmpstr = str_replace('{$keyword}', urlencode($keyword), $tmpstr);
  $tmpstr = str_replace('{$fid}', urlencode($fid), $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
