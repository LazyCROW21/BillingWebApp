var globalrowcount = 10;
var globalselecteditems = Array();

function AddItem()
{
    ++globalrowcount;
    //Item cross
    var newrowcol1 = document.createElement('div');
    newrowcol1.className = "row";
    newrowcol1.innerHTML = "&times;";
    newrowcol1.setAttribute("data-rowid", globalrowcount.toString());
    newrowcol1.setAttribute("onclick", "RemoveItem('"+globalrowcount.toString()+"')");
    
    //Item Name
    var newrowcol2 = document.createElement('div');
    newrowcol2.className = "row ItemN";
    newrowcol2.setAttribute('onclick', `openItemBox(${globalrowcount})`);
    newrowcol2.innerHTML = '-';
    newrowcol2.setAttribute("data-rowid", globalrowcount.toString());
    
    //Item Price
    var newrowcol3 = document.createElement('div');
    newrowcol3.className = "row ItemP";
    newrowcol3.innerText = '-';
    newrowcol3.setAttribute("data-rowid", globalrowcount.toString());
    
    //Item Qty
    var newrowcol4 = document.createElement('div');
    newrowcol4.className = "row ItemQ";
    newrowcol4.innerHTML = `<input type="number" class="ItemQty" min="0.001" max="99999" step="0.001" value="1" oninput="updateItemQty(this.value, ${globalrowcount})">`;
    newrowcol4.setAttribute("data-rowid", globalrowcount.toString());
    
    var maintable = document.getElementById('Item_Table');
    var additembtn = document.getElementById('AddNewItem');
    maintable.insertBefore(newrowcol1, additembtn);
    maintable.insertBefore(newrowcol2, additembtn);
    maintable.insertBefore(newrowcol3, additembtn);
    maintable.insertBefore(newrowcol4, additembtn);

    openItemBox(globalrowcount);
}

function RemoveItem(rowid)
{
    var removerow = document.getElementsByClassName('row');
    for(var i=(removerow.length-1); i>=0; i--)
    {
        if(removerow[i].getAttribute("data-rowid") === rowid)
        {
            removerow[i].parentNode.removeChild(removerow[i]);
        }
    }
    for(let i=0; i<(globalselecteditems.length); i++)
    {
        if(globalselecteditems[i]["data-rowid"] == rowid)
        {
            globalselecteditems.splice(i, 1);
        }
    }
}

function loadItemsJSON(path, inp, rowid)
{
    var xhr = new XMLHttpRequest();
    var inp_data = 'itemName='+inp;
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // console.log(xhr.responseText);
                var items = JSON.parse(xhr.responseText);
                // console.log(items);
                createItemList(items, rowid);
                return true;
            } 
            else 
            {
                console.error(xhr);
                return false;
            }
        }
    };
    xhr.open("POST", path, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send(inp_data);
}

// loadJSON('getitems.php', '',
//          function(data) { console.log(data); /* createItemList(data); */},
//          function(xhr) { console.error(xhr); }
// );

// function createItemList(items, rowid)
// {
//     var imgrid = document.getElementById('imgrid');
    
//     let imgHTML = '<img class="itemimg" src="" alt="No image">';
//     let btnHTML = '<button class="itemtitle">Kadhai Paneer</button>';
//     let titleHTML = '<span class="itemtitle"></span>';
//     let imgurl = '';
//     var newitem;
//     for(let i = 0; i<(items.length); i++)
//     {
//         newitem = document.createElement('div');
//         imgurl = items[i]["img_url"].substring(3);
//         imgHTML = `<img class="itemimg" src="${imgurl}" alt="No img">`;
//         btnHTML = `<button class="isbtn" onclick="selectItem(${items[i]['item_id']}, ${rowid}, '${items[i]['item_name']}', ${items[i]["Item_price"]})">select</button>`;
//         titleHTML = `<span class="itemtitle">${items[i]["item_name"]}, Rs.${items[i]["Item_price"]}</span>`;
//         newitem.innerHTML = imgHTML + btnHTML + titleHTML;
//         imgrid.appendChild(newitem);
//     }
//     // newitem.innerHTML = "";
// }

function createItemList(items, rowid)
{
    var imgrid = document.getElementById('imgrid');
    
    let imgHTML = '<img class="itemimg" src="" alt="No image">';
    let btnHTML = '<button class="itemaddbtn">Kadhai Paneer</button>';
    let imgurl = '';
    var newitem;
    for(let i = 0; i<(items.length); i++)
    {
        newitem = document.createElement('div');
        newitem.className = 'item';
        imgurl = items[i]["img_url"].substring(3);
        imgHTML = `<img class="itemimg" src="${imgurl}" alt="No img">`;
        btnHTML = `<button class="itemaddbtn" onclick="selectItem(${items[i]['item_id']}, ${rowid}, '${items[i]['item_name']}', ${items[i]["Item_price"]})">ADD +</button>`;
        newitem.innerHTML = `
        <div>
            ${imgHTML}
        </div>
        <div>
            <h4>${items[i]["item_name"]}</h4>
            <span>Rs. ${items[i]["Item_price"]}</span>
            ${btnHTML}
        </div>
        `;
        imgrid.appendChild(newitem);
    }
    // newitem.innerHTML = "";
}

