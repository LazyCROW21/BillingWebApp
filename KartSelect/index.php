<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>Shopping Site</title>
</head>
<body>
    <!-- Page title -->
    <div>
        <h4 class="text-center text-capitalize text-monospace my-2">
            <span class="multicolortext">Khodiyar Dairy Parlor</span>
        </h4>
    </div>
    <hr>
    <!-- form starts -->
    <div class="container">
        <form>
            <div class="form-group">
                <label for="nameinp">Name</label>
                <input type="text" id="nameinp" class="form-control" placeholder="Enter name" maxlength="32">
            </div>
            <div class="form-group">
                <label for="phoneinp">Phone number</label>
                <input type="text" class="form-control" id="phoneinp" aria-describedby="phoneHelp" placeholder="Enter Phone Number" maxlength="10" minlength="10">
                <small id="phoneHelp" class="form-text text-muted">We'll never share your phone number with anyone else.</small>
            </div>
            <div class="form-group">
                <label for="deliveryinp">Delivery Choice</label>
                <select class="form-control" id="deliveryinp">
                    <option value="" disabled selected>-- Choose --</option>
                    <option value="home_d">Delivery it to my registered Address</option>
                    <option value="pick_up">I will Pick my Order</option>
                </select>
            </div>
            <div class="form-group">
                <label for="expdateinp">Expected date for delivery choice</label>
                <input type="datetime-local" id="expdateinp" name="" class="form-control">
            </div>
        </form>
    </div>
    <!-- form end -->
    <hr>
    <div class="my-2">
        <h5 class="text-center text-capitalize text-monospace my-2">
            <span class="multicolortext">Item Select &#8595;</span>
        </h5>
    </div>
    <!-- Item display starts -->
    <div class="container my-4">
        <input class="form-control mb-4" name="" id="" type="text" maxlength="32" placeholder="Search" oninput="searchitem(this)">
        <div class="row w-100 px-0 mx-0">
            <!-- <div class="card border-dark col custcard" onclick="itemselect(this)">
                <div class="text-center">
                    <img class="img-thumbnail" src="img/download.png" alt="Card image cap">
                </div>
                <div class="custcardbody p-1">
                    <h6 class="card-title mb-1">Card title</h6>
                    <p class="text-muted mb-0">Rs. 1000</p>
                </div>
            </div> -->
            <?php
        require_once('../Admin_Panel/PHP/database.php');
    
        $get_item = "SELECT * FROM items WHERE item_stock > 0";
        $result = $conn->query($get_item);

        while($row = $result->fetch_assoc())
        {
            $item_id = $row['item_id'];
            $item_n = $row['item_name'];
            $item_p = $row['Item_price'];
            $item_img = substr($row['img_url'], 3);
            echo "
            <div class=\"card border-dark col-xl-3 custcard\" onclick=\"itemselect(this)\" id=\"$item_id\">
                <div class=\"text-center img-container\">
                    <img class=\"img-thumbnail\" src=\"$item_img\" alt=\"$item_n\">
                </div>
                <div class=\"custcardbody p-1\">
                    <h6 class=\"card-title mb-1\">$item_n</h6>
                    <p class=\"text-muted mb-0\">Rs. <span class=\"item_p\">$item_p</span></p>
                </div>
            </div>
            ";
        }
        $conn->close();
        ?>
        </div>

        <!-- End of Item select -->
        <hr>
        <div class="container" style="height: 32px;">
            <button class="btn btn-outline-primary float-right" onclick="checkout()">Check Out</button>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="js/main.js"></script>
</html>