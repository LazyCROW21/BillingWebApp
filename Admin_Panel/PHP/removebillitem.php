<?php
    if(isset($_POST['bill_id']) && isset($_POST['item_id']))
    {
        require('../PHP/database.php');

        $qry = 'DELETE FROM bill_item_list WHERE bill_id = ? AND item_id = ?';
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
        $stmt->close();
        $conn->close();
        exit('1');
    }
    else
    {
        die('No Information Recieved');
    }
?>