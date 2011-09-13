<?php
/*-------------------------
 *    Developed by Maicros
  * updated for Movie2k by Datafreak and Mezzo
 *     GNU/GPL v2 Licensed
 * ------------------------*/

include_once '../../config/config.php';
include_once 'Movie2kTemplate.php';
include_once "../../util/VideoUtil.php";
include_once "../../util/RssScriptUtil.php";
include_once '../../action/Action.php';
include_once '../../action/rss/SaveBookmarkAction.php';
include_once '../../action/rss/DeleteBookmarkAction.php';
define("SCRAPER_URL", SERVER_HOST_AND_PATH . "php/scraper/movie2k/");
$versionfile     = "version.xml";

if ( file_exists( "./$versionfile" ) )
   $versioninfolocal = simplexml_load_file( "./$versionfile" );
else
   $versioninfolocal = "0.0.0";

$version = ( $versioninfolocal != "0.0.0" ) ? $versioninfolocal->version : $versioninfolocal;
define("SCRAPER_VERSION", $version);


if(isset($_GET["cat"])) {    //Category movie view -----------------------------
    if ($_GET["cat"] == "ov"){
    	fetchMovieCategories();
    }
	else if ($_GET["cat"] == "all"){
		fetchAllMovieLetters();
	}
	else if ($_GET["cat"] == "kin"){
		fetchCineMovie();
	}
	else if ($_GET["cat"] == "top"){
		fetchTopMovie();
	}
	else
	if ($_GET["cat"] == "update"){
		include_once ( './update.php');
	}
    else{
    	$category = base64_decode($_GET["cat"]);
    	$title = base64_decode($_GET["title"]);
    	fetchMovieCategoryItems($category,$title);
    	}

}else if(isset($_GET["item"])) {    //Movie detail view -------------------------------
    $item = base64_decode($_GET["item"]);
    $title = base64_decode($_GET["title"]);
    $image = base64_decode($_GET["image"]);
    $host = base64_decode($_GET["host"]);
    fetchMovie($item,$title,$image,$host);

}else if(isset($_GET["letter"])) {
    $letter = $_GET["letter"];
    fetchAllMovieItems($letter);

}else if(isset($_GET["search"])){
	 $type = $_GET["search"];
	 fetchSearch($type);
}else {    // Show home view --------------------------

     $template = new Movie2kTemplate();

     $template->addItem(
        	"Alle Filme",
        	"",
     		SCRAPER_URL . "index.php?cat=all",
        	""
        );
     $template->addItem(
        	"Top Filme",
        	"",
     		SCRAPER_URL . "index.php?cat=top",
        	""
        );
     $template->addItem(
        	"Kinofilme",
        	"",
     		SCRAPER_URL . "index.php?cat=kin",
        	""
        );

     $template->addItem(
        	"Genres",
        	"",
     		SCRAPER_URL . "index.php?cat=ov",
        	""
        );
     $template->setSearch( array(
            resourceString("search_by") . "...",
            resourceString("search_by") . "...",
            "rss_command://search",
            SCRAPER_URL . "index.php?search=%s" . URL_AMP . "type=$type" . URL_AMP . "title=" . base64_encode("Search by"),
            ""
            )
            
    );
    $template->addItem(
        	"Update",
        	"",
     		SCRAPER_URL . "index.php?cat=update",
        	""
        );

    $template->generateView(Movie2kTemplate::VIEW_HOME, "");

}


//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function getCookie(){
 $cookiefile = "/tmp/mov2k";
/*
   if ( file_exists( $cookiefile ) )
   {
      $timeout = fileatime ( $cookiefile ) + (2 * 60 * 60); // reread cookie each 2 hours
      if ( 0 < ( $timeout - time() ) )
      {
         return file_get_contents( $cookiefile );
      }
   }
*/
$header = get_headers("http://www.movie2k.to");
//var_dump($header);
$cookiestring = str_replace("Set-Cookie: ", "", $header[1]);
$location = str_replace("Location: ", "", $header[2]);
//var_dump($cookiestring);


    //$content = file_get_contents('http://www.movie2k.to'.$location, false, $context);
    //var_dump($content);
    $ch = curl_init();

