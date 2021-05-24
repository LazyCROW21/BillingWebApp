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
    if(isset($_POST['addemptyb']))
    {
        $curr_date = date('Y-m-d H:i:s');
        $emptybill = "INSERT INTO bill_detail (order_date_time) VALUES ('$curr_date')";
        require_once('../php/database.php');
        if($conn->query($emptybill))
        {
            header('location: ../PAGES/view_bill.php');
        }
        else
        {
            die('Error: '.$conn->error);
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Basic CSS/JS -->
    <?php require_once('../TEMPLATES/layout_head.html'); ?>
    <!-- Basic CSS/JS -->

    <link rel="stylesheet" type="text/css" href="../CSS/viewbill_style.css">
    <title>
        View Bills
    </title>

    <!-- Special JS file -->
    <script src="../JS/bill_box.js"></script>

    <script>
        function slideopen(d_id)
        {
            var tr = document.getElementById(d_id);
            //tr.setAttribute("show", "true");
            if(tr.hasAttribute("show"))
            {
                if(tr.getAttribute("show") == "false")
                {
                    tr.setAttribute("show", "true");
                }
                else
                {
                    tr.setAttribute("show", "false");
                }
            }
            else
            {
                tr.setAttribute("show", "true");
            }
        }
    </script>

</head>
<body>
    <div class="confirm-wrapper" id="conf_wrap">
    <!-- confirm wrapper to make the background darker for the pop up -->
        <div class="confirm-box" id="confirmbox">
            <button class="box-x" onclick="closebox()">X</button>
            <!-- this div is filled by js function call using ajax + php -->
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
            <div class="view-bill">
                <h2>Pending Bills</h2>
                <!-- Adds empty bill -->
                <form action="" method="post">
                    <input type="submit" id="addemptybill" name="addemptyb" value="Add Empty Bill">    
                </form>
                
                <?php 
                    require_once('../PHP/get_new_bill.php');
                ?>
                <h2>Passed Bills</h2>
                <?php 
                    require_once('../PHP/get_passed_bill.php');
                ?>
            </div>
        </div>
    </div>
    <!-- footer -->
    <?php require_once('../TEMPLATES/layout_footer.html'); ?>
    <!-- footer -->
</body>
</html>