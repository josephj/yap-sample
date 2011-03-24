<?php
    /* addUpdate($user, $title, $description) : 新增一筆 Yahoo! Update {{{ */
    function addUpdate($user, $title, $description) {
        $guid = $user->session->guid;
        $link = 'http://apps.yahoo.com/dev-preview/IPLrTx56/YahooFullView/friend.php?guid='.$guid;
        $suid = time().$guid;
        $update_debug = $user->insertUpdate($suid, $title, $link, $description, NULL);  
        return $update_debug;
    }
    /* }}} */
    /* addCity($guid, $city) : 新增一個城市 {{{ */
    function addCity($city_name) {
        global $session;
        $city_name = strtoupper($city_name);
        $result = SelectData('yap_city', array('count(*)'), array('city_name'=>$city_name));
        $total = $result[0]['count(*)']; 
        if ($total != 0){
            return false;
        }
        $flickr = "select * from flickr.photos.search where text='".$city_name."' limit 1";
        $flickr_result = $session->query($flickr);
        $imgurl = "http://farm".$flickr_result->query->results->photo->farm.".static.flickr.com/".$flickr_result->query->results->photo->server."/".$flickr_result->query->results->photo->id."_".$flickr_result->query->results->photo->secret."_s.jpg"; 
        InsertData('yap_city', array('city_name' => $city_name, 'city_imgurl' => $imgurl));       
        return true;
    }
    /* }}} */
    // getBeenCity($guid) : 取得去過那些城市 {{{
    function getBeenCity($guid) {
        if (!isset($guid)) {
            return;
        }
        db_connect();
        $col_ary = array('city_id', 'city_name', 'city_imgurl');
        $sql = "SELECT b.city_id, b.city_name, b.city_imgurl FROM yap_been AS a LEFT JOIN yap_city AS b ON a.city_id = b.city_id WHERE guid = '" . $guid . "' And city_name != ''";
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
    // getCity() : 取得所有城市 {{{
    function getCity() {
        return SelectData('yap_city', array('city_id', 'city_name', 'city_imgurl'));
    }
    // }}}
    // getFriend() : 取得所有朋友 {{{
    function getFriend($user) {
        $start = 0; 
        $count = 100; 
        $total = 0;  
        return $user->getConnections($start, $count, $total);  
    }
    // }}}
    // getFriendNews() : 取得朋友的最新訊息 {{{
    function getFriendNews($user) {
        return $user->listConnectionUpdatesTransform(0,8,'(sort "pubDate" numeric descending (grouping 1 "collectionID" (select "source" eq "APP.'.APP_ID.'"(all))))');  
    }
    // }}}
    /* updateBeenCity($guid, $city) : 更新去過的城市 {{{ */
    function updateBeenCity($guid, $city) {
        $citys = explode(',' , $city);
        DeleteData('yap_been', array('guid' => $guid));
        foreach ($citys as $city) {
            InsertData('yap_been', array('guid' => $guid, 'city_id' => $city));
        }
        return true;
    }
    /* }}} */
    function getArray($arr, $index) {
        $new_array = array();
        foreach ($arr as $arr) {
            $new_array[] = $arr[$index];
        }
        sort($new_array);
        return $new_array;
    }
    function getGET($var, $value='') {
        return isset($_GET[$var]) ? trim($_GET[$var]) : $value ;
    };
    function getPOST($var, $value='') {
        return isset($_POST[$var]) ? trim($_POST[$var]) : $value ;
    };
    function getREQUEST($var, $value='') { 
        return isset($_REQUEST[$var]) ? trim($_REQUEST[$var]) : $value ;
    };
    function getUserIp() {
        $HTTP_ENV_VARS = $_ENV;
        $HTTP_SERVER_VARS = $_SERVER;
        if (getenv('HTTP_X_FORWARDED_FOR') != '') {
            $proxy_ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) ?
                $HTTP_SERVER_VARS['REMOTE_ADDR'] :
                ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ?
                 $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR);
            $client_ip = getenv('HTTP_X_FORWARDED_FOR');
        } 
        else {
            $client_ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR']))?
                $HTTP_SERVER_VARS['REMOTE_ADDR'] :
                ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ?
                 $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR);
            $proxy_ip = '';
        }
        return $client_ip;
    };
    function getSvnId($rev) {
        $rev = str_replace('$', '', $rev);
        $rev = str_replace('Rev:', '', $rev);
        return intval($rev);
    };
    function getCssVersion() {
        if (file_exists('../sitetag.us/CSS_VERSION')) {
            return intval(file_get_contents('../sitetag.us/CSS_VERSION'));
        } 
        else {
            return 1;
        }
    };
    function getJsVersion() {
        if (file_exists('../sitetag.us/JS_VERSION')) {
            return intval(file_get_contents('../sitetag.us/JS_VERSION'));
        } 
        else {
            return 1;
        };
    };
?>
