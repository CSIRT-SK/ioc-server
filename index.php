Page
<pre>
<?php
include_once 'controllers/Web.php';

$api = new Web();

echo var_export($api->iocGet(1));
?>
</pre>