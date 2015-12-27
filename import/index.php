<?php
$maxFileSize = 30000;
$uploadDir = getcwd() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
?>

<!DOCTYPE html>
<html>
    <body>
        <!-- TODO: maybe add 'action=""' so that the upload is not handled by the same page -->
        <form method="post" enctype="multipart/form-data">
            File to upload:
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxFileSize?>">
            <input type="file" name="ufile"> <br>
            <input type="submit" value="Upload File" name="submit">
        </form>
        <pre>
        
<?php
if (isset($_POST['submit']) && isset($_FILES['ufile']['size']) && $_FILES['ufile']['size'] !== 0) {
    echo var_export($_FILES);
    
    try {
        if ($_FILES['ufile']['size'] > $maxFileSize)
            throw new Exception('File too large');

        if (strcmp($_FILES['ufile']['type'], 'text/csv') !== 0) // for now only cimbl(csv)     // 'type' is not checked by php, only by client
            throw new Exception('Invalid type');
            
        if (!$lines = file($_FILES['ufile']['tmp_name']))
            throw new Exception('Could not read the file');
            
        
        $csv = array_map('str_getcsv', $lines);
            
        //echo var_export($csv);
        
        // find indices of relevant fields
        for ($i = 0; $i < count($csv[0]); $i++) {
            // names of relevant fields for different input formats should be stored in some constant (instead of hardcoded)
            if ($csv[0][$i] === 'type')
                $iType = $i;
            if ($csv[0][$i] === 'value')
                $iValue = $i;
            if ($csv[0][$i] === 'indicator_title')
                $iTitle = $i;
        }
        
        // extract the relevant fields
        $list = [];
        for ($i = 1; $i < count($csv); $i++) {
            $indicator = [
                'type' => $csv[$i][$iType],
                'value' => $csv[$i][$iValue],
                'title' => $csv[$i][$iTitle],
            ];
            
            // expolde compound indicators
            if (strpos($indicator['type'], '|')) {
                $types = explode('|', $indicator['type']);
                $values = explode('|', $indicator['value']);
                $indicator['type'] = 'AND';
                $indicator['value'] = [];
                for ($j = 0; $j < count($types); $j++) {
                    array_push($indicator['value'], [
                        'type' => $types[$j], 
                        'value' => $values[$j], 
                        'title' => $indicator['title'],
                    ]);
                }
            }
            
            array_push($list, $indicator);
        }
        
        echo var_export($list);
        // TODO: add the parsed indicators to the database
        
        for ($list as $indicator) {
            if ($indicator['type'] !== 'AND') {
                // insert straight to DB
            } else {
                // insert child indicators of AND - $indicator['value'][0], $indicator['value'][1] and retrieve their ids
                // insert new indicator for AND with child ids as value and value2
            }
        }
        
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else {
    echo 'No valid file';
}
?>

        </pre>
    </body>
</html>
