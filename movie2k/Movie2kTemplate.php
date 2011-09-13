<?php
/*-------------------------
 *    Developed by Maicros
 *     GNU/GPL v2 Licensed
 * ------------------------*/

class Movie2kTemplate {

    const VIEW_HOME = 1;

    const VIEW_CATEGORY = 2;
    const VIEW_CATEGORY2 = 8;
    const VIEW_PAGE_NUMBERS = 12;

    const VIEW_MOVIE = 3;
    const VIEW_MOVIE_DETAIL = 4;

    const VIEW_PLAY = 10;

    private $category = null;
    private $url = null;
    private $title = null;
    private $description = null;

    private $movieTitle = null;
    private $search = null;
    private $searchTerm = null;
    private $actualPage = null;
    private $maxPages = null;
    private $items = array();
    private $mediaItems = array();
    private $image = null;
    private $headerImage = null;
    private $letter = null;

    public function setMovieTitle($movieTitle) {
        $this->movieTitle = $movieTitle;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function setHeaderImage($headerImage) {
        $this->headerImage = $headerImage;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function setLetter($letter) {
        $this->letter = $letter;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setActualPage($actualPage) {
        $this->actualPage = $actualPage;
    }

    public function setMaxPages($maxPages) {
        $this->maxPages = $maxPages;
    }

    public function setSearch($search) {
        $this->search = $search;
    }

    public function setSearchTerm($searchTerm) {
        $this->searchTerm = $searchTerm;
    }

    public function addItem($title, $description, $link, $thumbnail) {
        $item = array($title, $description, $link, $thumbnail);
        array_push($this->items, $item);
    }

    public function addMediaItem($title, $description, $link, $thumbnail, $enclosureType) {
        $mediaItem = array($title, $description, $link, $thumbnail, $enclosureType);
        array_push($this->mediaItems, $mediaItem);
    }

    public function generateView($category, $title = null) {
        $this->title = $title;
        header("Content-type: text/xml");
        echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://purl.org/dc/elements/1.1/">'."\n";

        switch($category) {
            case Movie2kTemplate::VIEW_HOME;
                $this->getHomeHeader();
                break;
            case Movie2kTemplate::VIEW_CATEGORY2;
            	$enhanced = true;

            case Movie2kTemplate::VIEW_CATEGORY;
                $this->getCategoryHeader($enhanced, $download);
                break;
            case Movie2kTemplate::VIEW_PAGE_NUMBERS:
                $this->getPageNumbersHeader();
                break;

            case Movie2kTemplate::VIEW_MOVIE;
                $this->getMovieHeader();
                break;
            case Movie2kTemplate::VIEW_MOVIE_DETAIL;
                $this->getMovieDetailHeader();
                break;


            case Movie2kTemplate::VIEW_PLAY;
                $this->getPlayHeader();
                break;
        }

        echo '  <channel>'."\n";
        echo '      <title><![CDATA[' . utf8_encode($title) . ']]></title>' . "\n";

        $i=0;
        foreach ($this->mediaItems as $value) {
            $this->getMediaLink($value,$i) . "\n";
            ++$i;
        }
        if($this->search) {
            $this->getSearchLink($this->search,$i) . "\n";
            ++$i;
        }
        foreach ($this->items as $value) {
            $this->getLink($value,$i) . "\n";
            ++$i;
        }

        echo '  </channel>' . "\n";
        echo '</rss>';
    }

    /**
     * -------------------------------------------------------------------------
     */
    private function getHomeHeader() {
        ?>
<mediaDisplay name="photoView"
              showHeader="yes" rowCount="4" columnCount="3" drawItemText="no" showDefaultInfo="no"
              itemOffsetXPC="5" itemOffsetYPC="50" sliding="yes"
              itemBorderColor="-1:-1:-1" itemWidthPC="29" itemHeightPC="7"
              idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"
              bottomYPC="88" sideTopHeightPC="20" itemBackgroundColor="0:0:0" itemGap="0"
              backgroundColor="0:0:0" sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"
              fontSize="18">
                          <?php xVoDLoader(); ?>

    <!-- TOP MENU TITLES -->
    <text redraw="no"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="15" offsetYPC="2" widthPC="37" heightPC="5" fontSize="10" lines="1"> version: <?=SCRAPER_VERSION; ?>
    </text>
    <text redraw="no"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="65.5" offsetYPC="2" widthPC="7" heightPC="5" fontSize="10" lines="1">
    </text>
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="7" offsetYPC="3.1" widthPC="10" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_home"); ?>]]>
    </text>
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="81" offsetYPC="3.1" widthPC="18" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_add_bookmark"); ?>]]>
    </text>

    <!-- ACTUAL SELECTED ITEM AND NUMBER -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="90" offsetYPC="93" widthPC="10" heightPC="5" fontSize="16" lines="1">
        <script>
            getFocusItemIndex() + " / <?php echo count($this->items); ?>";
        </script>
    </text>

    <!-- MOVIE TITLE -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="30" offsetYPC="93" widthPC="60" heightPC="5" fontSize="16" lines="1">
        <script>
            getItemInfo("title");
        </script>
    </text>

    <itemDisplay>
        <text redraw="yes" backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
         offsetXPC="10" offsetYPC="10" widthPC="80" heightPC="80" fontSize="16" lines="1">
            <script>
                getItemInfo(-1,"title");
            </script>
        </text>
        <image redraw="no" offsetXPC="3" offsetYPC="10" widthPC="95" heightPC="80" >
            <script>
                getItemInfo(-1,"image");
            </script>
        </image>
        <image redraw="yes" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" >
            <script>
                if( getFocusItemIndex() == getQueryItemIndex() )
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/top-bar-focus_300.png";
                else
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/top-bar-unfocus_300.png";
            </script>
        </image>
    </itemDisplay>

    <onUserInput>
        <script>
        <?php RssScriptUtil::showAddBookmarkScript(); ?>
            if(userInput == "0"){
                showIdle();
                jumpToLink("homePageLink");
                redrawDisplay();
            }
        </script>
    </onUserInput>

    <backgroundDisplay>
        <image  offsetXPC="2" offsetYPC="0" widthPC="96" heightPC="100">
                    <? echo XTREAMER_IMAGE_PATH; ?>background/movies-black.jpg
        </image>
        <image  offsetXPC="20" offsetYPC="21" widthPC="60" heightPC="30">
                    <? echo SCRAPER_URL; ?>movie2k.jpg
        </image>
        <text redraw="no" align="center"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="10" offsetYPC="9" widthPC="80" heightPC="7" fontSize="24" lines="1">
       Movie2k.to
    </text>
        <text redraw="no" align="center"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="10" offsetYPC="16" widthPC="80" heightPC="5" fontSize="18" lines="1">

    </text>


    </backgroundDisplay>
</mediaDisplay>

<homePageLink>
    <link>
            <?php echo SERVER_HOST_AND_PATH."php/index.php"; ?>
    </link>
</homePageLink>
<onEnter> setRefreshTime(3000); </onEnter>
<onExit> setRefreshTime(-1); </onExit>
<onRefresh> redrawDisplay(); </onRefresh>
        <?php
    }

    /**
     * -------------------------------------------------------------------------
     */
    private function getPageNumbersHeader() {
        ?>
<mediaDisplay name="photoView"
              showHeader="no" rowCount="7" columnCount="15" drawItemText="no" showDefaultInfo="no"
              itemImageXPC="100" itemImageYPC="100" itemOffsetXPC="5" itemOffsetYPC="20" sliding="yes"
              itemWidthPC="5" itemHeightPC="6" itemBorderColor="-1:-1:-1"
              idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"
              bottomYPC="88" sideTopHeightPC="20" itemBackgroundColor="0:0:0"
              backgroundColor="0:0:0" sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"
              fontSize="11">
                          <?php xVoDLoader(); ?>

    <!-- ACTUAL SELECTED ITEM AND NUMBER -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="90" offsetYPC="93" widthPC="10" heightPC="5" fontSize="16" lines="1">
        <script>
            getFocusItemIndex() + " / <?php echo count($this->items); ?>";
        </script>
    </text>

    <!-- MOVIE TITLE -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="30" offsetYPC="93" widthPC="60" heightPC="5" fontSize="16" lines="1">
        <script>
            getItemInfo("description");
        </script>
    </text>

    <!-- TOP MENU TITLES -->
    <text redraw="no"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="0" offsetYPC="2" widthPC="50" heightPC="5" fontSize="10" lines="1">

    </text>
    <text redraw="no"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="67" offsetYPC="2" widthPC="30" heightPC="5" fontSize="10" lines="1">
    </text>

    <itemDisplay>
        <image redraw="no" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" >
            <script>
                if( getFocusItemIndex() == getItemInfo(-1,"itemid") )
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/top-bar-focus_150.png";
                else
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/top-bar-unfocus_150.png";
            </script>
        </image>
        <text redraw="yes"
              backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
              offsetXPC="20" offsetYPC="10" widthPC="60" heightPC="80" fontSize="16" lines="1">
            <script>
                getItemInfo(-1,"title");
            </script>
        </text>
    </itemDisplay>

    <backgroundDisplay>
        <image  offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
                    <? echo XTREAMER_IMAGE_PATH; ?>background/movies-black.jpg
        </image>
    </backgroundDisplay>
</mediaDisplay>
        <?php
    }


    /**
     * -------------------------------------------------------------------------
     */

 private function getCategoryHeader( $enhanced, $download = false ) {
        ?>
<mediaDisplay name="photoView"
              showHeader="yes"
              <?
              if ( true == $download  )
                 echo 'rowCount="9" columnCount="1" itemGapYPC="0"';
              else
              if ( true == $enhanced  )
                 echo 'rowCount="9" columnCount="3" itemGapYPC="0"';
              else
                 echo 'rowCount="5" columnCount="3"';

              ?> drawItemText="no" showDefaultInfo="no"
              itemImageXPC="100" itemImageYPC="100" itemOffsetXPC="4" itemOffsetYPC="14" sliding="yes"
              itemBorderColor="-1:-1:-1"
              idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"
              bottomYPC="88" sideTopHeightPC="20" itemBackgroundColor="0:0:0" itemGap="0"
              backgroundColor="0:0:0" sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"
              fontSize="18">
                          <?php xVoDLoader(); ?>

    <!-- MOVIE PAGES -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="5" offsetYPC="9" widthPC="90" heightPC="3" fontSize="10" lines="1">
                      <?php
                      for($i=1;$i<=$this->maxPages;++$i) {
                          if($i==$this->actualPage) {
                              echo "- " . $i . " - &#32;&#32;&#32;";
                          }else {
                              echo $i . "&#32;&#32;&#32;";
                          }
                      }
                      ?>
    </text>



    <!-- TOP MENU TITLES -->
    <text redraw="no"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="15" offsetYPC="2" widthPC="37" heightPC="5" fontSize="10" lines="1">
    </text>
    <text redraw="no"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="65.5" offsetYPC="2" widthPC="7" heightPC="5" fontSize="10" lines="1">
    </text>
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="7" offsetYPC="3.1" widthPC="10" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_home"); ?>]]>
    </text>
       <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="19" offsetYPC="3.1" widthPC="18" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_previous_page"); ?>]]>
    </text>
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="34.5" offsetYPC="3.1" widthPC="18" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_goto_page"); ?>]]>
    </text>
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="50" offsetYPC="3.1" widthPC="18" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_next_page"); ?>]]>
    </text>

    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="81" offsetYPC="3.1" widthPC="18" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_add_bookmark"); ?>]]>
    </text>
     <?php
            if($this->searchTerm) {
                $nextPageLink = SCRAPER_URL."index.php?search=" . $this->searchTerm . URL_AMP . "title=" . base64_encode($this->title) . URL_AMP . "page=" . (($this->actualPage>1) ? ($this->actualPage-1) : 1) . URL_AMP . "pages=" . $this->maxPages;
                $lastPageLink = SCRAPER_URL."index.php?search=" . $this->searchTerm . URL_AMP . "title=" . base64_encode($this->title) . URL_AMP . "page=" . (($this->actualPage<$this->maxPages) ? ($this->actualPage+1) : $this->maxPages) . URL_AMP . "pages=" . $this->maxPages;
                $gotoPagelink = SCRAPER_URL."index.php?search=" . $this->searchTerm . URL_AMP . "pages=". $this->maxPages . URL_AMP . "page=x";
            }
            else if($this->letter){
            $nextPageLink = SCRAPER_URL."index.php?letter=" . ($this->letter) . URL_AMP . "page=" . (($this->actualPage>1) ? ($this->actualPage-1) : 1) . URL_AMP . "pages=" . $this->maxPages;
            $lastPageLink = SCRAPER_URL."index.php?letter=" . ($this->letter) . URL_AMP . "page=" . (($this->actualPage<$this->maxPages) ? ($this->actualPage+1) : $this->maxPages) . URL_AMP . "pages=" . $this->maxPages;
            $gotoPagelink = SCRAPER_URL."index.php?letter=" . ($this->letter) . URL_AMP . "pages=" . $this->maxPages . URL_AMP . "page=x";

            }
            else {
                $nextPageLink = SCRAPER_URL."index.php?cat=" . base64_encode($this->category) . URL_AMP . "title=" . base64_encode($this->title) . URL_AMP . "page=" . (($this->actualPage>1) ? ($this->actualPage-1) : 1) . URL_AMP . "pages=" . $this->maxPages;
                $lastPageLink = SCRAPER_URL."index.php?cat=" . base64_encode($this->category) . URL_AMP . "title=" . base64_encode($this->title) . URL_AMP . "page=" . (($this->actualPage<$this->maxPages) ? ($this->actualPage+1) : $this->maxPages) . URL_AMP . "pages=" . $this->maxPages;
                $gotoPagelink = SCRAPER_URL."index.php?cat=" . base64_encode($this->category) . URL_AMP . "pages=" . $this->maxPages . URL_AMP . "page=x";
            }
            ?>

    <!-- ACTUAL SELECTED ITEM AND NUMBER -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="90" offsetYPC="93" widthPC="10" heightPC="5" fontSize="16" lines="1">
        <script>
            getFocusItemIndex() + " / <?php echo count($this->items); ?>";
        </script>
    </text>

    <!-- MOVIE TITLE -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="30" offsetYPC="93" widthPC="60" heightPC="5" fontSize="16" lines="1">
        <script>
            getItemInfo("title");
        </script>
    </text>

    <itemDisplay>
        <text redraw="yes" backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
        <?php
        if ( true == $enhanced  )
            echo ' offsetXPC="4" offsetYPC="5" widthPC="92" heightPC="90"  ';
        else
            echo ' offsetXPC="10" offsetYPC="10" widthPC="80" heightPC="80"';
        ?> fontSize="16" lines="1">
            <script>
                getItemInfo(-1,"title");
            </script>
        </text>
        <image redraw="no" offsetXPC="3" offsetYPC="10" widthPC="95" heightPC="80" >
            <script>
                getItemInfo(-1,"image");
            </script>
        </image>
        <image redraw="yes" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" >
            <script>
                if( getFocusItemIndex() == getQueryItemIndex() )
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/top-bar-focus_300.png";
                else
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/top-bar-unfocus_300.png";
            </script>
        </image>
    </itemDisplay>

    <onUserInput>
        <script>
        <?php RssScriptUtil::showAddBookmarkScript(); ?>
         if(userInput == "PG"){
                showIdle();
                url = "<?php echo $nextPageLink; ?>";
                jumpToLink("gotoPageLink");
                redrawDisplay();
            }
            if(userInput == "PD"){
                showIdle();
                url = "<?php echo $lastPageLink; ?>";
                jumpToLink("gotoPageLink");
                redrawDisplay();
            }
            if(userInput == "0"){
                showIdle();
                jumpToLink("homePageLink");
                redrawDisplay();
            }
            if(userInput == "video_search"){
                showIdle();
                url = "<?php echo $gotoPagelink; ?>";
                jumpToLink("gotoPageLink");
                redrawDisplay();

        </script>
    </onUserInput>

    <backgroundDisplay>
        <image  offsetXPC="2" offsetYPC="0" widthPC="96" heightPC="100">
                    <? echo XTREAMER_IMAGE_PATH; ?>background/movies-black.jpg
        </image>
    </backgroundDisplay>
</mediaDisplay>
<gotoPageLink>
    <link>
    <script>
        url;
    </script>
    </link>
</gotoPageLink>
<homePageLink>
    <link>
            <?php echo SERVER_HOST_AND_PATH."php/index.php"; ?>
    </link>
</homePageLink>
        <?php
    }
    /**
     * -------------------------------------------------------------------------
     */
    private function getMovieHeader() {
        ?>
<script>
    SwitchViewer(0);
    SwitchViewer(7);
</script>
<mediaDisplay name="photoView"
              showHeader="no" rowCount="2" columnCount="5" drawItemText="no"
              sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"
              itemOffsetXPC="15" itemOffsetYPC="12" itemWidthPC="13.5" itemHeightPC="36"
              itemGapXPC="0.5" itemGapYPC="0.5" itemBorderColor="-1:-1:-1"
              forceFocusOnItem="yes"
              itemCornerRounding="yes"
              itemImageWidthPC="13.5" itemImageHeightPC="36"
              backgroundColor="0:0:0" sliding="yes"
              idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"
              bottomYPC="100" sideTopHeightPC="0" >
                          <?php xVoDLoader(); ?>

    <!-- ACTUAL SELECTED ITEM AND NUMBER -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="90" offsetYPC="93" widthPC="10" heightPC="5" fontSize="16" lines="1">
        <script>
            getFocusItemIndex() + " / <?php echo count($this->items); ?>";
        </script>
    </text>

    <!-- MOVIE DESCRIPTION
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="0:154:205"
          offsetXPC="30" offsetYPC="86" widthPC="70" heightPC="7" fontSize="12" lines="2">
        <script>
            getItemInfo("description");
        </script>
    </text>
    -->

    <!-- MOVIE TITLE -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="30" offsetYPC="93" widthPC="60" heightPC="5" fontSize="16" lines="1">
        <script>
            getItemInfo("title");
        </script>
    </text>

    <!-- MOVIE PAGES -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="5" offsetYPC="9" widthPC="90" heightPC="3" fontSize="10" lines="1">
                      <?php
                      for($i=1;$i<=$this->maxPages;++$i) {
                          if($i==$this->actualPage) {
                              echo "- " . $i . " - &#32;&#32;&#32;";
                          }else {
                              echo $i . "&#32;&#32;&#32;";
                          }
                      }
                      ?>
    </text>

    <!-- TOP MENU TITLES -->
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="5.4" offsetYPC="3.1" widthPC="10" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_categories"); ?>]]>
    </text>
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="19" offsetYPC="3.1" widthPC="18" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_previous_page"); ?>]]>
    </text>
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="34.5" offsetYPC="3.1" widthPC="18" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_goto_page"); ?>]]>
    </text>
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="50" offsetYPC="3.1" widthPC="18" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_next_page"); ?>]]>
    </text>
            <?php if($this->type == "mov") { ?>
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="70" offsetYPC="3.1" widthPC="18" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_play"); ?>]]>
    </text>
                <?php }else { ?>
    <text redraw="no"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="67" offsetYPC="2" widthPC="7" heightPC="5" fontSize="10" lines="1">
    </text>
                <?php } ?>
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="83" offsetYPC="3.1" widthPC="18" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_add_bookmark"); ?>]]>
    </text>

            <?php
            if($this->searchTerm) {
                $nextPageLink = SCRAPER_URL."index.php?search=" . $this->searchTerm . URL_AMP . "title=" . base64_encode($this->title) . URL_AMP . "page=" . (($this->actualPage>1) ? ($this->actualPage-1) : 1) . URL_AMP . "pages=" . $this->maxPages;
                $lastPageLink = SCRAPER_URL."index.php?search=" . $this->searchTerm . URL_AMP . "title=" . base64_encode($this->title) . URL_AMP . "page=" . (($this->actualPage<$this->maxPages) ? ($this->actualPage+1) : $this->maxPages) . URL_AMP . "pages=" . $this->maxPages;
                $gotoPagelink = SCRAPER_URL."index.php?search=" . $this->searchTerm . URL_AMP . "pages=". $this->maxPages . URL_AMP . "page=x";
            }
            else if($this->letter){
            $nextPageLink = SCRAPER_URL."index.php?letter=" . ($this->letter) . URL_AMP . "page=" . (($this->actualPage>1) ? ($this->actualPage-1) : 1) . URL_AMP . "pages=" . $this->maxPages;
            $lastPageLink = SCRAPER_URL."index.php?letter=" . ($this->letter) . URL_AMP . "page=" . (($this->actualPage<$this->maxPages) ? ($this->actualPage+1) : $this->maxPages) . URL_AMP . "pages=" . $this->maxPages;
            $gotoPagelink = SCRAPER_URL."index.php?letter=" . ($this->letter) . URL_AMP . "pages=" . $this->maxPages . URL_AMP . "page=x";

            }
            else {
                $nextPageLink = SCRAPER_URL."index.php?cat=" . base64_encode($this->category) . URL_AMP . "title=" . base64_encode($this->title) . URL_AMP . "page=" . (($this->actualPage>1) ? ($this->actualPage-1) : 1) . URL_AMP . "pages=" . $this->maxPages;
                $lastPageLink = SCRAPER_URL."index.php?cat=" . base64_encode($this->category) . URL_AMP . "title=" . base64_encode($this->title) . URL_AMP . "page=" . (($this->actualPage<$this->maxPages) ? ($this->actualPage+1) : $this->maxPages) . URL_AMP . "pages=" . $this->maxPages;
                $gotoPagelink = SCRAPER_URL."index.php?cat=" . base64_encode($this->category) . URL_AMP . "pages=" . $this->maxPages . URL_AMP . "page=x";
            }
            ?>

    <itemDisplay>
        <text redraw="no"
              backgroundColor="0:0:0" foregroundColor="255:255:255"
              offsetXPC="5" offsetYPC="20" widthPC="90" heightPC="70" fontSize="15" lines="5">
            <script>
                getItemInfo(-1,"title");
            </script>
        </text>
        <image redraw="no" offsetXPC="1" offsetYPC="1" widthPC="98" heightPC="98" >
            <script>
                getItemInfo(-1,"image");
            </script>
        </image>
        <image redraw="no" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" >
            <script>
                if( getFocusItemIndex() == getItemInfo(-1,"itemid") )
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/wall-2x7-focus.png";
                else
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/wall-2x7-thumbnail.png";
            </script>
        </image>
    </itemDisplay>

    <onUserInput>
        <script>
        <?php RssScriptUtil::showAddBookmarkScript(); ?>
            if(userInput == "PG"){
                showIdle();
                url = "<?php echo $nextPageLink; ?>";
                jumpToLink("gotoPageLink");
                redrawDisplay();
            }
            if(userInput == "PD"){
                showIdle();
                url = "<?php echo $lastPageLink; ?>";
                jumpToLink("gotoPageLink");
                redrawDisplay();
            }
            if(userInput == "0"){
                showIdle();
                jumpToLink("homePageLink");
                redrawDisplay();
            }
            if(userInput == "video_search"){
                showIdle();
                url = "<?php echo $gotoPagelink; ?>";
                jumpToLink("gotoPageLink");
                redrawDisplay();

            }else if(userInput == "video_play"){
                showIdle();
                playUrl = getURL("<?php echo SCRAPER_URL . "enclosure.php?link="; ?>" + getItemInfo("link"));
                if(playUrl == "ERROR"){
                    cancelIdle();
                    redrawDisplay();
                }else{
                    playItemURL(playUrl,0);
                }
            }
        </script>
    </onUserInput>

    <backgroundDisplay>
        <image  offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
                    <? echo XTREAMER_IMAGE_PATH; ?>background/movies-black.jpg
        </image>
    </backgroundDisplay>

</mediaDisplay>

<gotoPageLink>
    <link>
    <script>
        url;
    </script>
    </link>
</gotoPageLink>

<homePageLink>
    <link>
            <?php echo SCRAPER_URL."index.php?type=" . $this->type; ?>
    </link>
</homePageLink>

        <?php
    }

    /**
     * -------------------------------------------------------------------------
     */
    private function getMovieDetailHeader() {
        ?>
<mediaDisplay name="threePartsView"
              showDefaultInfo="no" bottomYPC="0" itemGap="0" itemPerPage="7"
              showHeader="no" fontSize="14" itemBorderColor="-1:-1:-1" menuBorderColor="-1:-1:-1"
              itemImageXPC="72" itemImageHeightPC="0" itemImageWidthPC="0"
              imageFocus="null" imageUnFocus="null" imageParentFocus="null"
              itemXPC="72" itemYPC="5" itemWidthPC="26" itemHeightPC="12" capWidthPC="58"
              unFocusFontColor="101:101:101" focusFontColor="255:255:255"
              idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"
              backgroundColor="0:0:0" drawItemText="no" imageBorderPC="0" sliding="yes" >
                          <?php xVoDLoader(); ?>

    <!-- RSS PUZZLE, MOVIE PART -->
    <image redraw="no" offsetXPC="0" offsetYPC="0" widthPC="71.65" heightPC="100" backgroundColor="-1:-1:-1" >
                <? echo XTREAMER_IMAGE_PATH; ?>background/movies-inlist-noright.bmp
    </image>

    <!-- TOP MENU TITLES -->
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="5.5" offsetYPC="3.1" widthPC="10" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_categories"); ?>]]>
    </text>

    <!-- COVER SERIE IMAGE -->
    <image redraw="no" offsetXPC="2.8" offsetYPC="23.8" widthPC="18.9" heightPC="48.1" backgroundColor="-1:-1:-1" >
                <?php echo $this->image; ?>
    </image>

    <!-- MOVIE TITLE -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="23.6" offsetYPC="32.2" widthPC="20" heightPC="10" fontSize="12" lines="3">
        <script>
            getItemInfo("title");
        </script>
    </text>

    <!-- MOVIE DESCRIPTION -->
    <text redraw="yes"
          backgroundColor="-1:-1:-1" foregroundColor="160:160:160"
          offsetXPC="23" offsetYPC="43.5" widthPC="48" heightPC="36" fontSize="13" lines="12">
        <script>
            getItemInfo("description");
        </script>
    </text>

    <!-- PAGE SEASON TITLE -->
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="0:154:205"
          offsetXPC="23" offsetYPC="23.5" widthPC="50" heightPC="5" fontSize="16" lines="1">
                      <?php echo strtoupper($this->movieTitle); ?>
    </text>

    <!-- PAGE TITLE -->
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="0:154:205"
          offsetXPC="23" offsetYPC="27.8" widthPC="35" heightPC="5" fontSize="11" lines="1">
        <script>
            "<?php echo strtoupper($this->title); ?>";
        </script>
    </text>

    <itemDisplay>
        <image redraw="yes" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="50" lines="1">
            <script>
                if( getFocusItemIndex() == getItemInfo(-1,"itemid") )
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/top-bar-focus_300.png";
                else
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/top-bar-unfocus_300.png";
            </script>
        </image>
        <text redraw="no"
              backgroundColor="-1:-1:-1" foregroundColor="255:255:255" itemGap="0"
              offsetXPC="3" offsetYPC="0" widthPC="100" heightPC="50" fontSize="12" lines="1">
            <script>
                getItemInfo(-1,"title");
            </script>
        </text>
    </itemDisplay>

    <onUserInput>
        <script>
            userInput = currentUserInput();
            if ( userInput == "0" )      {
                showIdle();
                jumpToLink("homePageLink");
                redrawDisplay();
            }
        </script>
    </onUserInput>

</mediaDisplay>

<homePageLink>
    <link>
            <?php echo SCRAPER_URL."index.php?type=" . $this->type; ?>
    </link>
</homePageLink>

        <?php
    }

    /**
     * -------------------------------------------------------------------------
     */
    private function getPlayHeader() {
        ?>
<script>
    SwitchViewer(0);
    SwitchViewer(7);
</script>
<mediaDisplay name="threePartsView"
              showDefaultInfo="no" bottomYPC="0" itemGap="0" itemPerPage="18"
              showHeader="no" fontSize="14" itemBorderColor="-1:-1:-1" menuBorderColor="-1:-1:-1"
              itemImageXPC="72" itemImageHeightPC="0" itemImageWidthPC="0"
              imageFocus="null" imageUnFocus="null" imageParentFocus="null"
              itemXPC="72" itemYPC="5" itemWidthPC="26" itemHeightPC="14" capWidthPC="58"
              unFocusFontColor="101:101:101" focusFontColor="255:255:255"
              idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"
              backgroundColor="0:0:0" drawItemText="no" imageBorderPC="0">
                          <?php xVoDLoader(); ?>

    <!-- RSS PUZZLE, MOVIE PART -->
    <image redraw="no" offsetXPC="0" offsetYPC="0" widthPC="71.65" heightPC="100" backgroundColor="-1:-1:-1" >
                <? echo XTREAMER_IMAGE_PATH; ?>background/movies-inlist-noright.bmp
    </image>

    <!-- TOP MENU TITLES -->
    <text redraw="no"
          backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
          offsetXPC="5.5" offsetYPC="3.1" widthPC="10" heightPC="3" fontSize="10" lines="1">
        <![CDATA[<?php echo resourceString("header_menu_categories"); ?>]]>
    </text>

    <!-- MOVIE TITLE -->
    <text redraw="yes"
          backgroundColor="0:0:0" foregroundColor="255:255:255"
          offsetXPC="23.6" offsetYPC="33.2" widthPC="20" heightPC="10" fontSize="12" lines="3">
        <script>
            getItemInfo("title");
        </script>
    </text>

    <itemDisplay>
        <image redraw="yes" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" >
            <script>
                if( getFocusItemIndex() == getItemInfo(-1,"itemid") )
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/top-bar-focus_300.png";
                else
                    "<? echo XTREAMER_IMAGE_PATH; ?>background/top-bar-unfocus_300.png";
            </script>
        </image>
        <text redraw="no"
              backgroundColor="-1:-1:-1" foregroundColor="255:255:255"
              offsetXPC="3" offsetYPC="12" widthPC="94" heightPC="76" fontSize="12" lines="3">
            <script>
                getItemInfo(-1,"title");
            </script>
        </text>
    </itemDisplay>

    <onUserInput>
        <script>
            userInput = currentUserInput();
            if ( userInput == "0" )      {
                showIdle();
                jumpToLink("homePageLink");
                redrawDisplay();
            }
        </script>
    </onUserInput>

</mediaDisplay>

<homePageLink>
    <link>
            <?php echo SCRAPER_URL."index.php?type=" . $this->type; ?>
    </link>
</homePageLink>

        <?php
    }

    /**
     * -------------------------------------------------------------------------
     */
    private function getLink($item,$itemId = null) {
        echo "          <item>\n";
        echo "              <title><![CDATA[".utf8_encode($item[0])."]]></title>\n";
        if($item[1]) {
            echo "              <description><![CDATA[".utf8_encode($item[1])."]]></description>\n";
        }
        echo "              <link>" . utf8_encode($item[2]) . "</link>\n";
        echo "              <itemid>" . $itemId . "</itemid>\n";
        if($item[3]) {
            echo "              <media:thumbnail url=\"" . utf8_encode($item[3]) . "\"/>\n";
            echo "              <image>" . utf8_encode($item[3]) . "</image>\n";
        }
        echo "          </item>\n";
    }

    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    private function getMediaLink($item,$itemId = null) {
        echo "          <item>\n";
        echo "              <title><![CDATA[" . utf8_encode($item[0]) . "]]></title>\n";
        if($item[1]) {
            echo "              <description><![CDATA[" . utf8_encode($item[1]) . "]]></description>\n";
        }
        echo "              <link>" . utf8_encode($item[2]) . "</link>\n";
        if($item[3]) {
            echo "              <media:thumbnail url=\"" . utf8_encode($item[3]) . "\"/>\n";
        }
        echo "              <enclosure type=\"" . utf8_encode($item[4]) . "\" url=\"" . utf8_encode($item[2]) . "\"/>\n";
        echo "              <itemid>" . $itemId . "</itemid>\n";
        echo "          </item>\n";
    }

    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    private function getSearchLink($item,$itemid) {
        echo "          <item>\n";
        echo "              <title><![CDATA[" . $item[0] . "]]></title>\n";
        echo "              <description><![CDATA[$item[1]]]></description>\n";
        echo "              <link>" . $item[2] . "</link>\n";
        echo "              <search url=\"" . $item[3] . "\" />\n";
        echo "              <itemid>" . $itemid . "</itemid>\n";
        echo "              <media:thumbnail url=\""  . $item[4] . "\"/>\n";
        echo "          </item>\n";
    }


}

?>
