<?php
/**
 * The following variables are available to this template:
 *  - $feature: contains information about the chado feature.
 *  - $feature->featureloc->feature_id: this feature is located on these features.
 *  - $feature->featureloc->srcfeature_id: these features are located on this feature.
 */
$feature = $variables['node']->feature;

/*
 * Alignment context #1:
 * ---------------------
 * The feature for this node is the child in the alignment relationships.
 */
$parentlocs = array();
if (!empty($feature->featureloc->feature_id)) {

  $parentlocs['header'] = array(
    'assembly' => array('data' => 'Assembly', 'class' => 'assembly'),
    'sequence' => array('data' => 'Sequence', 'class' => 'sequence'),
    'start' => array('data' => 'Start', 'class' => 'start'),
    'end' => array('data' => 'End', 'class' => 'end'),
    'strand' => array('data' => 'Strand', 'class' => 'strand'),
  );

  // Build the table.
  foreach ($feature->featureloc->feature_id as $loc) {

    // Basic details for the row.
    $row = array(
      'data' => array(
        'assembly' => array('data' => '', 'class' => 'assembly'),
        'sequence' => array('data' => $loc->srcfeature_id->name, 'class' => 'sequence'),
        'start' => array('data' => $loc->fmin + 1, 'class' => 'start'),
        'end' => array('data' => $loc->fmax, 'class' => 'end'),
        'strand' => array('data' => '', 'class' => 'strand'),
      ),
      'class' => array('main'),
    );

    // Add link if we can.
    $nid = chado_get_nid_from_id('feature', $loc->srcfeature_id->feature_id);
    if ($nid) { $row['data']['sequence']['data'] = l($loc->srcfeature_id->name, 'node/'.$nid, array('attributes' => array('target'=>'_blank'))); }

    // Add strand.
    if ($loc->strand == '1') { $row['data']['strand']['data'] = 'Forward (+)'; }
    if ($loc->strand == '-1') { $row['data']['strand']['data'] = 'Reverse (-)'; }

    // Add the assembly (featureprop of srcfeature_id with type of "assembly").
    $assembly = chado_query("SELECT value FROM {featureprop} WHERE feature_id=:id AND type_id IN (SELECT cvterm_id FROM cvterm WHERE name='assembly')",
      array(':id' => $loc->srcfeature_id->feature_id))->fetchField();
    if ($assembly) { $row['data']['assembly']['data'] = $assembly; }

    // Add row to table.
    $parentlocs['rows'][] = $row;
  }

  // Sort each parentloc table by start position.
  usort($parentlocs['rows'], create_function('$a, $b', '
    // We want to sort first by assembly.
    if ($a["data"]["assembly"]["data"] != $b["data"]["assembly"]["data"]) {
      if (empty($a["data"]["assembly"]["data"])) return -1;
      if (empty($b["data"]["assembly"]["data"])) return 1;
      return ($a["data"]["assembly"]["data"] < $b["data"]["assembly"]["data"]) ? 1 : -1;
    }
    // And secondly by name.
    else {
      if ($a["data"]["sequence"]["data"] == $b["data"]["sequence"]["data"]) return 0;
      return ($a["data"]["sequence"]["data"] < $b["data"]["sequence"]["data"]) ? -1 : 1;
    }
  '));
}

/*
 * Alignment context #2:
 * ---------------------
 * The feature for this node is the parent in the alignment relationships.
 */
$childlocs = array();
if (!empty($feature->featureloc->srcfeature_id)) {

  $childloc_header = array(
    'sequence' => array('data' => 'Sequence', 'class' => 'sequence'),
    'start' => array('data' => 'Start', 'class' => 'start'),
    'end' => array('data' => 'End', 'class' => 'end'),
    'strand' => array('data' => 'Strand', 'class' => 'strand'),
  );

  // Build the table.
  foreach ($feature->featureloc->srcfeature_id as $loc) {

    // Basic details for the row.
    $row = array(
      'data' => array(
        'sequence' => array('data' => $loc->feature_id->name, 'class' => 'sequence'),
        'start' => array('data' => $loc->fmin + 1, 'class' => 'start'),
        'end' => array('data' => $loc->fmax, 'class' => 'end'),
        'strand' => array('data' => '', 'class' => 'strand'),
      ),
      'class' => array('main'),
    );

    // Add link if we can.
    $nid = chado_get_nid_from_id('feature', $loc->feature_id->feature_id);
    if ($nid) { $row['data']['sequence']['data'] = l($loc->feature_id->name, 'node/'.$nid, array('attributes' => array('target'=>'_blank'))); }

    // Add strand.
    if ($loc->strand == '1') { $row['data']['strand']['data'] = 'Forward (+)'; }
    if ($loc->strand == '-1') { $row['data']['strand']['data'] = 'Reverse (-)'; }

    // Add row to table.
    $childlocs[$loc->feature_id->type_id->name]['rows'][] = $row;
  }

  // Sort each childloc table by start position.
  foreach ($childlocs as $type => $table) {
    $childlocs[$type]['header'] = $childloc_header;
    usort($childlocs[$type]['rows'], create_function('$a, $b', '
      if ($a["data"]["start"]["data"] == $b["data"]["start"]["data"]) return 0;
      return ($a["data"]["start"]["data"] < $b["data"]["start"]["data"]) ? -1 : 1;
    '));
  }
}
?>

<?php if ($parentlocs) : ?>

  <div class="tripal_feature-data-block-desc tripal-data-block-desc">This <?php print $feature->type_id->name;?> is found on:</div>
  <?php print theme('table', $parentlocs); ?>

<?php endif; ?>

<?php if ($childlocs) : ?>

  <?php foreach ($childlocs as $type => $table) : ?>

    <br />
    <div class="tripal_feature-data-block-desc tripal-data-block-desc">These <?php print $type;?>(s) are found on this <?php print $feature->type_id->name;?>:</div>
    <?php print theme('table', $table); ?>

<?php endforeach; endif; ?>
