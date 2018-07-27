function disableRate(comment)
{
document.getElementById('raterulez_' + comment).style.display='none';
document.getElementById('ratesux_' + comment).style.display='none';
}

function rateComment(comment,rate)
{
//alert(comment);
disableRate(comment);
var curRate=document.getElementById('rate_' + comment).innerHTML;
if(rate=='sux') 
	curRate=curRate-1;
else 
	curRate=curRate-(-1);
document.getElementById('rate_'+comment).innerHTML=curRate;

updateAData2('rateComment',comment,rate,'rate_' + comment);

}