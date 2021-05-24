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

function checkdate(date1)
{
    var date2 = new Date();
    if(date1 >= date2)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function checktime(time1)
{
    if(time1.localeCompare('09:00') > 0)
    {
        if(time1.localeCompare('21:00') < 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}