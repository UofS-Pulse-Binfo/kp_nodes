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
    'node__chado_feature', 'tripal_feature_base', 'tripal_feature_alignments',
    'tripal_organism_base', 'tripal_organism_stocks', 'tripal_organism_feature_counts', 'tripal_organism_teaser',
    'tripal_stock_teaser',
    'tripal_feature_teaser',
    'tripal_project_base', 'tripal_project_relationships', 'tripal_project_teaser',
    'tripal_contact_base',
    'tripal_pub_base', 'tripal_pub_teaser',
    'node__jbrowse_instance'
  );
  foreach ($templates_to_override as $template_name) {
    $theme_registry[$template_name]['theme path'] = $module_path;
    $theme_registry[$template_name]['path'] = $module_path . '/theme';
  }

  // Additional information needed to override a tripal node.
  $theme_registry['node__chado_feature']['template'] = 'node--chado-feature';

  // Additional info req. for the jbrowse instance node.
  $theme_registry['node__jbrowse_instance']['template'] = 'node--jbrowse-instance';

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
  if (empty($image_fieldcollection->field_image_link)) {
    $variables['rendered_organism_image'] = $image;
  }
  else {
    $variables['rendered_organism_image'] = l(
      $image,
      $image_fieldcollection->field_image_link[$lang][0]['url'],
      array(
        'html' => TRUE,
        'attributes' => $image_fieldcollection->field_image_link[$lang][0]['attributes']
      )
    );
  }


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

  // Wrap image in link if available.
  if (isset($image_fieldcollection->field_image_link[$lang][0]['url'])) {
    $variables['rendered_organism_image'] = l(
      $image,
      $image_fieldcollection->field_image_link[$lang][0]['url'],
      array(
        'html' => TRUE,
        'attributes' => $image_fieldcollection->field_image_link[$lang][0]['attributes']
      )
    );
  }
  else {
    $variables['rendered_organism_image'] = $image;
  }
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


