function upd_sub_total()
{
    var plist = document.getElementsByClassName('price');
    var qlist = document.getElementsByClassName('qty');
    //var glist = document.getElementsByClassName('gst');
    var slist = document.getElementsByClassName('subttl');

    var tl = document.getElementById("bill_t");
    var dis = document.getElementById('b_discnt');
    var cash = document.getElementById('b_cr');
    var c2r = document.getElementById('c2r');
    var divchrg = document.getElementById('b_divchrg');

    var i = 0;
    var amt = 0.0;
    var total = 0.0;
    var p, q;
    while(i<plist.length)
    {
        p = parseFloat(plist[i].value);
        q = parseFloat(qlist[i].value);
        //g = parseFloat(glist[i].innerText);
        amt = p*q;
        amt = Math.round(amt*100)/100;
        total += amt;
        slist[i].innerText = amt;
        //console.log(amt);
        i++;
    }
    d = parseFloat(dis.value);
    var dc = parseFloat(divchrg.value);
    total -= d;
    total += dc;
    total = Math.round(total*100)/100;
    tl.innerText = total;
    var ret = parseFloat(cash.value) - total;
    if(ret < 0 || isNaN(ret))
    {
        c2r.innerText = 'Insufficient Payment!';
    }
    else
    {
        c2r.innerHTML = 'Change to return: ₹'+Math.round(ret*100)/100;
    }
}

// Adding new item
// /*
function setitemprice(itemid)
{
    // console.log('Item id: '+itemid);
    var pinput = document.getElementById('additem_p');    
    var qinput = document.getElementById('additem_q');

    var data = 'getitemprice=1&itemid='+itemid;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../PHP/addnewitem.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        var r = this.responseText;
        if(r == 'Error')
        {
            alert('Cannot get the item price please enter it manually!');
        }
        else
        {
            let idet = JSON.parse(r);
            pinput.value = idet["price"];
            qinput.value = '';
            qinput.placeholder = 'max: '+idet["stock"];
            qinput.max = idet["stock"];
        }
    }
    xhr.send(data);
}

function addnew()
{
    var rowid = Math.floor(Math.random() * 101)+Math.floor(Math.random() * 101)*1000;
    var item = document.getElementById('additem_id');
    var itemp = document.getElementById('additem_p');
    var itemq = document.getElementById('additem_q');
    //bill id
    var bid = document.getElementById('bill-id').value;
    //grid
    var rowholder = document.getElementById('itemrows');

    // price, qty, subttl
    var newitem = document.createElement('div');
    newitem.className = 'cbillrow cbill_row';

    var rvmbtn = '<button type="button" class="rmvitembtn" onclick="deleteitem('+bid+', '+item.value+', '+rowid+')">x</button>';
    var i_id = '<div><span>'+item.value+'</span>'+rvmbtn+'</div>';
    var i_name = '<div>'+item.options[item.selectedIndex].innerText.trim()+'</div>';
    var i_p = '<div><input name="itemp_'+item.value.trim()+'" class="price" type="number" value="'+itemp.value+'" step="0.01" oninput="upd_sub_total()"></div>';
    var i_q = '<div><input name="itemq_'+item.value.trim()+'" class="qty" type="number" value="'+itemq.value+'" step="0.001" oninput="upd_sub_total()"></div>';

    var data0 = 'bill_id='+bid;
    var data1 = 'item_id='+item.value.trim();
    var data = data0+'&'+data1;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../PHP/addnewitem.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        var r = this.responseText;
        if(r == 'Error')
        {
            alert('Cannot Add the item! please reload the page and try again!');
        }
        //var gst = '<div><p class="gst">'+r+'</p></div>';
        var subtval = parseFloat(itemp.value)*parseFloat(itemq.value);
    
        var subt = '<div class="subttl">'+subtval+'</div>';
        newitem.innerHTML = i_id + i_name + i_p + i_q + subt;
        rowholder.appendChild(newitem);
        itemq.value = "";
        upd_sub_total();
    }
    xhr.send(data);
}
/*
Version 2.0 of addnew() */
// function addnew()
// {
//     var item = document.getElementById('additem_id');
//     var itemp = document.getElementById('additem_p');
//     var itemq = document.getElementById('additem_q');
//     //bill id

//     console.log(item.value, itemp.value, itemq.value);
//     var rowcontrol = document.getElementById('itemrows');
//     var rowcnt = rowcontrol.getElementsByClassName('cbillrow cbill_row').length;
//     console.log('Row count: ', rowcnt);
// }

function deleteitem(bid, iid, rowid)
{
    var r = confirm('Are You Sure You Want To Delete This Item?');
    if(!r)
    {
        return 0;
    }
    //some ajax
    //var bid = 0;
    var bid = document.getElementById('bill-id').value;
    var data0 = 'bill_id='+bid;
    var data1 = 'item_id='+iid;
    var data = data0+'&'+data1;
    console.log(data);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../PHP/removebillitem.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        //console.log(data);
        var r = this.responseText;
        console.log(r);
        if(r != '1')
        {
            alert('Cannot Delete the item! please reload the page and try again!');
        }
    }
    xhr.send(data);
    var rid = 'row-'+rowid;
    var delrow = document.getElementById(rid);
    delrow.remove();
    upd_sub_total();
}