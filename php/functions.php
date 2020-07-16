
<?php
set_time_limit(0);

if($_POST['btn'] == "load"){
	$GLOBALS['markets'] = extractMarkets(curlRequest("https://api.bittrex.com/api/v1.1/public/getmarkets"), $_POST['base']);
	echo json_encode($GLOBALS['markets']);
}
if( $_POST['btn'] == "all"){
	$GLOBALS['markets'] = extractMarkets(curlRequest("https://api.bittrex.com/api/v1.1/public/getmarkets"), $_POST['base']);
	$GLOBALS['allMarketData'] = curlRequest("https://api.bittrex.com/api/v1.1/public/getmarketsummaries");
	startAnalyse();	
}
if($_POST['btn'] == "selected" ){
	$GLOBALS['markets'] = array($_POST['selected']);
	$GLOBALS['allMarketData'] = curlRequest("https://api.bittrex.com/api/v1.1/public/getmarketsummaries");
	startAnalyse();	
}



function startAnalyse(){
    rolling_curl( 'processRetrievedData');
	
}

function getCurrMarketOrderBookURL($market){
   return	$orderBookURL = "https://api.bittrex.com/api/v1.1/public/getorderbook?market=".$market."&type=both";	
}



function extractMarkets($obj,$base){
	$markets = array();
	foreach($obj as $curr){
	  if($curr -> BaseCurrency == $base){
			$markets[] = $curr -> MarketName;  
	  }		  
	}
	return $markets;
}



function processRetrievedData($data,$url){
	
	
	$buyOrders = $data -> buy;
	$sellOrders = $data -> sell;
	
	if(count($buyOrders) < 99){
		return;
	}
	
	$sumQuantityBuyOrdersMain100 = getSumQuantityBuyOrdersMain100($buyOrders);
	$sumQuantitySellOrdersMain100 = getSumQuantitySellOrdersMain100($sellOrders);
	
	$sumQuantityBuyOrdersBase100 = getSumQuantityBuyOrdersBase100($buyOrders);
	$sumQuantitySellOrdersBase100 = getSumQuantitySellOrdersBase100($sellOrders);
	
	$sumQuantityBuyOrdersMain75 = getSumQuantityBuyOrdersMain75($buyOrders);
	$sumQuantitySellOrdersMain75 = getSumQuantitySellOrdersMain75($sellOrders);
	
	$sumQuantityBuyOrdersBase75 = getSumQuantityBuyOrdersBase75($buyOrders);
	$sumQuantitySellOrdersBase75 = getSumQuantitySellOrdersBase75($sellOrders);
	
	$sumQuantityBuyOrdersMain50 = getSumQuantityBuyOrdersMain50($buyOrders);
	$sumQuantitySellOrdersMain50= getSumQuantitySellOrdersMain50($sellOrders);
	
	$sumQuantityBuyOrdersBase50 = getSumQuantityBuyOrdersBase50($buyOrders);
	$sumQuantitySellOrdersBase50 = getSumQuantitySellOrdersBase50($sellOrders);
	
	
	$sumQuantityBuyOrdersMain25 = getSumQuantityBuyOrdersMain25($buyOrders);
	$sumQuantitySellOrdersMain25 = getSumQuantitySellOrdersMain25($sellOrders);
	
	$sumQuantityBuyOrdersBase25 = getSumQuantityBuyOrdersBase25($buyOrders);
	$sumQuantitySellOrdersBase25 = getSumQuantitySellOrdersBase25($sellOrders);
	
	
	
	
	$medianRateBuyOrders100 = getMedianRateBuyOrders100($buyOrders);
	$medianRateSellOrders100 = getMedianRateSellOrders100($sellOrders);
	
	$medianRateBuyOrders75 = getMedianRateBuyOrders75($buyOrders);
	$medianRateSellOrders75 = getMedianRateSellOrders75($sellOrders);
	
	$medianRateBuyOrders50 = getMedianRateBuyOrders50($buyOrders);
	$medianRateSellOrders50 = getMedianRateSellOrders50($sellOrders);
	
	$medianRateBuyOrders25 = getMedianRateBuyOrders25($buyOrders);
	$medianRateSellOrders25 = getMedianRateSellOrders25($sellOrders);
	

	
	$market = exctractFromURL($url);
	$currMarketData = getCurrMarketData($market);	

	$winMarket = checkIfMarketIsWin($currMarketData,$sumQuantityBuyOrdersMain100,$sumQuantitySellOrdersMain100, $sumQuantityBuyOrdersBase100,$sumQuantitySellOrdersBase100,$sumQuantityBuyOrdersMain75,$sumQuantitySellOrdersMain75,$sumQuantityBuyOrdersBase75,$sumQuantitySellOrdersBase75,$sumQuantityBuyOrdersMain50,$sumQuantitySellOrdersMain50,$sumQuantityBuyOrdersBase50,$sumQuantitySellOrdersBase50,$sumQuantityBuyOrdersMain25,$sumQuantitySellOrdersMain25,$sumQuantityBuyOrdersBase25,$sumQuantitySellOrdersBase25);
	
	if($winMarket === true){
		extractInfoFromMarket($currMarketData,$medianRateBuyOrders100,$medianRateSellOrders100,$medianRateBuyOrders75,$medianRateSellOrders75,$medianRateBuyOrders50,$medianRateSellOrders50,$medianRateBuyOrders25,$medianRateSellOrders25);
	}
	else{
		return;
	}
		


	
	
	
}

