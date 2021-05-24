<?php
    require_once('../PHP/database.php');
    require '../../PHPMailer-5.2-stable/PHPMailerAutoload.php';

    if(!isset($_POST['bid']))
    {
        die('NoBillID');
    }

    $link = 'http://localhost/website_v8/Admin_Panel/PHP/printbill.php?bill_id='.$_POST['bid'].'&size=A4';
    $sentfrom = 'hardikkardam21@gmail.com';
    $replyto = 'hardikkardam21@gmail.com';
    $companyname = 'Khodiyar Dairy Parlor';
    $subject = 'Tax Invoice';
    $body = 'Dear Customer, we have sent you the tax invoice of your past purchase as per your request.<br>
    Please use the current link to download your Tax Invoice as pdf.<br>
    <a href="'.$link.'">Click here to get pdf</a>';
    $altbody = 'Dear Customer, we have you the tax invoice of your past purcchase as per your request.\n
    Please use the current link to download your Tax Invoice as pdf.\n
    link : '.$link;
    $cust_addr = '';

    $getmail = 'SELECT customer_email FROM customers
    INNER JOIN bill_detail
    ON bill_detail.cust_phone = customers.customer_phone
    WHERE bill_detail.bill_id = ?';
    $stmt = $conn->prepare($getmail);
    if(!$stmt)
    {
        die('Error in query! 2123');
    }
    $bid = $_POST['bid'];
    $stmt->bind_param('i', $bid);
    if($stmt->execute())
    {
        $result = $stmt->get_result();
        $cust_detail = $result->fetch_assoc();
        $cust_addr = $cust_detail['customer_email'];
    }
    else
    {
        die('Error in exeec! asfaasd');
    }

    $mail = new PHPMailer;

    //$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'hardikkardam21@gmail.com';                 // SMTP username
    $mail->Password = '******';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->setFrom($sentfrom, $companyname);
    $mail->addAddress($cust_addr, 'Customer');     // Add a recipient
    // $mail->addAddress('ellen@example.com');               // Name is optional
    $mail->addReplyTo($replyto, 'CustomerCare');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->AltBody = $altbody;

    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
?>