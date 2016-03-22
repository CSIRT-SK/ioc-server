<?php
include_once('../controllers/Web.php');

$api = new Web();

function successMsg() {
    echo '<p class="success">OK</p>';
}

function errorMsg() {
    echo '<p class="fail">Failed</p>';
}

function pageStart($title) {
?>
<!DOCTYPE html>

<html>
    <head>
        <title><?php echo $title;?></title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>
            <ul>
                <li><a href="index.php">index</a></li>
                <li><a href="ioc.php">ioc</a></li>
                <li><a href="set.php">set</a></li>
                    <li><a href="report.php">report</a></li>
                <li><a href="extra.php">extra</a></li>
            </ul>
        </header>
        <main>
<?php
}

function pageEnd() {
?>
        </main>
        <footer>
            &copy; Miso
        </footer>
    </body>
</html>

<?php
}
?>