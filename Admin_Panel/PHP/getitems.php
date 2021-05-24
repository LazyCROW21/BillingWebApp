<?php

    require('../PHP/database.php');

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

    $get_item_details1 = 'SELECT item_id, item_name, item_price, GST, item_stock FROM items WHERE item_name LIKE ? AND deleted = 0 ORDER BY ';
    $get_item_details2 = ' LIMIT 20 OFFSET '.$ofset;
    
    if(!isset($_SESSION['searchstrg']))
    {
        $_SESSION['searchstrg'] = '%';
    }
    //$searchstrg = '%';
    if(!isset($_SESSION['sortcol']))
    {
        $_SESSION['sortcol'] = 'item_id';
    }
    //$sortcol = 'item_id';
    if(!isset($_SESSION['orderflow']))
    {
        $_SESSION['orderflow'] = ' ASC ';
    }
    //$orderflow = ' ASC ';
    if(isset($_POST['search']) || isset($_POST['sortopt']))
    {

        if(isset($_POST['searchstr']))
        {
            // $searchstrg = '%'.trim($_POST['searchstr']).'%';
            $_SESSION['searchstrg'] = '%'.trim($_POST['searchstr']).'%';
        }

        if(isset($_POST['sortopt']))
        {
            if($_POST['sortopt'] == 'item_id')
            {
                // $sortcol = 'item_id';
                // $orderflow = ' ASC ';
                $_SESSION['sortcol'] = 'item_id';
                $_SESSION['orderflow'] = ' ASC ';
            }
            else if($_POST['sortopt'] == 'item_name')
            {
                // $sortcol = 'item_name';
                // $orderflow = ' ASC ';
                $_SESSION['sortcol'] = 'item_name';
                $_SESSION['orderflow'] = ' ASC ';
            }
            else if($_POST['sortopt'] == 'item_pricei')
            {                
                // $sortcol = 'item_price';
                // $orderflow = ' ASC ';
                $_SESSION['sortcol'] = 'item_price';
                $_SESSION['orderflow'] = ' ASC ';
            }
            else if($_POST['sortopt'] == 'item_priced')
            {                
                // $sortcol = 'item_price';
                // $orderflow = ' DESC ';
                $_SESSION['sortcol'] = 'item_price';
                $_SESSION['orderflow'] = ' DESC ';
            }
            else if($_POST['sortopt'] == 'GST')
            {                
                // $sortcol = 'GST';
                // $orderflow = ' ASC ';
                $_SESSION['sortcol'] = 'GST';
                $_SESSION['orderflow'] = ' ASC ';
            }
            else if($_POST['sortopt'] == 'item_stocki')
            {                
                // $sortcol = 'item_stock';
                // $orderflow = ' ASC ';
                $_SESSION['sortcol'] = 'item_stock';
                $_SESSION['orderflow'] = ' ASC ';
            }
            else if($_POST['sortopt'] == 'item_stockd')
            {                
                // $sortcol = 'item_stock';
                // $orderflow = ' DESC ';
                $_SESSION['sortcol'] = 'item_stock';
                $_SESSION['orderflow'] = ' DESC ';
            }
        }
    }

    $get_item_details = $get_item_details1.$_SESSION['sortcol'].$_SESSION['orderflow'].$get_item_details2;
    //$get_item_details = 'SELECT item_id, item_name, item_price, GST, item_stock FROM items WHERE deleted = 0 ORDER BY item_name ASC LIMIT 10 OFFSET '.$ofset;

    $get_total_items = 'SELECT item_id, item_name, item_price, GST, item_stock FROM items WHERE item_name LIKE ? AND deleted = 0';
    //$get_total_items = 'SELECT item_id FROM items WHERE deleted = 0';
    $stmt = $conn->prepare($get_total_items);
    if(!$stmt)
    {
        die('Error in Qry(search)');
    }
    $stmt->bind_param('s', $_SESSION['searchstrg']);
    if(!$stmt->execute())
    {
        die('Error in  Exec(search)');
    }
    $totalrowobj = $stmt->get_result();
    $totalrow = $totalrowobj->num_rows;
    $stmt->reset();

    $stmt = $conn->prepare($get_item_details);
    //echo $get_item_details;
    if(!$stmt)
    {
        die('Error in Query!');
    }
    $stmt->bind_param('s', $_SESSION['searchstrg']);
    if(!$stmt->execute())
    {
        die('Error in Exec!');
    }

    $result = $stmt->get_result();
    //print_r($result);
    $stmt->close();

    // searching
    echo '<div class="searchmenu"><form method="POST" action="">
            <select name="sortopt" onchange="this.form.submit();">
                <option value="item_id" selected>Item ID</option>
                <option value="item_name">Item Name</option>
                <option value="item_pricei">Price(increasing)</option>
                <option value="item_priced">Price(decreasing)</option>
                <option value="GST">GST</option>
                <option value="item_stocki">Stock(increasing)</option>
                <option value="item_stockd">Stock(decreasing)</option>
            </select>
            <span>Sort By:</span>
            <input type="submit" value="Search" name="search" max="32">
            <input type="text" placeholder="Search.." name="searchstr">
        </form></div>';
    // searching

    if($result)
    {
        $table_head = array('Item ID','Item Name', 'Price', 'GST', 'Item Stock');
        if($result->num_rows > 0)
        {
            $allrows = $result->fetch_all(MYSQLI_ASSOC);
            echo '<table>';
            echo '<thead>';
                foreach($table_head as $th)
                {
                    echo '<th>'.$th.'</th>';
                }
            echo '</thead>';
            echo '<tbody>';
                foreach($allrows as $row)
                {
                    echo '<tr>';
                    $item_id = $row['item_id'];
                    foreach($row as $k=>$v)
                    {
                        if($k == 'item_stock')
                        {
                            echo '<td>'.$v.'<button class="updbtn" onclick="edititem(\''.$item_id.'\')"></button></td>';
                            continue;
                        }
                        echo '<td>'.$v.'</td>';
                    }
                    echo '</tr>';
                }
            echo '</tbody>';
            echo '</table>';
            echo '<div class="prvnxt">';
                //echo '<form method="GET" action="">';
                echo '<input id="maxrow" type="hidden" name="maxrow" value="'.$totalrow.'">';
                echo '<button id="pbtn" class="prvbtn" onclick="changepage(-1, '.$pg.')">Previous</button>';
                echo '<div class="pg"><span>Page No: </span><span>'.$pg.' of '.ceil(($totalrow/20)).'</span></div>';
                echo '<button id="nbtn" class="nxtbtn" onclick="changepage(1, '.$pg.')">Next</button>';
                //echo '</form>';
            echo '</div>';
        }
        else
        {
            echo '<table>';
                echo '<thead>';
                    foreach($table_head as $th)
                    {
                        echo '<th>'.$th.'</th>';
                    }
                echo '<tbody>';
                    echo '<tr><td colspan="5">No Items..</td></tr>';
                echo '</tbody>';
                echo '</thead>';
            echo '</table>';
        }
    }
    else
    {
        die("Error in Query : ".$conn->error);
    }
    $conn->close();

?>