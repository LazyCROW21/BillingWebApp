<?php
    require_once('../../fpdf182/fpdf.php');
    require('../PHP/database.php');

    if(!isset($_GET['bill_id']))
    {
        die('No Bill number Entered!');
    }

    $get_bill_details = 'SELECT BD.bill_id AS bill_id, BD.cust_name AS cust_name, BD.cust_phone AS cust_phone, BD.cust_choice AS cust_choice, DATE_FORMAT(BD.ready_date, "%d-%m-%Y") AS ready_date, DATE_FORMAT(BD.ready_time, "%l:%i %p") AS ready_time, DATE_FORMAT(BD.order_date_time, "%d-%m-%Y, %l:%i %p") AS order_date_time, BD.discount AS discount, BD.delivery_charge AS delivery_charge, CT.customer_gst AS cust_gst FROM bill_detail BD
    LEFT JOIN customers CT 
    ON BD.cust_phone = CT.customer_phone 
    WHERE accepted = 1 AND bill_id = ?';
    $stmt = $conn->prepare($get_bill_details);
    if(!$stmt)
    {
        die('Error in query! contact support'.$conn->error);
        $conn->close();
    }

// SHOP DETAILS
    $shop_name = 'Khodiyar Dairy Parlor';
    //Khodiyar Dairy Parlar,Chanasma, Bus Stand Road, Main Bazaar, near Hanuman Temple, Ambedkar Nagar, Chanasma, Gujarat 384220Khodiyar Dairy Parlar,Chanasma, Bus Stand Road, Main Bazaar, near Hanuman Temple, Ambedkar Nagar, Chanasma, Gujarat 384220
    //$shop_addr1 = 'Bus Stand Road, Main Bazaar,';
    $shop_addr2 = 'Near Hanuman Temple, Tower Chok,';
    $shop_addr3 = ' Chanasma-384220, Patan, Gujarat.';
    $shop_contact = '931-614-0430';
    $shop_GST = '24AUDPP8933E1ZK';

// GETTING BILL DETAILS
    $bill_id = $_GET['bill_id'];
    $stmt->bind_param('i', $bill_id);
    if(!$stmt->execute())
    {
        $stmt->close();
        $conn->close();
        die('Error in execution! try again: '.$stmt->error);
    }

    $bd = $stmt->get_result();
    $bill_d = $bd->fetch_assoc();
    $customer = $bill_d['cust_name'];
    $phone = $bill_d['cust_phone'];
    $custgst = $bill_d['cust_gst'];
    $choice = $bill_d['cust_choice'];
    $exp_date = $bill_d['ready_date'];
    $exp_time = $bill_d['ready_time'];
    $order_datetime = $bill_d['order_date_time'];
    $discnt = $bill_d['discount'];
    $divchrg = $bill_d['delivery_charge'];

// get item list
    $getitem = 'SELECT BIL.item_id AS item_id, ITM.item_name AS item_name, ITM.item_price AS base_price, ITM.GST AS GST, BIL.item_qty AS item_qty FROM bill_item_list BIL 
    INNER JOIN items ITM
    ON ITM.item_id = BIL.item_id AND BIL.bill_id = ?';
    $stmt = $conn->prepare($getitem);
    if(!$stmt)
    {
        $conn->close();
        die('Error in query! contact support');
    }
    $stmt->bind_param('i', $bill_id);
    if(!$stmt->execute())
    {
        $stmt->close();
        $conn->close();
        die('Error in execution! try again: '.$stmt->error);
    }
    $itemlist = $stmt->get_result();

    $stmt->close();
    $conn->close();

