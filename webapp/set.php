<?php
include_once('page.php');
pageStart('set');
?>

<article>
    <h3>Indicator sets in database</h3>
    <div class="content">
        <?php printIocSetList();?>
    </div>
</article>

<?php
pageEnd();

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