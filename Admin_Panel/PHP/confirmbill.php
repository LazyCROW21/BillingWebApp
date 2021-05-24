<?php
    require_once('../PHP/database.php');
    // ON PRESSING SUBMIT BUTTON
    if(isset($_POST['conf_bill']) || isset($_POST['conf_bill_print']))
    {
        if(!isset($_POST['bill_id']) || !isset($_POST['discount']) || !isset($_POST['divchrg']))
        {
            die('InSuffienct Information!');
        }
        $bid = intval($_POST['bill_id']);
        $discnt = floatval($_POST['discount']);
        $divchrg = floatval($_POST['divchrg']);
        //set accepted to 1

        $ilist = $_POST;
        $item_list = array();
        foreach($ilist as $k => $v)
        {
            if(strpos($k,'itemp_') === 0)
            {
                $itemid = substr($k, 6);
                $itemqtykey = 'itemq_'.$itemid;
                if(isset($ilist[$itemqtykey]))
                {
                    $i_id = intval($itemid);
                    $i_p = intval($v);
                    $i_q = floatval($ilist[$itemqtykey]);
                    $itemobj = array($i_p, $i_q);
                    $item_list[$i_id] = $itemobj;
                    continue;
                }
                else
                {
                    die('Invalid Information! try again or contact support');
                }
            }
        }

        //Check Stocks
        $getstock = 'SELECT item_id, item_stock FROM items WHERE item_id = ?';
        $chkstmt = $conn->prepare($getstock);
        $chkitemid = -1;
        $chkitemstock = -1;
        if($chkstmt)
        {
            $chkstmt->bind_param('i', $chkitemid);
            foreach($item_list as $id => $info)
            {
                $chkitemid = $id;
                $chkitemstock = $info[1]; // gets stock
                if($chkstmt->execute())
                {
                    $result = $chkstmt->get_result();
                    $stkdetail = $result->fetch_assoc();
                    $curr_stk = $stkdetail['item_stock'];
                    if($curr_stk < $chkitemstock)
                    {
                        $chkstmt->close();
                        echo '<script>alert("Item with id: '.$chkitemid.', have max stock of: '.$curr_stk.', Please modify your bill!"); window.location.replace("../PAGES/view_bill.php"); </script>';      
                    }
                }
                else
                {
                    die('Error checking stock query! '.$conn->error);
                }
            }
        }
        else
        {
            die('Error in Query Stk: '.$conn->error);
        }

        $cust_n = NULL;
        $cust_p = NULL;
        $cust_c = NULL;
        $cust_rd = date('Y-m-d');
        $cust_rt = date('H:i:s');

        if(isset($_POST['ebd_cn']))
        {
            $cust_n = $_POST['ebd_cn'];
        }
        if(isset($_POST['ebd_cp']))
        {
            $cust_p = $_POST['ebd_cp'];
        }
        if(isset($_POST['ebd_cc']))
        {
            $cust_c = $_POST['ebd_cc'];
        }
        if(isset($_POST['ebd_rd']) && strlen($_POST['ebd_rd']) > 4)
        {
            $cust_rd = $_POST['ebd_rd'];
        }
        if(isset($_POST['ebd_rt']) && strlen($_POST['ebd_rt']) > 4)
        {
            $cust_rt = $_POST['ebd_rt'];
        }
        

        $upt_bill = 'UPDATE bill_detail SET cust_name = ?, cust_phone = ?, cust_choice = ?, ready_date = ?, ready_time = ?, accepted = 1, discount = ?, delivery_charge = ? WHERE bill_id = ?';
        $upt_bill_item = 'UPDATE bill_item_list SET selling_price = ?, item_qty = ? WHERE bill_id = ? AND item_id = ?';
        $upd_item_stock = 'UPDATE items SET item_stock = item_stock - ? WHERE item_id = ?';
        $stmt = $conn->prepare($upt_bill);
        if($stmt)
        {
            $stmt->bind_param('sssssddi', $cust_n, $cust_p, $cust_c, $cust_rd, $cust_rt, $discnt, $divchrg, $bid);
            if($stmt->execute())
            {
                if($stmt->affected_rows === 0)
                {
                    die('Something went wrong, check records/try again/contact support');
                }
                // success, now to update items
                $stmt = $conn->prepare($upt_bill_item);
                $i = 0;
                $p = 0; //price
                $q = 0; //qty
                $stmt->bind_param('ddii', $p, $q, $bid, $i);
                //updating stock
                $stk_stmt = $conn->prepare($upd_item_stock);
                $stk_stmt->bind_param('di', $q, $i);
                foreach($item_list as $id => $info)
                {
                    $i = $id;
                    $p = $info[0];
                    $q = $info[1];
                    if(!$stmt->execute() || !$stk_stmt->execute())
                    {
                        die('Error in execution! contact suport');
                    }
                }
                $stk_stmt->close();
                if(isset($_POST['conf_bill_print']))
                {
                    echo '<script>alert("BILL CONFIRMED!"); window.location.replace("../PAGES/view_bill.php"); window.open("../PHP/printbill.php?bill_id='.strval($bid).'&size=3inch"); </script>';
                }
                else
                {
                    echo '<script>alert("BILL CONFIRMED!"); window.location.replace("../PAGES/view_bill.php"); </script>';
                }
            }
            else
            {
                $stmt->close();
                $conn->close();
                die('Error in execution try again!');
            }
        }
        else
        {
            $conn->close();
            die('Error in query! contact support');
        }
        $stmt->close();
        $conn->close();
        exit();
    }

    // ON OPENING BILL AT FIRST
    if(isset($_POST['bill_id']))
    {
        $bill_id = $_POST['bill_id'];
    }
    else
    {
        die('Recieved No data! Contact Support or try again');
    }

    $get_detail = 'SELECT * FROM bill_detail WHERE accepted = 0 AND bill_id = ?';
    $get_item_detail = 'SELECT BIL.item_id AS item_id, ITM.item_name AS item_name, ITM.item_price AS rate, BIL.item_qty AS item_qty FROM bill_item_list BIL 
                INNER JOIN items ITM
                ON ITM.item_id = BIL.item_id AND BIL.bill_id = ?';

    // $table_h = array('ID', 'Item Name', 'Rate', 'Qty', 'GST<br>(%)', 'Amount');
    $table_h = array('ID', 'Item Name', 'Rate', 'Qty', 'Amount');

    // declare upper varaibles
    $bill_detail_row = array();

    $stmt = $conn->prepare($get_detail);
    if(!$stmt)
    {
        die('Error in query! Contact support');
    }
    $stmt->bind_param('i', $bill_id);
    if($stmt->execute())
    {
        $bill_detail = $stmt->get_result();
        //print_r($bill_detail);
        if($bill_detail->num_rows > 0)
        {
            $bill_detail_row = $bill_detail->fetch_assoc();
        }
        else
        {
            die('No bill details found!');
        }
    }
    else
    {
        die('Error in execution: '.$stmt->error);
    }
    echo '<div class="bill-box">';
    echo '<form method="POST" action="../PHP/confirmbill.php" id="confbill">';
        echo '<br>';
        echo '<div class="editbill_d">';
        echo '<div id="ebd_rdt"><p><b>Bill ID: </b>'.$bill_id.'</p></div>';
        echo '<div><p><b>CUSTOMER Name: </b></p></div> <div><input name="ebd_cn" type="text" value="'.$bill_detail_row['cust_name'].'"></div>';
        echo '<div><p><b>CUSTOMER Phone: </b></p></div> <div><input name="ebd_cp" type="text" value="'.$bill_detail_row['cust_phone'].'" maxlength="10"></div>';
        echo '<div><p><b>Pick Up Choice: </b></p></div> <div><select name="ebd_cc">';
            if($bill_detail_row['cust_choice'] == 'pick_up')
            {
                echo '<option value="pick_up" selected>Pick up</option>';
                echo '<option value="home_d">Home Delivery</option>';
            }
            elseif($bill_detail_row['cust_choice'] == 'home_d')
            {
                echo '<option value="pick_up">Pick up</option>';
                echo '<option value="home_d" selected>Home Delivery</option>';
            }
            else
            {
                echo '<option value="" selected disabled>-----</option>';
                echo '<option value="pick_up">Pick up</option>';
                echo '<option value="home_d">Home Delivery</option>';
            }
        echo '</select></div>';
        echo '<div><p><b>Ready Date: </b></p></div> <div><input name="ebd_rd" type="date" value="'.$bill_detail_row['ready_date'].'"></div>';
        echo '<div><p><b>Ready Time: </b></p></div> <div><input name="ebd_rt" type="time" value="'.$bill_detail_row['ready_time'].'"></div>';
        echo '<div id="ebd_rdt"><p><b>Order Date & Time: </b>'.date_format(date_create($bill_detail_row['order_date_time']), "d-m-Y, h:i A").'</p></div>';
        echo '</div>'; // end of edit bill details

        //edit bill
        $addp = '<input id="additem_p" type="number" step="0.01" min="0.01" placeholder="price">';
        $addq = '<input id="additem_q" type="number" step="0.001" min="0.001" placeholder="qty">';
        $addb = '<button type="button" id="additemtobillbtn" onclick="addnew()">Add</button>';
        // echo '<div class="editbill">';
            echo '<div class="additempanel">';
                echo '<div class="editmenuhead">Add New Item</div>';
                echo '<div><select id="additem_id" onchange="setitemprice(this.value)">';
            // Option print here
                echo '<option selected disabled>Choose Item</option>';
                $rows = $conn->query('SELECT item_id, item_name, item_price FROM items WHERE deleted = 0 AND item_stock > 0');
                while($row=$rows->fetch_assoc())
                {
                    echo '<option value="'.$row['item_id'].'">'.$row['item_name'].'</option>';
                }
                echo '</select></div>';
                echo '<div>'.$addp.'</div>';
                echo '<div>'.$addq.'</div>';
                echo '<div>'.$addb.'</div>';
            echo '</div>';
        // echo '</div>'; // end of edit bill

        // getting the item list
        // echo '<form method="POST" action="../PHP/confirmbill.php" id="confbill">';
            // form data for bill_id
            echo '<input type="hidden" id="bill-id" name="bill_id" value="'.$bill_id.'">';


        echo '<div id="cbill_ilist">';
            // normal bill

        $stmt = $conn->prepare($get_item_detail);
        if(!$stmt)
        {
            die('Error in query! Contact support');
        }
        $stmt->bind_param('i', $bill_id);
        if($stmt->execute())
        {
            $bill_items = $stmt->get_result();
            $bill_total = 0;
            //prints heads of table
            echo '<div class="cbillhead cbill_row">';
            foreach($table_h as $th)
            {
                echo '<div>'.$th.'</div>';
            }
            echo '</div>';
            if($bill_items->num_rows > 0) //print item row
            {
                $i = 0;
                echo '<div id="itemrows">'; // contains all rows
                while($bi_row = $bill_items->fetch_assoc())
                {
                    echo '<div class="cbillrow cbill_row" id="row-'.$i.'">'; // row start
                    $rate = 0;
                    $qty = 0;
                    // $gst = 0;
                    $item_id = $bi_row['item_id'];
                    foreach($bi_row as $key => $value)
                    {
                        if($key == 'item_id')
                        {
                            echo '<div><span>'.$value.'</span><button type="button" class="rmvitembtn" onclick="deleteitem('.$bill_id.', '.$value.', '.$i.')">x</button></div>';
                            continue;
                        }
                        if($key == 'rate')
                        {
                            $rate = $value;
                            echo '<div><input name="itemp_'.$item_id.'" class="price" type="number" value="'.$value.'" step="0.01" oninput="upd_sub_total()"></div>'; // naming is left
                            continue;
                        }
                        if($key == 'item_qty')
                        {
                            $qty = $value;
                            echo '<div><input name="itemq_'.$item_id.'" class="qty" type="number" value="'.$value.'" step="0.001" oninput="upd_sub_total()"></div>'; // naming is left
                            continue;
                        }
                        // if($key == 'GST')
                        // {
                        //     $gst = $value;
                        //     echo '<div><p class="gst">'.$value.'</p></div>';
                        //     continue;
                        // }
                        echo '<div>'.$value.'</div>';
                    }
                    $sub_total = floatval($rate)*floatval($qty);
                    $bill_total = $bill_total + $sub_total;
                    echo '<div class="subttl">'.$sub_total.'</div>'; // recalculate total & subtotal
                    echo '</div>'; // row end
                    $i++;
                }
                echo '</div>'; // all row end
            }
            else
            {
                echo '<div id="itemrows">No items..</div>';
            }
        }
        else
        {
            die('Error in execution: '.$stmt->error);
        }
        echo '</div>'; // close cbill list

        // total discount goes here
        echo '<div class="discount">';
        
            echo '<div><p><i>Delivery Charge:</i></p></div>';
            echo '<div><input id="b_divchrg" name="divchrg" type="number" value="0" oninput="upd_sub_total()"></div>';

            echo '<div><p><i>Discount(in Rs):</i></p></div>';
            echo '<div><input id="b_discnt" name="discount" type="number" value="0" step="0.01" oninput="upd_sub_total()"></div>';

            echo '<div><span><b>Total: ₹</b></span></div>';
            echo '<div><span class="total" id="bill_t">'.$bill_total.'</div>';

            echo '<div><span><b>Cash Recieved: </b></span></div>';
            echo '<div><input type="number" id="b_cr" step="0.01" min="0" oninput="upd_sub_total()"></div>';

            echo '<div id="c2r"><span></span></div>';
        echo '</div>'; // end of div discount

        echo '<input type="submit" value="CONFIRM" name="conf_bill">';
        echo '<input type="submit" value="CONFIRM & PRINT" name="conf_bill_print">';
        echo '</form>';
    echo '</div>';
?>