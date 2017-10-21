<?php
  // Render the table properties.
  // When nothing is returned, pane, table, link and all are not available.

  if (isset($tbl_caption)) {
    // Text showing how many stocks.
    print $tbl_caption;
    // Stock table.
    print $tbl_pager;
    // Pager.
    print $tbl_summary_table;
  }
?>
