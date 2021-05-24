var global_row_count = 1;

function test()
{
    alert('Hello world');
}

/*
function add_new_item(item_list)
{
    var item_table = document.getElementById("item_list");
    var row_count = item_table.rows.length;
    var ins_index = row_count-1;
    var new_row = item_table.insertRow(ins_index);
    new_row.id = "itemno"+ins_index.toString();
    
    var new_col1 = new_row.insertCell(0);
    new_col1.className = "col1";
    var new_col2 = new_row.insertCell(1);
    new_col2.className = "col2";
    
    var select_id = "item"+ins_index.toString();

    new_col1.innerHTML = '<select id="'+select_id+'"></select>';
    new_col2.innerHTML = '<input id="item_qty'+ins_index.toString()+'" type="number" maxlength="6">';
    generate_item_list(select_id, item_list);
    add_rmv_btn(ins_index);
}
*/
//version 2
function add_new_item(item_list, item_id_list)
{
    var item_table = document.getElementById("item_list");
    var row_count = item_table.rows.length;
    var ins_index = row_count-1;
    var new_row = item_table.insertRow(ins_index);
    
    var item_index = global_row_count;

    new_row.id = "itemno"+item_index.toString();
    
    var new_col1 = new_row.insertCell(0);
    new_col1.className = "col1";
    var new_col2 = new_row.insertCell(1);
    new_col2.className = "col2";
    
    var select_id = "item"+item_index.toString();
    var qty_id = "item_qty"+item_index.toString();
    var select_name = "item-"+item_index.toString();
    var qty_name = "item_qty-"+item_index.toString();

    new_col1.innerHTML = '<select id="'+select_id+'" name="'+select_name+'"></select>';
    new_col2.innerHTML = '<input id="'+qty_id+'" type="number" name="'+qty_name+'" min="0.001" max="999999" step="0.001" required>';
    generate_item_list(select_id, item_list, item_id_list);
    add_rmv_btn(item_index);
    global_row_count++;
}


/*
function generate_item_list(select_id)
{        
    var select = document.getElementById(select_id);
    var options = ["1", "2", "3", "4", "5"];
    for(var i = 0; i < options.length; i++) {
        var opt = options[i];
        var el = document.createElement("option");
        el.textContent = opt;
        el.value = opt;
        select.appendChild(el);
    }
}
*/
//version 2
function generate_item_list(select_id, item_list, item_id_list)
{        
    var select = document.getElementById(select_id);
    var dis_el = document.createElement("option");
    dis_el.textContent = "Choose an item";
    dis_el.value = "";
    dis_el.disabled = true;
    dis_el.selected = true;
    select.appendChild(dis_el);
    for(var i = 0; i < item_list.length; i++) {
        var opt = item_list[i];
        var el = document.createElement("option");
        el.textContent = opt;
        el.value = item_id_list[i];
        select.appendChild(el);
    }
}

function add_rmv_btn(item_id)
{
    let rmvbtn = document.createElement("button");
    rmvbtn.innerHTML = "x";
    rmvbtn.className = "rmvbtn";
    rmvbtn.setAttribute("type", "button");
    var par1 = 'rmv_item("itemno'+item_id+'")';
    rmvbtn.setAttribute("onclick", par1);
    var par2 = 'item'+item_id;
    var prvs_sib = document.getElementById(par2);
    var prnt = prvs_sib.parentNode;
    prnt.insertBefore(rmvbtn, prvs_sib);
}

function rmv_item(item_id)
{
    var ele = document.getElementById(item_id);
    ele.parentElement.removeChild(ele);
}