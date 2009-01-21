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
            <td class="nt" width="640">

              <table border="0" cellspacing="0" cellpadding="0" width="640">
                <form name="form1" method="get" action="ThreadList.php?Page={Page}&Sort={Sort}">
                  <font size="4"><b> Alla ämnen</b></font>&nbsp;&nbsp; 
                </form>
                <tr> 
                  <td colspan="2" class="nt">&nbsp;</td>
                  <td colspan="3" class="nt"><a href="ThreadList.php?Page={Page_Prev}&Sort={Sort}">Föregående</a> Nuvarande sida: {Page} <a href="ThreadList.php?Page={Page_Next}&Sort={Sort}">Nästa</a></td>
                </tr>
                <tr> 
                  <td width="277" class="nt" bgcolor="#CCCCCC"><b>&nbsp;Rubrik</b></td>
                  <td width="115" class="nt" bgcolor="#CCCCCC"><b>Inlagt av</b></td>
                  <td width="115" class="nt" bgcolor="#CCCCCC"><b>Sista inl&auml;gg</b></td>
                  <td width="74" class="nt" bgcolor="#CCCCCC"><a href="ThreadList.php?Page={Page}&Sort=LastTimestamp">Tid</a></td>
                  <td width="59" class="nt" bgcolor="#CCCCCC"><a href="ThreadList.php?Page={Page}&Sort=Posts">Svar</a>/<a href="ThreadList.php?Page={Page}&Sort=Reads">L&auml;st</a></td>
                </tr>
[BLOCK Threads]
                <tr class="{Thread_Color}">
                  <td width="277">
                  <img src="../bilder/nytt.gif" STYLE="Visibility: {New}">
                <a href="ShowThread.php?ID={Thread_ID}">{Thread_Rubrik}</a> <a href="ShowThread.php?ID={Thread_ID}" target="_blank"><img src="../bilder/w.gif" border="0"></a></td>  
				<td width="115">
                  <img src="../bilder/{User_Status}.gif">
                  <a href="ShowUser.php?ID={Thread_User_ID}">{Thread_User_Name}</a></td>
                  <td width="115">
                  <img src="../bilder/{Last_User_Status}.gif">
                  <a href="ShowUser.php?ID={Thread_Last_ID}">{Thread_Last_Name}</a></td>
                  <td width="74">{Thread_Last_Time}</td>
                  <td width="59" align="center">{Thread_Answers}/{Thread_Reads}</td>
                </tr>
[END Threads]
              </table>
[SET Thread_User_Online]
<img src="../bilder/on.gif">
[END Thread_User_Online]
 
<br><span class="nt">
<a href="ThreadList.php?Page={Page_Prev}&Sort={Sort}">Föregående</a> Nuvarande sida: {Page} <a href="ThreadList.php?Page={Page_Next}&Sort={Sort}">Nästa</a> </span>
</td> 
            <td  width="150" class="nt" valign="top">De 20 senaste <br>
 inloggade<br><br>
[BLOCK Latest]
&nbsp;<a href="ShowUser.php?ID={Latest_ID}">{Latest_Name}</a><br>
[END Latest]
              &nbsp;<br>
 
              <br>
              <br> </td>
        </tr>
      </table>
        <br><span class="nt">
Just nu &auml;r det: {Online} st online<br>
Det tog {Timer} sekunder att generera sidan.
Daft Konferens är släppt under GPL. För att se info, tryck <A HREF="COPYING">här</A>.<br>
För att ladda ner Daft Konferens i ZIP-format, tryck <A HREF="daftkonferens.zip">här</A>.</span>
      </td>
</tr> 
</table>
<img src="../bilder/rund2.gif" width="790" height="3" align="top"><br> 
</center>
</body>

</html>

[SET Empty]
[END Empty]