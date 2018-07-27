
<?
global $honapok;
?>
<div class=banner>
<h1>Statistika</h1>
</div>
Hello!<br /><br /> Már <b><?=(int)sizeof($monthList);?></b> hónapja vagy itt regisztálva, azota ősszesen <b><?=(int)$blogStat["selfComments"];?></b> hozzászolást, 
<b><?=(int)$blogStat["entries"]["total"];?></b> bejegyzést irtál, 
ebböl  <b><?=$blogStat["entries"]["ALL"];?></b> bejegyzés publikus, 
<b><?=(int)$blogStat["entries"]["REGISTERED"];?></b> csak regisztáltaknak szol, 
<b><?=(int)$blogStat["entries"]["FRIENDS"];?></b> meg csak csak baratoknak. 
Van még <b><?=(int)$blogStat["entries"]["PRIVATE"];?></b> nagyon vicces privát bejegyzésed.<br />

Legutólsó feldolgozott rekord dátuma: <?=$lastCollectionDate; ?> <br/><br/>
<!--
Összesen <b><?=(int)$blogStat["comments"];?></b> hozzászolást irták a bejegyzéseidhez.<br /><br /> -->

Nézzük csak részletesebben.<br /> <select name="yearmonth" onchange='updateAData("getUserMonthStat",this.value,"statsbody");document.getElementById("statsbody").innerHtml="<span class=info >Aki megnyomta, az buzi!</span>"'>
<option value="">Válassz magadnak egy hónapot</option>
<?
$s=" ";
foreach($monthList as $my)
{
	$s="<option value='{$my[0]}-{$my[1]}' >".$my[0]." ".$honapok[$my[1]]."</option>\n".$s ;
}
echo $s;
?>
</select>
<br /><br />
<div id=statsbody >
</div>