<?php
    require_once('../PHP/database.php');

    //when you click delete
    if(isset($_POST['del_item']))
    {
        $del_item = 'UPDATE items SET deleted = 1 WHERE item_id = ?';
        $stmt = $conn->prepare($del_item);
        if(!$stmt)
        {
            die('Error in preparing query!');
        }
        if(!isset($_POST['del_item_id']))
        {
            die('Insufficient Information! Try again');
        }
        $id = $_POST['del_item_id'];
        $stmt->bind_param('i', $id);
        if($stmt->execute())
        {
            $getimgurl = "SELECT img_url FROM items WHERE item_id = $id";
            $result = $conn->query($getimgurl);
            if($result)
            {
                if($result->num_rows > 0)
                {
                    $row = $result->fetch_assoc();
                    unlink($row['img_url']);
                }
            }
            echo '<script>alert("Item Removed!"); window.location.replace("../PAGES/view_item.php");</script>';
        }
        else
        {
            die('Error in execution! try again');
        }
    }
    
    //when you click update
    if(isset($_POST['upd_item']))
    {
        $upditem = 'UPDATE items SET item_name = ?, item_price = ?, GST=?, item_stock = ? WHERE item_id = ?';
        $stmt = $conn->prepare($upditem);
        if(!$stmt)
        {
            die('Error preparing query! contact support');
        }
        if(!isset($_POST['upd_it_id']) || !isset($_POST['upd_name']) || !isset($_POST['upd_price']) || !isset($_POST['upd_stk']) || !isset($_POST['upd_gst']))
        {
            die('Insuffiecient Information!');
        }
        $id = $_POST['upd_it_id'];
        $name = $_POST['upd_name'];;
        $price = $_POST['upd_price'];
        $gst = $_POST['upd_gst'];
        $stock = $_POST['upd_stk'];

        $stmt->bind_param('sdddi', $name, $price, $gst, $stock, $id);
        if($stmt->execute())
        {
            //file reupload
            if(isset($_FILES['fileToUpload']))
            {
                //remove old one
                $getimgurl = "SELECT img_url FROM items WHERE item_id = $id";
                $result = $conn->query($getimgurl);
                if($result)
                {
                    if($result->num_rows > 0)
                    {
                        $row = $result->fetch_assoc();
                        unlink($row['img_url']);
                    }
                }
                //add new one
                $item_url = '../../ItemIMG/';
                $target_dir = $item_url;
                $target_file = $target_dir . 'itemimg_' . $id . basename($_FILES["fileToUpload"]["name"]);
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
                        $upd_img_url = "UPDATE items SET img_url = '$target_file' WHERE item_id = $id";
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
            }
            //file reupload end
            echo '<script>alert("Item Updated!"); window.location.replace("../PAGES/view_item.php");</script>';
        }
        else
        {
            die('Error in execution! try again');
        }
    }

    // when you first open the menu
    if(!isset($_POST['item_id']))
    {
        die('No data recieved!');
    }

    $get_item = 'SELECT item_name, item_price, GST, img_url, item_stock FROM items WHERE deleted = 0 AND item_id = ?';
    $it_id = $_POST['item_id'];

    $stmt = $conn->prepare($get_item);
    if($stmt)
    {
        $stmt->bind_param('i', $it_id);
        if(!$stmt->execute())
        {
            die('Error in execution!');
        }
        $result = $stmt->get_result();
        //print_r($result);
        $it_d = $result->fetch_assoc();
        //echo '<div class="upd-item">';
        //echo '<div><button id="closeEdit" onclick="closeEditMenu()">x</button>';
        echo '<form id="uptform" method="POST" action="../PHP/edititem.php" enctype="multipart/form-data">';
            echo '<p><b>Item ID: </b>'.$it_id.'</p>';
            echo '<input name="upd_it_id" type="hidden" value="'.$it_id.'">';
            echo '<p><b>Item Name: </b><input name="upd_name" type="text" value="'.$it_d['item_name'].'" max="32" placeholder="Item name.." required></p>';
            echo '<p><b>Item Price: </b><input name="upd_price" type="number" value="'.$it_d['item_price'].'" step="0.01" min="0.01" placeholder="Item price.." required></p>';
            echo '<p><b>Item GST: </b><input name="upd_gst" type="number" value="'.$it_d['GST'].'" step="0.01" min="0" placeholder="GST(in %)" required></p>';
            echo '<p><b>Item Stock: </b><input name="upd_stk" type="number" value="'.$it_d['item_stock'].'" step="0.001" min="0.001" required></p>';
            $imgurl = $it_d['img_url'];
            echo "<img class=\"editimg\" src=\"$imgurl\" alt=\"No img\">";
            echo '<p><b>Change image(.jpg .png .jpeg only, under 1 mb):</b></p>';
            echo '<input type="file" name="fileToUpload" id="fileToUpload">';
            echo '<input name="upd_item" type="submit" value="UPDATE">';
        echo '</form>';
        //delete btn
        echo '<form id="delform" method="POST" action="../PHP/edititem.php" onsubmit="return del_warn();">';
            echo '<input name="del_item_id" type="hidden" value="'.$it_id.'">';
            echo '<input name="del_item" class="del-item-btn" type="submit" value="DELETE">';
        echo '</form>';
        //echo '</div>';
    }
    else
    {
        die('Error in query!');
    }
?>