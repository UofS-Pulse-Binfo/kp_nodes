<?php

/**
 * Alter the theme registry so that it knows about the templates we want to
 * override. While this is automatic for themes, it has to be done manually
 * for every template overridden in a module :(.
 *
 * Simply add the name of the template to the $templates_to_override array
 * below and place the new template in [MODULE DIR]/theme.
 */
function kp_nodes_theme_registry_alter(&$theme_registry) {

  $module_path = drupal_get_path('module', 'kp_nodes');

  // Tell the theme registry to use:
  $templates_to_override = array(
    'tripal_organism_base', 'tripal_organism_stocks', 'tripal_organism_teaser',
    'tripal_project_base', 'tripal_project_relationships',
  );
  foreach ($templates_to_override as $template_name) {
    $theme_registry[$template_name]['theme path'] = $module_path;
    $theme_registry[$template_name]['path'] = $module_path . '/theme';
  }

}

/**
 * Preprocess: Organism Base Template
 * tripal_organism_base.tpl.php
 */
function kp_nodes_preprocess_tripal_organism_base(&$variables) {

  $variables['organism'] = $variables['node']->organism;
  $variables['organism'] = tripal_core_expand_chado_vars($variables['organism'],'field','organism.comment');

  // Render the image with link to attribution.
  //-------------------------------------------
  $lang = isset($data->language) ? $data->language : LANGUAGE_NONE;

  // Retrieve the field collection details.
  $image_fieldcollection_id = $variables['node']->field_image_with_source[$lang][0]['value'];
  $image_fieldcollection = entity_load('field_collection_item', array($image_fieldcollection_id));
  $image_fieldcollection = $image_fieldcollection[$image_fieldcollection_id];

  // Render the image.
  $image_link = image_style_url('large', $image_fieldcollection->field_image[$lang][0]['uri']);
  if (!file_exists($image_link)) {
    $success = image_style_create_derivative('large', $image_fieldcollection->field_image[$lang][0]['uri'], $image_link);
  }
  $image = '<img src="'.$image_link.'">';
  $image = render($image);

  // Wrap image in link.
  $variables['rendered_organism_image'] = l(
    $image,
    $image_fieldcollection->field_image_link[$lang][0]['url'],
    array(
      'html' => TRUE,
      'attributes' => $image_fieldcollection->field_image_link[$lang][0]['attributes']
    )
  );

}

/**
 * Preprocess: Organism Teaser Template
 * tripal_organism_teaser.tpl.php
 */
function kp_nodes_preprocess_tripal_organism_teaser(&$variables) {

  $variables['organism'] = $variables['node']->organism;

  // Render the image with link to attribution.
  //-------------------------------------------
  $lang = isset($data->language) ? $data->language : LANGUAGE_NONE;

  // Retrieve the field collection details.
  $image_fieldcollection_id = $variables['node']->field_image_with_source[$lang][0]['value'];
  $image_fieldcollection = entity_load('field_collection_item', array($image_fieldcollection_id));
  $image_fieldcollection = $image_fieldcollection[$image_fieldcollection_id];

  // Render the image.
  $image_link = image_style_url('medium', $image_fieldcollection->field_image[$lang][0]['uri']);
  if (!file_exists($image_link)) {
    $success = image_style_create_derivative('medium', $image_fieldcollection->field_image[$lang][0]['uri'], $image_link);
  }
  $image = '<img src="'.$image_link.'">';
  $image = render($image);

  // Wrap image in link.
  $variables['rendered_organism_image'] = l(
    $image,
    $image_fieldcollection->field_image_link[$lang][0]['url'],
    array(
      'html' => TRUE,
      'attributes' => $image_fieldcollection->field_image_link[$lang][0]['attributes']
    )
  );

}

/**
 * Preprocess: Project Base Template
 * tripal_project_base.tpl.php
 */
function kp_nodes_preprocess_tripal_project_base(&$variables) {

  $variables['project'] = $variables['node']->project;

  // Date field.
  $display_settings = array('label' => 'hidden');
  $variables['date'] = field_view_field('node', $variables['node'], 'field_date_month_year',$display_settings);

  // Long Description Field.
  $display_settings = array('label' => 'hidden');
  $variables['long_description'] = field_view_field('node', $variables['node'], 'field_long_description',$display_settings);

}