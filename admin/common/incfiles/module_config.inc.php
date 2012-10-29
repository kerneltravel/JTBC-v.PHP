<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
$admc_pstate = 'public';

function pp_checkit()
{
  $tckname = $_POST['ckname'];
  if (!(ii_isnull($tckname)))
  {
    return 'Getenv: ' . getenv($tckname) . ', Get_cfg_var: ' . get_cfg_var($tckname) . ', Get_magic_quotes_gpc: ' . get_magic_quotes_gpc($tckname) . ', Get_magic_quotes_runtime: ' . get_magic_quotes_runtime($tckname);
  }
}

function pp_replace_pfs($array, $path, $folder)
{
  $tarray = $array;
  $tarray2 = Array();
  $tfolder = ii_get_lrstr($path, '/./', 'rightr');
  if (is_array($tarray))
  {
    foreach ($tarray as $key => $val)
    {
      $tkey = ii_creplace($key);
      $tkey = str_replace('{$path}', $path, $tkey);
      $tkey = str_replace('{$folder}', $tfolder, $tkey);
      $tarray2[$tkey] = $val;
    }
    return $tarray2;
  }
}

function pp_get_leftmenu_order($path)
{
  global $variable;
  $tstrers = ii_get_lrstr($path, '/./', 'rightr');
  $tstrers = ii_get_lrstr($tstrers, '/', 'leftr');
  switch($tstrers)
  {
    case ADMIN_FOLDER:
      return $variable[ADMIN_FOLDER . '.norder'];
      break;
    default:
      return ADMIN_FOLDER . ',' . $variable[ADMIN_FOLDER . '.morder'];
      break;
  }
}

function pp_get_leftmenu_array_config($path)
{
  global $nlng;
  $tarys = Array();
  $twebdir = dir($path);
  $torder = pp_get_leftmenu_order($path);
  while($tentry = $twebdir -> read())
  {
    if (!(is_numeric(strpos($tentry, '.'))))
    {
      if (!(ii_cinstr($torder, $tentry, ',')))
      {
        $torder .= ',' . $tentry;
      }
    }
  }
  $twebdir -> close();
  $torderary = split(',', $torder);
  if (is_array($torderary))
  {
    foreach($torderary as $key => $val)
    {
      if (!(ii_isnull($val)))
      {
        $tfilename = $path . $val . '/common/guide' . XML_SFX;
        if (file_exists($tfilename))
        {
          $tary = pp_replace_pfs(ii_get_xinfo($tfilename, $nlng), $path . $val, $val);
          $tarys += $tary;
          if (ii_get_xrootatt($tfilename, 'mode') == 'jtbcf') $tarys += pp_get_leftmenu_array_config($path . $val . '/');
        }
      }
    }
  }
  return $tarys;
}

function pp_get_leftmenu_array()
{
  global $adms_appstr;
  if (ii_cache_is($adms_appstr))
  {
    ii_cache_get($adms_appstr, 1);
  }
  else
  {
    $tpath = ii_get_actual_route('./');
    $tary = pp_get_leftmenu_array_config($tpath);
    ii_cache_put($adms_appstr, 1, $tary);
    $GLOBALS[$adms_appstr] = $tary;
  }
  return $GLOBALS[$adms_appstr];
}

function jtbc_cms_ckulogin()
{
  if (!(strtolower($_POST['valcode']) == strtolower($_SESSION['valcode']))) mm_client_alert(ii_itake('config.urndcodes_failed', 'lng'), -1);
  else
  {
    global $conn, $variable;
    $tislogin = 0;
    $tuname = ii_get_safecode($_POST['uname']);
    $tpassword = ii_md5($_POST['password']);
    if (jtbc_cms_cklogin($tuname, $tpassword)) $tislogin = 1;
    $tdatabase =  $variable['common.adminlog.ndatabase'];
    $tidfield =  $variable['common.adminlog.nidfield'];
    $tfpre =  $variable['common.adminlog.nfpre'];
    $tsqlstr = "insert into $tdatabase (" . ii_cfnames($tfpre, 'name') . "," . ii_cfnames($tfpre, 'time') . "," . ii_cfnames($tfpre, 'ip') . "," . ii_cfnames($tfpre, 'islogin') . ") values ('$tuname','" . ii_now() . "','" . ii_get_client_ip() . "','$tislogin')";
    if (ii_conn_query($tsqlstr, $conn))
    {
      if ($tislogin == 1)
      {
        header('location: admin_main.php');
      }
      else
      {
        mm_client_alert(ii_itake('config.ulogin_failed', 'lng'), -1);
      }
    }
    else
    {
      mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), -1);
    }
  }
}

