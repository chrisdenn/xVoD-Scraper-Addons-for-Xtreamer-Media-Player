<?php




getCinefilms();





function getCinefilms(){
	$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Cookie: lang=de;\r\n"
  )
);
$context = stream_context_create($opts);

	$content = file_get_contents('http://movie2k.to/index.php', false, $context);
	preg_match_all('/<div style=\"float:left\">(.*)<div id=\"xline\"><\/div>/siU', $content, $tables);
	//var_dump($tables);
	foreach($tables[0] as $moviedata){
	//var_dump($moviedata);
	preg_match('/<a href=\"(.*)\" ><img src=\"(.*)\" border=0 style=\"width:105px;max-width:105px;max-height:160px;min-height:140px;\" alt=\"(.*)\" title=\"(.*)\"><\/a>/siU', $moviedata, $url);
	var_dump($url);
	}
}













?>