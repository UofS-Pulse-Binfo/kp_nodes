<div class="tripal_organism-teaser tripal-teaser">
  <div class="col-left-img tripal-organism-teaser-title tripal-teaser-title" style="border: none; overflow: hidden">
     <?php print $rendered_organism_image; ?>
  </div>
  
  <div class="col-right-copy tripal-organism-teaser-text tripal-teaser-text">
    <?php       
      print l("<h3><i>$organism->genus $organism->species</i> ($organism->common_name)</h3>", "node/$node->nid", array('html' => TRUE));
      print "<p align=\"justify\">" . substr($organism->comment, 0, 650);
      if (strlen($organism->comment) > 650) {
        print " ... " . l("[more]", "node/$node->nid");
      }
      print "</p>";
    ?>
  </div>
  
  <div class="col-clear">&nbsp;</div>
</div>