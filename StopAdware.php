<?php
function getDomains($string) {
	global $hosts;
	$result = false;
	if ((substr($string, 0, 3)) == ":: ") {
		$result = trim(substr($string, 4, strlen($string) - 4), "\n\r\t");
		$hosts['v6'][] = $result;
	} elseif ((substr($string, 0, 9)) == "127.0.0.1") {
		$result = trim(substr($string, 10, strlen($string) - 10), "\n\r\t");
		$hosts['v4'][] = $result;
	} elseif ((substr($string, 0, 7)) == "0.0.0.0") {
		$result = trim(substr($string, 8, strlen($string) - 8), "\n\r\t");
		$hosts['v4'][] = $result;
	}
}

if (php_sapi_name() == 'cli') {

	$countHosts = ['v4'=>0, 'v6'=>0];

	while(1) {

		$servers = json_decode(file_get_contents('https://raw.githubusercontent.com/JeBance/StopAdware/gh-pages/servers.json'), true);

		$hosts = ['v4'=>[], 'v6'=>[]];

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
			$hosts['v4'] = array_values(array_unique($hosts['v4']));
			$hosts['v6'] = array_values(array_unique($hosts['v6']));
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
				$data .= "0.0.0.0 ".$hosts['v4'][$i]."\n";
			}
			$data .= "
# --------------------------------------------
# B E G I N S  I P V 6
# --------------------------------------------

";
			for ($i = 0; $i < count($hosts['v6']); $i++) {
				$data .= ":: ".$hosts['v6'][$i]."\n";
			}

			$fcreate = fopen('/PATH_TO_FILE/hosts', "w+");
			fwrite($fcreate, $data);
			fclose($fcreate);

			unset($data);

			$countHosts['v4'] = count($hosts['v4']);
			$countHosts['v6'] = count($hosts['v6']);
			echo "v4: ".$countHosts['v4']."\nv6: ".$countHosts['v6']."\n";
			
			unset($hosts);
		}

		exec('git add --all');
		exec('git commit -m "updated '.date('d.m.Y h:i').'"');
		exec('git push -u origin gh-pages');

		sleep(86400);	// updated every 24 hours
	}
}
?>
