<?php
    require_once('../PHP/database.php');

    if(isset($_POST['delcust']))
    {
        if(!isset($_POST['cust_id']))
        {
            die('No Customer Set');
        }
        $cid = $_POST['cust_id'];
        $delqry = 'DELETE FROM customers WHERE customer_id = ?';
        $stmt = $conn->prepare($delqry);
        if(!$stmt)
        {
            die('Error in query ! afaf');
        }
        $stmt->bind_param('i', $cid);
        if($stmt->execute())
        {
            echo '<script>alert("Customer Deleted!"); window.location.replace("../PAGES/manage_cust.php");</script>';
        }
        else
        {
            die('Error in execution! aifaf');
        }
    }
    if(isset($_POST['updcust']))
    {
        $cid = $_POST['cust_id'];
        $cname = $_POST['cust_name'];
        $cphn = $_POST['cust_phn'];
        $cemail = $_POST['cust_email'];
        $cgst = NULL;
        if(isset($_POST['cust_gst']))
        {
            $cgst = $_POST['cust_gst'];
        }
        $caddr = $_POST['cust_addr'];
        
        // first update phone no from all existing bills
        $getcurrdata = 'SELECT customer_phone FROM customers WHERE customer_id = ?';
        $stmt = $conn->prepare($getcurrdata);
        if(!$stmt)
        {
            die('Error in query! Contact Support');
        }
        else
        {
            $stmt->bind_param('i', $cid);
            if(!$stmt->execute())
            {
                die('Error in execution');
            }
            $result = $stmt->get_result();
            if($result->num_rows <= 0)
            {
                die('User is been delete or doesn\'t exist');
            }
            $cust_detail = $result->fetch_assoc();
            $cur_phn = $cust_detail['customer_phone'];
            $stmt->close();

            $updt_bill = 'UPDATE bill_detail 
            SET cust_phone = ?
            WHERE cust_phone = ?';
            $stmt = $conn->prepare($updt_bill);
            if(!$stmt)
            {
                die('Error in query! 1212');
            }
            $stmt->bind_param('ss', $cphn, $cur_phn);
            if(!$stmt->execute())
            {
                die('Error in execution');
            }
        }

        // then update master customer table
        $updt_cust = 'UPDATE customers 
        SET customer_name = ?,
         customer_phone = ?,
         customer_email = ?,
         customer_gst = ?,
         customer_addr = ?
         WHERE customer_id  = ?';
        
        $stmt = $conn->prepare($updt_cust);
        if(!$stmt)
        {
            die('Error in query! 1');
        }
        $stmt->bind_param('sssssi', $cname, $cphn, $cemail, $cgst, $caddr, $cid);
        if($stmt->execute())
        {
            echo '<script>alert("Successfully Updated!"); window.location.replace("../PAGES/manage_cust.php");</script>';
            // header('location: ../PAGES/manage_cust.php');
        }
        else
        {
            die('Error in execution, try again!');
        }
    }

    $cust_id = 8;
    if(isset($_POST['cid']))
    {
        $cust_id = $_POST['cid'];
    }
    else
    {
        die('No Customer ID recieved!');
    }
    $getcurrdata = 'SELECT * FROM customers WHERE customer_id = ?';
    
    $stmt = $conn->prepare($getcurrdata);
    if(!$stmt)
    {
        die('Error in query! Contact Support');
    }
    else
    {
        $stmt->bind_param('i', $cust_id);
        if(!$stmt->execute())
        {
            die('Error in execution');
        }
        $result = $stmt->get_result();
        if($result->num_rows <= 0)
        {
            die('User is been delete or doesn\'t exist');
        }
        $cust_detail = $result->fetch_assoc();
    }
?>

<h2>Edit CUSTOMER</h2>
<br>
<form id="editcustform" action="../php/editcust.php" method="POST">
    <input type="hidden" name="cust_id" value="<?php echo $cust_detail['customer_id'] ?>">

    <p><b>Customer Name:</b></p>
    <input type="text" id="custn" name="cust_name" value="<?php echo $cust_detail['customer_name'] ?>" maxlength="32" required><br>
    <p class="errormsg" id="errorn"></p>

    <p><b>Customer Phone:</b></p>
    <input type="text" id="custp" name="cust_phn" value="<?php echo $cust_detail['customer_phone'] ?>" maxlength="10" required><br>
    <p class="errormsg" id="errorp"></p>

    <p><b>Customer Email:</b></p>
    <input type="email" id="custe" name="cust_email" value="<?php echo $cust_detail['customer_email'] ?>" required><br>
    <p class="errormsg" id="errore"></p>

    <p><b>Customer Email:</b></p>
    <input type="text" id="custg" name="cust_gst" value="<?php echo $cust_detail['customer_gst'] ?>" maxlength="10"><br>
    <p class="errormsg" id="errorg"></p>

    <p><b>Customer Address:</b></p>
    <textarea name="cust_addr" maxlength="300" required><?php echo $cust_detail['customer_addr']; ?></textarea><br><br>
    <input id="dctm" name="delcust" type="submit" value="Delete" onclick="confirmdelcust()"><br>
    <input id="updctm" name="updcust" type="submit" value="Update">
</form>