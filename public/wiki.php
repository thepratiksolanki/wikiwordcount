<?php
$url = 'https://en.wikipedia.org/w/api.php?action=query&explaintext=true&format=json&pageids=21721040&prop=extracts';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_USERAGENT, 'MyBot/1.0 (http://www.mysite.com/)');

$result = curl_exec($ch);

if (!$result) {
  exit('cURL Error: '.curl_error($ch));
}

// var_dump($result);

$result = json_decode($result ,true);
$title = $result["query"]["pages"]["21721040"]["title"];
$result = $result["query"]["pages"]["21721040"]["extract"];
$result = explode(" ",$result);
#print_r($result);
$final = array();

$count = count($result);
for($i =0 ;$i<$count;$i++){
    if(ctype_alnum($result[$i])){
        $result[$i] = strtolower($result[$i]);
        if($final[$result[$i]]){
            $final[$result[$i]] += 1;
        } else {
            $final[$result[$i]] = 1;
        }
    }
}
arsort($final);
// print_r($final);

$sorted = array();
$count = count($final);
foreach($final as $key=>$value){
    if($sorted[$value]){
        $sorted[$value] = $sorted[$value].",".$key;
    } else {
        $sorted[$value] = $key;
    }
}
$type= php_sapi_name();
if ($type == 'cli' || $type == 'cgi-fcgi'){
    $break = "\n";
} else {
    $break = "<br>";
}
echo "URL : ".$url;
echo $break;
echo "Title : ".$title;
echo $break;
echo "Top 5 words : ";
echo $break;
$count = 1;
foreach($sorted as $key=>$value){
    if($count < 5){
        echo " - " .$key. " " .$value;
        echo $break;
        $count += 1;
    } else {
        break;
    }
}


?>