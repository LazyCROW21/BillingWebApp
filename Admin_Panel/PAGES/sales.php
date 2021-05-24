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

    <!-- specific CSS/JS -->
    <link rel="stylesheet" type="text/css" href="../CSS/sales_style.css">

    <title>
        Sale Analysis
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

        <div id="page-content" onclick="closeslidebar()">
            <div class="exportxls">
                <h2>Export data as Excel File:</h2>
                <form action="../PHP/excel.php" method="post">
                    <p><input type="radio" name="datafield" value="itemtable"> - Item List</p>
                    <p><input type="radio" name="datafield" value="customertable"> - Customer List</p>
                    <p><input type="radio" name="datafield" value="bill"> - Bills</p>
                    <p><input type="submit" name="exportbtn" value="Export"></p>
                </form>
            </div>
        </div>

        <!-- footer -->
        <?php require_once('../TEMPLATES/layout_footer.html'); ?>
        <!-- footer -->
    
    </div>
</body>
</html>