function jtbc_cms_login()
{
  global $admc_name, $admc_pword;
  $taction = $_GET['action'];
  if (!(ii_isnull($taction)))
  {
    if ($taction == 'login') jtbc_cms_ckulogin();
    else jtbc_cms_ulogout();
  }
  else
  {
    if (jtbc_cms_cklogin($admc_name, $admc_pword)) header('location: admin_main.php');
    else return ii_ireplace('module.login', 'tpl');
  }
}

function jtbc_cms_ulogout()
{
  setcookie(APP_NAME . 'admin[username]', '', 0, COOKIES_PATH);
  setcookie(APP_NAME . 'admin[password]', '', 0, COOKIES_PATH);
  session_unregister(APP_NAME . 'admin_popedom');
  session_unregister(APP_NAME . 'admin_username');
  header('location: ./');
}

function jtbc_cms_frame()
{
  return ii_ireplace('module.frame', 'tpl');
}

function jtbc_cms_manage()
{
  return ii_ireplace('module.manage', 'tpl');
}

function jtbc_cms_left()
{
  global $admc_popedom;
  $tarray = pp_get_leftmenu_array();
  if (is_array($tarray))
  {
    $tplstr = ii_ireplace('module.left', 'tpl');
    $tcrca = split('{@recurrence_ida}', $tplstr);
    if (count($tcrca) == 3)
    {
      $tcrcastr = $tcrca[1];
      $tcrcb = split('{@recurrence_idb}', $tcrcastr);
      if (count($tcrcb) == 3) $tcrcbstr = $tcrcb[1];
      $ttplstr = $tcrca[0];
      $tii = 0;
      foreach ($tarray as $key => $val)
      {
        if (is_numeric(strpos($key, 'description')))
        {
          if ($admc_popedom == -1 || $key == 'description'  || ii_cinstr($admc_popedom, ii_get_lrstr($key, ':', 'left'), ','))
          {
            $tstate = 1;
          }
          else
          {
            $tstate = 0;
          }
        }
        if ($tstate == 1)
        {
          if (ii_get_lrstr($key, ':', 'right') == 'description')
          {
            $ttplstr = str_replace(JTBC_CINFO, '', $ttplstr);
            $tstr = $tcrcb[0] . JTBC_CINFO . $tcrcb[2];
            $tstr = str_replace('{$description}', $val, $tstr);
            $tstr = str_replace('{$id}', $tii, $tstr);
            $ttplstr = $ttplstr . $tstr;
            $tii = $tii + 1;
          }
          else
          {
            $tkey = $key;
            if (is_numeric(strpos($tkey, ':')))
            {
              if ($admc_popedom == '-1' || ii_cinstr($admc_popedom, ii_get_lrstr($tkey, ':', 'left'), ','))
              {
                $tkey = ii_get_lrstr($tkey, ':', 'right');
              }
            }
            if (!(is_numeric(strpos($tkey, ':'))))
            {
              $tstr = str_replace('{$topic}', $val, $tcrcbstr);
              $tstr = str_replace('{$ahref}', $tkey, $tstr);
              $ttplstr = str_replace(JTBC_CINFO, $tstr . JTBC_CINFO, $ttplstr);
              $tstrs = $tstrs . $tstr;
            }
          }
        }
      }
      $ttplstr = $ttplstr . $tcrca[2];
      $ttplstr = str_replace(JTBC_CINFO, '', $ttplstr);
      return $ttplstr;
    }
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