function checkIfMarketIsWin($currMarketData,$sumQuantityBuyOrdersMain100,$sumQuantitySellOrdersMain100, $sumQuantityBuyOrdersBase100,$sumQuantitySellOrdersBase100,$sumQuantityBuyOrdersMain75,$sumQuantitySellOrdersMain75,$sumQuantityBuyOrdersBase75,$sumQuantitySellOrdersBase75,$sumQuantityBuyOrdersMain50,$sumQuantitySellOrdersMain50,$sumQuantityBuyOrdersBase50,$sumQuantitySellOrdersBase50,$sumQuantityBuyOrdersMain25,$sumQuantitySellOrdersMain25,$sumQuantityBuyOrdersBase25,$sumQuantitySellOrdersBase25){

	$coefDiffSumMain100 = 1.2;
	$coefDiffSumBase100 = 1.2;
	$coefDiffSumMain75 = 1.2;
	$coefDiffSumBase75 = 1.2;
	$coefDiffSumMain50 = 1.2;
	$coefDiffSumBase50 = 1.2;
	$coefDiffSumMain25 = 1.2;
	$coefDiffSumBase25 = 1.2;

	$coefDiffSumCalcMain100 = round($sumQuantityBuyOrdersMain100 / $sumQuantitySellOrdersMain100,2);
	$coefDiffSumCalcBase100 = round($sumQuantityBuyOrdersBase100 / $sumQuantitySellOrdersBase100,2);
	
	$coefDiffSumCalcMain75 = round($sumQuantityBuyOrdersMain75 / $sumQuantitySellOrdersMain75,2);
	$coefDiffSumCalcBase75 = round($sumQuantityBuyOrdersBase75 / $sumQuantitySellOrdersBase75,2);
	
	$coefDiffSumCalcMain50 = round($sumQuantityBuyOrdersMain50 / $sumQuantitySellOrdersMain50,2);
	$coefDiffSumCalcBase50 = round($sumQuantityBuyOrdersBase50 / $sumQuantitySellOrdersBase50,2);
	
	$coefDiffSumCalcMain25 = round($sumQuantityBuyOrdersMain25 / $sumQuantitySellOrdersMain25,2);
	$coefDiffSumCalcBase25 = round($sumQuantityBuyOrdersBase25 / $sumQuantitySellOrdersBase25,2);

	$minBaseVolume = getMinBaseVolume();
	

	
	if(($coefDiffSumCalcMain100 >= $coefDiffSumMain100 && $coefDiffSumCalcBase100 >= $coefDiffSumBase100 && $coefDiffSumCalcMain75 >= $coefDiffSumMain75 && $coefDiffSumCalcBase75 >= $coefDiffSumBase75 && $coefDiffSumCalcMain50 >= $coefDiffSumMain50 && $coefDiffSumCalcBase50 >= $coefDiffSumBase50 && $coefDiffSumCalcMain25 >= $coefDiffSumMain25 && $coefDiffSumCalcBase25 >= $coefDiffSumBase25 && $currMarketData -> BaseVolume >= $minBaseVolume)||$GLOBALS['markets'][0] == $_POST['selected']){
		
		echo "--------START--------",PHP_EOL;
		echo $currMarketData -> MarketName,PHP_EOL;
		echo PHP_EOL;
		echo "coefDiffSumCalcBase100 -> " . $coefDiffSumCalcBase100,PHP_EOL;
		echo "coefDiffSumCalcMain100 -> " . $coefDiffSumCalcMain100,PHP_EOL;
		echo "coefDiffSumCalcBase75 -> " . $coefDiffSumCalcBase75,PHP_EOL;
		echo "coefDiffSumCalcMain75 -> " . $coefDiffSumCalcMain75,PHP_EOL;
		echo "coefDiffSumCalcBase50 -> " . $coefDiffSumCalcBase50,PHP_EOL;
		echo "coefDiffSumCalcMain50 -> " . $coefDiffSumCalcMain50,PHP_EOL;
		echo "coefDiffSumCalcBase25 -> " . $coefDiffSumCalcBase25,PHP_EOL;
		echo "coefDiffSumCalcMain25 -> " . $coefDiffSumCalcMain25,PHP_EOL;
		return true;
	}
	else{
		return false;
	}
	
	
}
function extractInfoFromMarket($currMarketData,$medianRateBuyOrders100,$medianRateSellOrders100,$medianRateBuyOrders75,$medianRateSellOrders75,$medianRateBuyOrders50,$medianRateSellOrders50,$medianRateBuyOrders25,$medianRateSellOrders25){
	
	$winMarkets = array();
	
	$winMargin100 = round(calculateWinMargin100($currMarketData,$medianRateSellOrders100),2);
	$loseMargin100 = round(calculateLoseMargin100($currMarketData,$medianRateBuyOrders100),2);
	
	$winMargin75 = round(calculateWinMargin75($currMarketData,$medianRateSellOrders75),2);
	$loseMargin75 = round(calculateLoseMargin75($currMarketData,$medianRateBuyOrders75),2);
	
	$winMargin50 = round(calculateWinMargin50($currMarketData,$medianRateSellOrders50),2);
	$loseMargin50 = round(calculateLoseMargin50($currMarketData,$medianRateBuyOrders50),2);
	
	$winMargin25 = round(calculateWinMargin25($currMarketData,$medianRateSellOrders25),2);
	$loseMargin25 = round(calculateLoseMargin25($currMarketData,$medianRateBuyOrders25),2);
	
		echo "---------------------------",PHP_EOL;
		//echo "100 WIN UPLIFT -> " . $winMargin100 . "%",PHP_EOL;
		//echo "100 LOSE UPLIFT -> " . $loseMargin100 . "%",PHP_EOL;
		//echo "75 WIN UPLIFT -> " . $winMargin75 . "%",PHP_EOL;
		//echo "75 LOSE UPLIFT -> " . $loseMargin75 . "%",PHP_EOL;
		echo "50 WIN UPLIFT -> " . $winMargin50 . "%",PHP_EOL;
		echo "50 LOSE UPLIFT -> " . $loseMargin50 . "%",PHP_EOL;
		echo "25 WIN UPLIFT -> " . $winMargin25 . "%",PHP_EOL;		
		echo "25 LOSE UPLIFT -> " . $loseMargin25 . "%",PHP_EOL;
		echo PHP_EOL;
		echo "BUY NOW PRICE-> " . number_format($currMarketData -> Ask,8,'.',',') ,PHP_EOL;
		echo "25 BUY PRICE -> " . number_format($medianRateBuyOrders25,8,'.',','),PHP_EOL;
		echo "25 SELL PRICE -> " . number_format($medianRateSellOrders25,8,'.',','),PHP_EOL;
			
	
	if($loseMargin25/$winMargin25 > 1 && $GLOBALS['markets'][0] != $_POST['selected']){
		$winMarkets[] = $currMarketData -> MarketName;
		echo PHP_EOL;
		echo "PLAY ON IT!",PHP_EOL;
		echo PHP_EOL;
	}
	
	echo "--------END--------",PHP_EOL;
	echo PHP_EOL;
	echo PHP_EOL;
	echo PHP_EOL;

	
}

