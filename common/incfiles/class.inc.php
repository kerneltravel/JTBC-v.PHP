<?php
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
class cc_cache
{
  public $filename;
  public $cachename;

  function get_file_text()
  {
    return file_get_contents($this -> filename);
  }

  function put_file_text($data)
  {
    return file_put_contents($this -> filename, $data);
  }

  function get_file_array()
  {
    return include_once($this -> filename);
  }

  function set_file_array($data)
  {
    if (!is_array($data))
    {
      return false;
    }
    else
    {
      $tarraytext = 'array(';
      foreach($data as $key => $val)
      {
        if (is_array($val))
        {
          $tarraytext = $tarraytext . '\'' . $key . '\' => ' . $this -> set_file_array($val) . ',';
        }
        else
        {
          $tarraytext = $tarraytext . '\'' . $key . '\' => \'' . $val . '\',';
        }
      }
      $tarraytext = $tarraytext . ')';
      return $tarraytext;
    }
  }

  function put_file_array($data)
  {
    $ttext = '<?php' . chr(13) . chr(10);
    $ttext = $ttext . '$GLOBALS[\'' . $this -> cachename . '\'] = ';
    $ttext = $ttext . $this -> set_file_array($data) . ';' . chr(13) . chr(10);
    $ttext = $ttext . '?>';
    return file_put_contents($this -> filename, $ttext);
  }
}

class cc_cutepage
{
  public $id;
  public $sqlstr;
  public $offset;
  public $pagesize;
  public $rslimit;
  public $listkey;

  function init()
  {
    $trscount = $this -> get_rs_count();
    if (!isset($this -> rslimit)) $this -> rslimit = $trscount;
    else
    {
      if ($trscount < ($this -> rslimit)) $this -> rslimit = $trscount;
    }
  }

  function get_rs_count()
  {
    global $conn;
    $tsqlstr = 'select count(' . $this -> id . ') from' . ii_get_lrstr($this -> sqlstr, 'from', 'rightr');
    $trs = ii_conn_query($tsqlstr, $conn);
    $trs = ii_conn_fetch_array($trs);
    return $trs[0];
  }

  function get_rs_array()
  {
    global $conn;
    $toffset = $this -> offset;
    $tpagesize = $this -> pagesize;
    $trslimit = $this -> rslimit;
    if (!($toffset > $trslimit))
    {
      if (($toffset + $tpagesize) > $trslimit) $tpagesize = $trslimit - $toffset;
      $tsqlstr = $this -> sqlstr . ' limit ' . $toffset . ',' . $tpagesize;
      $trs = ii_conn_query($tsqlstr, $conn);
      $ti = 0;
      while ($trow = ii_conn_fetch_array($trs))
      {
        $tarray[$ti] = $trow;
        $ti += 1;
      }
      return $tarray;
    }
  }

  function get_pagestr()
  {
    global $nurltype, $ncreatefolder, $ncreatefiletype;
    $toffset = $this -> offset;
    $tpagesize = $this -> pagesize;
    $trslimit = $this -> rslimit;
    $tlistkey = $this -> listkey;
    $tpagenums = ceil($trslimit / $tpagesize);
    $tnpagenum = ceil($toffset / $tpagesize) + 1;
    if ($tnpagenum > $tpagenums) $tnpagenum = $tpagenums;
    $txpagenum = $tnpagenum + 1;
    if ($txpagenum > $tpagenums) $txpagenum = $tpagenums;
    $tstate1 = ($toffset > 0) ? 1 : 0;
    $tstate2 = (($toffset + $tpagesize) < $trslimit) ? 1 : 0;
    $tmpstr = ii_itake('global.tpl_common.cutepage', 'tpl');
    $tpl_firstpage = ii_ctemplate($tmpstr, '{@firstpage}');
    $tary = explode('{|}', $tpl_firstpage);
    if ($tstate1)
    {
      $tstr = $tary[1];
      $tstr = str_replace('{$URLfirst}', ii_iurl('listpage', 0, $nurltype, 'folder=' . $ncreatefolder . ';filetype=' . $ncreatefiletype . ';listkey=' . $tlistkey), $tstr);
    }
    else $tstr = $tary[0];
    $tmpstr = str_replace(JTBC_CINFO, $tstr, $tmpstr);
    $tpl_prepage = ii_ctemplate($tmpstr, '{@prepage}');
    $tary = explode('{|}', $tpl_prepage);
    if ($tstate1)
    {
      $tstr = $tary[1];
      $tstr = str_replace('{$URLpre}', ii_iurl('listpage', $toffset - $tpagesize, $nurltype, 'folder=' . $ncreatefolder . ';filetype=' . $ncreatefiletype . ';listkey=' . $tlistkey), $tstr);
    }
    else $tstr = $tary[0];
    $tmpstr = str_replace(JTBC_CINFO, $tstr, $tmpstr);
    $tpl_nextpage = ii_ctemplate($tmpstr, '{@nextpage}');
    $tary = explode('{|}', $tpl_nextpage);
    if ($tstate2)
    {
      $tstr = $tary[1];
      $tstr = str_replace('{$URLnext}', ii_iurl('listpage', $toffset + $tpagesize, $nurltype, 'folder=' . $ncreatefolder . ';filetype=' . $ncreatefiletype . ';listkey=' . $tlistkey), $tstr);
    }
    else $tstr = $tary[0];
    $tmpstr = str_replace(JTBC_CINFO, $tstr, $tmpstr);
    $tpl_lastpage = ii_ctemplate($tmpstr, '{@lastpage}');
    $tary = explode('{|}', $tpl_lastpage);
    if ($tstate2)
    {
      $tlastoffset = $trslimit - (($trslimit - $toffset) % $tpagesize);
      if ($tlastoffset == $trslimit) $tlastoffset = $trslimit - $tpagesize;
      $tstr = $tary[1];
      $tstr = str_replace('{$URLlast}', ii_iurl('listpage', $tlastoffset, $nurltype, 'folder=' . $ncreatefolder . ';filetype=' . $ncreatefiletype . ';listkey=' . $tlistkey), $tstr);
    }
    else $tstr = $tary[0];
    $tmpstr = str_replace(JTBC_CINFO, $tstr, $tmpstr);
    $tmpstr = str_replace('{$npagenum}', $tnpagenum, $tmpstr);
    $tmpstr = str_replace('{$pagenums}', $tpagenums, $tmpstr);
    $tmpstr = str_replace('{$xpagenum}', $txpagenum, $tmpstr);
    $tmpstr = str_replace('{$pagesize}', $tpagesize, $tmpstr);
    $tmpstr = str_replace('{$goURL}', ii_iurl('listpage', '\' + cc_cutepage(get_id(\'go-page-num\').value) + \'', $nurltype, 'folder=' . $ncreatefolder . ';filetype=' . $ncreatefiletype . ';listkey=' . $tlistkey), $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    return $tmpstr;
  }
}

class cc_socketmail
{
  public $server;
  public $port;
  public $username;
  public $password;
  public $from;
  public $to;
  public $subject;
  public $message;

