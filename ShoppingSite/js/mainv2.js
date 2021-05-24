class Item {
    constructor(item_id, item_name, item_qty, item_price) {
        this.id = item_id;
        this.name = item_name;
        this.qty = item_qty;
        this.price = item_price;
    }
}

function itemselect(item)
{
    // <img src="img/check.png" class="itemqty">
    let thecode = `
    <div class="itemcard h-100 w-100"></div>
    <div class="itemchk text-center"></div>
    <p class="itemqty">1</p>
    <button class="btn btn-danger minusbtn py-0 px-2" onclick="subqty(this)">&#9866;</button>
    <button class="btn btn-success plusbtn py-0 px-2" onclick="addqty(this)">&#10010;</button>
    `;
    if(item.children.length == 2)
    {
        item.innerHTML = item.innerHTML + thecode;
        // console.log(item.id)
        let item_name = item.getElementsByClassName('card-title')[0].innerText;
        let item_price = parseFloat(item.getElementsByClassName('item_p')[0].innerText);
        console.log(item_name, item_price);
        let itemObj = new Item(parseInt(item.id), item_name, 1, item_price);
        sessionStorage.setItem(("item-"+item.id.toString()), JSON.stringify(itemObj));
        console.log(JSON.stringify(itemObj))
    }
}

function addqty(item)
{
    item = item.parentNode;
    let curqty = item.getElementsByClassName('itemqty')[0].innerText;
    let curQtyInt = parseInt(curqty);
    item.getElementsByClassName('itemqty')[0].innerText = (curQtyInt + 1);
    let cur = sessionStorage.getItem(("item-"+item.id.toString()))
    let curObj = JSON.parse(cur);
    curObj.qty = curObj.qty + 1;
    sessionStorage.setItem(("item-"+item.id.toString()), JSON.stringify(curObj));
}

function subqty(item)
{
    event.stopPropagation();
    item = item.parentNode;
    let curqty = item.getElementsByClassName('itemqty')[0].innerText;
    let curQtyInt = parseInt(curqty);
    if(curQtyInt > 1)
    {
        item.getElementsByClassName('itemqty')[0].innerText = (curQtyInt - 1);
        let cur = sessionStorage.getItem(("item-"+item.id.toString()))
        let curObj = JSON.parse(cur);
        curObj.qty = curObj.qty - 1;
        sessionStorage.setItem(("item-"+item.id.toString()), JSON.stringify(curObj))
    }
    else
    {
        item.removeChild(item.getElementsByClassName('itemcard')[0]);
        item.removeChild(item.getElementsByClassName('itemchk')[0]);
        item.removeChild(item.getElementsByClassName('itemqty')[0]);
        item.removeChild(item.getElementsByClassName('minusbtn')[0]);
        item.removeChild(item.getElementsByClassName('plusbtn')[0]);
        sessionStorage.removeItem(("item-"+item.id.toString()));
    }
}

function searchitem(inp)
{
    let searchstring = inp.value.toLowerCase();
    let items = document.getElementsByClassName('custcard');
    let item;
    for(item of items)
    {
        if(!item.getElementsByClassName('card-title')[0].innerText.toLowerCase().includes(searchstring))
        {
            item.classList.add("d-none");
        }
        else
        {
            item.classList.remove("d-none");
        }
    }
}

