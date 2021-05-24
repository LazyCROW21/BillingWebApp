<?php
    session_start();
    if(isset($_SESSION['user']) && isset($_SESSION['pwd']))
    {
        if($_SESSION['user'] !== 'admin' && $_SESSION['pwd'] !== 'admin')
        {
            header('location: ../Login/');
        }
    }
    else
    {
        header('location: ../Login/');
    }
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Basic CSS/JS -->
    <?php require_once('../TEMPLATES/layout_head.html'); ?>
    <!-- Basic CSS/JS -->

    <link rel="stylesheet" type="text/css" href="../CSS/additem_style.css">
    <title>
        Add Items
    </title>
</head>
<body>
    <div id="main-block">
        <div id="headline">
            <img id="sliderbtn" src="../IMG/slidebar.png" height="32px" width="36px" onclick="openslidebar()">
            
            <img id="user-img" src="../IMG/favicon.png" onclick="toggleACList()">
            <span id="admin-user">Welcome, Admin</span>
            <div id="account">
                <div id="lgout"><a href="../PHP/logout.php">Logout</a></div>
            </div>
        </div>

        <!-- Slide bar -->
        <?php require_once('../TEMPLATES/layout_slidebar.html'); ?>
        <!-- Slide bar -->

        <div class="page-content" onclick="closeslidebar()">
            <?php 
                require_once('../TEMPLATES/additem.html');
                if(isset($_GET['add_item']))
                {
                    if($_GET['add_item'] == 1)
                    {
                        echo '<script> alert("ITEM ADDED SUCCESSFULLY"); window.location = "add_item.php"; </script>';
                    }
                }
            ?>
        </div>

        <!-- footer -->
        <?php require_once('../TEMPLATES/layout_footer.html'); ?>
        <!-- footer -->
    
    </div>
</body>
</html>