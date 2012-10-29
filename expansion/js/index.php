<?php
require('../../common/incfiles/common.inc.php');
require('common/incfiles/config.inc.php');
$myurs = ii_get_lrstr($nurs, '.', 'right');
$myid = ii_get_num($myurs, 0);
if ($myid != 0)
{
  $mysqlstr = "select * from $ndatabase where $nidfield=$myid";
  $myrs = ii_conn_query($mysqlstr, $conn);
  $myrs = ii_conn_fetch_array($myrs);
  if ($myrs)
  {
    $tjspath = $njspath . $myrs[ii_cfname('topic')] . '.' . $myid . '.js';
    if (ii_datediff(pp_get_retimetype($myrs[ii_cfname('retimetype')]), ii_get_date($myrs[ii_cfname('retime')]), ii_now()) > ii_get_num($myrs[ii_cfname('retimevalue')]))
    {
      $tjscontent = pp_encode2js(ii_creplace($myrs[ii_cfname('content')]));
      if (file_put_contents($tjspath, $tjscontent))
      {
        $tsqlstr = "update $ndatabase set " . ii_cfname('retime') . "='" . ii_now() . "' where $nidfield=$myid";
        @ii_conn_query($tsqlstr, $conn);
      }
    }
    $mystr = file_get_contents($tjspath);
    echo $mystr;
  }
}
?>