function getMinBaseVolume(){
	switch ($_POST['base']) {
    case "BTC":
        return 30;
        break;
    case "ETH":
        return 70;
        break;
    case "USD":
        return 50000;
        break;
   case "USDT":
		return 50000;
		break;
}
	
}

function getCurrMarketData($market){
	$allMarketData = $GLOBALS['allMarketData'];
	foreach($allMarketData as $currMarket){
		
		if($currMarket -> MarketName == $market){
			return $currMarket;
		}
	}
	
}

function calculateWinMargin25($currMarketData,$medianRateSellOrders25){
	$lastPrice = (float)$currMarketData -> Ask;
	return (($medianRateSellOrders25 - $lastPrice ) / $lastPrice ) * 100;
}

function calculateLoseMargin25($currMarketData,$medianRateBuyOrders25){
	$lastPrice = (float)$currMarketData -> Ask;
	return (($lastPrice - $medianRateBuyOrders25 ) / $lastPrice ) * 100;
}
function calculateWinMargin50($currMarketData,$medianRateSellOrders50){
	$lastPrice = (float)$currMarketData -> Ask;
	return (($medianRateSellOrders50 - $lastPrice ) / $lastPrice ) * 100;
}

function calculateLoseMargin50($currMarketData,$medianRateBuyOrders50){
	$lastPrice = (float)$currMarketData -> Ask;
	return (($lastPrice - $medianRateBuyOrders50 ) / $lastPrice ) * 100;
}

