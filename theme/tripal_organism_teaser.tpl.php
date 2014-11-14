

<div class="tripal_organism-teaser tripal-teaser">
  <div class="tripal-organism-teaser-title tripal-teaser-title"><?php
    print l("<i>$organism->genus $organism->species</i> ($organism->common_name)", "node/$node->nid", array('html' => TRUE));?>
  </div>
  <div class="tripal-organism-teaser-text tripal-teaser-text">
    <span class="organism-main-image">
      <?php print $rendered_organism_image; ?>
    </span>
    <?php
    print substr($organism->comment, 0, 650);
    if (strlen($organism->comment) > 650) {
      print "... " . l("[more]", "node/$node->nid");
    } ?>
  </div>
</div>
