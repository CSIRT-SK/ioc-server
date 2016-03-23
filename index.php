Page
<pre>
<?php
echo var_export($_SERVER);

include_once 'controllers/Web.php';

$api = new Web();

echo var_export($api->iocGet(1));
?>
</pre>