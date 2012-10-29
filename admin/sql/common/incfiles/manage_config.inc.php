<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
function jtbc_cms_admin_manage_run()
{
  global $conn;
  $tsqlstr = stripslashes(ii_cstr($_POST['sqlstrs']));
  if (ii_isnull($tsqlstr)) return;
  $tsqlstrary = explode(';', $tsqlstr);
  $tmpsucc = ii_itake('manage.succeed', 'lng');
  $tmpfail = ii_itake('manage.failed', 'lng');
  $font_red = ii_itake('global.tpl_config.font_red', 'tpl');
  $font_green = ii_itake('global.tpl_config.font_green', 'tpl');
  $html_kong = ii_itake('global.tpl_config.html_kong', 'tpl');
  $html_br = ii_itake('global.tpl_config.html_br', 'tpl');
  $tmprstr = '';
  if (count($tsqlstrary) > 1)
  {
    foreach($tsqlstrary as $key => $val)
    {
      if (!ii_isnull($val))
      {
        $trs = ii_conn_query($val, $conn);
        if ($trs) $tmprstr .= ii_htmlencode($val) . $html_kong . str_replace('{$explain}', $tmpsucc, $font_green) . $html_br;
        else $tmprstr .= ii_htmlencode($val) . $html_kong . str_replace('{$explain}', $tmpfail, $font_red) . $html_br;
      }
    }
    $tmpstr = $tmprstr;
  }
  else
  {
    $tsqlstr = $tsqlstrary[0];
    if (strtolower(ii_left($tsqlstr, 6)) == 'select')
    {
      if (!(strpos(strtolower($tsqlstr), 'limit'))) $tsqlstr .= ' limit 0,20';
      $trs = @ii_conn_query($tsqlstr, $conn);
      if ($trs)
      {
        $tpl_html = ii_itake('manage.table_html', 'tpl');
        $tstra = ii_ctemplate($tpl_html, '{@}');
        $tstrb = ii_ctemplate($tstra, '{@@}');
        $ti = 0; $tstrc = ''; $tstrd = ''; $tstre = '';
        while ($trow = ii_conn_fetch_array($trs))
        {
          $tstrd = ''; $tstre = '';
          foreach($trow as $key => $val)
          {
            if (!is_numeric($key))
            {
              if ($ti == 0) $tstrd .= str_replace('{$value}', str_replace('{$explain}', $html_kong . $key . $html_kong, $font_green), $tstrb);
              if (is_numeric($val) || is_string($val)) $tstre .= str_replace('{$value}', $html_kong . ii_left($val, 20) . $html_kong, $tstrb);
              else $tstre .= str_replace('{$value}', $html_kong . '*' . $html_kong, $tstrb);
            }
          }
          if ($ti == 0) $tstrc .= str_replace(JTBC_CINFO, $tstrd, $tstra);
          $tstrc .= str_replace(JTBC_CINFO, $tstre, $tstra);
          $ti += 1;
        }
        $tstrc = str_replace(JTBC_CINFO, $tstrc, $tpl_html);
        $theight = ($ti + 3) * 20;
        $tstrc = str_replace('{$height}', $theight, $tstrc);
        $tmpstr = $tstrc;
      }
      else $tmpstr = ii_htmlencode($tsqlstr) . $html_kong . str_replace('{$explain}', $tmpfail, $font_red) . $html_br;
    }
    else
    {
      $trs = ii_conn_query($tsqlstr, $conn);
      if ($trs) $tmprstr .= ii_htmlencode($tsqlstr) . $html_kong . str_replace('{$explain}', $tmpsucc, $font_green) . $html_br;
      else $tmprstr .= ii_htmlencode($tsqlstr) . $html_kong . str_replace('{$explain}', $tmpfail, $font_red) . $html_br;
      $tmpstr = $tmprstr;
    }
  }
  return $tmpstr;
}

function jtbc_cms_admin_manage_form()
{
  $tmpstr = ii_ireplace('manage.form', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage()
{
  return jtbc_cms_admin_manage_form();
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
