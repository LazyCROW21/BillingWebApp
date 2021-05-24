<?php
    if(isset($_POST['orderDetails']))
    {
        require_once('../Admin_Panel/PHP/database.php');

        $chkitem = 'SELECT item_id, item_name, item_price, item_stock FROM items WHERE item_id = ? AND deleted = 0';
        $chkcust = 'SELECT customer_id FROM customers WHERE customer_phone = ?';
        
        $OrderData = json_decode($_POST['orderDetails']);
        // print_r($OrderData);
    
        $custName = $OrderData->custName;
        $custPhone = $OrderData->custPhone;
        $custDelivery = $OrderData->custDelivery;
        $deliveryDate = $OrderData->deliveryDate;
        if($custName === '' || $custPhone === '' || $custDelivery === '' || $deliveryDate === '')
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
        $stmt->bind_param('s', $custPhone);
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
        // $itemlist = $OrderData->itemList;
        // $itemPrice = array();
        $OrderArr = (array)$OrderData;
        $itemListArr = array();
        foreach($OrderArr as $key => $value)
        {
            if(substr($key, 0, 4) === 'item')
            {
                $itemObj = json_decode($value);
                $itemListArr[] = $itemObj;
                $item_id = $itemObj->id;
                $item_stk = $itemObj->qty;
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
                        // $itemPrice[$item_id] = $row['item_price'];
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
        $rd = date('Y-m-d', strtotime($deliveryDate));
        $rt = date('H:i:s', strtotime($deliveryDate));
        $cur_date = date('Y-m-d H:i:s');
        $stmt->bind_param('ssssss', $custName, $custPhone, $custDelivery, $rd, $rt, $cur_date);
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
        foreach($itemListArr as $item)
        {
            $item_id = $item->id;
            $item_qty = $item->qty;
            $sel_p = $item->price;
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