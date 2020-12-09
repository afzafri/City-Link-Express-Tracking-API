<?php

require 'vendor/autoload.php';

use Afzafri\CityLinkExpressTrackingApi;

/*  City-Link Express Tracking API created by Afif Zafri.
    Tracking details are fetched directly from City-Link Express tracking website,
    parse the content, and return JSON formatted string.
    Please note that this is not the official API, this is actually just a "hack",
    or workaround for implementing City-Link Express tracking feature in other project.
    Usage: http://site.com/api.php?trackingNo=CODE , where CODE is your tracking number
*/

header("Access-Control-Allow-Origin: *"); # enable CORS

if(isset($_GET['trackingNo']))
{
    $trackingNo = $_GET['trackingNo']; # put your tracking number here

    $trackres = CityLinkExpressTrackingApi::crawl($trackingNo, true);

    print_r($trackres);exit();

    # output/display the JSON formatted string
    echo json_encode($trackres);
}

