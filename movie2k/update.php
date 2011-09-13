<?php
/*-------------------------
 *    Developed by Maicros
 *	  modified for movie2k.to
 *		by Mezzo mod by exe
 *     GNU/GPL v2 Licensed
 * ------------------------*/


$hdbox_repositoy = "https://github.com/chrisdenn/xVoD-Scraper-Addons-for-Xtreamer-Media-Player/tree/master/movie2k/";

if ( url_exists( $hdbox_repositoy.$versionfile ) )
   $versioninfohost  = simplexml_load_file( $hdbox_repositoy.$versionfile );
else
   $versioninfohost  = "0.0.0";

      /*
      Standardmäßig gibt version_compare()
         -1 zurück, wenn die erste Version kleiner ist als die zweite,
         0, wenn die Versionen gleich sind und
         1, wenn die zweite Version kleiner ist.
      */
      //echo " versioninfolocal: $versioninfohost->version  versioninfolocal: $versioninfolocal->version \n";



if( isset($_GET["update"]) && "true" == $_GET["update"] )
{
   update( $versioninfolocal, $versioninfohost, $hdbox_repositoy );
}
else
if ( 0 < version_compare( $versioninfohost->version, $versioninfolocal->version ) )
{
   // update possible
   newVersionAvailable( $versioninfolocal->version, $versioninfohost->version, $serverquery );
}
else
{
   nothingNew();
}





function update( $localversion, $hostversion, $hdbox_repositoy )
{
   //print_r( $hostversion );
   foreach( $hostversion->update->file as $updatefile ){
      //echo "$hdbox_repositoy$updatefile\n";
      $updateresult["$updatefile"] = copy ( "$hdbox_repositoy$updatefile" , "./$updatefile" );
   }
   //print_r( $updateresult );
        ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://purl.org/dc/elements/1.1/">
   <mediaDisplay name="photoView"
                 showHeader="yes" rowCount="1" columnCount="1" drawItemText="yes" showDefaultInfo="no"
                 itemOffsetXPC="25" itemOffsetYPC="88" sliding="yes" itemPerPage="1"
                 itemBorderColor="180:200:50" itemWidthPC="50" itemHeightPC="6"
                 bottomYPC="88" sideTopHeightPC="20" itemBackgroundColor="0:0:0" itemGap="0"
                 backgroundColor="0:0:0" sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"
                 idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"
                 fontSize="18">
        <?php xVoDLoader(); ?>
       <backgroundDisplay>
           <image  offsetXPC="2" offsetYPC="0" widthPC="96" heightPC="100">
                       <? echo XTREAMER_IMAGE_PATH; ?>background/movies-black.jpg
           </image>

           <text backgroundColor="0:0:0" offsetXPC="0" offsetYPC="0" widthPC="60" heightPC="8" lines="1"/>
           <text backgroundColor="0:0:0" offsetXPC="66" offsetYPC="0" widthPC="34" heightPC="8" lines="1"> Made by Mezzo feat. exe </text>


           <image  offsetXPC="20" offsetYPC="21" widthPC="60" heightPC="30">
                       <? echo SCRAPER_URL; ?>hdbox.png
           </image>
           <text redraw="no" align="center"
             backgroundColor="0:0:0" foregroundColor="255:255:255"
             offsetXPC="10" offsetYPC="9" widthPC="80" heightPC="7" fontSize="24" lines="1">
           <![CDATA[Update Ergebnis]]>
       </text>


             <?
             $offsetY = 52;
      foreach ( $updateresult as $file => $result ){
         echo '
           <text redraw="no" align="center"
                backgroundColor="0:0:0" foregroundColor="255:255:255"
                offsetXPC="10" offsetYPC="'.$offsetY.'" widthPC="80" heightPC="3" fontSize="16" lines="1">
     <![CDATA[Datei '.$file.' '. ( $result ? "erfolgreich aktualisiert" : "fehlgeschlagen" ) .']]>
          </text>
          ';
          $offsetY += 5;
      }
              ?>
       </backgroundDisplay>
   </mediaDisplay>

    <channel>
      <title>update</title>
      <item>
           <title>OK</title>
           <link><? echo SCRAPER_URL; ?></link>
       </item>
     </channel>
   </rss>
   <?

}

