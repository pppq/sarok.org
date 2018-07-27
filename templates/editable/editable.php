<script>
var additionalParams="<?=$additionalParams?>";
var idStyle="<?=$idStyle;?>";
</script>
<div id='editable'>
<div id='panel' >
<span id=buttons>
<a href='javascript:void(0)' class='control_b' name='control_b' tabindex="-1" onclick='rteCommand("bold", null)'></a>
<a href='javascript:void(0)' class='control_i' name='control_i' tabindex="-1" onclick='rteCommand("italic", null)'></a>
<a href='javascript:void(0)' class='control_u' name='control_u' tabindex="-1" onclick='rteCommand("underline", null)'></a>
<a href='javascript:void(0)' class='control_strike' name='control_strike' tabindex="-1" onclick='rteCommand("StrikeThrough",null)'></a>

<a href='javascript:void(0)' class='control_separator' tabindex="-1"></a>

<a href='javascript:void(0)' class='control_subscript' name='control_subscript' tabindex="-1" onclick='rteCommand("Subscript",null)'></a>
<a href='javascript:void(0)' class='control_supscript' name='control_supscript' tabindex="-1" onclick='rteCommand("Superscript",null)'></a>

<a href='javascript:void(0)' class='control_separator' tabindex="-1"></a>

<a href='javascript:void(0)' class='control_ordered' name='control_ordered' tabindex="-1" onclick='rteCommand("InsertOrderedList",null)'></a>
<a href='javascript:void(0)' class='control_unordered' name='control_unordered' tabindex="-1" onclick='rteCommand("InsertUnOrderedList",null)'></a>

<a href='javascript:void(0)' class='control_separator' tabindex="-1"></a>

<a href='javascript:void(0)' class='control_left' name='control_left' tabindex="-1" onclick='rteCommand("JustifyLeft",null)'></a>
<a href='javascript:void(0)' class='control_center' name='control_center' tabindex="-1" onclick='rteCommand("JustifyCenter",null)'></a>
<a href='javascript:void(0)' class='control_right' name='control_right' tabindex="-1" onclick='rteCommand("JustifyRight",null)'></a>

<a href='javascript:void(0)' class='control_separator' tabindex="-1"></a>

<a href='javascript:void(0)' class='control_indent' name='control_indent' tabindex="-1" onclick='rteCommand("Indent",null)'></a>
<a href='javascript:void(0)' class='control_unindent' name='control_unindent' tabindex="-1" onclick='rteCommand("Outdent",null)'></a>
<a href='javascript:void(0)' class='control_unindent' name='control_unindent' tabindex="-1" onclick='rteCommand("InsertHorizontalRule","cut")'></a>
<a href='javascript:void(0)' class='control_link' name='control_link' tabindex="-1" onclick='popUpURL()' id="control_link"></a>
</span>
<div class=popup id=popup_url ><input type=hidden id='control_url_hidden' />URL:<input type=text id='control_url' value="http://" /><input type=button onclick="addLink(document.getElementById('control_url').value,document.getElementById('control_url_hidden').value)" ></div>
</div>
<iframe id='innereditable' contenteditable="true"  marginwidth='0' marginheight='0' hspace=0 vspace=0 frameborder=0 align='left' style='float: none'></iframe>

<textarea id='innereditable_textarea' name='<?=$name;?>' onkeypress='javascript:isDirty=true'><?=$value;?></textarea>
<div id='innereditable_preview' class="<?=$idStyle;?>" style='display:none;margin:0px;'></div>
<span class="switchButton"> <a href='javascript:void(0)' id='mode_switch_text' onclick="switchStyles('text')" title="Körűlbelűl úgy néz ki majd a dolog"  tabindex='-1'>&lt;TEXT&gt;</a>
<a href='javascript:void(0)' id='mode_switch_html' onclick="switchStyles('html')" title="A jó régi html kód" tabindex='-1'>&lt;HTML&gt;</a>
<a href='javascript:void(0)' id='mode_switch_preview' onclick="switchStyles('preview')" title="Hát hogy hogy fog kinézni mindez az oldalon, cserékkel, mindennel, tutifix"  tabindex='-1'>Előnézet</a> </span>
</div>