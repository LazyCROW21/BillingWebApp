<?php
    require('../PHP/database.php');
    
    $offset = 0;
    if(!isset($_GET['page']))
    {
        $offset = 0;
        unset($_SESSION['maxpage']);
    }
    else
    {
        $offset = (intval($_GET['page']) - 1)*15;
    }
    if(!isset($_SESSION['maxpage']))
    {
        $getallcust = 'SELECT customer_id FROM customers';
        $getrows = $conn->query($getallcust);
        $maxrow = $getrows->num_rows;
        $_SESSION['maxpage'] = ceil($maxrow/15);
    }
    
    $getcust = 'SELECT * FROM customers ORDER BY customer_name LIMIT 15 OFFSET '.$offset;
    $result = $conn->query($getcust);

    echo '<div class="custbox">';
        echo '<h2>View Customers</h2>';
        echo '<div class="custtable">';
            echo '<div class="custrow custheadrow">
            <div>C.ID</div>
            <div>Name</div>
            <div>PhoneNo.</div>
            <div>E-mail</div>
            <div>GST Number</div>
            <div>Address</div>
        </div>';

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row['customer_gst'] == NULL)
                {
                    $row['customer_gst'] = ' --- ';
                }
                echo '<div class="custrow">
                <div>'.$row['customer_id'].'</div>
                <div>'.$row['customer_name'].'</div>
                <div>'.$row['customer_phone'].'</div>
                <div>'.$row['customer_email'].'</div>
                <div>'.$row['customer_gst'].'</div>
                <div>'.$row['customer_addr'].'</div>
                <div class="editcustbtn" onclick="editcust('.$row['customer_id'].')">Edit</div>
            </div>';
            }
        } 
        else 
        {
            echo "<div>0 results..</div>";
        }
    
        echo '</div>';
    echo '</div>';
?>
<div class="viewcustpage">
    <?php
        if(isset($_GET['page']))
        {
            if($_GET['page'] <= 1)
            {
                echo '<button id="prev" onclick="chncustpage('.$_GET['page'].', -1);" disabled>PREV</button>';
            }
            else
            {
                echo '<button id="prev" onclick="chncustpage('.$_GET['page'].', -1);">PREV</button>';
            }
            if($_GET['page'] >= $_SESSION['maxpage'])
            {
                echo '<button id="nxt" onclick="chncustpage('.$_GET['page'].', 1);" disabled>NEXT</button>';
            }
            else
            {
                echo '<button id="nxt" onclick="chncustpage('.$_GET['page'].', 1);">NEXT</button>';
            }
        }
        else
        {
            echo '<button id="prev" onclick="chncustpage(1, -1);" disabled>PREV</button>';
            if($_SESSION['maxpage'] <= 1)
            {
                echo '<button id="nxt" onclick="chncustpage(1, 1);" disabled>NEXT</button>';
            }
            else
            {
                echo '<button id="nxt" onclick="chncustpage(1, 1);">NEXT</button>';
            }
        }
    ?>
</div>