<?php
  $file_csv = "tmp_".date("Y-m-d").".csv";
  header('Content-type: application/csv');
  header(sprintf('Content-Disposition: attachment; filename="%s"',$file_csv));
  readfile("./".$file_csv);
  exit;
?>