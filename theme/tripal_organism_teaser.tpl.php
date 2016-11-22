<div class="tripal_organism-teaser tripal-teaser">
  <div class="col-left-img tripal-teaser-image tripal_organism-teaser-image" style="border: none; overflow: hidden">
     <?php print $rendered_organism_image; ?>
  </div>

  <div class="col-right-copy">
    <div class="tripal-organism-teaser-title tripal-teaser-title"><h2>
      <?php print l("<i>$organism->genus $organism->species</i> ($organism->common_name)",
        "node/$node->nid",
        array('html' => TRUE));?>
    </h2></div>
    <div class="tripal-organism-teaser-text tripal-teaser-text">
    <?php
      $p = explode('</p>', $organism->comment);
      print "<p align=\"justify\">" . str_replace('<p>', '', $p[0]);
      if (strlen($organism->comment) > 650) {
        print " ... " . l("[more]", "node/$node->nid");
      }
      print "</p>";
    ?>
    </div>
  </div>

  <div class="col-clear">&nbsp;</div>
</div>
