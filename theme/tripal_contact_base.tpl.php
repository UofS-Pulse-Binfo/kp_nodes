<?php
$contact = $variables['node']->contact;

// expand contact to include pubs 
$options = array('return_array' => 1);
$contact = chado_expand_var($contact, 'table', 'pubauthor_contact', $options);
$pubauthor_contacts = $contact->pubauthor_contact; 
$citations = array();

if (count($pubauthor_contacts) > 0) { 
   foreach ($pubauthor_contacts as $pubauthor_contact) {
    $pub = $pubauthor_contact->pubauthor_id->pub_id;
    $pub = chado_expand_var($pub, 'field', 'pub.title');
    $citation = $pub->title;  // use the title as the default citation
    
    // get the citation for this pub if it exists
    $values = array(
      'pub_id' => $pub->pub_id, 
      'type_id' => array(
        'name' => 'Citation',
      ),
    );
    $options = array('return_array' => 1);
    $citation_prop = chado_generate_var('pubprop', $values, $options); 
    if (count($citation_prop) == 1) {
      $citation_prop = chado_expand_var($citation_prop, 'field', 'pubprop.value');
      $citation = $citation_prop[0]->value;
    }
    
    // if the publication is synced then link to it
    if ($pub->nid) {
      // replace the title with a link
      $link = l($pub->title, 'node/' . $pub->nid ,array('attributes' => array('target' => '_blank')));
      $patterns = array(
        '/(\()/', '/(\))/', 
        '/(\])/', '/(\[)/',
        '/(\{)/', '/(\})/',
        '/(\+)/', '/(\.)/', '/(\?)/', 
      );
      $fixed_title = preg_replace($patterns, "\\\\$1", $pub->title);
      $citation = preg_replace('/' . $fixed_title . '/', $link, $citation);
    }
    
    $citations[$pub->pyear][] = $citation;
  }
}

// First give an actual overview.
$headers = array();
$rows = array();

// Contact Name row
$rows[] = array(
  array(
    'data' => 'Name',
    'header' => TRUE,
    'width' => '20%',
  ),
  $contact->name,
);
// Contact Type row
$rows[] = array(
  array(
    'data' => 'Type',
    'header' => TRUE
  ),
  $contact->type_id->name,
);

$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_contact-table-base',
    'class' => 'tripal-data-table'
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);
print theme_table($table);

if ($citations) {
  krsort($citations);
?>

<style>
.year-row {
  margin-bottom: 10px;
  display: flex;
  width: 100%;
  align-self: flex-end;
}
.year {
  background-color: #132D3C;
  color: white;
  font-weight: bold;
  text-align: center;
  width: 50px;
  padding-top: 10px;
  margin-left: 25px;
  
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.citations {
  width: 90%;
}
.citation {
  margin: 15px 0px;
}
</style>
  
  <h2>Publications</h2>

<?php foreach ($citations as $year => $values) {?>

  <div class="year-row">
    <div class="citations">
    <?php
      sort($values);
      foreach ($values as $citation) {
        print '<div class="citation">' . $citation . '</div>';
      }
    ?>
    </div>
    <div class="year"><?php print $year;?></div>
  </div>
  
  <?php } ?>
  
<?php } ?>
