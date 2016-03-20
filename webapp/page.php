<?php
include_once('../controllers/Web.php');

$api = new Web();

function pageStart($title) {
?>
<!DOCTYPE html>

<html>
    <head>
        <title><?php echo $title;?></title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <nav>
            <ul>
                <li><a href="index.php">index</a></li>
                <li><a href="ioc.php">ioc</a></li>
                <li><a href="set.php">set</a></li>
                <li><a href="report.php">report</a></li>
            </ul>
        </nav>
        <main>
<?php
}

function pageEnd() {
?>
        </main>
        <footer>
            footer &copy; Miso
        </footer>
    </body>
</html>

<?php
}
?>