<!DOCTYPE html>

<!--
    Simple web client that connects to the API, retrieves indicator list and prints it out in a table
-->
<html>
    <head>
        <title>Sample text</title>
    </head>
    <body>
        <h3>Indicator list</h3>
        <?php $params = [
                'controller' => 'indicator',
                'action' => 'list'
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'http://localhost:8080/_test_api/?' . http_build_query($params),
                CURLOPT_RETURNTRANSFER => true
            ]);
            $result = json_decode(curl_exec($curl), true);
            if ($result === false) {
                echo 'cURL error [' . curl_error() . ']';
            } else {
                if (!$result['success']) {
                    echo 'Server error [' . $result['errormsg'] . ']';
                } else {
                    // request was successful, parse the data into table
        ?>
        <table style="border: 1px solid black">
            <tr>
                <th>Title</th><th>Type</th><th>Value</th>
            </tr>
            <?php
                    foreach ($result['data'] as $entry) {
                        echo '<tr>';
                        echo '<td>' . $entry['name'] . '</td><td>' . $entry['type'] . '</td><td>' . $entry['value'];
                        if ($entry['value2'] !== NULL) echo ' | ' . $entry['value2'] . '</td>';
                        echo '</tr>';
                    }
            ?>
        </table>
        <?php
                }
            }
        ?>
    </body>
</html>