[BLOCK Login_Outside]
<table width="617" border="0" cellpadding="0" cellspacing="0">
  <form name="Login" method="post" action="Login.php">
     
    <tr valign="bottom"> 
      <td class="nt">&nbsp;Anv&auml;ndarnamn: 
        <input type="text" name="UserName" class="logininput" value="" size="15" maxlength="20">
        L&ouml;senord: 
        <input type="password" name="Password" class="logininput" size="10" maxlength="10">
        <input type="image" border="0" name="imageField" src="../bilder/loggain.gif" width="69" height="18">
      </td>
    </tr>
  </form>
</table>
<br>
&nbsp;[<a href="ThreadList.php">Konferensen</a>] [<a href="RegisterUser.php">Registrera dig</a>]
[<a href="ForgotPass.php">Gl&ouml;mt l&ouml;senordet</a>]
[<a href="Search.php">S&ouml;k</a>]
[<A HREF="Help.php">Hj&auml;lp</A>]
&nbsp;
<br>
<br>
[END Login_Outside]

[SET Login_Inside]
&nbsp;Du &auml;r inloggad som <a href="ChangeProfile.php">{User_Name}</a> 
och har {Messages_New} 
nya <A HREF="/konferens/Messages.php">meddelanden</A>  
<br>
<br>
&nbsp;[<a href="ThreadList.php">Konferensen</a>]
[<a href="NewThread.php">Skriv Inl&auml;gg</a>]
[<a href="ChangeProfile.php">Din Profil</a>]
[<a href="Search.php">S&ouml;k</a>]
[<A HREF="Help.php">Hj&auml;lp</A>]
[<a href="Logout.php">Logga ut</a>]
<br>
<br>
[END Login_Inside]