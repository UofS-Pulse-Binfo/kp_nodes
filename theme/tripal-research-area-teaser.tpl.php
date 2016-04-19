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

<div class="tripal_research_area-teaser tripal-teaser">
  <div class="col-left-img tripal-teaser-image tripal_research_area-teaser-image" style="border: none; overflow: hidden">
    <?php
      print '<img src="'.$teaser_img.'" alt="'.$teaser_title
            .'" title="'.$teaser_title.'" height="145" width="220" />';
    ?>
  </div>

  <div class="col-right-copy">
    <div class="tripal_research_area-teaser-title tripal-teaser-title"><h2>
      <?php print '<a href="../node/'.$teaser_nodeid.'">'.$teaser_title.'</a>';?>
    </h2></div>
    <div class="tripal_research_area-teaser-text tripal-teaser-text">
      <?php
        print '<p align="justify">'.substr($teaser_summary, 0, 310);
        if (strlen($teaser_summary) > 310) {
          print " ... " . l("[more]", 'node/'.$teaser_nodeid);
        }
        print "</p>";
      ?>
    </div>
  </div>

  <div class="col-clear">&nbsp;</div>
</div>