// setze die URL und andere Optionen
curl_setopt($ch, CURLOPT_URL, "http://www.movie2k.to/".$location);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_COOKIE, $cookiestring);
preg_match_all('|Set-Cookie: (.*);|U', curl_exec($ch), $cookie, PREG_SET_ORDER);
//var_dump($cookie);
curl_close($ch);
 $opts = array(
            'http'=>array(
                    'method'=>"GET",
                    'header'=> "Host: www.movie2k.to\r\n".
                                "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; es-ES; rv:1.9.2.10) Gecko/20100914 Firefox/3.6.10 (.NET CLR 3.5.30729)\r\n".
                                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n".
                                "Cookie: ".$cookie[0][1]."; ".$cookie[1][1].";lang=de\r\n"
            )
    );
    //"Cookie: sitechrx=" . $hash . ";Path=/;\r\n"
    $context = stream_context_create($opts);


//var_dump($data);
// schließe den cURL-Handle und gebe die Systemresourcen frei


    //var_dump($context);
    //$context .= "\r\n";
    file_put_contents( $cookiefile, $cookie[0][1]."; ".$cookie[1][1].";lang=de\r\n" );
    return $context;

}


function fetchAllMovieItems($letter){
	//$letter = strtolower($letter);
	$template = new Movie2kTemplate();
    $template->setLetter($letter);

    //If page equal "x" goto page number list, in other case process actual category page
    if( isset($_GET["page"]) && $_GET["page"] == "x" ) {
        $maxPages = $_GET["pages"];
        for($i=1;$i<=$maxPages;++$i) {
            $template->addItem(
                    $i,
                    resourceString("goto_page") . $i,
                    SCRAPER_URL . "index.php?letter=" . $letter . URL_AMP . "page=" . $i . URL_AMP . "pages=" . $maxPages,
                    ""
            );
        }
        $template->generateView(Movie2kTemplate::VIEW_PAGE_NUMBERS );

    }else {
        if(!isset($_GET["page"])) {
            $pages = getPages("http://www.movie2k.to/movies-all-" . $letter . ".html",$letter);
            $template->setActualPage(1);
            $template->setMaxPages($pages[1]);
            $content = $pages[0];
        }else {
            $template->setActualPage($_GET["page"]);
            $template->setMaxPages($_GET["pages"]);
            $content = file_get_contents("http://www.movie2k.to/movies-all-" . $letter . "-" . ($_GET["page"]) . ".html", false, getCookie());
            $newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
            $content = str_replace($newlines, "", html_entity_decode($content,ENT_QUOTES,"UTF-8"));
        }

		//Get movies block
		$content = strstr($content,"tablemoviesindex");
		$content = substr($content, 0, strpos($content,"</TABLE>"));
		//var_dump($content);
		//preg_match_all("/<a href\=\"(.*)\">(.*)<\/a>/siU", $content, $links, PREG_SET_ORDER);
	preg_match_all("/<a href\=\"(.*)\">(.*)<\/a>/siU", $content, $links, PREG_SET_ORDER);
	//preg_match_all("/<a href=\"(.*)\">(.*)<\/a>/U", $content, $links, PREG_SET_ORDER);
	//var_dump($links);
		if($links) {
            foreach ($links as $link) {
				if(!stristr($link[2], '<img src')) {
					$template->addItem(
							$link[2],
							"",
							SCRAPER_URL . "index.php?title=" . base64_encode($link[2]) . URL_AMP . "item=" . base64_encode("http://www.movie2k.to/".$link[1]),
							""
					);
				}
            }
        }
        $template->generateView(Movie2kTemplate::VIEW_CATEGORY2, "");
        }
}


/**
 */
