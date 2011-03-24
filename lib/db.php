<?php
    include('lib/Yahoo.inc');  
    include('lib/function.php');
        $consumer_key = 'dj0yJmk9M0doZmtSNWhUdllqJmQ9WVdrOU1uWnBaMVZwTXpBbWNHbzlNVEUxTVRnM05EZzROQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD0wYQ--';
        $shared_secret = 'aedcf4be5988dc31489d47e3c76736a29f2d33fe';
        $app_id = '2vigUi30';
    define('APP_ID', $app_id);
    $session = YahooSession::requireSession($consumer_key, $shared_secret, $app_id);
    $user = $session->getSessionedUser();  
    $guid = $user->session->guid;
    $profile = $user->loadProfile();
    $TinyUsercard = $user->loadTinyUsercard();  
    $nickname = $profile->nickname;
    function db_connect() {
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        if (!$conn) {
            return false;
        }
        mysql_select_db(DB_NAME, $conn);
        return $conn;
    };
    function db_query($sql, $dbh='') {
        $r = mysql_query($sql);
        return $r;
    };
    function db_quote($str) {
        return mysql_escape_string($str);
    };
    // {{{ InsertData($table, $val_ary)
    function InsertData($table, $val_ary) {
        db_connect();
        $col='';
        $val='';
        foreach ($val_ary as $key => $value) {
            $col .= $key.',';
            if (get_magic_quotes_gpc()) {
                $val .= "'$value',";
            } else {
                $val .= "'".addslashes($value)."',";
            }
        }
        $col = rtrim($col, ',');
        $val = rtrim($val, ',');
        $sql = "INSERT INTO $table ($col) VALUES($val)";
        $result = mysql_query($sql);
        return $result;
    };
    // }}}
    // {{{ SelectData($table, $col_ary, $where_ary, $oder_ary)
    function SelectData($table, $col_ary, $where_ary='', $order_ary='') {
        db_connect();
        $col = implode(',', $col_ary);
        $where = '';
        $order = '';

        if ($where_ary != '') {
            $where = ' WHERE';
            foreach ($where_ary as $key => $val) {
                $where .= " $key='$val' AND";
            }
            $where = preg_replace('/AND$/', '', $where);
        }

        if ($order_ary != '') {
            $order = ' ORDER BY ';
            $order.= implode(',', $order_ary);
        }

        $sql = "SELECT $col FROM $table $where $order";
        $result = mysql_query($sql);
        $return = array();
        $i = 0;
        while ($row = mysql_fetch_assoc($result)) {
            foreach ($col_ary as $key) {
                $return[$i][$key] = $row[$key]; 
            }
            $i++;
        }
        return $return;
    }
    // }}}
    // {{{ UpdateData($table, $col_ary, $where_ary='')
    function UpdateData($table, $col_ary, $where_ary='')
    {
        db_connect();
        if ($where_ary == '') {
            return FALSE;
        }

        $set   = '';
        foreach ($col_ary as $key => $val) {
            if (get_magic_quotes_gpc()) {
                $set .= " $key = '$val' ,"; 
            } else {
                $set .= " $key = '".addslashes($val)."' ,";
            }
        }
        $set = rtrim($set, ',');

        if ($where_ary != '') {
            $where = ' WHERE';
            foreach($where_ary as $key => $val) {
                if(get_magic_quotes_gpc()) {
                    $where .= " $key = '$val' AND";
                } else {
                    $where .= " $key = '".addslashes($val)."' AND";
                }
            }
            $where = preg_replace('/AND$/', '', $where);   
        }

        $sql = "UPDATE $table SET $set $where";
        $result = mysql_query($sql);

        return $result;
    }
    // }}}
    // {{{ DeleteData($table, $where_ary='', $where_or_ary)
    function DeleteData($table, $where_ary='', $where_str='') {
        db_connect();

        // could be delete all data
        if($where_ary == '' && $where_str == '') {
            return FALSE;
        }

        if($where_ary != '') {
            $where = ' WHERE';
            foreach($where_ary as $key => $val) {
                $where .= " $key = '$val' AND";
            }
            $where = preg_replace('/AND$/', '', $where);   
        } else if($where_str != '') {
            $where = ' WHERE ' . $where_str;
        }

        $sql = "DELETE FROM $table $where";

        $result = mysql_query($sql);

        return $result;
    }
    // }}}
?>
