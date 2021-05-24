<?php
    if(isset($_POST['data']))
    {
        require_once('../Admin_Panel/PHP/database.php');

        $chkitem = 'SELECT item_id, item_name, item_price, item_stock FROM items WHERE item_id = ? AND deleted = 0';
        $chkcust = 'SELECT customer_id FROM customers WHERE customer_phone = ?';
        $OrderData = json_decode($_POST['data']);

        $cust_N = $OrderData->custN;
        $cust_P = $OrderData->custP;
        $cust_C = $OrderData->custC;
        $cust_RDT = $OrderData->custRDT;
        if($cust_N === '' || $cust_P === '' || $cust_C === '' || $cust_RDT === '')
        {
            die('ERROR DATA MISSING');
        }
        // check customer
        $stmt = $conn->prepare($chkcust);
        if(!$stmt)
        {
            die('Something went wrong contact support');
            // die('ERROR IN QRY1');
        }
        $stmt->bind_param('s', $cust_P);
        if($stmt->execute())
        {
            $result = $stmt->get_result();
            if($result->num_rows !== 1)
            {
                $stmt->close();
                die('CUSTOMER NOT FOUND');
            }
        }
        else
        {
            $stmt->close();
            die('ERROR IN EXEC');
        }
        $stmt->close();
        
        //check stocks
        $stmt = $conn->prepare($chkitem);
        $err = '';

        if(!$stmt)
        {
            die('Something went wrong contact support');
            // die('ERROR IN QRY2 '.$conn->error);
        }

        $item_id = 0;
        $item_stk = 0.0;
        $stmt->bind_param('i', $item_id);
        $itemlist = $OrderData->itemList;
        $itemPrice = array();
        foreach($itemlist as $item)
        {
            $item_id = $item->item_id;
            $item_stk = $item->qty;
            if($stmt->execute())
            {
                $resp = $stmt->get_result();
                if($resp->num_rows > 0)
                {
                    $row = $resp->fetch_assoc();
                    $curr_stk = $row['item_stock'];
                    if($curr_stk < $item_stk)
                    {
                        $item_name = $row['item_name'];
                        $err = $err."$item_name HAVE MAX STOCK $curr_stk\n";
                    }
                    $itemPrice[$item_id] = $row['item_price'];
                }
                else
                {
                    $err = $err."$item_id DOESN'T EXIST\n";
                }
            }
            else
            {
                die('ERROR IN EXEC');
            }
        }
        $stmt->close();
        if($err !== '')
        {
            die($err);
        }

        //now actually insert the date
        $insBill = 'INSERT INTO bill_detail (cust_name, cust_phone, cust_choice, ready_date, ready_time, order_date_time) VALUES (?, ?, ?, ?, ?, ?)';
        $insBillItems = 'INSERT INTO bill_item_list (bill_id, item_id, selling_price, item_qty) VALUEs (?, ?, ?, ?)';

        $stmt = $conn->prepare($insBill);
        if(!$stmt)
        {
            die('Something went wrong! insq1');
        }
        $rd = date('Y-m-d', strtotime($cust_RDT));
        $rt = date('H:i:s', strtotime($cust_RDT));
        $cur_date = date('Y-m-d H:i:s');
        $stmt->bind_param('ssssss', $cust_N, $cust_P, $cust_C, $rd, $rt, $cur_date);
        if(!$stmt->execute())
        {
            $stmt->close();
            die('ERROR IN EXEC');
        }
        $bid = $stmt->insert_id;
        $stmt->close();

        //add bill items
        $stmt = $conn->prepare($insBillItems);
        if(!$stmt)
        {
            die('ERROR PLEASE CONTACT SUPPORT');
        }
        $item_id = 0;
        $item_qty = 0.0;
        $sel_p = 0.0;
        $stmt->bind_param('iidd', $bid, $item_id, $sel_p, $item_qty);
        foreach($itemlist as $item)
        {
            $item_id = $item->item_id;
            $item_qty = $item->qty;
            $sel_p = $itemPrice[$item_id];
            if(!$stmt->execute())
            {
                die('ERROR IN EXEC');
            }
        }
        echo 'CONFIRMED';
    }
    else
    {
        die('ERROR');
    }
?>