function fetchMovieCategories() {
    $template = new Movie2kTemplate();

    $content = file_get_contents("http://www.movie2k.to/genres-movies.html", false, getCookie());
    //var_dump(getCookie());
    //var_dump($content);
    $newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
    $content = str_replace($newlines, "", html_entity_decode($content));

	//Get Genre block
	$content = strstr($content,"tablemovies");
	$content = substr($content, 0, strpos($content,"</TABLE>"));

	preg_match_all("/<a href\=\"(.*)\">(.*)<\/a>/U", $content, $links, PREG_SET_ORDER);

	foreach ( $links as $link ) {
        $template->addItem(
                $link[2],
                "",
                SCRAPER_URL . "index.php?cat=" . base64_encode("http://www.movie2k.to/" . $link[1]) . URL_AMP . "title=" . base64_encode($link[2]),
                ""
        );
    }
    $template->generateView(Movie2kTemplate::VIEW_CATEGORY2, "");
}

/**
 */

function fetchMovieCategoryItems($category,$title,$search=null) {
    $template = new Movie2kTemplate();
    $template->setCategory($category);


    //If page equal "x" goto page number list, in other case process actual category page
    if( isset($_GET["page"]) && $_GET["page"] == "x" ) {
        $maxPages = $_GET["pages"];
        for($i=1;$i<=$maxPages;++$i) {
            $template->addItem(
                    $i,
                    resourceString("goto_page") . $i,
                    SCRAPER_URL . "index.php?cat=" . base64_encode($category) . URL_AMP . "page=" . $i . URL_AMP . "pages=" . $maxPages,
                    ""
            );
        }
        $template->generateView(Movie2kTemplate::VIEW_PAGE_NUMBERS );

    }else {
        if(!isset($_GET["page"])) {
            $pages = getPages($category);
            $template->setActualPage(1);
            $template->setMaxPages($pages[1]);
            $content = $pages[0];
        } else {
            $template->setActualPage($_GET["page"]);
            $template->setMaxPages($_GET["pages"]);
            preg_match("/http:\/\/www\.movie2k\.to\/movies-genre-(.*)-(.*)\.html/", $category, $title);

			$category = str_replace($title[2], $_GET["page"], $category);

		}

		$content = file_get_contents($category, false, getCookie());
		$newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
		$content = str_replace($newlines, "", html_entity_decode($content,ENT_QUOTES,"UTF-8"));

		//Get movies block
		$content = strstr($content,"tablemoviesindex");
		$content = substr($content, 0, strpos($content,"</TABLE>"));

		preg_match_all("/<a href\=\"(.*)\">(.*)<\/a>/siU", $content, $links, PREG_SET_ORDER);



		if($links) {
            foreach ($links as $link) {
				if(!stristr($link[2], '<img src')) {
					$template->addItem(
							$link[2],
							"",
							SCRAPER_URL . "index.php?title=" . base64_encode($link[2]) . URL_AMP . "item=" . base64_encode("http://www.movie2k.to/".$link[1]) . URL_AMP . "image=" . base64_encode($link[3]),
							$link[3]
					);
				}
            }
        }

        $template->generateView(Movie2kTemplate::VIEW_CATEGORY2, "");
    }
}

/**
 */

function fetchAllMovieLetters() {
    $template = new Movie2kTemplate();
    $letters = array(
            "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","1"
    );
    foreach ($letters as $letter) {
        $template->addItem(
                $letter,
                resourceString("goto_letter") . $letter,
                SCRAPER_URL . "index.php?letter=" . $letter,
                ""
        );
    }
    $template->generateView(Movie2kTemplate::VIEW_PAGE_NUMBERS );
}


/**
 */