/**
 * Implements hook preprocess.
 * Generate variables/content for theme Project List of Stocks.
 */
 function kp_nodes_preprocess_kp_nodes_project_stocks(&$variables) {
   // Get the project ID number.
   $project_id = $variables['node']['#node']->project->project_id;

   // First check if table Project Stocks exists.
   $sql = "SELECT relname FROM pg_class WHERE relname = 'project_stock'";
   $r = db_query($sql);

   if ($r->rowCount() > 0) {
     // Table header with sort option.
     $sort_header = array('data' => t('Name'), 'field' => 'name', 'sort' => 'ASC');
     // Get the sort order from the url.
     $sort  = tablesort_get_sort($sort_header);

     // Table exists.
     $sql = sprintf("SELECT
               t1.name, t1.uniquename,
               INITCAP(genus) || ' ' || LOWER(species) AS species,
               t2.name AS type,
               'node/' || link.nid AS node_link
             FROM
               {stock} AS t1
               LEFT JOIN chado_stock AS link USING(stock_id)
               LEFT JOIN {project_stock} USING(stock_id)
               LEFT JOIN {organism} USING(organism_id)
               LEFT JOIN {cvterm} AS t2 ON cvterm_id = type_id
             WHERE project_id = :project_id
             ORDER BY t1.name %s", $sort);

     $args = array(':project_id' => $project_id);
     $g = chado_query($sql, $args);

     // Total stocks found.
     $total_stocks = $g->rowCount();

     if ($total_stocks > 0) {
       // Stocks available - construct table.

       // Array to hold table headers.
       $arr_tbl_headers = array();
       // Array to hold stock rows.
       $arr_tbl_rows = array();

       // HEADER.
       $arr_tbl_headers = array(
         $sort_header,
         array('data' => t('Accession')),
         array('data' => t('Species')),
         array('data' => t('Type')),
       );

       // ROWS.
       foreach($g as $v) {
         // Link to germ node.
         $node_link = l($v->name, $v->node_link, array('attributes' => array('target' => '_blank')));
         // Push rows into arr_stocks.
         array_push($arr_tbl_rows,
           array(
             $node_link,
             $v->uniquename,
             '<em>' . $v->species . '</em>',
             $v->type,
           )
         );
       }

       // Set theme variablees.
       $v = kp_nodes_construct_table($arr_tbl_rows, $arr_tbl_headers, 'tbl-project-stock-generic', $project_id);
       $variables['tbl_caption'] = $v[0];
       $variables['tbl_pager'] = $v[1];
       $variables['tbl_summary_table'] = $v[2];

       // End construct table.
     }
     else {
       // No stocks - no table.
       return 0;
     }
   }
   else {
     // No such table - No row, no table.
     return 0;
   }
 }


/**
 * Implements hook preprocess.
 * Generate variables/content for theme Project List of Stocks (AGILE Project only).
 */
function kp_nodes_preprocess_kp_nodes_project_stocks_AGILE(&$variables) {
  // This array lists all sub project of project AGILE:
  $arr_subprojects = array(
    'Deployment of Tepary Bean Genetics to Improve Stress Tolerance in Common Bean',
    'Using Crop Wild Relatives for Future Lentil Breeding: Evaluation of Drought and Disease Resistance of Interspecific Hybrid Lines',
    'AGILE: Application of Genomic Innovation in the Lentil Economy',

    'Field Experiment to Assess the Performance of 15 Rhizobium Strains',
    'Flowering Time Investigation in Lentil Collection',
    'Greenhouse Experiment to Assess the Response to R. leguminosarum of 36 Lentil Accessions',
    'Lentil Diversity Panel Biomass',
    'LR-11 Flowering Time',
    'LR-68',
    'Tracing domestication traits (shattering and plant growth habit) in an interspecific lentil population: LR-70',
    'Tracing domestication traits (shattering) in an interspecific lentil population: LR-86',
    'Field Experiment to Assess the Performance of 15 Rhizobium Strains',
    'LR-86 Light Quality Field Trial',
  );

  // Alter uofs_projects view and remove the pager option.
  // This will allow us to examine all projects available to identify which
  // belongs to sub-projects of AGILE.
  $prj_view = views_get_view('uofs_projects');
  $prj_view->init_display();
  $pager = $prj_view->display_handler->get_option('pager');
  $pager['type'] = 'none';
  $prj_view->display_handler->set_option('pager', $pager);

  $prj_view->pre_execute();
  $prj_view->display_handler->preview();
  $prj_view->post_execute();

  if ($prj_view->result) {
    // Array to hold table headers.
    $arr_tbl_headers = array();
    // Array to hold stock rows.
    $arr_tbl_rows = array();

    // HEADER.
    $arr_tbl_headers = array(
      array('data' => t('Experiment')),
      array('data' => t('Year')),
    );

    // ROWS.
    foreach($prj_view->result as $p) {
      if (in_array($p->project_name, $arr_subprojects)) {
        $project_name_link = '<h3>' . l($p->project_name, url('node/' . $p->node_nid, array('absolute' => TRUE))) . '</h3>';

        array_push($arr_tbl_rows,
          array(
            $project_name_link .
            strip_tags($p->projectprop_project_value),
            strip_tags($p->field_field_date_month_year[0]['rendered']['#markup']),
          )
        );
      }
    }

    $v = kp_nodes_construct_table($arr_tbl_rows, $arr_tbl_headers, 'tbl-project-associated-project');
    $variables['tbl_caption'] = $v[0];
    $variables['tbl_pager'] = $v[1];
    $variables['tbl_summary_table'] = $v[2];
  }
  else {
    return 0;
  }
}


/**
 * Helper function: Construct table with summary and pager.
 */
function kp_nodes_construct_table($arr_rows, $arr_headers, $table_attr_id, $project_id = null) {
  // TABLE SUMMARY.
  // Table captions showing number of accessions.
  $total_stocks = count($arr_rows);
  $stock_caption = ($table_attr_id == 'tbl-project-stock-generic')
    ? '<em>' . $total_stocks . '</em> Accessions.' . '<span style="float: right"><a href="project_germplasm/' . base64_encode('kp_nodes_project:' . $project_id) . '" target="_blank">Download List</a></span>'
    : '<em>' . $total_stocks . '</em> Associated Projects.';

  // TABLE PAGER.
  // Number or rows per page.
  $pager_rows_per_page = 50;
  // Current page #.
  $pager_current_page = pager_default_initialize($total_stocks, $pager_rows_per_page);
  // Load set of rows per page.
  $pager_row_set = array_chunk($arr_rows, $pager_rows_per_page, TRUE);

  $stock_pager = theme('pager', array(
    'quantity'   => $total_stocks,
    'parameters' => array('pane' => 'germplasm'))
  );

  // TABLE STOCKS.
  // Array to hold table properties.
  $arr_tbl_prop = array();
  // Headers.
  $arr_tbl_prop['header'] = $arr_headers;
  // Rows.
  $arr_tbl_prop['rows'] = $pager_row_set[ $pager_current_page ];
  // Config.
  $arr_tbl_prop['sticky']     = TRUE;
  $arr_tbl_prop['attributes'] = array('id' => $table_attr_id);

  $stock_table = theme('table', $arr_tbl_prop);
  // Append page=germplasm query string to keep the List Of Stocks Pane active when sorting.
  // replace ? and add the pane parameter.
  if (!isset($_GET['pane'])) {
    // Only when $pane is not set - note: pager adds the pane string.
    $stock_table = str_replace('?', '?pane=germplasm&', $stock_table);
  }

  // RETURN TABLE SUMMARY, TABLE PAGER AND TABLE STOCKS.
  return array($stock_caption, $stock_pager, $stock_table);
}
