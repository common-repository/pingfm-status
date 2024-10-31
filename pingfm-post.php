<?php
import_request_variables('gp');

$in = $_POST['message'];
$message = stripslashes(htmlentities($in));

//Uncomment the line below if the timezone on your timestamp is incorrect.  List of timezone codes is available here: http://usphp.com/manual/en/timezones.php
//putenv('TZ=America/New_York');
$post_timestamp = date('n/j/Y g:i a');
$data = "<b>$post_timestamp:</b>&nbsp;&nbsp;$message";

$fp=fopen('pingfm-post.txt','w');
fwrite($fp, $data);
fclose($fp);

?>
