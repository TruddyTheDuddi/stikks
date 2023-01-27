<?php
include_once 'tools.php';

$r = new JSON_Resp();

// Check if url get is set
if(!isset($_GET["url"])) {
    $r->set_error("Bru, no URL provided");
    exit();
}

// Check if url base is gamejolt.com/p/
if(!preg_match("/^https:\/\/gamejolt\.com\/p\//", $_GET["url"])) {
    $r->set_error("Bru, that URL is most likely not pointing towards a gamejolt post...");
    exit();
}

// Explode the url at - and get the last part
$parts = explode("-", $_GET["url"]);
if(count($parts) == 0) {
    $r->set_error("Bru, that URL is most likely not pointing towards a gamejolt post...");
    exit();
}
$hash = $parts[count($parts)-1];

// Get the json data 
$context = stream_context_create(['http' => ['ignore_errors' => true]]);
$resp = @file_get_contents('https://gamejolt.com/site-api/web/posts/view/'.$hash, false, $context);
$resp_json = json_decode($resp, true);

// Valid response? Should contain post field
if(!isset($resp_json["payload"]["post"])) {
    $res_code = "";
    if(isset($http_response_header[0])){
        $res_code = "(".$http_response_header[0].")";
    }
    $r->set_error("Could not find post... $res_code");
    exit();
}

// Add author info
$r->add_field("pfp", $resp_json["payload"]["post"]["user"]["img_avatar"]);
$r->add_field("username", $resp_json["payload"]["post"]["user"]["username"]);


// Check if sticker fields count exits, otherwise set them to 0
if(!isset($resp_json['payload']['post']['sticker_counts'])) {
    $r->add_field("num", 0);
    $r->add_field("cnum", 0);
    $r->add_field("stickers", []);
    exit();
}

$stkr_arr = json_decode($resp_json['payload']['post']['sticker_counts'], true);

// Sum up the normal and charged stickers
$nb_normal = 0;
$nb_charged = 0;
foreach($stkr_arr as $k => $v) {
    $nb_normal += $v['num'];
    $nb_charged += $v['cnum'];
}

$r->add_field("num", $nb_normal);
$r->add_field("cnum", $nb_charged);

// $stickers = json_encode($stkr_arr, true);
$r->add_field("stickers", $stkr_arr);

exit();

?>