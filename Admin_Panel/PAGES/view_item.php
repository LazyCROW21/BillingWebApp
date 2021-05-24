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

    <link rel="stylesheet" type="text/css" href="../CSS/viewitem_style.css">

    <title>
        View Items
    </title>
</head>
<body>
    <div id="upd_wrap" class="upd-wrapper">
        <div id="upd_box" class="upd-box">
            <div><button id="closeEdit" onclick="closeEditMenu()">x</button>
            </div>
        </div>
    </div>

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
            <div class="view-item">
                <h2>Current Items</h2>  
                <?php 
                    //require_once('../TEMPLATES/viewitem.html'); //pretty much useless
                    require_once('../PHP/getitems.php');
                ?>
            </div>
        </div>
    </div>
    <!-- footer -->
    <?php require_once('../TEMPLATES/layout_footer.html'); ?>
    <!-- footer -->
</body>
</html>