// Form Submission
function stringminmax(strng, min, max)
{
    var len = strng.length;
    if(len<min || len>max)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function specialchartest(strng)
{
    var format = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
    return format.test(strng);
}

function specialchartestwspace(strng)
{
    var format = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
    return format.test(strng);
}

function isnumber(num)
{
    var reg = /^\d+$/;
    return reg.test(num);
}

function check_digit(strng)
{
    var format = /[1234567890]/;
    return format.test(strng);
}

function correctDate(d)
{
    var inpd = new Date(d);
    var d = new Date();
    if(d > inpd)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function checkout()
{
    let custName = document.getElementById('nameinp').value;
    let custPhone = document.getElementById('phoneinp').value;
    let custDelivery = document.getElementById('deliveryinp').value;
    let deliveryDate = document.getElementById('expdateinp').value;
    sessionStorage.setItem('custName', custName);
    sessionStorage.setItem('custPhone', custPhone);
    sessionStorage.setItem('custDelivery', custDelivery);
    sessionStorage.setItem('deliveryDate', deliveryDate);
    if(sessionStorage.length <= 4)
    {
        alert('At least select 1 item')
        return -1;
    }
    window.location.href = "checkout.php";
}

function createItemList()
{
    if(sessionStorage.length <= 4)
    {
        alert("Please select some item before checking out!");
        window.location.replace('index.php');
    }

    document.getElementById('confnameinp').value = sessionStorage.getItem('custName');
    document.getElementById('confphoneinp').value = sessionStorage.getItem('custPhone');
    if(document.getElementById('confdeliveryinp').value == "home_d")
    {
        document.getElementById('confdeliveryinp').selectedIndex = 0;
    }
    else
    {
        document.getElementById('confdeliveryinp').selectedIndex = 1;
    }
    document.getElementById('confexpdateinp').value = sessionStorage.getItem('deliveryDate');

    let itable = document.getElementById('ItemTable');
    let row;
    let itemCnt = 1;
    let itemObj;
    let sskey;
    let total = 0;
    for(let i=0; i < sessionStorage.length; i++)
    {
        sskey = sessionStorage.key(i);
        if(sskey == "custName" || sskey == "custDelivery" || sskey == "custPhone" || sskey == "deliveryDate")
        {
            continue;
        }
        row = document.createElement("div");
        itemObj = JSON.parse(sessionStorage.getItem(sskey));
        // console.log(itemObj);
        row.className = "col-12 bg-light text-dark mb-2 px-0 mx-0";
        row.innerHTML = `
        <div class="row px-0 mx-0">
            <div class="col-1 text-right col-sm-1">
                ${itemCnt}
            </div>
            <div class="col-10 col-sm-5">
                ${itemObj.name}
            </div>
            <div class="col-4 text-center col-sm-2 px-0 mx-0">
                <div class="row px-0 mx-0" id="${itemObj.id}">
                    <div class="col-4 px-0 mx-0"><button class="btn btn-danger py-1 btn-sm" onclick="subchkqty(this)">&#9866;</button></div>
                    <div class="col-4 px-0 mx-0"><span class="px-2 chkqty">${itemObj.qty}</span></div>
                    <div class="col-4 px-0 mx-0"><button class="btn btn-success py-1 btn-sm" onclick="addchkqty(this)">&#10010;</button></div>
                </div>
            </div>
            <div class="col-4 text-right col-sm-2 price">
                ${itemObj.price}
            </div>
            <div class="col-4 text-right col-sm-2 amount">
                ${(itemObj.price * itemObj.qty)}
            </div>
        </div>
        `;
        itable.appendChild(row);
        itemCnt += 1;
        total += (itemObj.price * itemObj.qty);
    }
    document.getElementById("total").innerText = total;
}

function calculateTotal()
{
    let allqty = document.getElementsByClassName('chkqty');
    let allprice = document.getElementsByClassName('price');
    let allamount = document.getElementsByClassName('amount');
    let totalamt = 0;
    let amount;
    let total = document.getElementById('total');
    for(let i = 0; i<allamount.length; i++)
    {
        amount = parseFloat(allprice[i].innerText) * parseFloat(allqty[i].innerText);
        amount = Math.round(amount * 100) / 100
        allamount[i].innerText = amount;
        totalamt += amount;
    }
    total.innerText = totalamt;
}

function addchkqty(item)
{
    let row = item.parentNode.parentNode;
    // console.log(row);
    let curqty = parseInt(row.getElementsByClassName("chkqty")[0].innerText);
    row.getElementsByClassName("chkqty")[0].innerText = (curqty + 1);
    let cur = sessionStorage.getItem(("item-"+row.id.toString()))
    let curObj = JSON.parse(cur);
    curObj.qty = curObj.qty + 1;
    sessionStorage.setItem(("item-"+row.id.toString()), JSON.stringify(curObj));
    calculateTotal();
}

function subchkqty(item)
{
    let row = item.parentNode.parentNode;
    // console.log(row);
    let curqty = parseInt(row.getElementsByClassName("chkqty")[0].innerText);
    if(curqty > 1)
    {
        row.getElementsByClassName("chkqty")[0].innerText = (curqty - 1);
        let cur = sessionStorage.getItem(("item-"+row.id.toString()))
        let curObj = JSON.parse(cur);
        curObj.qty = curObj.qty - 1;
        sessionStorage.setItem(("item-"+row.id.toString()), JSON.stringify(curObj));
    }
    else
    {
        let r = confirm("Want to remove this item from Kart?");
        if(r)
        {
            let superrow = row.parentNode.parentNode.parentNode;
            let kartTable = document.getElementById("ItemTable");
            kartTable.removeChild(superrow);
            sessionStorage.removeItem(("item-"+row.id.toString()));
        }
    }
    calculateTotal();
}

function corfirmOrder()
{
    var orderDetails = JSON.stringify(sessionStorage).replace("&", "and");
    var xhr = new XMLHttpRequest();
    var data = "orderDetails="+orderDetails;

    xhr.onreadystatechange = function()
    {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                if(this.responseText === "CUSTOMER NOT FOUND")
                {
                    sessionStorage.clear();
                    alert("Sorry, You are not a registered customer!")
                }
                else if(this.responseText === "CONFIRMED")
                {
                    sessionStorage.clear();
                    window.location.replace('confirm.html');
                }
                else
                {
                    console.log(this.responseText);
                    alert(this.responseText);
                }
            } 
            else 
            {
                console.error(xhr);
                alert("Error submitting your bill please try again later!");
            }
        }
    };
    xhr.open("POST", "process.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send(data);
}