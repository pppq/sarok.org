<? require 'header.php' ?>

<FORM Method="POST" Action="list.php?getval=TEST" class="filterForm" >
   <P>
     <INPUT type="hidden" name="recipient" value="nobody@december.com">

    <INPUT type="hidden" name="subject" value="Level 2 HTML Form +">
    <INPUT type="hidden" name="realname" value="UnknownRealName"> <INPUT type= 
    "hidden" name="email" value="UnknownEmail"> Your age: <INPUT type="text"
    name="user-age" size="2"><BR>
     Your gender: <INPUT type="radio" name="user-gender" value="M">Male
    <INPUT type="radio" name="user-gender" value="F">Female<BR>
     Check the all names of the people listed whom you have heard
    about:<BR>
     <INPUT type="checkbox" name="knows-marc">Marc Andreessen <INPUT
    type="checkbox" name="knows-lisa">Lisa Schmeiser <INPUT type= 
    "checkbox" name="knows-al">Al Gore <INPUT type="checkbox" name=
    "knows-bbg">Boutros Boutros-Ghali<BR>

     What is your favorite Web browser? <SELECT name=
    "favorite-web-browser">
     <OPTION>
      Arena
     </OPTION>
     <OPTION>
      Cello
     </OPTION>
     <OPTION>
      Chimera
     </OPTION>

     <OPTION>
      Lynx
     </OPTION>
     <OPTION>
      MacWeb
     </OPTION>
     <OPTION>
      Mosaic
     </OPTION>
     <OPTION selected>

      Netscape
     </OPTION>
     <OPTION>
      SlipKnot
     </OPTION>
     <OPTION>
      Viola
     </OPTION>
     <OPTION>
      Web Explorer
     </OPTION>

     <OPTION>
      None of the above
     </OPTION>
    </SELECT><BR>
     Which of these ice cream flavors have you tried? <SELECT name=
    "triedicecream[]" multiple size="3">
     <OPTION value="conservative">
      Vanilla
     </OPTION>
     <OPTION value="choco">

      Chocolate
     </OPTION>
     <OPTION value="cherry">
      Cherry Garcia
     </OPTION>
     <OPTION value="strange">
      Pizza Pancake
     </OPTION>
    </SELECT><BR>
     Guess the secret password: <INPUT type="password" name=
    "password-guess"><BR>

     Do you have an informal nickname? <INPUT type="radio" name=
    "nickname" value="No" checked>No <INPUT type="radio" name=
    "nickname" value="Yes">Yes, it is: <INPUT type="text" disabled name=
    "user-nickname" size="12" maxlength="12"><BR>
     Enter your personal motto:<BR>
     <TEXTAREA name="user-motto" rows="2" cols="40">
     All is well that ends well.
    </TEXTAREA><BR>
     When you are done with the above responses, please submit this
    information by clicking on your current geographic location on this
    map:<BR>
    
     <INPUT type="submit" value="Send this survey"> <BR>

   </P>
  </FORM>
  
  Whatever.
<? 
//print_r($_GET);
extract($_POST);
$total=23;
$total=isset($_GET["total"])?$_GET["total"]:$total;
if(isset($curPageNum))
{
$startPage=max(0,$curPageNum*$itemsPerPage); 
$endPage=min(($curPageNum+1)*$itemsPerPage,$total);
}
else
{
	$startPage=0;
	$endPage=$total;
}
if(isset($itemID) and is_numeric($itemID))
{
	$startPage=$itemID-1;
	$endPage=$itemID;
	$total=1;
}
//$endPage=$total;
?>
<table class=listing title='users'>
<thead>
<tr>
<th>UserID</th>   
<td>Name</td>
<td>Email</td>
</tr>
</thead>
<?
for($i=$startPage+1;$i<$endPage+1;$i++)
{
	?>
<tbody id="<?=$i;?>">
<tr>
<th>user<?=$i;?>@accre.com</th>
<td>Name <?=$i;?></td>
<td>email<?=$i;?>@email.com</td>
</tr>
</tbody>

<? } ?>
</table>
<!-- 
<table class=listing title='users'>
<thead>
<tr>
<th>UserID</th>   
<td>Name</td>
<td>Email</td>
</tr>
</thead>
<?
for($i=$startPage+1;$i<$endPage+1;$i++)
{
	?>
<tbody id="<?=$i;?>">
<tr>
<th>user<?=$i;?>@accre.com</th>
<td>Name <?=$i;?></td>
<td>email<?=$i;?>@email.com</td>
</tr>
</tbody>

<? } ?>
</table>

-->

<? require 'footer.php' ?>