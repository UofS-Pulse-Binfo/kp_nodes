<?php

function kp_nodes_theme_registry_alter(&$theme_registry) {

  $module_path = drupal_get_path('module', 'kp_nodes');

  // Tell the theme registry to use:
  $templates_to_override = array(
    'tripal_organism_base', 'tripal_organism_stocks',
    'tripal_project_base', 'tripal_project_relationships',
  );
  foreach ($templates_to_override as $template_name) {
    $theme_registry[$template_name]['theme path'] = $module_path;
    $theme_registry[$template_name]['path'] = $module_path . '/theme';
  }

}