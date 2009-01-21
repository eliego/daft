<html><head><title>{Title}</title>


<link rel="STYLESHEET" href="../CSS/konferens.css" type="TEXT/CSS">

<script language="javascript">
function validate(form) {
	
	if (form.subject.value.length < 1) {
		alert('Fyll i en rubrik');
		return false;
	}

	if (form.subject.value.length > 35) {
		alert('Din rubrik är för lång!');
		return false;
	}
	
	if (form.body.value.length < 1) {
		alert('Fyll i ett meddelande');
		return false;
	}

	return true;
}
</script></head>
<body bgcolor="#335178" topmargin="0" marginheight="0">
<center>
<table width="790" border="0" cellspacing="0" cellpadding="0" height="66">
  <tbody><tr> 
    <td><a href="http://www.daft.t.se/"><img src="../bilder/l.gif" width="124" height="60" border="0"></a>            
	
	
	</td>
  </tr> 
</tbody></table> <table width="790" border="0" cellspacing="0" cellpadding="0" bgcolor="#cccccc"> 
<tbody><tr bgcolor="#f5f5f5"> <td class="nt"> <img src="../bilder/rundning.gif" width="790" height="3" align="top"><br> 
<br>   <span class="normalrubrik">    Daft Konferens    </span> 
<p><br> 

[INCLUDE /home/daft/Templates/MenuAndLogin.tpl AS MaL]

<img src="../bilder/stortavla.gif" width="140" height="350" align="right"> 
<table width="650" border="0" cellspacing="0" cellpadding="0"> <tbody><tr> <td class="nt"> 
<table width="643" border="1" cellspacing="0" cellpadding="0" align="center"> 
<tbody><tr> <td height="30" class="nt" width="435"> <p><b> Skriv Inlägg</b></p> <form name="form" method="post" action="DoNewThread.php" onsubmit="return validate(this)"> 
<p>   Rubrik<br>   <input type="text" name="subject" maxlength="35" size="50" class="inputruta"> 
</p><p>  Inlägg<br>   <textarea name="body" cols="60" wrap="VIRTUAL" rows="10" class="inputruta">{Signature}</textarea> 
<br><input type="submit" name="Submit" value="Posta"> </p></form> 
</td></tr> 
</tbody></table></td></tr> </tbody></table></p></td></tr> </tbody></table><img src="../bilder/rund2.gif" width="790" height="3" align="top"> 
<br>
</center>
</body></html>
