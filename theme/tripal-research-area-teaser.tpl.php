<?php
  //dpm($node);
  //node id
  $teaser_nodeid = $node['#node']->nid;
  //return research area
  $teaser_title = $node['#node']->title;
  //get medium size version of the image
  $teaser_img = image_style_url('medium', $node['#node']->field_page_thumnail['und'][0]['uri']);
  //summary text information
  $teaser_summary = $node['#node']->body['und'][0]['value'];
?>

<div class="tripal_organism-teaser tripal-teaser">
  <div class="col-left-img tripal-organism-teaser-title tripal-teaser-title" style="border: none; overflow: hidden">
    <?php 
      print '<img src="'.$teaser_img.'" alt="'.$teaser_title
            .'" title="'.$teaser_title.'" height="145" width="220" />'; 
    ?>  
  </div>
  
  <div class="col-right-copy tripal-organism-teaser-text tripal-teaser-text">
    <?php       
      //title - research area
      print '<h3><a href="../node/'.$teaser_nodeid.'">'.$teaser_title.'</a></h3>';
      //text information
      print '<p align="justify">'.substr($teaser_summary, 0, 310);
      if (strlen($teaser_summary) > 310) {
        print " ... " . l("[more]", 'node/'.$teaser_nodeid);
      }
      print "</p>";
    ?>
  </div>
  <div class="col-clear">&nbsp;</div>
</div>