<?php
    require('../PHP/database.php');
    
    //basic table headers
    $grid_head = array('Bill ID','Customer Name', 'Customer Phone', 'Order Date&Time', ' ');

    $get_bills = 'SELECT bill_id, cust_name, cust_phone, DATE_FORMAT(order_date_time, "%d-%m-%Y, %l:%i %p") AS order_date_time FROM bill_detail WHERE accepted = 0 ORDER BY order_date_time DESC';
    $result = $conn->query($get_bills);

    if($result)
    {
        echo '<div class="pog">'; // generate grid(table)
        if($result->num_rows > 0)
        {
            $allrows = $result->fetch_all(MYSQLI_ASSOC);
            // Grid heading
            echo '<div class="pog-new_b_head">';
                foreach($grid_head as $gh)
                {
                    echo '<div>'.$gh.'</div>';
                }
            echo '</div>';
            
            // data rows
            foreach($allrows as $row)
            {
                //generate button;
                $btn_id = 'new_bill-'.$row['bill_id'];
                $open_btn = '<button class="open-bill-btn" onclick="openbill(\''.$btn_id.'\')">OPEN</button>';
                $reject_btn = '<button class="reject-bill-btn" onclick="rejectbill(\''.$btn_id.'\')">REJECT</button>';
                echo '<div class="pog-nbr">';
                    echo '<div>'.$row['bill_id'].'</div>';
                    echo '<div>'.$row['cust_name'].'</div>';
                    echo '<div>'.$row['cust_phone'].'</div>';
                    echo '<div class="odt">'.$row['order_date_time'].'</div>';
                    echo '<div>'.$open_btn.$reject_btn.'</div>';
                echo '</div>';
            }
        }
        else // display empty table
        {
            // Grid heading
            echo '<div class="pog-new_b_head">';
                foreach($grid_head as $gh)
                {
                    echo '<div>'.$gh.'</div>';
                }
            echo '</div>';
            //Print no bills found
            echo '<div class="rdt">';
                echo '<p>No Pending Bills..</p>';
            echo '</div>';
        }
        echo '</div>'; // close pog
    }
    else
    {
        $conn->close();
        die("Error in Query : ".$conn->error);
    }
?>