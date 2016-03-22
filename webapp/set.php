<?php
include_once('page.php');
pageStart('set');
?>

<h2>Definiton sets</h2>

<article>
    <h3>Indicator sets in database</h3>
    <div class="content">
        <?php printIocSetList();?>
    </div>
</article>

<article>
    <h3>Create set / add new node</h3>
    <div class="content">
        <?php printSetAdd();?>
    </div>
</article>

<article>
    <h3>Delete node from set</h3>
    <div class="content">
        <?php printSetDel();?>
    </div>
</article>

<article>
    <h3>Edit tree structure</h3>
    <div class="content">
        <?php printSetEdit();?>
    </div>
</article>

<?php
pageEnd();

function printSetEdit() {
    global $api;
    try {
        if (isset($_POST['editSubmit'], $_POST['editId'], $_POST['editParent'])){
            if ($_POST['editId'] == $_POST['editParent']) throw new Exception();
            $ioc = $api->iocGet($_POST['editId']);
            $api->iocUpdate($_POST['editId'], $ioc['name'], $ioc['type'], $ioc['value'], $ioc['value2'], $_POST['editParent']);
            successMsg();
        }
    } catch (Exception $e) {
        errorMsg();
    }
?>
<form method="post">
    <label>Indicator: <select name="editId">
        <?php
        $list = $api->iocList();
        foreach ($list as $ioc) {
            echo '<option value=', $ioc['id'], '>', $ioc['name'], '</option>';
        }
        ?>
    </select></label><br>
    <label>Parent: <select name="editParent">
        <option value=0>none</option>
        <?php
        $list = $api->iocList();
        foreach ($list as $ioc) {
            echo '<option value=', $ioc['id'], '>', $ioc['name'], '</option>';
        }
        ?>
    </select></label><br>
    <input type="submit" name="editSubmit" value="Update">
</form>
<?php
}

function printSetDel() {
    global $api;
    try {
        if (isset($_POST['delSubmit'], $_POST['delName'], $_POST['delIoc'])){
            if ($api->setHide($_POST['delName'], $_POST['delIoc'], true) != 1) throw new Exception();
            successMsg();
        }
    } catch (Exception $e) {
        errorMsg();
    }
?>
<form method="post">
    <label>Set: <select name="delName">
        <?php
        $list = $api->setList();
        foreach ($list as $set) {
            echo '<option value="', $set['name'], '">', $set['name'], '</option>';
        }
        ?>
    </select></label><br>
    <label>Indicator: <select name="delIoc">
        <?php
        $list = $api->iocList();
        foreach ($list as $ioc) {
            echo '<option value="', $ioc['id'], '">', $ioc['name'], '</option>';
        }
        ?>
    </select></label><br>
    <input type="submit" name="delSubmit" value="Delete">
</form>
<?php
}

function printSetAdd() {
    global $api;
    try {
        if (isset($_POST['addSubmit'], $_POST['addName'], $_POST['addId'])){
            if (empty($_POST['addName'])) throw new Exception();
            $api->setAddIoc($_POST['addName'], $_POST['addId']);
            successMsg();
        }
    } catch (Exception $e) {
        errorMsg();
    }
?>
<form method="post">
    <label>Set name: <input type="text" name="addName"></label><br>
    <label>Root node: <select name="addId">
        <?php
        $list = $api->iocList();
        foreach ($list as $ioc) {
            echo '<option value=', $ioc['id'], '>', $ioc['name'], '</option>';
        }
        ?>
    </select></label><br>
    <input type="submit" name="addSubmit" value="Add">
</form>
<?php
}

// set table
function printIocSetList() {
    global $api;
    $results = $api->setList();
    echo '<div class="header"><span class="name">Name</span><span class="type">Type</span><span class="value">Value</span></div>';
    foreach ($results as $res) {
        $name = $res['name'];
        $tree = $api->setGetTree($name);
        echo '<div class="tree">', $name, '<ul>';
        foreach ($tree as $entry) printIocTree($entry);
        echo '</ul></div>';
    }
    echo '</div>';
}

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