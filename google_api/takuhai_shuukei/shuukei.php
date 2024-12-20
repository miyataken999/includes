<?php
// phpinfo();

try {
	exec("/usr/bin/php /usr/local/src/google_api/takuhai_shuukei/shuukei_exec.php > /dev/null &");
} catch (Exception $e) {
	echo $e->getMessage();
	exit();
}