function fetchMovie($movie,$title,$image,$host) {
    $template = new Movie2kTemplate();
    $template->setMovieTitle($title);
    $template->setImage($image);
	if($host){
	switch($host){
		case "Stream2k";
	 		$content = file_get_contents($movie, false, getCookie());
   			preg_match("/flashvars=\"config=(.*)\"/U", $content, $popup);
    		//var_dump($popup);
            $content = file_get_contents($popup[1]);
   			//var_dump($content);
    		preg_match("/<file>(.*)<\/file>/U", $content, $links);
    		//var_dump($links);
    		$template->addMediaItem(
						$links[1],
						"",
						$links[1],
						"",
						VideoUtil::getEnclosureMimetype($links[1])
				);
   			break;
   case "Novamov";
	 		$content = file_get_contents($movie, false, getCookie());
   			//var_dump($content);
   			preg_match("/'http:\/\/www\.novamov\.com\/embed\.php\?v=(.*)'/siU", $content, $popup);
    		//var_dump($popup);
            $content = file_get_contents(substr(($popup[0]),1,-1));
   			//var_dump($content);
    		preg_match("/flashvars.filekey=\"(.*)\"/U", $content, $key);
    		//var_dump($key);
    		$content = file_get_contents("http://www.novamov.com/api/player.api.php?user=undefined&pass=undefined&file=".$popup[1]."&key=".$key[1]);
    		//var_dump($content);
    		preg_match("/url=(.*)&title/U", $content, $links);
    		//var_dump($links);
    		$template->addMediaItem(
						$links[1],
						"",
						$links[1],
						"",
						VideoUtil::getEnclosureMimetype($links[1])
				);
   			break;
   	case "Movshare";
	 		$content = file_get_contents($movie, false, getCookie());
   			//var_dump($content);
   			preg_match("/http:\/\/www\.movshare\.net\/embed\/(.*)\//siU", $content, $popup);
    		//var_dump($popup);
            $content = file_get_contents($popup[0]);
   			//var_dump($content);
    		preg_match("/<param name=\"src\" value=\"(.*)\"/U", $content, $links);

    		//var_dump($links);
    		$template->addMediaItem(
						$links[1],
						"",
						$links[1],
						"",
						VideoUtil::getEnclosureMimetype($links[1])
				);
   			break;
   	case "Putlocker";
	 		$content = file_get_contents($movie, false, getCookie());
   			//var_dump($content);
   			preg_match("/\"http:\/\/www\.putlocker\.com\/file\/(.*)\"/siU", $content, $popup);
    		//var_dump($popup);

    		$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, substr(($popup[0]),1,-1));
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($ch);
//print_r($content);
preg_match('|Set-Cookie: (.*);|U', $content, $cookie);
preg_match("/type=\"hidden\" value=\"(.*)\" name=\"hash\"/siU", $content, $hash);
//var_dump($hash);
$data = "hash=". $hash[1] ."&confirm=Please+wait+for+0+seconds";
//var_dump($data);
//var_dump($cookie);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, substr(($popup[0]),1,-1));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie[1]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($ch);
//var_dump($content);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.putlocker.com/get_file.php?embed_stream=".$popup[1]);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie[1]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($ch);
curl_close($ch);
preg_match("/<\/link><media:content url=\"(.*)\"/U", $content, $links);
    		//var_dump($links);
    		$template->addMediaItem(
						$links[1],
						"",
						$links[1],
						"",
						VideoUtil::getEnclosureMimetype($links[1])
				);
   			break;
   			case "Sockshare";
	 		$content = file_get_contents($movie, false, getCookie());
   			//var_dump($content);
   			preg_match("/\"http:\/\/www\.sockshare\.com\/file\/(.*)\"/siU", $content, $popup);
    		//var_dump($popup);

    		$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, substr(($popup[0]),1,-1));
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($ch);
//print_r($content);
preg_match('|Set-Cookie: (.*);|U', $content, $cookie);
preg_match("/type=\"hidden\" value=\"(.*)\" name=\"hash\"/siU", $content, $hash);
//var_dump($hash);
$data = "hash=". $hash[1] ."&confirm=Please+wait+for+0+seconds";
//var_dump($data);
//var_dump($cookie);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, substr(($popup[0]),1,-1));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie[1]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($ch);
//var_dump($content);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.sockshare.com/get_file.php?embed_stream=".$popup[1]);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie[1]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($ch);
curl_close($ch);
preg_match("/<\/link><media:content url=\"(.*)\"/U", $content, $links);
    		//var_dump($links);
    		$template->addMediaItem(
						$links[1],
						"",
						$links[1],
						"",
						VideoUtil::getEnclosureMimetype($links[1])
				);
   			break;
   				case "Divxstage";
	 		$content = file_get_contents($movie, false, getCookie());
   			//var_dump($content);
   			preg_match("/http:\/\/www\.divxstage\.eu\/video\/(.*)\"/siU", $content, $popup);
    		//var_dump($popup);
            $content = file_get_contents($popup[0]);
   			//var_dump($content);
    		preg_match("/s1\.addParam\('flashvars'\,'file=(.*)&type=video'\)/siU", $content, $links);

    		//var_dump($links);
    		$template->addMediaItem(
						$links[1],
						"",
						$links[1],
						"",
						VideoUtil::getEnclosureMimetype($links[1])
				);
   			break;






   }
   }
   else {
    //Parse movie page
    $content = file_get_contents($movie, false, getCookie());
    //var_dump($content);
    $newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
    $content = str_replace($newlines, "", html_entity_decode($content,ENT_QUOTES,"UTF-8"));
   // var_dump($content);
    preg_match_all("/<tr id=\"tablemoviesindex2\">(.*)<\/td><\/tr>/siU", $content, $mirrors);
    //var_dump($mirrors);

    foreach ($mirrors[1] as $mirrorlink){
    preg_match_all("/<a href=\"(.*)\">(.*)<img border=(.*) style=\"(.*)\" src=\"(.*)\" alt=\"(.*)\" title=\"(.*)\" width=\"(.*)\"> Â(.*)<\/a><\/td><td align=\"(.*)\" width=\"(.*)\"(.*)/siU",    $mirrorlink, $mirror, PREG_SET_ORDER);
    //var_dump($mirror);



	//preg_match("/<div class=\"beschreibung\">(.*)<iframe/U", $content, $desc);
    //$description = $desc;
    //$template->setDescription($description[1]);

    //Get megavideo id and link
    //preg_match("/flashvars=\"config=(.*)\"/U", $content, $popup);
    //var_dump($popup);
    //$content = file_get_contents($popup[1]);
   //var_dump($content);
    //preg_match("/<file>(.*)<\/file>/U", $content, $links);
    //var_dump($links);


    if($mirror) {

                    $template->addItem(
                    substr($mirror[0][9],1),
                    $mirror[0][2],
                    SCRAPER_URL . "index.php?title=" . base64_encode($title) . URL_AMP . "item=" . base64_encode("http://movie2k.to/".$mirror[0][1]) . URL_AMP . "host=" . base64_encode(substr($mirror[0][9],1)). URL_AMP . "image=" . base64_encode($image),
                    ""

            );
        }
      }
}

    $template->generateView(Movie2kTemplate::VIEW_MOVIE_DETAIL);
}
/**
 */

