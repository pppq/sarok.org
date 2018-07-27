<form class='settings' action="/registration/step2/" method="post" enctype="multipart/form-data" onsubmit="this.style.visibility='hidden'">
<h1>Regisztráció</h1>
<fieldset>
<legend>Kötelező adatok</legend>
<table>
<tr>
<td class='key'>Belépési neved:</td>
<td class='value'>
  <input type="text" name="login" id="login1" value="<?=$login;?>" maxLength=15 onblur="updateAData('checkLogin',this.value,'login_info')"><br />
  <label for="login1" class=info id=login_info>Legalabb ket betus kell legyen, nem tartalmazhat egy csomo karaktert, pl. pontokat es ekezeteket</label>
</td>
</tr>
<tr>
<td class='key'>Email cimed:</td>
<td class='value'>
  <input type="text" name="email" id="email1" maxLength=45 onblur="updateAData('checkEmail',this.value,'email_info')"><br />
  <label for="email1" class=info id=email_info>Emailed, amelyre majd jon a jelszo</label>
</td>
</tr>
<tr>
<td class='key'>Jelszavad:</td>
<td class='value'>
  <input type="password" name="pass1" id="p1"/><br />
  <label for="p1" class=info id=pass_info>a ket jelszonak meg kellene jegyezni</label>
</td>
</tr>
<tr>
<td class='key'>Jelszavad még egyszer:</td>
<td class='value'>
  <input type="password" name="pass2" id="p2" onblur="updateAData2('checkPass',this.value,document.getElementById('p1').value,'pass_info')"/><br />
</td>
</tr>
<tr>
<td class='key'>Van/volt blogod máshol?</td>
<td class='value' colspan=2 >
  <input type="checkbox" name="blogger" value="Y" id="blogger" class="checkbox" /><label for="blogger" class="info">Ha ezt itt most bepipálod, akkor regisztráció után rögtön a blogimport/rss beállitásokhoz ugrasz. Ha nem pipálod be, akkor a főlapra. De utána bármikor visszamehetsz a blogimport/rss beállitásokhoz</label>
</td>
</tr>
<tr>
<td class='value' colspan=2>
  <div class=agreement>
  <h3>Szabalyzat</h3>
  <ol>
  <li>Megprobalok erdekesen irni. Vagy csak baratoknak.</li>
  <li>Nem irok torvenyellenes dolgokat.</li>
  <li>Nem bantok regisztralt tagokat. Meg ha szemetek, akkor sem.</li>
  <li>A weboldal fenntartoja indokolt esetben megnezheti az adataimat, nem publikus bejegyzeseimet, privat uzeneteimet. A weboldal fenntartoja nem irhat az en nevemben, nem adhatja ki harmadik felnek az adataimat, nem pletykalhatja el oket, stb.</li>
  <li>A weboldal fenntartoja es a moderatorok indokolt esetben torolhetnek engem vagy a bejegyzeseimet, esetleg kitilthatjak engem.</li>
  <li>A torolt dolgaim meg egy honapig maradnak a szerveren rejtett formaban, egy honapig meg visszaallithatom oket.</li>
  <li>A weboldal nem vallal felelosseget az adatvesztesert. Ez egy ingyenes szoglaltatas</li>
</ol>
  </div>
  <input type="checkbox" name="giveYourSoulToMe" value="YES" id="soul" class="checkbox" onfocus="document.getElementById('submit_button').disabled=!this.checked;" onchange="document.getElementById('submit_button').disabled=!this.checked;" onblur="document.getElementById('submit_button').disabled=!this.checked;"/><label for="soul">Elolvastam, megertettem es elfogadtam a szabalyzatot, elmultam mar 19,5 eves, es jofej vagyok.</label>
</td>
</tr>
</table>
</fieldset>
<input type=submit name=submit value="Regisztrálok, viszlát offline élet!" disabled id="submit_button">
<!--<fieldset>
<legend>Ha mar van blogod mashol (nem kötelező adatok)</legend>
Ezeket az adatokat siman be tudod potolni kesobb, a beallitasok megfelelo menujeben.
<table>
<tr>
<td class='key'>Van mar blogom freeblogon vagy blogspoton, es athozom ide:</td>
<td class='value'>
  <input type="file" name="blog" id="blog1" ><br />
  <label for="blog1" class=info id=blog_info>Add meg azt az allomanyt, amit megkaptad a regi blogod exportalaskor.</label>
</td>
</tr>
<tr>
<td class='key'>Van mar blogom valahol mashol, csak megadom az RSS cimet:</td>
<td class='value'>
  <input type="text" name="rss" id="rss1"><br />
  <label for="rss1" class=info id=rss_info>A blogod rss cime. Ilyenkor a sarok.org-os blogod frissul, ha irsz egy bejegyzest a regi blogodra. Azert jo, mert tovabbra is a regi helyeden vezetheted a blogodat, kozben itt is megjelenik</label>
</td>
</tr>
</table>
</fieldset>-->
</form>


