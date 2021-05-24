<?php
if(isset($_POST['bill_id']))
{
    require('../PHP/database.php');

    //turn off auto commit
    $conn -> autocommit(FALSE);

    $bill_id = $_POST['bill_id'];
    $del_bill_detail = 'DELETE FROM bill_detail WHERE accepted = 0 AND bill_id = ?';
    $del_bill_item = 'DELETE FROM bill_item_list WHERE bill_id = ?';

    $stmt = $conn->prepare($del_bill_detail);
    if($stmt)
    {
        $stmt->bind_param('i', $bill_id);
        if($stmt->execute())
        {
            $stmt->prepare($del_bill_item);
            if($stmt)
            {
                $stmt->bind_param('i', $bill_id);
                if($stmt->execute())
                {
                    if($conn->commit())
                    {
                        echo 1;
                        $stmt->close();
                        $conn->close();
                    }
                    else
                    {
                        echo 0;
                        $stmt->close();
                        $conn->close();
                    }
                    
                }
                else
                {
                    die('Error in execution! contact support: '.$stmt->error);
                    $stmt->close();
                    $conn->close();
                }
            }
            else
            {
                die('Error in query! contact support: '.$stmt->error);
                $stmt->close();
                $conn->close();
            }
        }
        else
        {
            die('Error in execution! contact support: '.$stmt->error);
            $stmt->close();
            $conn->close();
        }
    }
    else
    {
        die('Error in query! contact support: '.$stmt->error);
        $stmt->close();
        $conn->close();
    }
}
?>