<?php
    require('../database.php') ;
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php' ;?>
<body>
    <?php include 'includes/navbar.php' ;?>
    <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"/>
            <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
</body>
</html>
