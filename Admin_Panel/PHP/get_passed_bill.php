<?php
    require('../PHP/database.php');
    //session_start();
    // Paging
    $pg = 1;
    $ofset = 0;
    if(!isset($_GET['setpg']))
    {
        $pg = 1;
    }
    else
    {
        $pg = intval($_GET['setpg']);
    }
    $ofset = ($pg - 1)*20;

    // Searching
    echo '<div class="searchmenu"><form method="POST" action="">
        <div class="sortbox">
        <span>Sort:</span>
        <select name="sortopt" onchange="this.form.submit();">
            <option value="" disabled selected>SORT BY</option>
            <option value="bill_id">Bill ID</option>
            <option value="cust_name">Customer Name</option>
            <option value="order_date_time">Order Date Time</option>
            <option value="ready_date">Ready Date</option>
        </select>
        <input type="reset" value="RESET">
        </div>

        <div class="chkbox">
        <span>Pick UP: </span>
        <input type="checkbox" name="cust_choice1" value="pick_up" onchange="this.form.submit();">
        <span>Home Delivery:</span>
        <input type="checkbox" name="cust_choice2" value="home_d" onchange="this.form.submit();">
        </div>

        <div class="sbar">
        <input type="text" placeholder="Search.." name="searchstr">
        <input type="submit" value="Search" name="search" max="32">
        </div>
        </form></div><br>';
    // Searching

    //basic table headers
    $grid_head = array('Bill ID','Customer Name', 'Customer Phone', 'Total', 'Order Date&Time', ' ');

    //$get_bill_details = 'SELECT bill_id, cust_name, cust_phone, cust_choice, ready_date, ready_time, order_date_time, discount FROM bill_detail WHERE accepted = 1 ORDER BY order_date_time DESC';
    $get_bill_details1 = 'SELECT bill_id, cust_name, cust_phone, cust_choice, DATE_FORMAT(ready_date, "%d-%m-%Y") AS ready_date, DATE_FORMAT(ready_time, "%l:%i %p") AS ready_time, DATE_FORMAT(order_date_time, "%d-%m-%Y, %l:%i %p") AS order_date_time, discount, delivery_charge FROM bill_detail WHERE accepted = 1 AND (cust_name LIKE ? OR cust_phone LIKE ? OR bill_id = ? OR cust_name IS NULL) AND (cust_choice LIKE ? OR cust_choice IS NULL) ORDER BY ';
    $get_bill_details2 = ' LIMIT 20 OFFSET '.$ofset;

    if(!isset($_SESSION['searchstr']))
    {
        $_SESSION['searchstr'] = '%';
        $_SESSION['bid'] = 0;
    }
    if(!isset($_SESSION['sortopt']))
    {
        $_SESSION['sortopt'] = 'bill_detail.order_date_time';
        $_SESSION['orderflow'] = ' DESC ';
    }
    if(!isset($_SESSION['cust_choice']))
    {
        $_SESSION['cust_choice'] = '%';
    }

    if(isset($_POST['search']) || isset($_POST['sortopt']))
    {
        if(isset($_POST['search']))
        {
            $_SESSION['searchstr'] = '%'.trim($_POST['searchstr']).'%';
            try{
                $num = intval(trim($_POST['searchstr']));
                //echo 'Done Sucessfully';
                $_SESSION['bid'] = $num;
            }
            catch(Exception $e){
                $_SESSION['bid'] = 0;
                //echo 'Message: ' .$e->getMessage();
            }
        }
        if(isset($_POST['sortopt']))
        {
            if($_POST['sortopt'] == 'bill_id')
            {
                $_SESSION['sortopt'] = 'bill_id';
                $_SESSION['orderflow'] = ' ASC ';
            }
            else if($_POST['sortopt'] == 'cust_name')
            {
                $_SESSION['sortopt'] = 'cust_name';
                $_SESSION['orderflow'] = ' ASC ';
            }
            else if($_POST['sortopt'] == 'order_date_time')
            {
                $_SESSION['sortopt'] = 'bill_detail.order_date_time';
                $_SESSION['orderflow'] = ' DESC ';
            }
            else if($_POST['sortopt'] == 'ready_date')
            {
                $_SESSION['sortopt'] = 'bill_detail.ready_date';
                $_SESSION['orderflow'] = ' DESC ';
            }
        }
    }
    if(isset($_POST['cust_choice1']) && $_POST['cust_choice1'] === 'pick_up')
    {
        if(isset($_POST['cust_choice2']))
        {
            $_SESSION['cust_choice'] = '%';
        }
        else
        {
            $_SESSION['cust_choice'] = '%pick_up%';
        }
    }
    else if(isset($_POST['cust_choice2']) && $_POST['cust_choice2'] === 'home_d')
    {
        if(isset($_POST['cust_choice1']))
        {
            $_SESSION['cust_choice'] = '%';
        }
        else
        {
            $_SESSION['cust_choice'] = '%home_d%';
        }
    }
    if(!isset($_POST['cust_choice2']) && !isset($_POST['cust_choice1']))
    {
        $_SESSION['cust_choice'] = '%';
    }

    //=========================================

    $getrows = 'SELECT bill_id FROM bill_detail WHERE accepted = 1 AND (cust_name LIKE ? OR cust_phone LIKE ? OR bill_id = ? OR cust_name IS NULL) AND (cust_choice LIKE ?)';
    $stmt = $conn->prepare($getrows);
    if(!$stmt)
    {
        die('Error in qry! (Paging)');
    }
    $stmt->bind_param('ssis', $_SESSION['searchstr'], $_SESSION['searchstr'], $_SESSION['bid'], $_SESSION['cust_choice']);
    if(!$stmt->execute())
    {
        die('Error in exec!');
    }
    // for paging counting total items
    $totalrowobj = $stmt->get_result();
    $totalrow = $totalrowobj->num_rows;
    // ===========================================
    $stmt->reset();

    $mainqry = $get_bill_details1.$_SESSION['sortopt'].$_SESSION['orderflow'].$get_bill_details2;
    $stmt = $conn->prepare($mainqry);
    // echo $mainqry;
    if(!$stmt)
    {
        die('Error in qry! 2'.$conn->error);
    }
    $stmt->bind_param('ssis', $_SESSION['searchstr'], $_SESSION['searchstr'], $_SESSION['bid'], $_SESSION['cust_choice']);
    if(!$stmt->execute())
    {
        die('Error in exec!'.$stmt->error);
    }

    $result = $stmt->get_result();
    $stmt->close();
    //$result = $conn->query($get_bill_details);
    // print_r($_SESSION);
    if($result)
    {
        if($result->num_rows > 0)
        {
            $allrows = $result->fetch_all(MYSQLI_ASSOC);
            echo '<div class="pog">';
                // Grid heading
                echo '<div class="pog-head">';
                    foreach($grid_head as $gh)
                    {
                        echo '<div>'.$gh.'</div>';
                    }
                echo '</div>';
                // data rows
                // getting ready for details
                $details = 'SELECT BIL.item_id AS item_id, ITM.item_name AS item_name, BIL.selling_price AS selling_price, BIL.item_qty AS item_qty, ITM.GST AS GST FROM bill_item_list BIL 
                INNER JOIN items ITM
                ON ITM.item_id = BIL.item_id AND BIL.bill_id = ?';
                $stmt = $conn->prepare($details);
                $total = 0;
                if($stmt)
                {
                    foreach($allrows as $billrow) // data element
                    {
                        $bill_id = $billrow['bill_id'];
                        $total = 0; // calculated
                        $item_list = array();
                        // change
                        $stmt->bind_param('i', $bill_id);
                        if($stmt->execute())
                        {
                            $item_detail = $stmt->get_result();
                            if($item_detail->num_rows > 0)
                            {
                                $i = 0;
                                while($item = $item_detail->fetch_assoc())
                                {
                                    $amount = floatval($item['selling_price'])*floatval($item['item_qty'])*floatval((100+floatval($item['GST']))/100);
                                    $gst = floatval($item['selling_price'])*floatval($item['item_qty'])*floatval($item['GST'])/(100 + floatval($item['GST']));
                                    $gst = $gst/2;
                                    // adding to array item_list
                                    $item_list[$i] = array('item_id'=>$item['item_id'], 'item_name'=>$item['item_name'],
                                                    'selling_price'=>$item['selling_price'], 'item_qty'=>$item['item_qty'],
                                                    'GST'=>$item['GST'], 'SCGST'=>$gst, 'amount'=>$amount);
                                    $total = $total + $amount;
                                    $i++;
                                }
                            }
                            else
                            {
                                echo 'No Item purchased';
                            }
                            //total is total minus discount
                            $total = $total - $billrow['discount'];
                            // New version
                            echo '<div class="data-element">';
                            echo '<div class="pog-head pog-basic">';
                                echo '<div>'.$billrow['bill_id'].'</div>';
                                echo '<div>'.$billrow['cust_name'].'</div>';
                                echo '<div>'.$billrow['cust_phone'].'</div>';
                                echo '<div>'.$total.'</div>'; // Incomplete
                                echo '<div class="odt">'.$billrow['order_date_time'].'</div>';
                                // left to print: ready date/time 
                                $vd_btn = 'bill-d-btn-'.$billrow['bill_id']; // bill detail btn - bill id
                                echo '<div class="view-d" onclick="slideopen(\''.$vd_btn.'\')">View Details</div>';
                            echo '</div>'; // end of basic detail
                            
                                echo '<div class="pog-full " id="'.$vd_btn.'">'; // print item details here
                                    echo '<p class="rdt"><span><b>Ready Date&Time: </b></span>'.$billrow['ready_date'].', '.$billrow['ready_time'].'</p>';
                                    echo '<p class="rdt"><span><b>Discount: </b>₹</span>'.$billrow['discount'].'</p>';
                                    echo '<p class="rdt"><span><b>Delivery Charge: </b>₹</span>'.$billrow['delivery_charge'].'</p>';

                                    // Item details here
                                    echo '<div class="item_list item_lhb">';  //Grid Head Item_ID - Item Name - Price - Qty - Subtotal
                                        // echo '<div class="irowidname">';
                                            echo '<div>Item Id</div>';
                                            echo '<div>Item Name</div>';
                                        // echo '</div>';
                                        // echo '<div class="irowpqta">';
                                            echo '<div>Rate</div>';
                                            echo '<div>Qty</div>';
                                            echo '<div>SGST</div>';
                                            echo '<div>CGST</div>';
                                            echo '<div>Amount</div>';
                                        // echo '</div>';
                                    echo '</div>'; // end of item_list
                                    // Item Rows
                                    foreach($item_list as $item_row)
                                    {
                                        echo '<div class="item_list">';
                                        // echo '<div class="irowidname">';
                                            echo '<div>'.$item_row['item_id'].'</div>';
                                            echo '<div>'.$item_row['item_name'].'</div>';
                                        // echo '</div>';
                                        // echo '<div class="irowpqta">';
                                            echo '<div>'.$item_row['selling_price'].'</div>';
                                            echo '<div>'.$item_row['item_qty'].'</div>';
                                        //echo '</div>';
                                        //echo '<div class="irowpq">';
                                            echo '<div>₹'.number_format($item_row['SCGST'], 2, '.', '').'  @'.($item_row['GST']/2).'%</div>';
                                            echo '<div>₹'.number_format($item_row['SCGST'], 2, '.', '').'  @'.($item_row['GST']/2).'%</div>';
                                        
                                            echo '<div>'.number_format($item_row['amount'], 2, '.', '').'</div>';
                                        // echo '</div>';
                                        echo '</div>'; // end of row
                                    }
                                    //print bill buttons
                                    $emailbtn = '<button class="pbill" onclick="emailbill('.$billrow['bill_id'].')">Email Invoice</button>';
                                    $pbtnA4 = '<button class="pbill" onclick="printbill('.$billrow['bill_id'].', 0)">Invoice(A4)</button>';
                                    $pbtn3inch = '<button class="pbill" onclick="printbill('.$billrow['bill_id'].', 1)">Invoice(3in Roll)</button>';
                                    echo $emailbtn.$pbtnA4.$pbtn3inch;
                                    // Till here
                                echo '</div>'; // end of pog-full details tab
                            echo '</div>'; // data element
                        }
                        else
                        {
                            $stmt->close();
                            $conn->close();
                            echo '<br>Error in execution, try again!</br>';
                        }
                    }
                }
                else
                {
                    $conn->close();
                    die('Error in query! Contact support!');
                }
            echo '</div>'; // end of pog
            $stmt->close();
            $conn->close();
        }
        else
        {
            echo '<div class="pog">';
            // Grid heading
            echo '<div class="pog-head">';
                foreach($grid_head as $gh)
                {
                    echo '<div>'.$gh.'</div>';
                }
            echo '</div>';
                //Print no bills found
                echo '<div class="rdt">';
                    echo '<p>No Bills..</p>';
                echo '</div>';
            echo '</div>';
        }
    }
    else
    {
        $conn->close();
        die("Error in Query : ".$conn->error);
    }
    echo '<div class="prvnxt">';
    echo '<input id="maxrow" type="hidden" name="maxrow" value="'.$totalrow.'">';
    echo '<button id="pbtn" class="prvbtn" onclick="changepage(-1, '.$pg.')">Previous</button>';
    echo '<div class="pg"><span>Page No: </span><span>'.$pg.' of '.ceil(($totalrow/20)).'</span></div>';
    echo '<button id="nbtn" class="nxtbtn" onclick="changepage(1, '.$pg.')">Next</button>';
    echo '</div>';
?>