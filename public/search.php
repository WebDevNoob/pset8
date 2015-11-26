<?php
	require(__DIR__ . "/../includes/config.php");

	if (empty($_GET["geo"])){
	   http_response_code(400);
	   exit;
	}

	$searchTerms = array_map('trim',explode(",",urldecode($_GET['geo'])));
	$sql = "SELECT * FROM `places` WHERE ";
	$junkVar = 0;
	$places = [];
 	
	if(!((array_search("US", $searchTerms))==0)){
		unset($searchTerms[array_search("US", $searchTerms)]);
	} 

	$numTerms = count($searchTerms);

	foreach($searchTerms as $terms){
		if(is_numeric($terms)){
			$sql .= 'postal_code LIKE "' . htmlspecialchars($terms, ENT_QUOTES) . '%"';
		}else{
			$sql .= '(place_name LIKE "' . htmlspecialchars($terms, ENT_QUOTES) . '%" OR ';
			if((strlen($terms) == 2)){
				$sql .= 'admin_code1 LIKE "' . htmlspecialchars($terms, ENT_QUOTES) . '%" OR ';
			}
			$sql .= 'admin_name1 LIKE "' . htmlspecialchars($terms, ENT_QUOTES) . '%")';
		}
		$junkVar = $junkVar + 1;
		if($junkVar < $numTerms){
			$sql .= " AND ";
		}
	}

	$places = query($sql);

    $headers = [
        "Accept" => "*/*",
        "Connection" => "Keep-Alive",
        "User-Agent" => sprintf("curl/%s", curl_version()["version"])
    ];

    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($places, JSON_PRETTY_PRINT));
?>
