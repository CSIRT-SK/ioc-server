<?php
include_once('page.php');
pageStart('report');
?>

<h2>Scan reports</h2>

<article>
    <h3>List of reports</h3>
    <div class="content">
        <?php printRepList();?>
    </div>
</article>

<?php
pageEnd();

function printRepList() {
    global $api;
    $list = $api->repList();
    echo '<table class="repTable"><tr><th>Organization</th><th>Device</th><th>Time</th><th>Set</th><th>IOC</th><th>Result</th></tr>';
    foreach ($list as $entry) {
        echo '<tr><td>', $entry['org'], '</td><td>', $entry['device'], '</td><td>', date("j.n.Y G:i:s",strtotime($entry['timestamp'])), '</td><td>', $entry['setname'], '</td>';
        $name = $api->iocGet($entry['ioc_id'])['name'];
        echo '<td>', $name, '</td><td>';
        if ($entry['result']) echo '<p class="fail">found</p>';
        else echo '<p class="success">not found</p>';
        echo '</td></tr>';
    }
    echo '</table>';
}
?>