<?php
$hosts = ['v4'=>[], 'v6'=>[]];
$countHosts = ['v4'=>0, 'v6'=>0];
$servers = [
		   'https://adaway.org/hosts.txt',
		   'https://raw.githubusercontent.com/StevenBlack/hosts/master/hosts',
		   'https://pgl.yoyo.org/adservers/serverlist.php?hostformat=hosts&showintro=0&mimetype=plaintext',
		   'https://winhelp2002.mvps.org/hosts.txt',
		   'https://block.energized.pro/spark/formats/hosts.txt',
		   'https://block.energized.pro/bluGo/formats/hosts.txt',
		   'https://block.energized.pro/blu/formats/hosts.txt',
		   'https://block.energized.pro/basic/formats/hosts.txt',
		   'https://block.energized.pro/porn/formats/hosts.txt',
		   'https://block.energized.pro/ultimate/formats/hosts.txt',
		   'https://block.energized.pro/unified/formats/hosts.txt',
		   'https://energized.pro/extensions/porn-lite/formats/hosts-ipv6.txt',
		   'https://energized.pro/extensions/social/formats/hosts-ipv6.txt',
		   'https://energized.pro/extensions/regional/formats/hosts-ipv6.txt',
		   'https://energized.pro/extensions/xtreme/formats/hosts-ipv6.txt',
		   'https://github.com/Ultimate-Hosts-Blacklist/Ultimate.Hosts.Blacklist/blob/master/hosts/hosts0',
		   'https://github.com/Ultimate-Hosts-Blacklist/Ultimate.Hosts.Blacklist/blob/master/hosts/hosts1',
		   'https://github.com/Ultimate-Hosts-Blacklist/Ultimate.Hosts.Blacklist/blob/master/hosts/hosts2',
		   'https://github.com/Ultimate-Hosts-Blacklist/Ultimate.Hosts.Blacklist/blob/master/hosts/hosts3',
		   'https://github.com/Ultimate-Hosts-Blacklist/Ultimate.Hosts.Blacklist/blob/master/hosts/hosts0.deny',
		   'https://github.com/Ultimate-Hosts-Blacklist/Ultimate.Hosts.Blacklist/blob/master/hosts.windows/hosts0.windows',
		   'https://github.com/Ultimate-Hosts-Blacklist/Ultimate.Hosts.Blacklist/blob/master/hosts.windows/hosts1.windows',
		   'https://github.com/Ultimate-Hosts-Blacklist/Ultimate.Hosts.Blacklist/blob/master/hosts.windows/hosts2.windows',
		   'https://github.com/Ultimate-Hosts-Blacklist/Ultimate.Hosts.Blacklist/blob/master/hosts.windows/hosts3.windows',
		   'https://github.com/Ultimate-Hosts-Blacklist/Ultimate.Hosts.Blacklist/blob/master/hosts.windows/hosts4.windows'
		   ];

function getDomains($string) {
	global $hosts;
	$result = false;
	if ((substr($string, 0, 3)) == ":: ") {
		$result = substr($string, 4, strlen($string) - 4);
		if (!in_array($result, $hosts['v6'])) $hosts['v6'][] = $result;
	} elseif ((substr($string, 0, 9)) == "127.0.0.1") {
		$result = substr($string, 10, strlen($string) - 10);
		if (!in_array($result, $hosts['v4'])) $hosts['v4'][] = $result;
	} elseif ((substr($string, 0, 7)) == "0.0.0.0") {
		$result = substr($string, 8, strlen($string) - 8);
		if (!in_array($result, $hosts['v4'])) $hosts['v4'][] = $result;
	}
}

if (php_sapi_name() == 'cli') {
	while(1) {
		for ($i = 0; $i < count($servers); $i++) {
			try {
				$file = new SplFileObject($servers[$i]);
				echo $servers[$i];
				while (!$file->eof()) {
					$string = $file->current();
					if (!empty($string)) {
						getDomains($string);
					}
					$file->next();
				} unset($file);
				echo "\n";
			} catch (Exception $e) {
				echo "Not found ".$servers[$i]."\n";
			}
		}

		if (($countHosts['v4'] < count($hosts['v4'])) || ($countHosts['v6'] < count($hosts['v6']))) {
			sort($hosts['v4']);
			sort($hosts['v6']);
			$data = "# --------------------------------------------
#
#                 StopAdware
#                   ------
#          ad.porn.malware blocking.
#                   ------
#      Merged collection of hosts from
#             reputable sources.
#                   ------
#  https://jebance.github.io/StopAdware/hosts
#
# --------------------------------------------
# L O C A L  H O S T
# --------------------------------------------

127.0.0.1 localhost
127.0.0.1 localhost.localdomain
127.0.0.1 local
255.255.255.255 broadcasthost
::1 localhost
::1 ip6-localhost
::1 ip6-loopback
fe80::1%lo0 localhost
ff00::0 ip6-localnet
ff00::0 ip6-mcastprefix
ff02::1 ip6-allnodes
ff02::2 ip6-allrouters
ff02::3 ip6-allhosts
0.0.0.0 0.0.0.0

# --------------------------------------------
# B E G I N S  I P V 4
# --------------------------------------------

";
			for ($i = 0; $i < count($hosts['v4']); $i++) {
				$data .= "0.0.0.0 ".$hosts['v4'][$i];
			}
			$data .= "
# --------------------------------------------
# B E G I N S  I P V 6
# --------------------------------------------

";
			for ($i = 0; $i < count($hosts['v6']); $i++) {
				$data .= ":: ".$hosts['v6'][$i];
			}

			$fcreate = fopen('hosts', "w+");
			fwrite($fcreate, $data);
			fclose($fcreate);
			unset($data);
			$countHosts['v4'] = count($hosts['v4']);
			$countHosts['v6'] = count($hosts['v6']);
			echo "v4: ".$countHosts['v4']."\nv6: ".$countHosts['v6']."\n";
		}

		sleep(86400);	// updated every 24 hours
	}
}
?>
