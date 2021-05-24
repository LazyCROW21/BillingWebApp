<?php
    if(isset($_POST['getitemprice']))
    {
        require_once('../PHP/database.php');
        if(!isset($_POST['itemid']))
        {
            die('Error');
        }
        $stmt = $conn->prepare('SELECT item_price, item_stock FROM items WHERE item_id = ?');
        if(!$stmt)
        {
            die('Error');
        }
        $stmt->bind_param('i', intval($_POST['itemid']));
        if($stmt->execute())
        {
            $result = $stmt->get_result();
            $price = $result->fetch_assoc();
            // $itemprice = $price['item_price'];
            $itemdet = array('price' => $price['item_price'], 'stock' => $price['item_stock']);
            exit(json_encode($itemdet));
        }
        else
        {
            die('Error');
        }
    }
    if(isset($_POST['bill_id']) && isset($_POST['item_id']))
    {
        require_once('../PHP/database.php');

        $qry = 'INSERT INTO bill_item_list (bill_id, item_id) VALUES (?, ?)';
        $stmt = $conn->prepare($qry);
        if(!$stmt)
        {
            die('Error in query! contact support');
        }
        $bid = $_POST['bill_id'];
        $itemid = $_POST['item_id'];
        $stmt->bind_param('ii', $bid, $itemid);
        if(!$stmt->execute())
        {
            $stmt->close();
            $conn->close();
            die('Error in execution! try again :'.$stmt->error);
        }
        $stmt = $conn->prepare('SELECT GST FROM items WHERE item_id = ?');
        $stmt->bind_param('i', $itemid);
        $stmt->execute();
        $result = $stmt->get_result();
        $gst = $result->fetch_assoc();
        $gstrate = $gst['GST'];
        //echo "$gstrate";
        $stmt->close();
        $conn->close();
        exit($gstrate);
    }
    else
    {
        die('No Information recieved');
    }
?>