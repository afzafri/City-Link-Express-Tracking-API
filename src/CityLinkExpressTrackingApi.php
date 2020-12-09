<?php 

namespace Afzafri;

class CityLinkExpressTrackingApi
{
    public static function crawl($trackingNo, $include_info = false)
    {
		$url = "https://www.citylinkexpress.com/MY/ShipmentTrack.aspx";
		
		# store post data into array
		$postdata = http_build_query(
				array(
						'no' => $trackingNo,
						'type' => 'consignment',
				)
		);

		# use cURL instead of file_get_contents(), this is because on some server, file_get_contents() cannot be used
		# cURL also have more options and customizable
		$ch = curl_init(); # initialize curl object
		curl_setopt($ch, CURLOPT_URL, $url); # set url
		curl_setopt($ch, CURLOPT_POST, 1); # set option for POST data
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); # set post data array
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # receive server response
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); # tell cURL to accept an SSL certificate on the host server
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); # tell cURL to graciously accept an SSL certificate on the target server
		$result = curl_exec($ch); # execute curl, fetch webpage content
		$httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE); # receive http response status
		$errormsg = (curl_error($ch)) ? curl_error($ch) : "No error"; # catch error message
		curl_close($ch);  # close curl

		$trackres = array();
		$trackres['http_code'] = $httpstatus; # set http response code into the array
	    $trackres['error_msg'] = $errormsg; # set error message into array

	    # use DOMDocument to parse HTML
		$dom = new \DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($result);
		libxml_clear_errors();
	    
	    // xpath
	    $xpath = new \DOMXPath($dom);

	    // ----- Get tracking result box -----
		$trackUL = $xpath->query("//*[contains(@id, 'btmPanel')]");
		$tmp_dom = new \DOMDocument(); 
		$tmp_dom->appendChild($tmp_dom->importNode($trackUL[0],true));
		// xpath
		$xpath = new \DOMXPath($tmp_dom);

		// Get tracking list
		$trackDetails = $xpath->query("tr");

		$date = "";
	    if($trackDetails->length > 0) # check if there is records found or not
		{
			$trackres['status'] = 1;
	        $trackres['message'] = "Record Found"; # return record found if number of row > 0

	        foreach ($trackDetails as $detail) 
	        {
	            $tmp_dom = new \DOMDocument(); 
	            $tmp_dom->appendChild($tmp_dom->importNode($detail,true));
	            // xpath
				$xpath = new \DOMXPath($tmp_dom);
				
				$columns = $xpath->query("td");

				// ----- Get Date -----
				if($columns->length == 7) {
					$trackDayDate = $xpath->query("//*[contains(@class, 'tabletitle')]");
					if($trackDayDate->length > 0) {
						$date = self::formatDate(self::cleanDetail($trackDayDate[0]->nodeValue), 'l, F d, Y');
						$date = ($date) ? $date->format('d/m/Y') : "";
					}
				}

				// ---- Get Tracking Details----
	            $location = "";
				$process = "";
				$time = "";
				if($columns->length == 3) {
					$tmp_dom_detail = new \DOMDocument(); 
					$tmp_dom_detail->appendChild($tmp_dom_detail->importNode($columns[1],true));
					// xpath
					$xpath_detail = new \DOMXPath($tmp_dom_detail);
					$detailtable = $xpath_detail->query("table");

					foreach ($detailtable as $table) {
						$tmp_dom_process_row = new \DOMDocument(); 
						$tmp_dom_process_row->appendChild($tmp_dom_process_row->importNode($table,true));
						// xpath
						$xpath_process_row = new \DOMXPath($tmp_dom_process_row);
						$processrow = $xpath_process_row->query("tr");

						foreach ($processrow as $row) {
							$tmp_dom_process_column = new \DOMDocument(); 
							$tmp_dom_process_column->appendChild($tmp_dom_process_column->importNode($row,true));
							// xpath
							$xpath_process_column = new \DOMXPath($tmp_dom_process_column);
							$process_column = $xpath_process_column->query("//*[contains(@class, 'table_detail')]");

							$process = ($process_column->length > 0) ? self::cleanDetail($process_column[0]->nodeValue) : ""; 
							$time = ($process_column->length > 1) ? self::cleanDetail($process_column[1]->nodeValue) : ""; 
							$location = ($process_column->length > 2) ? self::cleanDetail($process_column[2]->nodeValue) : ""; 

							if($process_column->length > 0) {
								// Append Data into JSON
								$trackres['data'][] = array(
									"date" => $date,
									"time" => $time,
									"location" => $location,
									"process" => $process,
								);
							}
						}
					}
				}
	        }
	    } 
	    else 
	    {
	    	$trackres['status'] = 0;
	        $trackres['message'] = "No Record Found"; # return record not found if number of row < 0
	        # since no record found, no need to parse the html furthermore
	    }

		if ($include_info) {
		    $trackres['info']['creator'] = "Afif Zafri (afzafri)";
		    $trackres['info']['project_page'] = "https://github.com/afzafri/City-Link-Express-Tracking-API";
		    $trackres['info']['date_updated'] =  "09/12/2020";
		}

		return $trackres;
    }

	static function cleanDetail($str, $explode = false) {
	    if($str != null || $str != "") {
	        if($explode) {
	            $strArr = explode(":", $str);
	            $str = (count($strArr) > 1) ? $strArr[1] : ""; 
	        } 

	        $converted = strtr($str, array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES))); 
	        $str = trim($converted, chr(0xC2).chr(0xA0));
	        $str = trim(preg_replace('/\s+/', ' ', $str));
	    }

	    return $str;
	}

	static function formatDate($date, $format = 'd/m/Y') {
	    $datetime = new \DateTime();
	    $newDate = $datetime->createFromFormat($format, $date);
	    return $newDate;
	}
}