function calculateWinMargin75($currMarketData,$medianRateSellOrders75){
	$lastPrice = (float)$currMarketData -> Ask;
	return (($medianRateSellOrders75 - $lastPrice ) / $lastPrice ) * 100;
}

function calculateLoseMargin75($currMarketData,$medianRateBuyOrders75){
	$lastPrice = (float)$currMarketData -> Ask;
	return (($lastPrice - $medianRateBuyOrders75 ) / $lastPrice ) * 100;
}





function calculateWinMargin100($currMarketData,$medianRateSellOrders100){
	$lastPrice = (float)$currMarketData -> Ask;
	return (($medianRateSellOrders100 - $lastPrice ) / $lastPrice ) * 100;
}

function calculateLoseMargin100($currMarketData,$medianRateBuyOrders100){
	$lastPrice = (float)$currMarketData -> Ask;
	return (($lastPrice - $medianRateBuyOrders100 ) / $lastPrice ) * 100;
}

function exctractFromURL($url){
	preg_match('/(?<=\bmarket=\b).*?(?=\b&\b)/', $url, $matches);
	return $matches[0];
}





function getMedianRateBuyOrders25($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Rate;
		if($counter == 25){
			break;
		}
	}
	$median = $sum / 25;
	return $median;
	
}
function getMedianRateSellOrders25($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Rate;
		if($counter == 25){
			break;
		}
	}
	$median = $sum / 25;
	return $median;
}


function getMedianRateBuyOrders50($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Rate;
		if($counter == 50){
			break;
		}
	}
	$median = $sum / 50;
	return $median;
	
}
function getMedianRateSellOrders50($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Rate;
		if($counter == 50){
			break;
		}
	}
	$median = $sum / 50;
	return $median;
}

function getMedianRateBuyOrders75($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Rate;
		if($counter == 75){
			break;
		}
	}
	$median = $sum / 75;
	return $median;
	
}
function getMedianRateSellOrders75($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Rate;
		if($counter == 75){
			break;
		}
	}
	$median = $sum / 75;
	return $median;
}

function getMedianRateBuyOrders100($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Rate;
		if($counter == 100){
			break;
		}
	}
	$median = $sum / 100;
	return $median;
	
}
function getMedianRateSellOrders100($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Rate;
		if($counter == 100){
			break;
		}
	}
	$median = $sum / 100;
	return $median;
}




function getSumQuantityBuyOrdersBase75($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Quantity * (float)$buyOrder -> Rate;
		if($counter == 75){
			break;
		}
	}
	return $sum;
}
function getSumQuantitySellOrdersBase75($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Quantity * (float)$sellOrder -> Rate;
		if($counter == 75){
			break;
		}
	}
	return $sum;
}



function getSumQuantityBuyOrdersMain75($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Quantity;
		if($counter == 75){
			break;
		}
	}
	return $sum;
}
function getSumQuantitySellOrdersMain75($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Quantity;
		if($counter == 75){
			break;
		}
	}
	return $sum;
}

function getSumQuantityBuyOrdersBase50($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Quantity * (float)$buyOrder -> Rate;
		if($counter == 50){
			break;
		}
	}
	return $sum;
}
function getSumQuantitySellOrdersBase50($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Quantity * (float)$sellOrder -> Rate;
		if($counter == 50){
			break;
		}
	}
	return $sum;
}



