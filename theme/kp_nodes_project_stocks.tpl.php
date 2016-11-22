<?php
  // Render the table properties.
  // When nothing is returned, pane, table, link and all are not available.

  if (isset($info_stock_count)) {
    // Text showing how many stocks.
    print $info_stock_count;
    // Stock table.
    print $table_project_stocks;
    // Pager.
    print $pager_project_stocks;
  }
?>
