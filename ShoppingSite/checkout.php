<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>Document</title>
</head>
<body>
    <div class="container">
        <div>
            <h4 class="text-center text-capitalize text-monospace my-2">
                <span class="multicolortext">Khodiyar Dairy Parlor</span>
            </h4>
            <h5 class="text-center text-capitalize text-monospace my-2">
                <span class="multicolortext">Kart Check Out</span>
            </h5>
        </div>
        <hr>
        <!-- Customer Detail -->
        <div class="form-group">
            <label for="nameinp">Name</label>
            <input type="text" id="confnameinp" class="form-control" placeholder="Enter name" maxlength="32" readonly>
        </div>
        <div class="form-group">
            <label for="phoneinp">Phone number</label>
            <input type="text" class="form-control" id="confphoneinp" aria-describedby="phoneHelp" placeholder="Enter Phone Number" maxlength="10" minlength="10" readonly>
            <small id="phoneHelp" class="form-text text-muted">We'll never share your phone number with anyone else.</small>
        </div>
        <div class="form-group">
            <label for="deliveryinp">Delivery Choice</label>
            <select class="form-control" id="confdeliveryinp" readonly>
                <option value="" selected disabled>-- Choose --</option>
                <option value="home_d">Delivery it to my registered Address</option>
                <option value="pick_up">I will Pick my Order</option>
            </select>
        </div>
        <div class="form-group">
            <label for="expdateinp">Expected date for delivery choice</label>
            <input type="datetime-local" id="confexpdateinp" name="" class="form-control" readonly>
        </div>

        <!-- Item Table -->
        <div class="row" id="ItemTable">
            <!-- Heading row -->
            <div class="col-12 bg-dark text-white mb-2 mx-0 px-0">
                <div class="row px-0 mx-0">
                    <div class="col-1 text-right col-sm-1">
                        #
                    </div>
                    <div class="col-10 col-sm-5">
                        Item Name
                    </div>
                    <div class="col-4 text-center col-sm-2">
                        Qty
                    </div>
                    <div class="col-4 text-right col-sm-2">
                        Price
                    </div>
                    <div class="col-4 text-right col-sm-2">
                        Amount
                    </div>
                </div>
            </div>

            <!-- Item rows -->
        </div>
        <div class="float-right pb-4">
            <b>Total :</b> Rs. <span id="total"></span>
        </div>
        <br><br>
        <div class="float-right pb-4">
            <button class="btn btn-outline-success" onclick="corfirmOrder()">Confirm Order</button>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="js/mainv2.js"></script>
<script>
window.onload = function() {
    createItemList();
};
</script>
</html>