function getSumQuantityBuyOrdersMain50($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Quantity;
		if($counter == 50){
			break;
		}
	}
	return $sum;
}
function getSumQuantitySellOrdersMain50($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Quantity;
		if($counter == 50){
			break;
		}
	}
	return $sum;
}



function getSumQuantityBuyOrdersBase25($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Quantity * (float)$buyOrder -> Rate;
		if($counter == 25){
			break;
		}
	}
	return $sum;
}
function getSumQuantitySellOrdersBase25($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Quantity * (float)$sellOrder -> Rate;
		if($counter == 25){
			break;
		}
	}
	return $sum;
}



function getSumQuantityBuyOrdersMain25($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Quantity;
		if($counter == 25){
			break;
		}
	}
	return $sum;
}
function getSumQuantitySellOrdersMain25($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Quantity;
		if($counter == 25){
			break;
		}
	}
	return $sum;
}

function getSumQuantityBuyOrdersBase100($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Quantity * (float)$buyOrder -> Rate;
		if($counter == 100){
			break;
		}
	}
	return $sum;
}

function getSumQuantitySellOrdersBase100($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Quantity * (float)$sellOrder -> Rate;
		if($counter == 100){
			break;
		}
	}
	return $sum;
}

function getSumQuantityBuyOrdersMain100($buyOrders){
	$sum = 0;
	$counter = 0;
	foreach($buyOrders as $buyOrder){
		$counter++;
		$sum += (float)$buyOrder -> Quantity;
		if($counter == 100){
			break;
		}
	}
	return $sum;
}

function getSumQuantitySellOrdersMain100($sellOrders){
	$sum = 0;
	$counter = 0;
	foreach($sellOrders as $sellOrder){
		$counter++;
		$sum += (float)$sellOrder -> Quantity;
		if($counter == 100){
			break;
		}
	}
	return $sum;
}




function rolling_curl($callback) {
	$markets = $GLOBALS['markets'];
	$options = getCurlOptions();
	$rolling_window = 10;
	$rolling_window = (sizeof($markets) < $rolling_window) ? sizeof($markets) : $rolling_window;

	$master = curl_multi_init();


	// start the first batch of requests
	for ($i = 0; $i < $rolling_window; $i++) {
		$ch = curl_init();
		$options[CURLOPT_URL] = getCurrMarketOrderBookURL($markets[$i]);
		curl_setopt_array($ch,$options);
		curl_multi_add_handle($master, $ch);
	}

	do {
		
		while(($execrun = curl_multi_exec($master, $run)) == CURLM_CALL_MULTI_PERFORM);
		if($execrun != CURLM_OK){
			break;
		}
		curl_multi_select($master);
		// a request was just completed -- find out which one
		while($done = curl_multi_info_read($master)) {
		   
			$info = curl_getinfo($done['handle']);

			
			if (!empty($info['url']))  {
				 //echo $info['http_code'],PHP_EOL;


				$output = curl_multi_getcontent($done['handle']);
				$output = json_decode($output);
				$output = $output ->result;

				// request successful.  process output using the callback function.
				
				$callback($output,$info['url']);

				// start a new request (it's important to do this before removing the old one)
				if($i <= count($markets) - 1){
					$ch = curl_init();
					$url = getCurrMarketOrderBookURL($markets[$i++]);   // increment i
					$options[CURLOPT_URL] = $url;  

					curl_setopt_array($ch,$options);
					curl_multi_add_handle($master, $ch);

					// remove the curl handle that just completed
					curl_multi_remove_handle($master, $done['handle']);
				}

				
			}else{
				echo "FAILED",PHP_EOL;
			} 
		}
	} while ($run);



	curl_multi_close($master);
}






function getCurlOptions(){
	
	
	$options = array(
					 CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
					 CURLOPT_SSLVERSION => 1,
					 CURLOPT_SSL_VERIFYHOST => false,
					 CURLOPT_SSL_VERIFYPEER => false,
					 CURLOPT_RETURNTRANSFER => 1,
					 );
	return $options;                 
} 



function curlRequest($url){
	$ch = curl_init();
	$options = getCurlOptions();
	$options[CURLOPT_URL] = $url;
	curl_setopt_array($ch,$options);
	
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	curl_close ($ch);
	$obj = json_decode($result);
	$obj = $obj ->result;
	return $obj;
}





















?>