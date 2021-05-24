<?php
    require_once('../Admin_Panel/PHP/database.php');

    $itemname = '%';
    if(isset($_POST['itemName']))
    {
        $itemname = '%'.strtolower($_POST['itemName']).'%';
    }
    // else
    // {
    //     die('No itemName recieved');
    // }

    $get_item = "SELECT * FROM items WHERE LOWER(item_name) LIKE ?";
    $stmt = $conn->prepare($get_item);
    // $result = $conn->query($get_item);
    if(!$stmt)
    {
        die("Error in Query");
    }
    $stmt->bind_param('s', $itemname);
    if(!$stmt->execute())
    {
        die('Error in Execution');
    }
    $result = $stmt->get_result();
    $item_data = array();
    while($row = $result->fetch_assoc())
    {
        array_push($item_data, $row);
        $rowjson = json_encode($row);
        //echo $rowjson.'<br>';
    }
    //echo '<br><br>';
    $item_data_json = json_encode($item_data);
    echo $item_data_json;
    $stmt->close();
    $conn->close();
?>