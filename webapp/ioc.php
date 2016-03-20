<?php
include_once('page.php');
pageStart('ioc');
?>

<h2>Indicators of compromise</h2>

<article>
    <h3>Indicators in database</h3>
    <div class="content">
        <?php printIocListTable();?>
    </div>
</article>

<article>
    <h3>Add new indicator</h3>
    <div class="content">
        <?php printAddIoc();?>
    </div>
</article>

<article>
    <h3>Edit an indicator</h3>
    <div class="content">
        <?php printEditIoc();?>
    </div>
</article>

<article>
    <h3>Delete an indicator</h3>
    <div class="content">
        <?php printDeleteIoc();?>
    </div>
</article>

<article>
    <h3>Deleted indicators</h3>
    <div class="content">
        <?php printDeletedListTable();?>
    </div>
</article>

<article>
    <h3>Restore an indicator</h3>
    <div class="content">
        <?php printRestoreIoc();?>
    </div>
</article>

<?php
pageEnd();

// restore
function printRestoreIoc() {
    global $api;
    try {
        if (isset($_POST['resSubmit'], $_POST['resId'])) {
            if($api->iocHide($_POST['resId'], false) == 1)
                successMsg();
            else
                errorMsg();
        }
    } catch (Exception $e) {
        errorMsg();
    }
?>
<form method="post">
    <label>Restore: <select name="resId">
        <?php
        $list = $api->iocListHidden();
        foreach ($list as $ioc) {
            echo '<option value=', $ioc['id'], '>', $ioc['name'], '</option>';
        }
        ?>
    </select></label>
    <input type="submit" name="resSubmit" value="Restore">
</form>
<?php
}

// delete
function printDeleteIoc() {
    global $api;
    try {
        if (isset($_POST['delSubmit'], $_POST['delId'])) {
            if($api->iocHide($_POST['delId'], true) == 1)
                successMsg();
            else
                errorMsg();
        }
    } catch (Exception $e) {
        errorMsg();
    }
?>
<form method="post">
    <label>Delete: <select name="delId">
        <?php
        $list = $api->iocList();
        foreach ($list as $ioc) {
            echo '<option value=', $ioc['id'], '>', $ioc['name'], '</option>';
        }
        ?>
    </select></label>
    <input type="submit" name="delSubmit" value="Delete">
</form>
<?php
}

// edit
function printEditIoc() {
    global $api;
    try {
        if (isset($_POST['editSubmit'], $_POST['editName'], $_POST['editType'], $_POST['editId'])){
            if (empty($_POST['editName']) || empty($_POST['editType'])) throw new Exception();
            $ioc = $api->iocGet($_POST['editId']);
            $api->iocUpdate($ioc['id'], $_POST['editName'], $_POST['editType'], $_POST['editValue'], $_POST['editValue2'], $ioc['parent']);
            successMsg();
        }
    } catch (Exception $e) {
        errorMsg();
    }
?>
<form method="post">
    <label>Edit: <select name="editId">
        <?php
        $list = $api->iocList();
        foreach ($list as $ioc) {
            echo '<option value=', $ioc['id'], '>', $ioc['name'], '</option>';
        }
        ?>
    </select></label><br>
    <label>Name: <input type="text" name="editName"></label><br>
    <label>Type: <select name="editType">
        <?php
        foreach (iocTypes() as $type) {
            echo '<option value="', $type, '">', $type, '</option>';
        }
        ?>
    </select></label><br>
    <label>Value: <input type="text" name="editValue"></label><br>
    <label>Value2: <input type="text" name="editValue2"></label><br>
    <input type="submit" name="editSubmit" value="Edit">
</form>
<?php
}

// add
function printAddIoc() {
    global $api;
    try {
        if (isset($_POST['addSubmit'], $_POST['addName'], $_POST['addType'])){
            if (empty($_POST['addName']) || empty($_POST['addType'])) throw new Exception();
            $api->iocAdd($_POST['addName'], $_POST['addType'], $_POST['addValue'], $_POST['addValue2']);
            successMsg();
        }
    } catch (Exception $e) {
        errorMsg();
    }
?>
<form method="post">
    <label>Name: <input type="text" name="addName"></label><br>
    <label>Type: <select name="addType">
        <?php
        foreach (iocTypes() as $type) {
            echo '<option value="', $type, '">', $type, '</option>';
        }
        ?>
    </select></label><br>
    <label>Value: <input type="text" name="addValue"></label><br>
    <label>Value2: <input type="text" name="addValue2"></label><br>
    <input type="submit" name="addSubmit" value="Add">
</form>
<?php
}

function successMsg() {
    echo '<p class="success">OK</p>';
}

function errorMsg() {
    echo '<p class="fail">Failed</p>';
}

function iocTypes() {
    return ['registry', 'filename', 'filehash', 'dns', 'cert', 'processname', 'processhash', 'AND', 'OR'];
}

function printDeletedListTable() {
    global $api;
    $list = $api->iocListHidden();
    echo '<table class="iocTable"><tr><th>Name</th><th>Type</th><th colspan=2>Values</th></tr>';
    foreach ($list as $entry) {
        echo '<tr><td>', $entry['name'], '</td><td>', $entry['type'], '</td><td>', $entry['value'], '</td><td>', $entry['value2'], '</td></tr>';
    }
    echo '</table>';
}

function printIocListTable() {
    global $api;
    $list = $api->iocList();
    echo '<table class="iocTable"><tr><th>Name</th><th>Type</th><th colspan=2>Values</th></tr>';
    foreach ($list as $entry) {
        echo '<tr><td>', $entry['name'], '</td><td>', $entry['type'], '</td><td>', $entry['value'], '</td><td>', $entry['value2'], '</td></tr>';
    }
    echo '</table>';
}
?>
