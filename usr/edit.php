<?php
  $master_title = "Page Specific Title Text";
  ob_start();
?>
Lorem ipsum dolor sit amet, consectetuer adipiscing 
elit, sed nonummy nibh euismod tincidunt ut laoreet 
dolore magna aliat volutpat. Ut wisi enim ad minim 
veniam, quis nostrud exercita ullamcorper 
suscipit lobortis nisl ut aliquip ex consequat.
<?php
  $master_content = ob_get_clean();
  include $_SERVER['DOCUMENT_ROOT']."/master.php";
?>
