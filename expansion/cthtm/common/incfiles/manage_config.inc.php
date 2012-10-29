<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
jtbc_cms_admin_init();

function jtbc_cms_admin_manage_index()
{
  $tmpstr = ii_ireplace('manage.index', 'tpl');
  return $tmpstr;
}

function jtbc_cms_admin_manage()
{
  return jtbc_cms_admin_manage_index();
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
