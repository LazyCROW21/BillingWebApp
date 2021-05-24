<?php
    if(isset($_POST['add_item']))
    {
        if(!isset($_POST['item_name']) || !isset($_POST['item_price']) || !isset($_POST['item_stock']) || !isset($_FILES['fileToUpload']))
        {
            die('Values missing try again!');
        }
        else
        {
            $item_id = 0;
            $item_name = $_POST['item_name'];
            $item_price = $_POST['item_price'];
            $item_gst = $_POST['item_gst'];
            $item_stock = $_POST['item_stock'];
            $item_url = '../../ItemIMG/';
            $item_del = 0;

            require('../PHP/database.php');

            $ins_item = 'INSERT INTO items (item_name, item_price, GST, item_stock, deleted) VALUES (?, ?, ?, ?, ?)';

            $stmt = $conn->prepare($ins_item);
            if($stmt)
            {
                $stmt->bind_param('sdddi', $item_name, $item_price, $item_gst, $item_stock, $item_del);
                if($stmt->execute())
                {
                    $item_id = $stmt->insert_id;
                    // need to add upload f code here
                    $target_dir = $item_url;
                    $target_file = $target_dir . 'itemimg_' . $item_id . basename($_FILES["fileToUpload"]["name"]);
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                    // Check if image file is a actual image or fake image
                    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                    if($check !== false)
                    {
                        //echo "File is an image - " . $check["mime"] . ".";
                        $uploadOk = 1;
                    } 
                    else
                    {
                        die("File is not an image.");
                        $uploadOk = 0;
                    }

                    // Check if file already exists
                    if (file_exists($target_file))
                    {
                        die("Sorry, file already exists.");
                        $uploadOk = 0;
                    }

                    // Check file size
                    if ($_FILES["fileToUpload"]["size"] > 1000000)
                    {
                        die("Sorry, your file is too large.");
                        $uploadOk = 0;
                    }

                    // Allow certain file formats
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif" )
                    {
                        die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
                        $uploadOk = 0;
                    }

                    // Check if $uploadOk is set to 0 by an error
                    if ($uploadOk == 0)
                    {
                        die("Sorry, your file was not uploaded.");
                        // if everything is ok, try to upload file
                    } 
                    else 
                    {
                        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) 
                        {
                            //echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
                            $upd_img_url = "UPDATE items SET img_url = '$target_file' WHERE item_id = $item_id";
                            if(!$conn->query($upd_img_url))
                            {
                                die('Something went wrong, contact support.');
                            }
                        } 
                        else
                        {
                            die("Sorry, there was an error uploading your file.");
                        }
                    }
                    // till here
                    header('Location: ../PAGES/add_item.php?add_item=1');
                    //echo '<script> alert("Item Added Successfully!") </script>';
                }
                else
                {
                    die('Error in execution try again!');
                }
            }
            else
            {
                die('Query Failed! Contact support');
            }
        }
    }
?>