function searchItem(arg){
    var imgrid = document.getElementById('imgrid');
    imgrid.innerHTML = "";
    let rowid = document.getElementById('searchitem').getAttribute('data-rowid');
    // console.log(arg);
    loadItemsJSON('getitems.php', arg, rowid);
}
function openItemBox(rowid)
{
    var itembox = document.getElementById('imbox');
    itembox.style.display = 'block';
    var searchinp = document.getElementById('searchitem');
    searchinp.value = "";
    searchinp.setAttribute('data-rowid', rowid);
    loadItemsJSON('getitems.php', '', rowid);
}
function closeItemBox()
{
    let itembox = document.getElementById('imbox');
    itembox.style.display = 'none';
    let imgrid = document.getElementById('imgrid');
    imgrid.innerHTML = "";
}

class itemClass{
    constructor(i_id, r_id, q)
    {
        this.item_id = i_id;
        this.row_id = r_id;
        // this.item_name = i_n;
        // this.item_price = i_p;
        this.qty = q;
    }
}

function selectItem(itemid, rowid, itemname, itemprice)
{
    // let data = {"item_id": itemid, "data-rowid": rowid, "qty": 1.0}
    let data = new itemClass(itemid, rowid, 1.0);
    let flag = 0;
    // console.log(itemid, rowid);
    for(let i = 0; i < globalselecteditems.length; i++)
    {
        if(globalselecteditems[i].item_id === data.item_id)
        {
            // console.log("ITEM ID MATCH");
            alert("You have Already Added this item!");
            return false;
        }
        if(globalselecteditems[i].row_id === data.row_id)
        {
            // console.log("ROW ID MATCH");
            globalselecteditems[i] = data;
            flag = 1;
        }
    }
    if(flag == 0)
    {
        globalselecteditems.push(data);
    }
    let row = document.getElementsByClassName('row');
    for(let r = 0; r<(row.length); r++)
    {
        if(row[r].getAttribute('data-rowid') == rowid)
        {
            if(row[r].className == 'row ItemN')
            {
                row[r].innerText = itemname;
            }
            else if(row[r].className == 'row ItemP')
            {
                row[r].innerText = itemprice;
            }
            else if(row[r].className == 'row ItemQ')
            {
                let qtyinp = row[r].getElementsByClassName('ItemQty');
                qtyinp[0].value = 1.0;
            }
        }
    }
    // console.log(globalselecteditems);
    closeItemBox();
}

function updateItemQty(q, rowid)
{
    for(let i=0; i<globalselecteditems.length; i++)
    {
        if(globalselecteditems[i].row_id === rowid)
        {
            globalselecteditems[i].qty = q;
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

function ConfirmOrder()
{
    let cust_name = document.getElementById('custN').value;
    let cust_phone = document.getElementById('custP').value;
    let cust_delivery = document.getElementsByName('custC');
    let cust_d = '';
    let cust_expectD = document.getElementById('rdt').value;
    for(let c = 0; c < cust_delivery.length; c++)
    {
        if(cust_delivery[c].checked)
        {
            cust_d = cust_delivery[c].value;
            break;
        }
    }
    let err = false;
    if(!stringminmax(cust_name, 3, 32) || specialchartestwspace(cust_name))
    {
        let errp = document.getElementById('custNE');
        errp.innerText = "*Name should be 3-32 Alphabets Only!";
        err = true;
    }
    else
    {
        let errp = document.getElementById('custNE');
        errp.innerText = "";
    }
    if(!stringminmax(cust_phone, 10, 10) || !isnumber(cust_phone) || specialchartest(cust_phone))
    {
        let errp = document.getElementById('custPE');
        errp.innerText = "*Phone number should be 10 digit number Only!";
        err = true;
    }
    else
    {
        let errp = document.getElementById('custPE');
        errp.innerText = "";
    }
    if(cust_d == '')
    {
        let errp = document.getElementById('custCE');
        errp.innerText = "*Please choose an option!";
        err = true;
    }
    else
    {
        let errp = document.getElementById('custCE');
        errp.innerText = "";
    }
    if(!correctDate(cust_expectD) || cust_expectD == '')
    {
        let errp = document.getElementById('custRDTE');
        errp.innerText = "*Enter a future date & time, btw 9:00 a.m and 9:00 p.m!";
        err = true;
    }
    else
    {
        let errp = document.getElementById('custRDTE');
        errp.innerText = "";
    }
    if(globalselecteditems.length < 1)
    {
        let errp = document.getElementById('custPOE');
        errp.innerText = "*Please at least select 1 item!";
        err = true;
    }
    else
    {
        let errp = document.getElementById('custPOE');
        errp.innerText = "";
    }
    if(err)
    {
        return false;
    }

    let FinalOrder = {
        custN: cust_name,
        custP: cust_phone,
        custC: cust_d,
        custRDT: cust_expectD,
        itemList: globalselecteditems
    }
    
    var FinalOrderJSON = JSON.stringify(FinalOrder);
    console.log(FinalOrderJSON);
    var path = 'process.php';
    
    var xhr = new XMLHttpRequest();
    var inp_data = 'data='+FinalOrderJSON;
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) 
            {
                var OrderResponse = xhr.responseText;
                if(OrderResponse == 'ERROR DATA MISSING')
                {
                    alert('Data missing please fill the details correctly and try again');
                }
                else if(OrderResponse == 'CUSTOMER NOT FOUND')
                {
                    alert('You are not a registered customer');
                }
                else if(OrderResponse == 'CONFIRMED')
                {
                    console.log('Order Confirmed!');
                    window.location = 'confirm.html';
                }
                else if(OrderResponse == 'ERROR IN EXEC')
                {
                    alert('Something went wrong, try again!');
                }
                else
                {
                    alert(xhr.responseText);
                }
            } 
            else 
            {
                console.error(xhr);
                return false;
            }
        }
    };
    xhr.open("POST", path, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send(inp_data);
}