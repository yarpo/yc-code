<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<script language="Javascript" type="text/javascript">
    //<![CDATA[
    google.load("search", "1", {"language" : "<?php echo $this->params['language']['value']; ?>",
         <?php if ( !empty( $this->params['theme']['value'] ) ) {
         echo "style:google.loader.themes." . $this->params['theme']['value'];
         } ?>
        });

    function onLoad() {

      // Create a search control
      var searchControl = new google.search.SearchControl();

      // create a searcher options object
      // set up for open expansion mode
      // load a searcher with these options
      var options = new google.search.SearcherOptions();
      options.setExpandMode(google.search.SearchControl.EXPAND_MODE_OPEN);
      
      // Add in a full set of searchers
      var webSearch = new google.search.WebSearch();
      webSearch.setUserDefinedLabel("<?php echo $this->params['label']['value']; ?>");
      
      <?php if ( !empty( $this->params['site']['value'] ) ) {?>
      webSearch.setSiteRestriction("<?php echo $this->params['site']['value']; ?>");
      <?php } ?>
      
      searchControl.addSearcher( webSearch, options );

      // create a drawOptions object
      var drawOptions = new google.search.DrawOptions();

      // tell the searcher to draw itself in linear mode
      drawOptions.setDrawMode(google.search.SearchControl.DRAW_MODE_LINEAR);
      
      // tell the searcher to draw itself and tell it where to attach
      searchControl.draw(document.getElementById("searchcontrol"), drawOptions );

      <?php if ( !empty( $this->q ) ) { ?>
      // execute an inital search
      searchControl.execute("<?php echo $this->q;?>");
      <?php } ?>

    }
    google.setOnLoadCallback(onLoad);
        
    //]]>
</script>
<div id="searchcontrol"></div>
<?php if( $this->params['showlink']['value'] ) {?>
<div><a href="http://joomlamind.com" style="font-size: xx-small;">JMGoogleSearch by JoomlaMind.com</a></div>
<?php } ?>