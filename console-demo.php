<?php

require 'vendor/autoload.php';

use Afzafri\CityLinkExpressTrackingApi;

if (isset($argv[1])) {
	print_r(CityLinkExpressTrackingApi::crawl($argv[1]));
} else {
	echo "Usage: " . $argv[0] . " <Tracking code>\n";
}