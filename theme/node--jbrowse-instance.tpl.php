<?php

/**
 * @file
 * Bartik's theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 */

if ($teaser) { ?>
<div class="tripal-jbrowse-teaser tripal-teaser">
  <div class="tripal-jbrowse-teaser-title tripal-teaser-title"><h2><?php
    print l($node->title, "node/$node->nid", array('html' => TRUE));?>
  </h2></div>
  <div class="tripal-jbrowse-teaser-text tripal-teaser-text">
    <?php if (!empty($field_long_description)) {
      print render($field_long_description);
    }
    else {
      print "A JBrowse instance providing a graphical means of exploring the features of a genome.";
    }
    ?>
  </div>
</div>


<?php
}
else {

  libraries_load('tether');
  libraries_load('shepherd');
  drupal_add_css(drupal_get_path('theme','kptheme').'/css/shepherd_tour.css');

?>

<!-- Shepherd Tour -->
<script type="text/javascript">
(function ($) {
  Drupal.behaviors.kpnodesJBrowseShepherd = {
    attach: function (context, settings) {

    tour = new Shepherd.Tour({
      defaults: {
        classes: 'shepherd-theme-arrows',
        scrollTo: true
      }
    });

    tour.addStep( 'example', {
      //title: 'Bookmark',
      text: 'To bookmark your current position, use the URL provided by the "Share" button.',
      attachTo: {
        element: '#JBrowseInstance',
        on: 'top'
      },
      tetherOptions:{
        attachment: 'top right',
        targetAttachment: 'bottom right',
        offset: '0 88px',
      },
      scrollTo: false,
      buttons: [
        {
          text: 'Ok, Got it!',
          classes: 'shepherd-button-complete',
          action: tour.hide
        }
      ]
    });

    tour.start();

  }};
}(jQuery));
</script>

<?php
  // Determine the URL of the JBrowse based on the node properties
  $url = $node->field_jburl['und'][0]['url'];
  $query = array();
  $query['loc'] = (!empty($node->field_jbloc)) ? $node->field_jbloc['und'][0]['safe_value'] : '';
  $query['tracks'] = (!empty($node->field_jbtracks)) ? $node->field_jbtracks['und'][0]['safe_value'] : '';

  // Override any node properties based on the URL.
  foreach ($_GET as $k => $v) {
    if ($k != 'q') {
      $query[$k] = $v;
    }
  }

  $url = url($url, array('query' => $query));
?>

<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <?php if (!$page): ?>
    <h2<?php print $title_attributes; ?>>
      <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
    </h2>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <div class="content clearfix"<?php print $content_attributes; ?>>
    <div id="node-content">
      <?php
        // We hide the comments and links now so that we can render them later.
        hide($content['comments']);
        hide($content['links']);
        print render($content);
      ?>
    </div>

    <div id="JBrowseInstance">
      <iframe src="<?php print $url;?>" width="100%" height="100%" allowfullscreen webkitallowfullscreen mozallowfullsc\
reen oallowfullscreen msallowfullscreen>
      </iframe>
    </div>
  </div>

</div>

<?php } ?>
