<?php
include_once('page.php');
pageStart('extra');
?>

<h2>Extra functions</h2>

<article>
    <h3>Import from CSV</h3>
    <div class="content">
        <?php importCsv();?>
    </div
</article>

<?php
pageEnd();

function importCsv() {
    define('MAX_FILE_SIZE',30000);
?>
<form method="post" enctype="multipart/form-data">
    File to upload:
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE?>">
    <input type="file" name="ufile"> <br>
    <input type="submit" value="Upload File" name="submit">
</form>
<?php
if (isset($_POST['submit']) && isset($_FILES['ufile']['size']) && $_FILES['ufile']['size'] !== 0) {
    echo '<br><br>';
    try {
        if ($_FILES['ufile']['size'] > MAX_FILE_SIZE)
            throw new Exception('File too large');

        if (strcmp($_FILES['ufile']['type'], 'text/csv') !== 0) // for now only cimbl(csv)     // 'type' is not checked by php, only by client
            throw new Exception('Invalid type');
            
        if (!$lines = file($_FILES['ufile']['tmp_name']))
            throw new Exception('Could not read the file');
            
        $list = parseCsvFile($lines);
            
        echo '<pre>', var_export($list), '</pre>';
        // TODO: add the parsed indicators to the database
        
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else {
    echo 'No valid file';
}

}

function parseCsvFile($lines) {
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
    return $list;
}
?>