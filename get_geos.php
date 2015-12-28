<?php
function getip($i = 0) {
        $ips = serverips();
        return (array_key_exists($ips,$i) && $i > 0 ? $ips[$i] : $ips[0]);
}
function serverips() {
        $ipv4s = `ip -4 addr | grep -oP "([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})" | sed '/^127.0.0.1$/d;' | sort -u`;
        $ips = explode(',',$ipv4s);
        return $ips;
}
function randomip() {
        $ips = serverips();
        return $ips[array_rand($ips)];
}

function jsonrequest($url, $ip = NULL) {

        $iptouse = is_null($ip) ? randomip() : $ip;

        $opts = array(
                'socket' => array(
                                'bindto' => $iptouse.':'.rand(49152,65534),
                                ),
                );
        stream_context_set_default($opts);
        $result = file_get_contents($url);
        return json_decode($result);
}
$db = array('user'=>'DBUSERNAME','pass'=>'DBPASSWORD','host'=>'DBHOST');
$db['db'] = $db['user'];
$table = 'requests';
$dsn = 'mysql:host='.$db['host'].';dbname='.$db['db'];
$insert = 'INSERT INTO '.$table.' (requestid, ip, country_code, country_name, region_code, region_name, city, zip_code, time_zone, latitude, longitude, metro_code)
VALUES (NULL, :ip, :country_code, :country_name, :region_code, :region_name, :city, :zip_code, :time_zone, :latitude, :longitude, :metro_code);';
$select = "SELECT requestid FROM {$table} WHERE ip = :ip";
$baseurl = 'http://freegeoip.net/json/';
$file = (file_exists('newattackers.txt') ? 'newattackers.txt' : 'MASTERLIST');
$i=0;
$iplist = array_map('trim',file($file));
try {
        $pdo = new PDO($dsn,$db['user'],$db['pass']);
        foreach($iplist as $ip) {
                        ++$i;
                        $s_query = $pdo->prepare($select);
                        $s_query->execute(array(':ip'=>$ip));
                        $s_result = $s_query->fetch(PDO::FETCH_LAZY);
                        if($s_result === FALSE) {
                                echo '!'.$ip.PHP_EOL;
                                $url = $baseurl.$ip;
                                $jsonrequest = jsonrequest($url);
                                if($jsonrequest !== FALSE) {
                                        $i_query = $pdo->prepare($insert);
                                        $dataset = array();
                                        foreach(get_object_vars($jsonrequest) as $k => $v) {
                                                $dataset[':'.$k] = $v;
                                        }
                                        $i_query->execute($dataset);
                                }
                        } else {
                                echo '.'.$ip.PHP_EOL;
                                continue;
                        }
                        sleep(1);
        }
        $pdo = NULL;
} catch(PDOException $e) {
        var_dump($e);// echo $e->getMessage();
}
$pdo = NULL;
unlink($file);
