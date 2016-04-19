<?php
$node  = $variables['node'];
$stock = $variables['node']->stock;
$stock = chado_expand_var($stock,'field','stock.description'); ?>

<div class="tripal_stock-teaser tripal-teaser">
  <div class="tripal-stock-teaser-title tripal-teaser-title"><h2><?php
    print l($node->title, "node/$node->nid", array('html' => TRUE));?>
  </h2></div>
  <div class="tripal-stock-teaser-text tripal-teaser-text teaser-items">
    <div class="item">
      <div class="item-title">Name</div>
      <div class="item-value"><?php print $stock->name?></div>
    </div>
    <div class="item">
      <div class="item-title">Accession</div>
      <div class="item-value"><?php print $stock->uniquename?></div>
    </div>
    <div class="item">
      <div class="item-title">Scientific Name</div>
      <?php $organism = $stock->organism_id->genus ." " . $stock->organism_id->species ." (" . $stock->organism_id->common_name .")";
        if (property_exists($stock->organism_id, 'nid')) {
          $organism = l("<i>" . $stock->organism_id->genus . " " . $stock->organism_id->species . "</i> (" . $stock->organism_id->common_name .")", "node/".$stock->organism_id->nid, array('html' => TRUE));
        } ?>
      <div class="item-value"><?php print $organism?></div>
    </div>
    <div class="item">
      <div class="item-title">Type</div>
      <?php $type = ucwords(preg_replace('/_/', ' ', $stock->type_id->name)); ?>
      <div class="item-value"><?php print $type?></div>
    </div>
  </div>
</div>
