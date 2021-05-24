<?php
    if(isset($_POST['addcust']))
    {
        if(!isset($_POST['cust_name']) || !isset($_POST['cust_phn']) || !isset($_POST['cust_addr']))
        {
            header('location: ../PAGES/manage_cust.php');
            exit(0);
        }
        $name = $_POST['cust_name'];
        $phn = $_POST['cust_phn'];
        $email = $_POST['cust_email'];
        $gstno = 'nogst';
        if(isset($_POST['cust_gst']))
        {
            $gstno = $_POST['cust_gst'];
        }
        $addr = $_POST['cust_addr'];

        require('../PHP/database.php');

        $chkqry = 'SELECT customer_id FROM customers WHERE customer_phone = ? OR customer_email = ?';
        $ins_cust = 'INSERT INTO customers (customer_name, customer_phone, customer_email, customer_gst, customer_addr) VALUES (?, ?, ?, ?, ?)';

        $stmt = $conn->prepare($chkqry);
        if($stmt)
        {
            $stmt->bind_param('ss', $phn, $email);
            if($stmt->execute())
            {
                $result = $stmt->get_result();
                if($result->num_rows > 0)
                {
                    header('Location: ../PAGES/manage_cust.php?add_cust=-1');
                }
            }
            else
            {
                die('Error in execution try again!');
            }
        }
        else
        {
            die('Query Failed! Contact support');
        }
        $stmt->close();
        $stmt = $conn->prepare($ins_cust);
        if($stmt)
        {
            $stmt->bind_param('sssss', $name, $phn, $email, $gstno, $addr);
            if($stmt->execute())
            {
                header('Location: ../PAGES/manage_cust.php?add_cust=1');
                //echo '<script> alert("Item Added Successfully!") </script>';
            }
            else
            {
                die('Error in execution try again!');
            }
        }
        else
        {
            die('Query Failed! Contact support');
        }
    }
    else
    {
        header('location: ../PAGES/manage_cust.php');
    }
?>