  function send_mail()
  {
    $tserver = $this -> server;
    $tport = $this -> port;
    $tusername = $this -> username;
    $tpassword = $this -> password;
    $tfrom = $this -> from;
    $tto = $this -> to;
    $tsubject = $this -> subject;
    $tmessage = $this -> message;

    if (ii_isnull($tserver) || ii_isnull($tport)) return false;
    else
    {
      $tfp = fsockopen($tserver, $tport);
      if (!$tfp) return false;
      else
      {
        set_socket_blocking($tfp, true);
        $tlastmessage = fgets($tfp, 512);
        if (substr($tlastmessage, 0, 3) != 220) return false;
        else
        {
          fputs($tfp, "HELO JTBC\r\n");
          $tlastmessage = fgets($tfp, 512);
          if(substr($tlastmessage, 0, 3) != 220 && substr($tlastmessage, 0, 3) != 250) return false;
          else
          {
            fputs($tfp, "AUTH LOGIN\r\n");
            $tlastmessage = fgets($tfp, 512);
            if(substr($tlastmessage, 0, 3) != 334) return false;
            else
            {
              fputs($tfp, base64_encode($tusername) . "\r\n");
              $tlastmessage = fgets($tfp, 512);
              if(substr($tlastmessage, 0, 3) != 334) return false;
              else
              {
                fputs($tfp, base64_encode($tpassword) . "\r\n");
                $tlastmessage = fgets($tfp, 512);
                if(substr($tlastmessage, 0, 3) != 235) return false;
                else
                {
                  fputs($tfp, "MAIL FROM: $tfrom\r\n");
                  $tlastmessage = fgets($tfp, 512);
                  if(substr($tlastmessage, 0, 3) != 250)
                  {
                    $tfstype = 1;
                    fputs($tfp, "MAIL FROM: <$tfrom>\r\n");
                    $tlastmessage = fgets($tfp, 512);
                  }
                  if(substr($tlastmessage, 0, 3) != 250) return false;
                  else
                  {
                    foreach(explode(',', $tto) as $ttouser)
                    {
                      $ttouser = trim($ttouser);
                      if($ttouser)
                      {
                        fputs($tfp, "RCPT TO: $ttouser\r\n");
                        $tlastmessage = fgets($tfp, 512);
                        if(substr($tlastmessage, 0, 3) != 250)
                        {
                          fputs($tfp, "RCPT TO: <$ttouser>\r\n");
                          $tlastmessage = fgets($tfp, 512);
			}
                      }
                    }
                    fputs($tfp, "DATA\r\n");
                    $tlastmessage = fgets($tfp, 512);
                    if(substr($tlastmessage, 0, 3) != 354) return false;
                    else
                    {
                      if ($tfstype != 1) fputs($tfp, "To: $tto\r\nFrom: $tfrom\r\nSubject: " . str_replace("\n", ' ', $tsubject) . "\r\n\r\n$tmessage\r\n.\r\n");
                      else fputs($tfp, "To: <$tto>\r\nFrom: <$tfrom>\r\nSubject: " . str_replace("\n", ' ', $tsubject) . "\r\n\r\n$tmessage\r\n.\r\n");
                      fputs($tfp, "QUIT\r\n");
                      return true;
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}
//****************************************************
// JTBC CMS Power by Jetiben.com
// Email: jetiben@hotmail.com
// Web: http://www.jtbc.net.cn/
//****************************************************
?>
