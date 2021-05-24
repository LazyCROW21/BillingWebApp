function openurl(path)
{
    //var full_path = "C://xampp/htdocs/Website_v2/Admin_Panel/"+path;
    window.open(path, '_blank');
}

//Logout btn

function toggleACList()
{
    var acntlist = document.getElementById('account');
    if(!acntlist.hasAttribute('show'))
    {
        acntlist.setAttribute('show', 'true');
    }
    else if(acntlist.getAttribute('show') == 'true')
    {
        acntlist.setAttribute('show', 'false');
    }
    else
    {
        acntlist.setAttribute('show', 'true');
    }
}

function openslidebar()
{
    var slidebar = document.getElementById("slidebar");
    slidebar.setAttribute('Show', "true");
}

function closeslidebar()
{
    var slidebar = document.getElementById("slidebar");
    slidebar.setAttribute('Show', "false");
}

// For Viewing Bill
function openbill(id)
{
    if(typeof(id) != 'string')
    {
        console.log('Not a string');
        return -1;
    }
    var id_index = id.lastIndexOf('-');
    id_index++;
    var bill_id = parseInt(id.substr(id_index), 10);
    var data = "bill_id="+bill_id;
        
    var cw = document.getElementById("conf_wrap");
    cw.style.zIndex = 2;
    cw.style.display = 'block';

    var box = document.getElementById("confirmbox");
    box.style.display = 'block';
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../PHP/confirmbill.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        //console.log(data);
        box.innerHTML = box.innerHTML + this.responseText;
    }
    xhr.send(data);
}

function closebox()
{
    var cw = document.getElementById("conf_wrap");
    cw.style.zIndex = 0;
    cw.style.display = 'none';
    var box = document.getElementById("confirmbox");
    box.style.display = 'none';
    box.innerHTML = '<button class="box-x" onclick="closebox()">X</button>';
}

function rejectbill(id)
{
    if(typeof(id) != 'string')
    {
        return -1;
    }
    var r = confirm("Are you sure you want to delete?");
    if(r)
    {
        var id_index = id.lastIndexOf('-');
        id_index++;
        var bill_id = parseInt(id.substr(id_index), 10);
        var data = "bill_id="+bill_id;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../PHP/rejectbill.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            //console.log(this.responseText);
            var resp = this.responseText;
            if(resp != '1')
            {
                alert('Cannot Delete! Try Again or Contact support');
            }
            else if(resp == '1')
            {
                alert('Bill Deleted Successfully');
                window.location.reload();
            }
        }
        xhr.send(data);
    }
    else
    {
        return 0;
    }
}
// Till here

// Opens Item update details
function edititem(id)
{
    var wrap = document.getElementById('upd_wrap');
    wrap.style.display = "block";
    wrap.style.zIndex = 3;

    var box = document.getElementById('upd_box');
    box.style.display = "block";
    box.style.zIndex = 3;
    
    var data = 'item_id='+id;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../PHP/edititem.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        //console.log(this.responseText);
        upd_box.innerHTML = upd_box.innerHTML + this.responseText;
    }
    xhr.send(data);
}

function closeEditMenu()
{
    var wrap = document.getElementById('upd_wrap');
    wrap.style.display = "none";
    wrap.style.zIndex = 0;

    var box = document.getElementById('upd_box');
    box.style.display = "none";
    box.style.zIndex = 3;
    box.innerHTML = '<div><button id="closeEdit" onclick="closeEditMenu()">x</button></div>';
}

function del_warn()
{
    var r = confirm('Are you sure you want to delete this item?');
    if(r)
    {
        return true;
    }
    else
    {
        return false;
    }
}
// Till here

// prints bill
function printbill(id, type)
{
    if(type==0)
    {
        window.open('../PHP/printbill.php?bill_id='+id+'&size=A4');
    }
    else if(type==1)
    {
        window.open('../PHP/printbill.php?bill_id='+id+'&size=3inch');
    }
}

// For item paging

function changepage(pn, currpg)
{
    var maxr = document.getElementById('maxrow').value;
    var maxpg = Math.ceil(parseInt(maxr)/20);
    if(pn == -1)
    {
        if(currpg == 1)
        {
            var prvbtn = document.getElementById('pbtn');
            prvbtn.disabled = true;
        }
        else
        {
            var newurl =  window.location.href;
            var indexofq = newurl.lastIndexOf('?');
            if(indexofq != -1)
            {
                newurl = newurl.slice(0, indexofq);
            }
            var nxtpg = parseInt(currpg) - 1;
            newurl = newurl+'?setpg='+nxtpg;
            window.location.replace(newurl);

            var nxtbtn = document.getElementById('nbtn');
            nxtbtn.disabled = false;
        }
    }
    else if(pn == 1)
    {
        if(currpg >= maxpg)
        {
            var nxtbtn = document.getElementById('nbtn');
            nxtbtn.disabled = true;
        }
        else
        {
            var newurl =  window.location.href;
            var indexofq = newurl.lastIndexOf('?');
            if(indexofq != -1)
            {
                newurl = newurl.slice(0, indexofq);
            }
            var nxtpg = parseInt(currpg) + 1;
            newurl = newurl+'?setpg='+nxtpg;
            window.location.replace(newurl);

            var prvbtn = document.getElementById('pbtn');
            prvbtn.disabled = false;
        }
    }
}


//Editing Customer

function editcust(cid)
{
    var wrap = document.getElementById('editwrap');
    wrap.style.display = "block";
    wrap.style.zIndex = 3;

    var box = document.getElementById('editcustbox');
    box.style.display = "block";
    box.style.zIndex = 3;

    var data = 'cid='+cid;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../PHP/editcust.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        //console.log(this.responseText);
        box.innerHTML = box.innerHTML + this.responseText;
    }
    xhr.send(data);
}
function closeeditcust()
{
    var wrap = document.getElementById('editwrap');
    wrap.style.display = "none";
    wrap.style.zIndex = 0;

    var box = document.getElementById('editcustbox');
    box.style.display = "none";
    box.style.zIndex = 0;
    box.innerHTML = '<img src="../img/closeEditCustBtn.jpg" alt="closebtn" id="closecustebox" onclick="closeeditcust()">';
}


//Email Invoice
function emailbill(bid)
{
    var data = 'bid='+bid;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../PHP/emailbill.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if('Message has been sent' != this.responseText)
        {
            alert('Error in sending mail! please check email address or customer entry');
        }
    }
    xhr.send(data);
}

//paging in customer page
function chncustpage(curpage, chng)
{
    var newurl =  window.location.href;
    var indexofq = newurl.lastIndexOf('?');
    if(indexofq != -1)
    {
        newurl = newurl.slice(0, indexofq);
    }

    if(chng == 1)
    {
        var nxtpg = parseInt(curpage + 1);
        newurl = newurl+'?page='+nxtpg;
        window.location.replace(newurl);
    }
    else if(chng == -1)
    {
        var nxtpg = parseInt(curpage - 1);
        newurl = newurl+'?page='+nxtpg;
        window.location.replace(newurl);
    }
}