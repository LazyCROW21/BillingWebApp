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

    <!-- specific css/js -->
    <link rel="stylesheet" type="text/css" href="../CSS/cust_style.css">
    <script src="../JS/validator.js"></script>

    <title>
        Manage Customers
    </title>

    <script>
        function validatecustform()
        {
            var cn = document.getElementById('custn');
            var cp = document.getElementById('custp');
            if(cn.value.length < 3 || specialchartestwspace(cn.value))
            {
                var en = document.getElementById('errorn');
                en.innerText = "*Name should be at least 3 characters long or please remove special character!";
                return false;
            }
            if(cp.value.length != 10 || !isnumber(sp.value))
            {
                var ep = document.getElementById('errorp');
                ep.innerText = "*Phone no. is either not 10 digits long or have non-digit characters!";
                return false;
            }
            return true
        }

        function confirmdelcust()
        {
            var r = confirm('Are you sure you want to delete this customer?');
            if(!r)
            {  
                document.getElementById("editcustform").addEventListener("submit", function(event){
                event.preventDefault(); }, true);
            }
        }
    </script>

</head>
<body>
    <?php
        if(isset($_GET['add_cust']) && $_GET['add_cust'] == 1)
        {
            echo '<script>alert("Registered Successfully!");</script>';
        }
        else if(isset($_GET['add_cust']) && $_GET['add_cust'] == -1)
        {
            echo '<script>alert("The PhoneNo/Email is already Taken!");</script>';
        }
    ?>
    <div id="main-block">
        <div id="headline">
            <img id="sliderbtn" src="../IMG/slidebar.png" height="32px" width="36px" onclick="openslidebar()">
            
            <img id="user-img" src="../IMG/favicon.png" onclick="toggleACList()">
            <span id="admin-user">Welcome, Admin</span>
            <div id="account">
                <div id="lgout"><a href="../PHP/logout.php">Logout</a></div>
            </div>
        </div>
        
        <div id="editwrap">
            <div id="editcustbox">
                <img src="../img/closeEditCustBtn.jpg" alt="closebtn" id="closecustebox" onclick="closeeditcust()">
            </div>
        </div>
        
        <!-- Slide bar -->
        <?php require_once('../TEMPLATES/layout_slidebar.html'); ?>
        <!-- Slide bar -->

        <div class="page-content" onclick="closeslidebar()">
            <?php
                require('../TEMPLATES/addcust.html');
                require('../PHP/viewcust.php');
            ?>
        </div>
    </div>
    <!-- footer -->
    <?php require_once('../TEMPLATES/layout_footer.html'); ?>
    <!-- footer -->
</body>
</html>