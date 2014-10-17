<?php
/* Typically in a Tripal template, the data needed is retrieved using a call to
 * tripal_core_expand_chado_vars function.  For example, to retrieve all
 * of the project relationships for this node, the following function call would be made:
 *
 *   $project = tripal_core_expand_chado_vars($project,'table','project_relationship');
 *
 * However, this function call can be extremely slow when there are numerous relationships.
 * This is because the tripal_core_expand_chado_vars function is recursive and expands
 * all data following the foreign key relationships tree.  Therefore, to speed retrieval
 * of data, a special variable is provided to this template:
 *
 *   $project->all_relationships;
 *
 * This variable is an array with two sub arrays with the keys 'object' and 'subject'.  The array with
 * key 'object' contains relationships where the project is the object, and the array with
 * the key 'subject' contains relationships where the project is the subject
 */
$project = $variables['node']->project;

$all_relationships = $project->all_relationships;
$object_rels = $all_relationships['object'];
$subject_rels = $all_relationships['subject'];

// Display sub-projects
//-----------------------------------------------
if (isset($object_rels['Sub-Project of'])) { ?>
  <h3>Sub-Projects</h3>
  <div class="tripal_project-data-block-desc tripal-data-block-desc">The following projects are sub-projects of this one:</div>
  <ul class="tripal_project_rel">
<?php
  foreach ($object_rels['Sub-Project of'] as $record) {
    $record = $record->record;

    // link the project to it's node
    $subject_name = $record->subject_project_id->name;
    if (property_exists($record, 'nid')) {
      $subject_name = l($subject_name, "node/" . $record->subject_project_id->nid, array('attributes' => array('target' => "_blank")));
    }

    print '<li>' . $subject_name . '</li>';
  }
  unset($object_rels['Sub-Project of']);
  print '</ul>';
}

// Display Parent Projects
//-----------------------------------------------
if (isset($subject_rels['Sub-Project of'])) { ?>
  <h3>Parent Projects</h3>
  <div class="tripal_project-data-block-desc tripal-data-block-desc">The following project is the parent of this one:</div>
  <ul class="tripal_project_rel">
<?php
  foreach ($subject_rels['Sub-Project of'] as $record) {
    $record = $record->record;

    // link the project to it's node
    $object_name = $record->object_project_id->name;
    if (property_exists($record, 'nid')) {
      $object_name = l($object_name, "node/" . $record->object_project_id->nid, array('attributes' => array('target' => "_blank")));
    }

    print '<li>' . $object_name . '</li>';
  }
  unset($subject_rels['Sub-Project of']);
  print '</ul>';
}
?>

<?php
// Display any remaining Relationships
//-----------------------------------------------
if (count($object_rels) > 0 or count($subject_rels) > 0) { ?>
  <div class="tripal_project-data-block-desc tripal-data-block-desc">This project is related to the following other projects:</div>
  <ul class="tripal_project_rel"><?php

  // first add in the subject relationships.
  foreach ($subject_rels as $rel_type => $objects){
    foreach ($objects as $object){
      // link the project to it's node
      $object_name = $object->record->object_project_id->name;
      if (property_exists($object->record, 'nid')) {
        $object_name = l($object_name, "node/" . $object->record->nid, array('attributes' => array('target' => "_blank")));
      }
      print "<li>$project->name is \"$rel_type\" $object_name</li>";
    }
  }

  // second add in the object relationships.
  foreach ($object_rels as $rel_type => $subjects){
    foreach ($subjects as $subject){
      // link the project to it's node
      $subject_name = $subject->record->subject_project_id->name;
      if (property_exists($subject->record, 'nid')) {
        $subject_name = l($subject_name, "node/" . $subject->record->nid, array('attributes' => array('target' => "_blank")));
      }
      print "<li>$subject_name is \"$rel_type\" $project->name</li>";
    }
  }

  print '</ul>';
}
