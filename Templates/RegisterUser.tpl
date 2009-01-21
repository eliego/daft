<html>
<head>
<title>{Title}</title>
<link rel="STYLESHEET" href="../CSS/konferens.css" type="TEXT/CSS">
</head>
<body bgcolor="#335178" topmargin="0" marginheight="0">
<center>
<table width="790" border="0" cellspacing="0" cellpadding="0" height="66">
  <tr> 
    <td><A HREF="/"><IMG SRC="../bilder/l.gif" WIDTH="124" HEIGHT="60" BORDER="0"></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	
	
	</td>
  </tr> 
</table> <table width="790" border="0" cellspacing="0" cellpadding="0" bgcolor="#CCCCCC"> 
<tr bgcolor="#F5F5F5"> 
    <td class="nt"> <img src="../bilder/rundning.gif" width="790" height="3" align="top"><br>
      <br>
      &nbsp;&nbsp;<span class="normalrubrik">&nbsp;&nbsp;&nbsp;&nbsp;Daft Konferens&nbsp;&nbsp;&nbsp;&nbsp;</span> 
      <p><img src="../bilder/stortavla.gif" width="140" height="350" align="right"><br>
        
[INCLUDE /home/daft/Templates/MenuAndLogin.tpl AS MaL]

      <table width="650" border="0" cellspacing="0" cellpadding="0"> 
<tr> <td class="nt"> 
            <table width="640" border="1" cellspacing="0" cellpadding="0" align="center">
              <tr> 
                <td height="30" class="nt" colspan="2"><b>&nbsp;H&auml;r registrerar 
                  du dig</b></td>
              </tr>
              <tr> 
                <td class="nt" colspan="2" valign="top">
<script language="javascript">
function validate(form) {
	if (form.Name.value.length < 1) {
		alert('Fyll i ditt alias!');
		return false; }
		
	if (form.Name.value.length > 20) {
		alert('Du har för långt alias!');
		return false; }
		
	if (form.RealName.value.length < 1) {
		alert('Fyll i ditt riktiga namn!');
		return false; }
		
	if (form.RealName.value.length > 60) {
		alert('Du har för långt namn!');
		return false; }

	if (form.Email.value.length < 1) {
		alert('Fyll i din e-postadress!');
		return false; }
		
	if (form.Email.value.length > 50) {
		alert('Du har för lång e-postadress!');
		return false; }
	
	if (form.Email.value != form.Email2.value) {
		alert('Dina e-postadresser matchar inte!');
		return false; }
		
	if (form.Signature.value.length > 255) {
		alert('Din signatur är för lång!');
		return false; }
		
	if (form.Age.value > 999) {
		alert('Du är för gammal!');
		return false; }
		
	if (form.HomePage.value.length > 250) {
		alert('Du har för lång adress till din hemsida!');
		return false; }
	
	if (form.Other.value.length > 10000) {
		alert('Du har skrivit för mycket om dig själv!');
		return false; }

	return true;
}

</script>
<form method="post" action="DoRegisterUser.php" name="reg" onsubmit="return validate(this)">
  <table width="617" border="0">
     
    <tr> 
      <td class="nt" valign="top" width="72">Alias: </td>
      <td class="nt" width="535"> 
        <input type="text" name="Name" class="nt" size="30" value="" maxlength="20">
        Obligatoriskt </td>
    </tr>
    <tr> 
      <td class="nt" valign="top" width="72">Namn:</td>
      <td class="nt" width="535"> 
        <input type="text" name="RealName" class="nt" size="30" value="" maxlength="60">
        Obligatoriskt </td>
    </tr>
    <tr> 
      <td class="nt" valign="top" width="72">E-post:</td>
      <td class="nt" width="535"> 
        <input type="text" name="Email" class="nt" size="30" value="" maxlength="50">
        Obligatoriskt </td>
    </tr>
    <tr> 
      <td class="nt" width="72">Upprepa<br>
        E-post</td>
      <td class="nt" width="535"> 
        <input type="text" name="Email2" class="nt" size="30" maxlength="50">
        Beh&ouml;vs f&ouml;r att vi ska kunna skicka ditt l&ouml;senord. </td>
    </tr>
    <tr> 
      <td class="nt" width="72">Signatur:</td>
      <td class="nt" width="535"> 
        <textarea name="Signature" class="nt" cols="55" rows="3" maxlength="255"></textarea>
      </td>
    </tr>
    <tr> 
      <td class="nt" valign="top" width="72">&Aring;lder</td>
      <td class="nt" width="535"> 
        <input type="text" name="Age" class="nt" maxlength="3" size="2" value="">
        &aring;r </td>
    </tr>
    <tr> 
      <td class="nt" valign="top" width="72">Hemsida:</td>
      <td class="nt" width="535"> 
        <input type="text" name="HomePage" class="nt" maxlength="150" size="30" value="">
      </td>
    </tr>
    <tr> 
      <td class="nt" valign="top" width="72">&Ouml;vrigt:</td>
      <td class="nt" width="535"> 
        <textarea name="Other" class="nt" cols="55" rows="5" maxlength="1000"></textarea>
      </td>
    </tr>
    <tr> 
      <td class="nt" width="72">&nbsp;</td>
      <td class="nt" width="535"> 
        <input type="submit" name="Register" value="Registrera" class="nt>
      </td>
    </tr>
  </table>
  <table width="617" border="0">
  </table>
</form>
<br>
                  <p>&nbsp; 
                </td>
              </tr>
            </table>
          </td></tr> 
</table></td></tr> </table><img src="../bilder/rund2.gif" width="790" height="3" align="top"> 
<br>
</center>
</body>
</html>