//start building pdf

    if($_GET['size'] === 'A4')
    {
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetTitle('Cash Bill');
        $pdf->AddPage();
        $pdf->AddFont('OCR-A', '', 'OCR_A.php');

        // Heading
        $pdf->SetFont('OCR-A','',28);
        $pdf->Cell(190, 12, $shop_name, 'LTR', 1, 'C');
        $pdf->SetFont('OCR-A','', 12);
        //$pdf->Cell(190, 7, $shop_addr1, 'LR', 1, 'C');
        $pdf->Cell(190, 7, $shop_addr2, 'LR', 1, 'C');
        $pdf->Cell(190, 7, $shop_addr3, 'LR', 1, 'C');
        //$pdf->Cell(190, 8, '', 'LR', 1, 'C');
        $pdf->Cell(190, 7, 'PHONE NO: '.$shop_contact, 'LR', 1, 'C');
        $pdf->Cell(190, 7, 'GST TIN: '.$shop_GST, 'LBR', 1, 'C');

        //Invoice Details
        $pdf->Cell(190, 8, '------- TAX INVOICE -------', 'LTR', 1, 'C');
        $pdf->SetX(-75);
        $pdf->Cell(65, 6, 'Invoice No: '.$bill_id, 'R', 1, 'R');
        if($customer != NULL)
        {
            $pdf->SetX(-110);
            $pdf->Cell(100, 6, 'Customer Name: '.$customer, 'R', 1, 'R');
            $pdf->SetY(($pdf->GetY())-12);
            $pdf->SetX(10);
            $pdf->Cell(125, 6, 'Customer Phone: '.$phone, 'L', 1, 'L');
            if($custgst != NULL)
            {
                $pdf->Cell(190, 6, 'Customer GST: '.$custgst, 'LR', 1, 'L');
            }
            $pdf->Cell(190, 6, 'Choice: '.$choice, 'LR', 1, 'L');
            $pdf->Cell(190, 6, 'Expt Date: '.$exp_date, 'LR', 1, 'R');
            $pdf->Cell(190, 6, 'Expt Time: '.$exp_time, 'LR', 1, 'R');
            $pdf->SetY(($pdf->GetY())-12);
        }
        else
        {
            $pdf->SetY(($pdf->GetY())-6);
        }
        $pdf->Cell(100, 6, 'Order Date & Time: '.$order_datetime, 'L', 1, 'L');
        // $pdf->Ln(6);
        $pdf->Cell(190, 6, '', 'LBR', 1, 'C');

        //Item list
        $pdf->SetFont('Arial','B', 10);
        // item id, name, price, qty, amount
        $head = array('S.No.', 'Item Name', 'Qty', 'Rate', 'Taxable', 'SGST', 'CGST', 'Amount');
        $sno = 1;
        $hsize = array(10, 62, 18, 18.75, 18.75, 18.75, 18.75, 25);
        //190
        $i = 0;
        $pdf->Ln(5);
        foreach($head as $h)
        {
            $pdf->Cell($hsize[$i], 5, $h, 'LTR', 0, 'C');
            $i++;
        }
        $pdf->Ln(5);
        $pdf->Cell(10, 5, '', 'LB', 0, 'C');
        $pdf->Cell(62, 5, '', 'LB', 0, 'C');
        $pdf->Cell(18, 5, '', 'LB', 0, 'C');
        $pdf->Cell(18.75, 5, '', 'LB', 0, 'C');
        $pdf->Cell(18.75, 5, 'Value', 'LB', 0, 'C');
        $pdf->Cell(18.75, 5, '', 'LB', 0, 'C');
        $pdf->Cell(18.75, 5, '', 'LB', 0, 'C');
        $pdf->Cell(25, 5, '', 'LBR', 0, 'C');

        $rawtotal = 0.0;
        $rawtax = 0.0;
        $total = 0.0;
        $subtotal = 0.0;
        $numr = $itemlist->num_rows;
        $row_count = 1;
        $page_count = 1;
        $max_page = 1;
        if($customer != NULL)
        {
            if($numr > 7)
            {
                $max_page = ceil(($numr - 7)/14) + 1;
            }
            else
            {
                $max_page = 1;
            }
        }
        else
        {
            if($numr > 10)
            {
                $max_page = ceil(($numr - 10)/14) + 1;
            }
            else
            {
                $max_page = 1;
            }
        }
        //page 1 = 7 items
        //page > 1 = 14 items
        //page without customer detail:
        //page 1 = 11 items
        //page > 1 = 14 itemsw
        $maxrowperpage = 8;
        if($customer == NULL)
        {
            $maxrowperpage = 10;
        }
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(-1);
        while($row = $itemlist->fetch_assoc())
        {
            if($row_count > $maxrowperpage)
            {
                if($page_count == 1)
                {
                    $maxrowperpage = 14;
                }
                //15+65+22+22 = 80+44 =124
                $pdf->Ln(7);
                $pdf->Cell(124, 7, 'Page '.$page_count.' of '.$max_page, 'LTB', 0, 'L');
                $pdf->Cell(40, 7, 'Subtotal:', 'LTB', 0, 'R');
                $pdf->Cell(26, 7, number_format($subtotal, 2, '.', ''), 'TRB', 0, 'R');
                $subtotal = 0.0;
                $row_count = 0;
                //$maxrowperpage = 26;
                $page_count++;
                $pdf->AddPage();
                $pdf->Cell(190, 7, 'Invoice No: '.$bill_id, 'LTRB', 0, 'L');
                //continue;
            }

            $p = floatval($row['base_price'] * (100-$row['GST']) / (100+$row['GST']));
            $p = round($p, 2);
            $q = floatval($row['item_qty']);
            $q = round($q, 3);
            $g = floatval($row['GST']/2);
            $tv = floatval($p * $q);
            $scgst = floatval($row['base_price'] * $q * ($row['GST']/($row['GST']+100)) / 2);
            $amount = floatval($row['base_price'] * $q);
            $amount = round($amount, 2);
            $rawtotal += $tv;
            $rawtax += (2 * $scgst);
            $subtotal += $amount;
            $total += $amount;
            $row_count++;
            
            // 10, 62, 18, 18.75, 18.75, 18.75, 18.75, 25

            $pdf->Ln(6);
            $pdf->Cell(10, 6, $sno, 'L', 0, 'C');
            $pdf->Cell(62, 6, $row['item_name'], 'L',0, 'L');
            $pdf->Cell(18, 6, number_format($q, 3, '.', ''), 'L', 0, 'C');
            $pdf->Cell(18.75, 6, number_format($row['base_price'], 2, '.', ''), 'L', 0, 'R');
            //taxable amount
            $pdf->Cell(18.75, 6, number_format($tv, 2, '.', ''), 'L', 0, 'R');
            
            $pdf->Cell(18.75, 6, number_format($scgst, 2, '.', ''), 'L', 0, 'R');
            $pdf->Cell(18.75, 6, number_format($scgst, 2, '.', ''), 'L', 0, 'R');
            $pdf->Cell(25, 6, number_format($amount, 2, '.', ''), 'LR', 0, 'R');
            $sno++;
            $pdf->Ln(6); // print gst %
            $pdf->Cell(10, 6, '', 'L', 0, 'L');
            $pdf->Cell(62, 6, '', 'L', 0, 'L');
            $pdf->Cell(18, 6, '', 'L', 0, 'C');
            $pdf->Cell(18.75, 6, '', 'L', 0, 'R');
            $pdf->Cell(18.75, 6, '', 'L', 0, 'R');
            $pdf->Cell(18.75, 6, '@'.$g.'%', 'L', 0, 'R');
            $pdf->Cell(18.75, 6, '@'.$g.'%', 'L', 0, 'R');
            $pdf->Cell(25, 6, '', 'LR', 0, 'R');
            //number_format($number, 2, '.', '');
        }

        // fill the empty form
        $j = $maxrowperpage - $row_count;
    
        $x = 14*($j + 1);
        
        // 10, 62, 18, 18.75, 18.75, 18.75, 18.75, 25

        $pdf->Ln(6);
        $pdf->Cell(10, $x, '', 'LB', 0, 'L');
        $pdf->Cell(62, $x, '', 'LB', 0, 'L');
        $pdf->Cell(18, $x, '', 'LB', 0, 'C');
        $pdf->Cell(18.75, $x, '', 'LB', 0, 'R');
        $pdf->Cell(18.75, $x, '', 'LB', 0, 'R');
        $pdf->Cell(18.75, $x, '', 'LB', 0, 'R');
        $pdf->Cell(18.75, $x, '', 'LB', 0, 'R');
        $pdf->Cell(25, $x, '', 'LBR', 0, 'R');

        $pdf->Ln($x);
        // $pdf->Cell(10, 1, '', 'LB', 0, 'L');
        // $pdf->Cell(62, 1, '', 'LB', 0, 'L');
        // $pdf->Cell(18, 1, '', 'LB', 0, 'C');
        // $pdf->Cell(18.75, 1, '', 'LB', 0, 'R');
        // $pdf->Cell(18.75, 1, '', 'LB', 0, 'R');
        // $pdf->Cell(18.75, 1, '', 'LB', 0, 'R');
        // $pdf->Cell(18.75, 1, '', 'LB', 0, 'R');
        // $pdf->Cell(25, 1, '', 'LBR', 0, 'R');
        
        // last sub total
        $pdf->Ln(0);
        $pdf->Cell(127.25, 6, 'Page '.$page_count.' of '.$max_page, 'L', 0, 'L');
        $pdf->Cell(37.5, 6, 'Subtotal:', 'LB', 0, 'R');
        $pdf->Cell(25.25, 6, number_format($subtotal, 2, '.', ''), 'RB', 0, 'R');

        //Total Raw Amount
        $pdf->Ln(6);
        $pdf->Cell(127.25, 6, '', 'L', 0, 'R');
        $pdf->Cell(37.5, 6, 'Total Before Tax:', 'L', 0, 'R');
        $pdf->Cell(25.25, 6, number_format($rawtotal, 2, '.', ''), 'R', 0, 'R');

        //Total CGST Added
        $pdf->Ln(6);
        $pdf->Cell(127.25, 6, '', 'L', 0, 'R');
        $pdf->Cell(37.5, 6, 'Total CGST:', 'L', 0, 'R');
        $pdf->Cell(25.25, 6, number_format(($rawtax/2), 2, '.', ''), 'R', 0, 'R');

        //Total SGST Added
        $pdf->Ln(6);
        $pdf->Cell(127.25, 6, '', 'L', 0, 'R');
        $pdf->Cell(37.5, 6, 'Total SGST:', 'L', 0, 'R');
        $pdf->Cell(25.25, 6, number_format(($rawtax/2), 2, '.', ''), 'R', 0, 'R');

        //Total GST Added
        // $pdf->Ln(6);
        // $pdf->Cell(127.25, 6, '', 'L', 0, 'R');
        // $pdf->Cell(37.5, 6, 'Total Tax:', 'L', 0, 'R');
        // $pdf->Cell(25.25, 6, number_format($rawtax, 2, '.', ''), 'R', 0, 'R');

        // print delivery charge
        $pdf->Ln(6);
        $pdf->Cell(127.25, 6, '', 'L', 0, 'R');
        $pdf->Cell(37.5, 6, 'Delivery Charge:', 'L', 0, 'R');
        $pdf->Cell(25.25, 6, number_format($divchrg, 2, '.', ''), 'R', 0, 'R');

        // print discount
        $pdf->Ln(6);
        $pdf->Cell(127.25, 6, '', 'L', 0, 'R');
        $pdf->Cell(37.5, 6, 'Discount:', 'L', 0, 'R');
        $pdf->Cell(25.25, 6, number_format($discnt, 2, '.', ''), 'R', 0, 'R');

        // prints total
        $total -= $discnt;
        $total += $divchrg;
        $pdf->Ln(6);
        $pdf->Cell(127.25, 6, '', 'LB', 0, 'R');
        $pdf->Cell(37.5, 6, 'Total:', 'LB', 0, 'R');
        $pdf->Cell(25.25, 6, number_format($total, 2, '.', ''), 'BR', 1, 'R');
        //number_format($number, 2, '.', '');
        
        //footer
        $pdf->SetY(-27);     
        $pdf->Cell(190, 6, 'Thank you for your shopping!', 0, 1, 'C');

        //$pdf->Cell(190, 7, 'T&C goes here, whatever...', 0, 1, 'R');
        //output
        $pdf->Output();
    }
    else if($_GET['size'] === '3inch') // 3inch roll
    {
        $numr = $itemlist->num_rows; // to decide the height of pdf
        $roll_h = ((2*$numr) + 30)*0.175;
        $pdf = new FPDF('P', 'in', array(3, $roll_h));
        $pdf->SetMargins(0.125, 0.25);
        $pdf->SetTitle('Cash Bill');
        $pdf->SetAutoPageBreak(false, 0.25);
        $pdf->AddPage();
        $pdf->AddFont('OCR-A', '', 'OCR_A.php');

        $pdf->SetFont('OCR-A','',12);
        // Heading
        $pdf->Cell(2.75, 0.2, $shop_name, 0, 1, 'C');
        $pdf->SetFont('OCR-A','', 9);
        //$pdf->Cell(2.75, 0.175, $shop_addr1, 0, 1, 'C');
        $pdf->Cell(2.75, 0.175, $shop_addr2, 0, 1, 'C');
        $pdf->Cell(2.75, 0.175, $shop_addr3, 0, 1, 'C');
        $pdf->Cell(2.75, 0.175, 'PHONE NO: '.$shop_contact, 0, 1, 'C');
        $pdf->Cell(2.75, 0.175, 'GST TIN: '.$shop_GST, 'B', 1, 'C');

        //Invoice Details
        $pdf->Cell(2.75, 0.175, '----- TAX INVOICE -----', 0, 1, 'C');
        $pdf->Cell(2.75, 0.175, 'Invoice No: '.$bill_id, 0, 1, 'L');
        if($customer != NULL)
        {
            $pdf->Cell(2.75, 0.175, 'Customer Name: '.$customer, 0, 1, 'L');
            $pdf->Cell(2.75, 0.175, 'Customer Phone: '.$phone, 0, 1, 'L');
            if($custgst != NULL)
            {
                $pdf->Cell(190, 7, 'Customer GST: '.$custgst, 'LR', 1, 'L');
            }
            $pdf->Cell(2.75, 0.175, 'Choice: '.$choice, 0, 1, 'L');
            $pdf->Cell(2.75, 0.175, 'Expt Date: '.$exp_date, 0, 1, 'L');
            $pdf->Cell(2.75, 0.175, 'Expt Time: '.$exp_time, 0, 1, 'L');    
        }
        $pdf->Cell(2.75, 0.175, 'Order Date & Time: '.$order_datetime, 'B', 1, 'L');
        
        //Item list
        //$pdf->Ln(0.275);
        $pdf->Cell(2.75, 0.175, '#S. no. - Item Name: ', 0, 1, 'L');
        $pdf->Cell(0.6875, 0.175, 'Qty', 'B', 0, 'L');
        $pdf->Cell(0.6875, 0.175, 'Rate', 'B', 0, 'L');
        $pdf->Cell(0.6875, 0.175, 'GST', 'B', 0, 'L');
        $pdf->Cell(0.6875, 0.175, 'Amount', 'B', 1, 'L');

        $sno = 1;
        $total = 0.0;
        $gstsum = array();
        while($row = $itemlist->fetch_assoc())
        {
            $p = floatval($row['base_price'] * $row['GST'] / (100 + $row['GST']));
            $p = round($p, 2);
            $q = floatval($row['item_qty']);
            $q = round($q, 3);
            $g = floatval($row['GST']);
            $rawamount = floatval($p * $q);
            $amount = floatval($row['base_price'] * $q);
            $amount = round($amount, 2);
            $total += $amount;
            $pdf->MultiCell(2.75, 0.175, '#'.$sno.' - '.$row['item_name'], 0, 'L');
            $sno++;

            $pdf->Cell(0.6875, 0.175, number_format($q, 3, '.', ''), 0, 0, 'L');
            $pdf->Cell(0.6875, 0.175, number_format(round($row['base_price'], 2), 2, '.', ''), 0, 0, 'L');
            $pdf->Cell(0.6875, 0.175, number_format($g, 2, '.', '').'%', 0, 0, 'L');
            $pdf->Cell(0.6875, 0.175, number_format($amount, 2, '.', ''), 0, 1, 'R');
            $pdf->Ln(0.125);
            if(isset($gstsum["$g"]))
            {
                $gstsum["$g"] += $rawamount;
            }
            else
            {
                $gstsum["$g"] = $rawamount;
            }
            //number_format($number, 2, '.', '');
        }

        // print delivery charge
        $pdf->Ln(0.125);
        $pdf->Cell(2.75, 0.25, 'Delivery Charge: '.number_format($divchrg, 2, '.', ''), 'T', 1, 'R');

        // print discount
        //$pdf->Ln(0.125);
        $pdf->Cell(2.75, 0.25, 'Discount: '.number_format($discnt, 2, '.', ''), 'T', 1, 'R');

        // prints total
        $total -= $discnt;
        $total += $divchrg;
        //$pdf->Ln(0.125);
        $pdf->Cell(2.75, 0.25, 'Total: '.number_format($total, 2, '.', ''), 'T', 1, 'R');
        
        // GST Summary
        //$pdf->Ln(0.25);
        $pdf->SetFont('OCR-A','', 7);
        $pdf->Cell(2.75, 0.25, '----- GST Summary -----', 'TB', 1, 'C');
        $pdf->Cell(0.55, 0.25, 'SGST', 'LBR', 0, 'C');
        $pdf->Cell(0.55, 0.25, 'SGST AMT', 'LBR', 0, 'C');
        $pdf->Cell(0.55, 0.25, 'CGST', 'LBR', 0, 'C');
        $pdf->Cell(0.55, 0.25, 'CGST AMT', 'LBR', 0, 'C');
        $pdf->Cell(0.55, 0.25, 'TOTAL', 'LBR', 1, 'C');

        foreach($gstsum as $x=>$a)
        {
            if($x == 0)
            {
                continue;
            }
            $amt = $a/2;
            $amt = round($amt, 2);
            $pdf->Cell(0.55, 0.21, ($x/2), 'LR', 0, 'R');
            $pdf->Cell(0.55, 0.21, $amt, 'LR', 0, 'R');
            $pdf->Cell(0.55, 0.21, ($x/2), 'LR', 0, 'R');
            $pdf->Cell(0.55, 0.21, $amt, 'LR', 0, 'R');
            $pdf->Cell(0.55, 0.21, ($amt*2), 'LR', 1, 'R');
        }
        $pdf->Cell(2.75, 0.25, '', 'T', 1, 'C');
        //footer
        $pdf->SetFont('OCR-A','', 9);
        
        //$pdf->Ln(0.2);
        $pdf->Cell(2.75, 0.2, 'Thank You for your Shopping!', 0, 0, 'C');
        //$pdf->Ln(0.6);
        //$pdf->Cell(2.75, 0.2, 'T&C goes here, whatever...', 0, 0, 'L');
        
        //output
        $pdf->Output();
    }
?>