function nothingNew( )
{
        ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://purl.org/dc/elements/1.1/">
   <mediaDisplay name="photoView"
                 showHeader="yes" rowCount="1" columnCount="1" drawItemText="yes" showDefaultInfo="no"
                 itemOffsetXPC="25" itemOffsetYPC="88" sliding="yes" itemPerPage="1"
                 itemBorderColor="180:200:50" itemWidthPC="50" itemHeightPC="6"
                 bottomYPC="88" sideTopHeightPC="20" itemBackgroundColor="0:0:0" itemGap="0"
                 backgroundColor="0:0:0" sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"
                 idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"
                 fontSize="18">
        <?php xVoDLoader(); ?>
       <backgroundDisplay>
           <image  offsetXPC="2" offsetYPC="0" widthPC="96" heightPC="100">
                       <? echo XTREAMER_IMAGE_PATH; ?>background/movies-black.jpg
           </image>

           <text backgroundColor="0:0:0" offsetXPC="0" offsetYPC="0" widthPC="60" heightPC="8" lines="1"/>
           <text backgroundColor="0:0:0" offsetXPC="66" offsetYPC="0" widthPC="34" heightPC="8" lines="1"> Made by Mezzo feat. exe </text>


           <image  offsetXPC="20" offsetYPC="21" widthPC="60" heightPC="30">
                       <? echo SCRAPER_URL; ?>movie2k.png
           </image>
           <text redraw="no" align="center"
             backgroundColor="0:0:0" foregroundColor="255:255:255"
             offsetXPC="10" offsetYPC="9" widthPC="80" heightPC="7" fontSize="24" lines="1">
           <![CDATA[Kein neues Update verfügbar, diese Version ist aktuell]]>
       </text>

     </backgroundDisplay>

     <itemDisplay>
        <text redraw="yes" align="center" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" fontSize="18">
            <script>
                getItemInfo(-1,"title");
            </script>
        </text>
    </itemDisplay>

   </mediaDisplay>

   <channel>
      <title>update</title>
      <item>
           <title>OK</title>
           <link><? echo SCRAPER_URL; ?></link>
      </item>
   </channel>
</rss>
   <?
}

function newVersionAvailable( $localversion, $hostversion, $serverquery )
{
        ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://purl.org/dc/elements/1.1/">
   <mediaDisplay name="photoView"
                 showHeader="yes" rowCount="1" columnCount="2" drawItemText="yes" showDefaultInfo="no"
                 itemOffsetXPC="25" itemOffsetYPC="56" sliding="yes" itemPerPage="2"
                 itemBorderColor="180:200:50" itemWidthPC="25" itemHeightPC="7"
                 bottomYPC="88" sideTopHeightPC="20" itemBackgroundColor="0:0:0" itemGap="0"
                 backgroundColor="0:0:0" sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"
                 idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"
                 fontSize="18">
        <?php xVoDLoader(); ?>
       <backgroundDisplay>
           <image  offsetXPC="2" offsetYPC="0" widthPC="96" heightPC="100">
                       <? echo XTREAMER_IMAGE_PATH; ?>background/movies-black.jpg
           </image>

           <text backgroundColor="0:0:0" offsetXPC="0" offsetYPC="0" widthPC="60" heightPC="8" lines="1"/>
           <text backgroundColor="0:0:0" offsetXPC="66" offsetYPC="0" widthPC="34" heightPC="8" lines="1"> Made by Mezzo feat. exe </text>


           <image  offsetXPC="20" offsetYPC="21" widthPC="60" heightPC="30">
                       <? echo SCRAPER_URL; ?>movie2k.png
           </image>
           <text redraw="no" align="center"
             backgroundColor="0:0:0" foregroundColor="255:255:255"
             offsetXPC="10" offsetYPC="9" widthPC="80" heightPC="7" fontSize="24" lines="1">
           <![CDATA[Neue Version verfügbar!]]>
       </text>

           <text redraw="no" align="center"
             backgroundColor="0:0:0" foregroundColor="255:255:255"
             offsetXPC="10" offsetYPC="16" widthPC="80" heightPC="5" fontSize="18" lines="1">
           <![CDATA[Aktuell: <? echo $localversion; ?> - Online verfügbar: <? echo $hostversion; ?>]]>
       </text>

           <text redraw="no" align="center"
             backgroundColor="0:0:0" foregroundColor="255:255:255"
             offsetXPC="10" offsetYPC="51" widthPC="80" heightPC="5" fontSize="18" lines="1">
  <![CDATA[Aktualisieren?]]>
       </text>

     </backgroundDisplay>

     <itemDisplay>
        <text redraw="yes" align="center" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" fontSize="18">
            <script>
                getItemInfo(-1,"title");
            </script>
        </text>
    </itemDisplay>
   </mediaDisplay>

    <channel>
      <title>update</title>
          <item>
              <title>JA</title>
              <link><? echo SCRAPER_URL."?".$serverquery; ?>&amp;update=true</link>
          </item>
          <item>
              <title>NEIN</title>
              <link><? echo SCRAPER_URL; ?></link>
          </item>
     </channel>
   </rss>
   <?
}


/*
function url_exists($url) {
    $hdrs = @get_headers($url);
    return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
}
*/
?>
