<? extract($_GET); 
sleep(1);
?>
<form class="propertyForm" action="dump.php">
 <table>
 <tr>
 <th>Your age: </th>
 <td><INPUT type="text"
    name="user-age" size="2" value=27></td>
 </tr>
 
 <tr>
 <th>Your gender: </th>
 <td><INPUT type="radio" name="user-gender" value="M" id='m'><label for=m>Male</label>
    <INPUT type="radio" name="user-gender" value="F" id='f' checked ><label for='f'>Female</label></td>
    
 </tr>
 
 <tr>
 <th> Check the all names of the people listed whom you have heard
    about:</th>
 <td><INPUT type="checkbox" name="knows-marc" id='knows-marc' checked ><label for=knows-marc >Marc Andreessen </label>
     <INPUT type="checkbox" name="knows-lisa" id=knows-lisa ><label for=knows-lisa >Lisa Schmeiser</label> 
     <INPUT type="checkbox" name="knows-al" id=knows-al checked><label for=knows-al >Al Gore </label></td>
  </tr>
    
  <tr>
  <th>What is your favorite Web browser? </th>
  <td>
     <SELECT name=
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
    </SELECT>
  </td>
  </tr>   

 <tr>
  <th>Which of these ice cream flavors have you tried? </th>
  <td>
   <SELECT name=
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
    </SELECT>
  </td>
  </tr>
  <tr>
 <th>Do you have an informal nickname? </th>
 <td><INPUT type="radio" name="nickname" value="No" id='no'><label for=no>No</label> 
    <INPUT type="radio" name="nickname" value="Yes" checked  id=yes ><label for=yes >Yes, it is: <INPUT type="text"  name="user-nickname" value="pistike" size="12" maxlength="12"></label></td>
  </tr>
  <tr>
 <th colspan=2>Enter your personal motto:</th>
   </tr>
   <tr>
 <td colspan=2><TEXTAREA name="user-motto" rows="2" cols="40">
     All is well that ends well.
    </TEXTAREA></td>
   </tr>
</table>
     <INPUT type="submit" value="Send this survey">
  </FORM>