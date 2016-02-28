<?php
define('SET', 'setname');
define('API', 'https://158.195.250.203/ioc-server/api.php');
define('CACERT', 'C:\xampp\apache\bin\certtest\ca.pem');
define('CLIENTCERT', 'C:\xampp\apache\bin\certtest\client.pem');
define('CLIENTKEY', 'C:\xampp\apache\bin\certtest\client.key');

define('DEBUG', false);

function printIocTree($node) {
    echo '<li><span class="name">' . $node['name'] . '</span><span class="type">' . $node['type'] . '</span>';
    if ($node['value'] != NULL) {
        echo '<span class="value">[ ' . $node['value'];
        if ($node['value2'] != NULL) {
            echo ' | ' . $node['value2'];
        }
        echo ' ]</span>';
    }
    if (isset($node['children'])) {
        echo '<ul>';
        foreach ($node['children'] as $child) {
            printIocTree($child);
        }
        echo '</ul>';
    }
    
}
?>

<!DOCTYPE html>

<!--
    Simple web client that connects to the API, retrieves indicator list and prints it out in a table
-->
<html>
    <head>
        <title>Sample text</title>
        <link href="style.css" rel="stylesheet">
    </head>
    <body>
        <h3>Indicator list "<?php echo SET?>"</h3>
        <?php $params = [
                'controller' => 'indicator',
                'action' => 'request',
                'name' => SET
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => API . '?' . http_build_query($params),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_CAINFO => CACERT,
                CURLOPT_SSLCERT => CLIENTCERT,
                CURLOPT_SSLKEY => CLIENTKEY
            ]);
            $curl_answer = curl_exec($curl);
            if (DEBUG) {
                echo '<pre>';
                echo var_export($curl_answer);
                echo '</pre><br>';
            }

            if ($curl_answer === false) {
                echo 'cURL error [' . curl_error($curl) . ']';
            } else {
                $result = json_decode($curl_answer, true);
                if (DEBUG) {
                    echo '<pre>';
                    echo var_export($result);
                    echo '</pre><br>';
                }
                if ($result['success'] == true) {
                    echo '<div class="header"><span class="name">Name</span><span class="type">Type</span><span class="value">Value</span></div>';
                    echo '<div class="tree">' . SET . '<ul>';
                    printIocTree($result['data']);
                    echo '</ul></div>';
                }
            }
        ?>
    </body>
</html>