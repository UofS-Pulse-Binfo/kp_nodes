<?php
$node = $variables['node'];
$feature = $variables['node']->feature; ?>

<div class="tripal_feature-teaser tripal-teaser">
  <div class="tripal-feature-teaser-title tripal-teaser-title"><h2><?php
    print l($node->title, "node/$node->nid", array('html' => TRUE));?>
  </h2></div>
  <div class="tripal-feature-teaser-text tripal-teaser-text teaser-items">
    <div class="item">
      <div class="item-title">Name</div>
      <div class="item-value"><?php print $feature->name?></div>
    </div>
    <div class="item">
      <div class="item-title">Unique Name</div>
      <div class="item-value"><?php print $feature->uniquename?></div>
    </div>
    <div class="item">
      <div class="item-title">Scientific Name</div>
      <?php $organism = $feature->organism_id->genus ." " . $feature->organism_id->species ." (" . $feature->organism_id->common_name .")";
        if (property_exists($feature->organism_id, 'nid')) {
          $organism = l("<i>" . $feature->organism_id->genus . " " . $feature->organism_id->species . "</i> (" . $feature->organism_id->common_name .")", "node/".$feature->organism_id->nid, array('html' => TRUE));
        } ?>
      <div class="item-value"><?php print $organism?></div>
    </div>
    <div class="item">
      <div class="item-title">Type</div>
      <?php $type = ucwords(preg_replace('/_/', ' ', $feature->type_id->name)); ?>
      <div class="item-value"><?php print $type?></div>
    </div>
  </div>
</div>
