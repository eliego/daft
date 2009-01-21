<html>
<head>
<title>{title}</title>
<link rel="STYLESHEET" href="../CSS/konferens.css" type="TEXT/CSS">

</head>

<body bgcolor="#335178" topmargin="0" marginheight="0">
<center>
<table width="790" border="0" cellspacing="0" cellpadding="0" height="66">
  <tr> 
    <td><A HREF="../"><IMG SRC="../bilder/l.gif" WIDTH="124" HEIGHT="60" BORDER="0"></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	
	
	</td>
  </tr> 
</table> 
<table width="790" border="0" cellspacing="0" cellpadding="0" bgcolor="#CCCCCC">
  <tr bgcolor="#F5F5F5"> 
    <td class="nt"> <img src="../bilder/rundning.gif" width="790" height="3" align="top"><br>
      <br>
      &nbsp;&nbsp;<span class="normalrubrik">&nbsp;&nbsp;&nbsp;&nbsp;Daft Konferens&nbsp;&nbsp;&nbsp;&nbsp;</span> 
      <p><br>
        
[INCLUDE /home/daft/Templates/MenuAndLogin.tpl AS MaL]

      <table width="790" border="1" cellspacing="0" cellpadding="0">
        <tr> 
            
          <td  class="nt" width="640" valign="top" > 
            
<table width="100%" border="0" cellspacing="0" cellpadding="3">
[BLOCK Empty]
[END Empty]
[BLOCK MultiPage]
<a href="ShowThread.php?ID={id}&Page={Page}">{Page}</a>
[END MultiPage]
[BLOCK Orig]
  <tr bgcolor="#CCCCCC" class="nt"> 
    <td class="nt" width="55%"><b>Rubrik:</b> {Show_Thread_Heading}</td>
    <td class="nt" width="45%"><b>Datum:</b> {Show_Thread_Date}</td>
  </tr>
  <tr bgcolor="#CCCCCC" class="nt"> 
    <td class="nt" width="55%"><b>Av:</b> 
    <img src='../bilder/{Thread_Creater_Status}.gif' width='9' height='7'> 
      <a href="ShowUser.php?ID={Show_Thread_Writer_ID}">{Show_Thread_Writer_Name} 
      </a>  
    </td>
    <td class="nt" width="45%">L&auml;st:<b>{Reads}</b> 
      Svar:<b>{Show_Thread_Answers}</b> 
      &Aring;lder:<b> {Show_Thread_Old}</b> 
      dagar</td>
  </tr>
  <tr class="nt"> 
    <td colspan="2" class="nt"><span class="nt12">{Show_Thread_Writer_Say}</span></td>
  </tr>
</table>

<br>
<br>
[END Orig]
<table width="100%" border="0" cellspacing="0" cellpadding="3">
[BLOCK Posts]   
  <tr bgcolor="#CCCCCC" class="nt"> 
    <td class="nt" width="54%"><b>Svar:</b> 
      <img src='../bilder/{Post_User_Status}.gif' width='9' height='7'>
      <a href="ShowUser.php?ID={Show_User_ID}"> 
      {Show_User_Name}
      </a> 
      
    </td>
    <td class="nt" width="45%"><b>Datum:</b> 
      {Show_Post_Date}
    </td>
  </tr>
  <tr class="nt"> 
    <td colspan="2" class="nt"><span class="nt12">
      {Show_Thread_Say}</span><p>&nbsp;</p>
    </td>
  </tr>
[END Posts]
</table>
[BLOCK Empty2]
[END Empty2]
[BLOCK MultiPage2]
<a href="ShowThread.php?ID={id}&Page={Page}">{Page}</a>
[END MultiPage2]
<p></p>
<hr noshade>
[BLOCK CantPost]
Du måste vara inloggad för att svara på ett inlägg
[END CantPost]
<br>
          </td>
            <td  width="150" class="nt" valign="top">&nbsp; 
            
            </td>
        </table>
      
        <br>
        
<span class="nt">
Just nu &auml;r det: {Online} st online<br>
Det tog {Timer} sekunder att generera sidan.
Daft Konferens är släppt under GPL. För att se info, tryck <A HREF="COPYING">här</A>.<br>
För att ladda ner Daft Konferens i ZIP-format, tryck <A HREF="daftkonferens.zip">här</A>.</span>
      </td>
</tr> 
</table>
<img src="../bilder/rund2.gif" width="790" height="3" align="top"> 
</body>
</html>

[SET Show_User_Online]
      <img src='../bilder/on.gif' width='9' height='7'>
[END Show_User_Online]
[SET NewPost]
<form name="form1" method="post" action="Post.php?ID={id}">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody><tr>
      <td width="63%">
        <p><b>  Skriv ett svar</b>
            
        </p>
        <p><b>  Inlägg</b><br>
            
          <textarea name="body" cols="50" wrap="VIRTUAL" rows="10">{User_Signature}</textarea>
        </p>
        <p>   
          <input type="submit" name="Submit" value="Svara">
        </p>
</td>
    </tr>
  </tbody></table>
  </form>
[END NewPost]
[SET MultiPages]
<FONT COLOR="#CC0000">Flera sidor</FONT>
[END MultiPages]
[SET Tom]
[END Tom]
