function block_pagination(pagename,page,dest,sub)
{

if(pagename=='account_details')
{

window.location =pagename+'.php?page='+page+'&id='+dest+'&type='+sub;
}
else
{
window.location =pagename+'.php?page='+page+'&destination='+dest+'&Submit='+sub;
}

}