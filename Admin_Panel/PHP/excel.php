<?php
    require_once('../PHP/database.php');
    $output = '';
    
    $sel_qry = '';
    $sel_th = array();
    $sel_col = array();  
    $sel_filename = '';

    //For Items
    $item_th = array('Item ID', 'Name', 'Rate', 'GST', 'Stock');
    $item_qry = 'SELECT item_id, item_name, Item_price, GST, item_stock FROM items WHERE deleted = 0 ORDER BY item_name ASC';
    $item_col = array('item_id', 'item_name', 'Item_price', 'GST', 'item_stock');

    //For Customers
    $cust_th = array('Customer ID', 'Name', 'Phone No', 'Email', 'GST Number', 'Address');
    $cust_qry = 'SELECT customer_id, customer_name, customer_phone, customer_email, customer_gst, customer_addr FROM customers ORDER BY customer_name ASC';
    $cust_col = array('customer_id', 'customer_name', 'customer_phone', 'customer_email', 'customer_gst', 'customer_addr');

    //For Bills
    $bill_th = array('Invoice No', 'Customer Name', 'Customer Phone', 'Delivery Choice', 'Order Date&Time', 'Amount', 'SGST', 'CGST', 'Discount', 'Delivery Charge', 'Total');
    $bill_qry = 'SELECT bd.bill_id AS bid, bd.cust_name AS custName, bd.cust_phone AS custPhone, bd.cust_choice AS custChoice, DATE_FORMAT(bd.order_date_time, "%d-%m-%Y, %l:%i %p") AS OrderDateTime, SUM(i_detail.RATE * i_detail.qty) AS Amount, SUM(i_detail.RATE * i_detail.qty * i_detail.itm_gst/200) AS SGST, SUM(i_detail.RATE * i_detail.qty * i_detail.itm_gst/200) AS CGST, bd.discount AS Discnt, bd.delivery_charge AS divchrg, (SUM(i_detail.RATE * i_detail.qty * (100 + i_detail.itm_gst)/100)) - bd.discount + bd.delivery_charge AS Total FROM bill_detail bd INNER JOIN (SELECT bil.bill_id AS b_id, bil.item_id AS i_id, bil.selling_price AS RATE, bil.item_qty AS qty, itm.gst AS itm_gst FROM bill_item_list bil INNER JOIN items itm ON bil.item_id = itm.item_id) i_detail ON bd.bill_id = i_detail.b_id AND bd.accepted = 1 GROUP BY i_detail.b_id ORDER BY bd.order_date_time DESC';
    $bill_col = array('bid', 'custName', 'custPhone', 'custChoice', 'OrderDateTime', 'Amount', 'SGST', 'CGST', 'Discnt', 'divchrg', 'Total');

// SELECT bd.bill_id AS bid, bd.cust_name AS custName, bd.cust_phone AS custPhone, bd.cust_choice AS custChoice, bd.order_date_time AS OrderDateTime,SUM(i_detail.RATE * i_detail.qty) AS Amount, SUM(i_detail.RATE * i_detail.qty)*(i_detail.itm_gst/200) AS SGST, SUM(i_detail.RATE * i_detail.qty)*(i_detail.itm_gst/200) AS CGST, bd.discount AS Discnt, SUM(i_detail.RATE * i_detail.qty)*(100 + i_detail.itm_gst)/100 AS Total FROM bill_detail bd INNER JOIN (SELECT bil.bill_id AS b_id, bil.item_id AS i_id, bil.selling_price AS RATE, bil.item_qty AS qty, itm.gst AS itm_gst FROM bill_item_list bil INNER JOIN items itm ON bil.item_id = itm.item_id) i_detail ON bd.bill_id = i_detail.b_id GROUP BY i_detail.b_id

    if(isset($_POST['exportbtn']))
    {
        if($_POST['datafield'] === 'itemtable')
        {
            $sel_qry = $item_qry;
            $sel_th = $item_th;
            $sel_col = $item_col;
            $sel_filename = 'items';
        }
        elseif($_POST['datafield'] === 'customertable')
        {
            $sel_qry = $cust_qry;
            $sel_th = $cust_th;
            $sel_col = $cust_col;
            $sel_filename = 'customers';
        }
        elseif($_POST['datafield'] === 'bill')
        {
            $sel_qry = $bill_qry;
            $sel_th = $bill_th;
            $sel_col = $bill_col;
            $sel_filename = 'bills';
        }

        $result = $conn->query($sel_qry);
        if(!$result)
        {
            die('Error in Query !');
        }
        if($result->num_rows > 0)
        {
            $output .= '<table border="1"><tr>';
            foreach($sel_th as $th)
            {
                $output .= '<th>'.$th.'</th>';
            }

            $output .= '</tr>';
            while($row = $result->fetch_assoc())
            {
                $output .= '<tr>';
                foreach($sel_col as $col)
                {
                    $output .= '<td>'.$row[$col].'</td>';
                }
                $output .= '</tr>';
            }
            $output .= '</table>';
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$sel_filename.".xls");
            echo $output;
        }
    }

?>