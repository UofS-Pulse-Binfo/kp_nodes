<?php
  // Render the table properties.
  // When nothing is returned, pane, table, link and all are not available.

  if (isset($caption_count_stocks)) {
    // Text showing how many stocks.
    print $caption_count_stocks;
    // Stock table.
    print $table_project_stocks;
    // Pager.
    print $pager_project_stocks;
  }
?>
