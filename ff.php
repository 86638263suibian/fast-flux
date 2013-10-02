#!/usr/bin/php -q
<?php
echo "http://code.google.com/p/fast-flux/\n";
echo "(C) 2013 Adam Ziaja <adam@adamziaja.com> http://adamziaja.com\n";
$domain = $argv[1];
include_once 'geoip.inc'; // https://raw.github.com/maxmind/geoip-api-php/master/geoip.inc
try {
    $db = new PDO('mysql:host=localhost;dbname=botnet;charset=utf8', 'LOGIN', 'PASSWORD');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->query('CREATE TABLE IF NOT EXISTS botnet (botnet_ip VARCHAR(15) NOT NULL UNIQUE, botnet_datetime DATETIME NOT NULL, botnet_country TEXT NOT NULL, botnet_asn TEXT NOT NULL)');
} catch (PDOException $e) {
    print 'Exception : ' . $e->getMessage();
}
while (1) {
    $time = date("Y-m-d H:i:s");
    $ip = gethostbyname($domain);
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        $country = strtolower(geoip_country_code_by_name($ip));
        $asn = htmlspecialchars(geoip_name_by_addr(geoip_open('/usr/share/GeoIP/GeoIPASNum.dat', GEOIP_STANDARD), $ip), ENT_QUOTES);
        echo "$domain $ip\n";
        try {
            $db->query("INSERT IGNORE INTO botnet (botnet_ip, botnet_datetime, botnet_country, botnet_asn) VALUES ('$ip', '$time', '$country', '$asn')");
        } catch (PDOException $e) {
            print 'Exception : ' . $e->getMessage();
        }
    }
}
?>
