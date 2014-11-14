<?php
$feature  = $variables['node']->feature;
$feature = chado_expand_var($feature, 'table', 'featureprop',array('return_array' => TRUE));

///////////////////////////////////////
// Markers
// Get the correct type for markers (ie: featureprop with "Marker Type" type)
$type = $feature->type_id->name;
$is_marker = FALSE;
if ($feature->type_id->name == 'marker') {
  $is_marker = TRUE;

  // Get the marker type
  foreach ($feature->featureprop as $k => $prop) {
    if ($prop->type_id->name == 'marker_type') {
      $subtype = $prop->value;
      unset($feature->featureprop[$k]);
    }
  }

  // get the source variation (ie: using feature_relationship get the feature this is_marker_of)
  $feature = chado_expand_var($feature, 'table', 'feature_relationship',array('return_array' => TRUE));
  if (isset($feature->feature_relationship) AND $feature->feature_relationship->subject_id != NULL) {
    foreach ($feature->feature_relationship->subject_id as $rel) {
      if ($rel->type_id->name == 'is_marker_of') {
        $variant_feature = chado_generate_var('feature', array('feature_id' => $rel->object_id->feature_id));
      }
    }
  }
}

///////////////////////////////////////
// Variants (ie: SNPs)
$variant_types = array('SNP', 'MNP','indel');
$is_variant = FALSE;
if (in_array($feature->type_id->name, $variant_types)) {

  $is_variant = TRUE;
  $type = 'variant';
  $subtype = $feature->type_id->name;

  // Get available markers  (ie: using feature_relationship get the features is_marker_of the current variant)
  $marker_features = array();
  $feature = chado_expand_var($feature, 'table', 'feature_relationship',array('return_array' => TRUE));
  if (isset($feature->feature_relationship) AND $feature->feature_relationship->object_id != NULL) {
    foreach ($feature->feature_relationship->object_id as $rel) {
      if ($rel->type_id->name == 'is_marker_of') {
        $marker = chado_generate_var('feature', array('feature_id' => $rel->subject_id->feature_id));
        if (isset($marker->nid)) {
          $marker_features[] = l($marker->name, 'node/'.$marker->nid);
        }
        else {
          $marker_features[] = $marker->name;
        }
      }
    }
  }

}
?>

<div class="tripal_feature-data-block-desc tripal-data-block-desc"></div> <?php

// Is Obsolete Row
if($feature->is_obsolete == TRUE){
  print '<div class="tripal_feature-obsolete"><span class="title">OBSOLETE</span>';

  // Add a description/message if one is available
  $result = chado_select_record('featureprop',array('value'), array('type_id' => array('name' => 'obsolete_message'), 'feature_id' => $feature->feature_id));
  if (is_array($result)) {
    print ': ';
    foreach ($result as $prop) {
      print '<span class="message">'.$prop->value.'</span>';
    }
  }

  print '</div>';
}

// the $headers array is an array of fields to use as the colum headers.
// additional documentation can be found here
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
// This table for the analysis has a vertical header (down the first column)
// so we do not provide headers here, but specify them in the $rows array below.
$headers = array();

// the $rows array contains an array of rows where each row is an array
// of values for each column of the table in that row.  Additional documentation
// can be found here:
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
$rows = array();

// Name row
$rows[] = array(
    array(
      'data' => 'Name',
      'header' => TRUE,
      'width' => '20%',
    ),
    $feature->name
);
// Unique Name row
$rows[] = array(
  array(
    'data' => 'Unique Name',
    'header' => TRUE
  ),
  $feature->uniquename
);
// Type row
$rows[] = array(
  array(
    'data' => 'Type',
    'header' => TRUE
  ),
  ucfirst($type)
);
// Marker Type row
if ($is_marker) {
  $rows[] = array(
    array(
      'data' => 'Marker Type',
      'header' => TRUE
    ),
    $subtype
  );
}
elseif ($is_variant) {
  $rows[] = array(
    array(
      'data' => 'Variant Type',
      'header' => TRUE
    ),
    $subtype
  );
}
// Source Variant row if this is a marker
if (isset($variant_feature)) {
    $rows[] = array(
    array(
      'data' => 'Source Variant',
      'header' => TRUE
    ),
    l($variant_feature->name, 'node/'.$variant_feature->nid) . ' ('.$variant_feature->type_id->name.')'
  );
}
// Organism row
$organism = $feature->organism_id->genus ." " . $feature->organism_id->species ." (" . $feature->organism_id->common_name .")";
if (property_exists($feature->organism_id, 'nid')) {
  $organism = l("<i>" . $feature->organism_id->genus . " " . $feature->organism_id->species . "</i> (" . $feature->organism_id->common_name .")", "node/".$feature->organism_id->nid, array('html' => TRUE));
}
$rows[] = array(
  array(
    'data' => 'Source Species',
    'header' => TRUE,
  ),
  $organism
);
// Seqlen row
if($feature->seqlen > 1) {
  $rows[] = array(
    array(
      'data' => 'Sequence length',
      'header' => TRUE,
    ),
    $feature->seqlen
  );
}
// Available Markers row if this is a variant
if ($is_variant and !empty($marker_features)) {
  $rows[] = array(
    array(
      'data' => 'Developed Markers',
      'header' => TRUE,
    ),
    implode(', ', $marker_features)
  );
}
// allow site admins to see the feature ID
if (user_access('administer tripal')) {
  // Feature ID
  $rows[] = array(
    array(
      'data' => 'Feature ID',
      'header' => TRUE,
      'class' => 'tripal-site-admin-only-table-row',
    ),
    array(
      'data' => $feature->feature_id,
      'class' => 'tripal-site-admin-only-table-row',
    ),
  );
}


// the $table array contains the headers and rows array as well as other
// options for controlling the display of the table.  Additional
// documentation can be found here:
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_feature-table-base',
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);

// once we have our table array structure defined, we call Drupal's theme_table()
// function to generate the table.
print theme_table($table);
