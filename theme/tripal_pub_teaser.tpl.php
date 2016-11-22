<?php
$node = $variables['node'];
$pub = $variables['node']->pub;

// get the citation
$values = array(
  'pub_id' => $pub->pub_id,
  'type_id' => array(
    'name' => 'Citation',
  ),
);
$citation = chado_generate_var('pubprop', $values);
$citation = chado_expand_var($citation, 'field', 'pubprop.value');

// get the abstract
$values = array(
  'pub_id' => $pub->pub_id,
  'type_id' => array(
    'name' => 'Abstract',
  ),
);
$abstract = chado_generate_var('pubprop', $values);
$abstract = chado_expand_var($abstract, 'field', 'pubprop.value');
$abstract_text = '';
if ($abstract) {
  $abstract_text = strip_tags($abstract->value);
  $abstract_text = htmlspecialchars($abstract_text);
  $abstract_text = substr($abstract_text, 0, 450);
  $abstract_text .= "... " . l("[more]", "node/$node->nid");
}

$teaser_text = "<ul id=\"tripal-pub-teaser-citation\"><li>" . $citation->value . "</li></ul>" . $abstract_text;
?>

<div class="tripal_pub-teaser tripal-teaser">
  <div class="pyear"><?php print $pub->pyear; ?></div>
  <div class="tripal-pub-teaser-title tripal-teaser-title"><h2><?php
    print l($pub->title, "node/$node->nid", array('html' => TRUE));?>
  </h2></div>
  <div class="tripal-pub-teaser-text tripal-teaser-text">
    <div class="citation"><?php print $citation->value; ?></div>
    <div class="abstract"><?php print $abstract_text; ?></div>
  </div>
</div>
