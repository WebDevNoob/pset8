<?php

    require(__DIR__ . "/../includes/config.php");

    // ensure proper usage
    if (empty($_GET["geo"]))
    {
        http_response_code(400);
        exit;
    }

    // escape user's input
    $geo = urlencode($_GET["geo"]);

    $headers = [
        "Accept" => "*/*",
        "Connection" => "Keep-Alive",
        "User-Agent" => sprintf("curl/%s", curl_version()["version"])
    ];

    $context = stream_context_create([
        "http" => [
            "header" => implode(array_map(function($value, $key) { return sprintf("%s: %s\r\n", $key, $value); }, $headers, array_keys($headers))),
            "method" => "GET"
        ]
    ]);

    $contents = @file_get_contents("http://en.wikipedia.org/w/api.php?action=query&prop=extracts&titles={$geo}&format=json&formatversion=2&exintro=1", false, $context);
    if ($contents === false)
    {
        http_response_code(503);
        exit;
    }
	  
// pattern for first match of a paragraph
$pattern = '#<p>(.*)</p>#Us'; // http://www.phpbuilder.com/board/showthread.php?t=10352690
preg_match($pattern, $contents, $matches);
$info = $matches[1];

    // output articles as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($info, JSON_PRETTY_PRINT));

?>