function fetchTopMovie() {
    $template = new Movie2kTemplate();

    $content = file_get_contents("http://www.movie2k.to/movies-top.html", false, getCookie());
    $newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
    $content = str_replace($newlines, "", html_entity_decode($content,ENT_QUOTES,"UTF-8") );

   //Get movies block
	$content = strstr($content,"tablemoviesindex");
	$content = substr($content, 0, strpos($content,"</TABLE>"));

	preg_match_all("/<a href\=\"(.*)\">(.*)<\/a>/siU", $content, $links, PREG_SET_ORDER);

	if($links) {
		foreach ($links as $link) {
			if(!stristr($link[2], '<img src')) {
				$template->addItem(
						$link[2],
						"",
						SCRAPER_URL . "index.php?title=" . base64_encode($link[2]) . URL_AMP . "item=" . base64_encode($link[1]) . URL_AMP . "image=" . base64_encode($link[3]),
						$link[3]
				);
			}
		}
	}

	$template->generateView(Movie2kTemplate::VIEW_CATEGORY2, "");
}

function fetchCineMovie(){
	 $template = new Movie2kTemplate();

	$content = file_get_contents('http://movie2k.to/index.php', false, getCookie());
	preg_match_all('/<div style=\"float:left\">(.*)<div id=\"xline\"><\/div>/siU', $content, $tables);
	//var_dump($tables);
	foreach($tables[0] as $moviedata){
	//var_dump($moviedata);
	preg_match('/<a href=\"(.*)\" ><img src=\"(.*)\" border=0 style=\"width:105px;max-width:105px;max-height:160px;min-height:140px;\" alt=\"(.*)\" title=\"(.*)\"><\/a>/siU', $moviedata, $url);
	$title = str_replace(" kostenlos", "", $url[3]);
	$template->addItem(
						$title,
						"",
						SCRAPER_URL . "index.php?title=" . base64_encode($title) . URL_AMP . "item=" . base64_encode("http://movie2k.to/".$url[1]) . URL_AMP . "image=" . base64_encode($url[2]),
						$url[2]
						);

	//var_dump($url);
	}
	$template->generateView(Movie2kTemplate::VIEW_MOVIE, "");
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

function getPages($url) {

    $content = file_get_contents($url, false, getCookie());

    $newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
    $content = str_replace($newlines, "", html_entity_decode($content,ENT_QUOTES,"UTF-8"));

    //Get page list begin
    preg_match_all("/<div id=\"boxgrey\"><a href=\"movies-(.*)-(.*)-(.*).html\">(.*) <\/a><\/div>/U", $content, $pages, PREG_SET_ORDER);
	//var_dump($pages);
	$pages = end($pages);

    if($pages) {
        $numPages = $pages[4];
    }else {
        $numPages = 1;
    }

	return array($content,$numPages);
}
function fetchSearch($type){
	$template = new Movie2kTemplate();
	$cookiefile = "/tmp/mov2k";
	$data = "search=$type";
	$data_count = strlen($data);
	$cookie = file_get_contents( $cookiefile );

	$fp = fsockopen("www.movie2k.to", 80);

	fputs($fp, "POST /movies.php?list=search HTTP/1.1\r\n");
	fputs($fp, "Accept: */*\r\n");
	fputs($fp, "Host: www.movie2k.to\r\n");
	fputs($fp, "Cookie: $cookie\r\n");
	fputs($fp, "Referer: http://www.movie2k.to/movies.php?list=search\r\n");
	fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
	fputs($fp, "Content-Length: $data_count\r\n");
	fputs($fp, "Connection: close\r\n\r\n");
	fputs($fp, $data);

	while(!feof($fp)) {
		$content .= fgets($fp, 128);
	}
	fclose($fp);
	var_dump($content);
	$newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
    $content = str_replace($newlines, "", html_entity_decode($content,ENT_QUOTES,"UTF-8"));

    //Get movies block
	$content = strstr($content,"tablemoviesindex");
	$content = substr($content, 0, strpos($content,"</TABLE>"));

	preg_match_all("/<a href\=\"(.*)\">(.*)<\/a>/siU", $content, $links, PREG_SET_ORDER);

	if($links) {
		foreach ($links as $link) {
			if(!stristr($link[2], '<img src')) {
				$template->addItem(
						$link[2],
						"",
						SCRAPER_URL . "index.php?title=" . base64_encode($link[2]) . URL_AMP . "item=" . base64_encode($link[1]) . URL_AMP . "image=" . base64_encode($link[3]),
						$link[3]
				);
			}
		}
	}

 $template->generateView(Movie2kTemplate::VIEW_MOVIE, "");


}

?>
