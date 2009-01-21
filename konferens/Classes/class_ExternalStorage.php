<?PHP

// class_ExternalStorage.php
// Skriven av Eli Kaufman f?r Daft
// Copyright Daft 2003 under GPL

// This file is part of PHPDaft.

// PHPDaft is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.

// PHPDaft is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with PHPDaft; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  US

// Byta plats p? close() och trigger()

class ExternalStorage
{
	// Membervars:
	// Databasanslutning
	var $rMySQL;
	
	// Senaste felkod
	var $iErrorNo;
	
	// Senaste felmeddelande
	var $sErrorMsg;
	
	// Metoder:
	// Konstruktor
	function ExternalStorage ()
	{
		// Endast singleton-instansiering
		if (!(is_a($this, "ExternalStorage")))
			trigger_error("ExternalStorage f?r bara singelton-instansieras!", E_USER_ERROR);
			
		// Initiera membervars
		$this->iErrorMsg = "";
		$this->iErrorNo = 0;
		
		// Returnera
		return(NULL);
	}
	
	// ?ppna f?rbindelse till db
	function open ()
	{
		// Konfigurationsobjekt
		$oConfig =& Configuration::createInstance();
		
		// Anslut
		if (!($this->rMySQL = mysql_connect($oConfig->getMysqlHost(), $oConfig->getMysqlUser(), $oConfig->getMysqlPass()))) {
			trigger_error(mysql_error(), E_USER_WARNING);
			return(FALSE); }
			
		// V?lj databas
		if (!(mysql_select_db($oConfig->getMysqlDb(), $this->rMySQL))) {
			trigger_error(mysql_error($this->rMySQL), E_USER_WARNING);
			return(FALSE); }
			
		// Returnera
		return(TRUE);
	}
	
	// St?ng f?rbindelsen till db
	function close ()
	{		
		// St?ng anslutningen
		if (!(mysql_close($this->rMySQL))) {
			trigger_error(mysql_error($this->rMySQL), E_USER_WARNING);
			return(FALSE); }
			
		// Returnera
		return(TRUE);
	}
	
	// Ladda en tr?ds data fr?n databasen
	function loadThread (&$oThread)
	{			
		// Kolla s? att det verkligen ?r en tr?d
		if (!(is_a($oThread, "Thread"))) {
			trigger_error("19", E_USER_WARNING);
			return(FALSE); }
			
		// ?ppna
		if (!($this->open())) {
			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// H?mta tr?den fr?n databasen
		$sQuery = "SELECT Threads.Created, Threads.Reads, Threads.UserID, Threads.Rubrik, COUNT(Posts.ID) AS Posts, Users.Name AS UserName, Users.Online
			   	   FROM Threads LEFT OUTER JOIN Posts ON Threads.ID = Posts.ThreadID INNER JOIN Users ON Threads.UserID = Users.ID
				   WHERE Threads.ID = ".$oThread->getThreadID()." GROUP BY Threads.Created LIMIT 1";
		
		if (!($rResult = mysql_query($sQuery, $this->rMySQL))) {
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);			
			$this->close();
			return(FALSE); }		
				
		// St?ng
		if (!($this->close())) {
			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// F?nga resultatet
		if (!($a_mThreadData = mysql_fetch_array($rResult))) {
			// Tr?den finns inte
			trigger_error("21", E_USER_WARNING);
			return(FALSE); }
			
		// Tryck in resultatet i tr?den
		$oThread->iCreatedTimestamp = $a_mThreadData['Created'];
		$oThread->iReads = $a_mThreadData['Reads'];
		$oThread->oUser = new User($a_mThreadData['UserID'], stripslashes($a_mThreadData['UserName']), $a_mThreadData['Online']);
		$oThread->sRubrik = stripslashes($a_mThreadData['Rubrik']);
		$oThread->iPosts = $a_mThreadData['Posts'];
		
		// Returnera
		return(TRUE);
	}
	
	// Spara en tr?ds data
	function saveThread (&$oThread)
	{			
		// Kolla s? att det verkligen ?r en tr?d
		if (!(is_a($oThread, "Thread"))) {
			trigger_error("19", E_USER_WARNING);
			return(FALSE); }

		// Konstruera query
		$oUser = $oThread->getUser();
		$sQuery = "INSERT INTO Threads (Created, UserID, Rubrik) VALUES (".$oThread->getCreatedTimestamp().
		         ", ".$oUser->getID().", \"".addslashes($oThread->getRubrik())."\")";
		// ?ppna
		if (!($this->open())) {
			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }	
				
		// Skicka query
		if (!(mysql_query($sQuery, $this->rMySQL))) {
			$this->close();
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);			
			return(FALSE); }
			
		// F?nga ID't
		$iID = mysql_insert_id($this->rMySQL);
		
		// St?ng
		if (!($this->close())) {
			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
		
		// Returnera
		return($iID);		
	}
	
	// Spara en posts data
	function savePost (&$oPost)
	{			
		// Kolla s? att det verkligen ?r en post
		if (!(is_a($oPost, "Post"))) {
			trigger_error("9", E_USER_WARNING);
			return(FALSE); }
			
		// Konstruera query
		$oUser = $oPost->getUser();
		$sQuery1 = "INSERT INTO Posts (ThreadID, UserID, Text, Created) VALUES (".$oPost->getThread().", ".
				   $oUser->getID().", \"".
				   addslashes($oPost->getText())."\", ".$oPost->getCreatedTimestamp().")";
				   
		$sQuery2 = "UPDATE Threads SET LastUserID = ".$oUser->getID().", LastTimestamp = ".time()." WHERE ID = ".$oPost->getThread();
		
		// ?ppna
		if (!($this->open())) {
			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }	
				 
		// Skicka query
		if (!(mysql_query($sQuery2, $this->rMySQL))) {
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);			
			$this->close();
			return(FALSE); }
			
		if (!(mysql_query($sQuery1, $this->rMySQL))) {
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);			
			$this->close();
			return(FALSE); }
			
		// H?mta ID't
		$iID = mysql_insert_id($this->rMySQL);
			
		// St?ng
		if (!($this->close())) {
			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
		
		// Returnera
		return($iID);	
	}
	
	// Ladda poster
	function loadPosts ($oThread, $iOffset = 0, $iNumber = NULL)
	{ 
		// Kolla s? att det ?r en riktig MiniThread
		if (!(is_a($oThread, "MiniThread"))) {
			trigger_error("22", E_USER_WARNING);
			return(FALSE); }
			
		// ?ppna
		if (!($this->open())) {
			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
					
		// H?mta poster
		$sLimit = isset($iNumber) ? " LIMIT ".$iOffset.", ".$iNumber : "";
		$sQuery = "SELECT Posts.*, Users.Name AS UserName, Users.Online FROM Posts
				   INNER JOIN Users ON Posts.UserID = Users.ID WHERE ThreadID = ".$oThread->getThreadID()." ORDER BY Posts.Created". $sLimit; 
		if (!($rResult = mysql_query($sQuery, $this->rMySQL))) {
			$this->close();
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);			
			return(FALSE); }
			
		// St?ng
		if (!($this->close())) {
			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
						
		// F?nga poster
		$a_oPosts = array(); $n = 0;
		while ($a_mPost = mysql_fetch_array($rResult))
		{
			$a_oPosts[$n] = new Post(new User($a_mPost['UserID'], stripslashes($a_mPost['UserName']), $a_mPost['Online']), stripslashes($a_mPost['Text']), $a_mPost['ThreadID']);
			$a_oPosts[$n]->iCreatedTimestamp = $a_mPost['Created'];
			$a_oPosts[$n]->iPostID = $a_mPost['ID'];
			$n++;
		}
		
		// Returnera
		return($a_oPosts);
	}
	
	// Felhanterare
	function errorHandler ($iErrorNo, $sErrorMsg)
	{
		// Tryck in i membervars
		$this->iErrorNo = $iErrorNo;
		$this->sErrorMsg = $sErrorMsg;
		
		// Returnera
		return(NULL);
	}
	
	// H?mta senaste felkod
	function getErrorNo ()
	{
		return($this->iErrorNo);
	}
	
	// H?mta senaste felmeddelande
	function getErrorMsg ()
	{
		return($this->sErrorMsg);
	}
	
	// Markera att en tr?d blivir l?st
	function readThread ($oThread)
	{
		// Kolla s? att vi har en riktig post
		if (!(is_a($oThread, "MiniThread"))) {
			trigger_error("19", E_USER_WARNING);
			return(FALSE); }
			
		// ?ppna
		if (!($this->open())) {
			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
						
		// Uppdatera db
		if (!(mysql_query("UPDATE Threads SET Reads = Reads + 1 WHERE ID = ".$oThread->getThreadID(), $this->rMySQL))) {
			$this->close();
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);			
			return(FALSE); }
			
		// St?ng
		if (!($this->close())) {
			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// Returnera
		return(TRUE);		
	}

	// H?mta tr?dar av en viss anv?ndare i form av MiniThreads
	function getMiniThreadsByUser ($oUser, $iOffset = 0, $iNumber = 0)
	{		
		// Kolla s? att vi f?tt en riktig user
		if (!(is_a($oUser, "User"))) {
			trigger_error("6", E_USER_WARNING);
			return(FALSE); }
			
		// ?ppna
		if (!($this->open())) {
			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }

		// Skicka query
		if ($iNumber)
			$iNumber = " LIMIT ".$iNumber.",".$iOffset;
		if (!($rResult = mysql_query("SELECT ID, Rubrik FROM Threads WHERE UserID = ".$oUser->getID()." ORDER BY Created DESC".$iNumber, $this->rMySQL))) {
			$this->close();
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);			
			return(FALSE); }
			
		// St?ng
		if (!($this->close())) {
			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }	
					
		// F?nga tr?dar
		$a_oMiniThreads = array();
		while ($a_mThreadData = mysql_fetch_array($rResult))
			$a_oMiniThreads[] = new MiniThread($a_mThreadData['ID'], stripslashes($a_mThreadData['Rubrik']));

		
		// Returnera
		return($a_oMiniThreads);
	}
	
	// H?mta tr?dar d?r en viss anv?ndare postat i form av MiniThreads
	function getMiniThreadsByUserHasPosted ($oUser, $iOffset = 0, $iNumber = 0)
	{		
		// Kolla s? att vi f?tt en riktig user
		if (!(is_a($oUser, "User"))) {
			trigger_error("6", E_USER_WARNING);
			return(FALSE); }
			
		// ?ppna
		if (!($this->open())) {
			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }

		// Skicka query
		if ($iNumber)
			$iNumber = " LIMIT ".$iNumber.",".$iOffset;
		if (!($rResult = mysql_query("SELECT Threads.ID, Threads.Rubrik FROM Posts LEFT OUTER JOIN Threads ON Posts.ThreadID = Threads.ID WHERE Posts.UserID = ".$oUser->getID()." ORDER BY Created DESC".$iNumber, $this->rMySQL))) {
			$this->close();
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);			
			return(FALSE); }
			
		// St?ng
		if (!($this->close())) {
			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }	
					
		// F?nga tr?dar
		$a_oMiniThreads = array();
		while ($a_mThreadData = mysql_fetch_array($rResult))
			$a_oMiniThreads[] = new MiniThread($a_mThreadData['ID'], stripslashes($a_mThreadData['Rubrik']));

		
		// Returnera
		return($a_oMiniThreads);
	}
	
	// H?mta senast uppdaterade tr?dar
	function getLatestListThreads ($sSort, $iNumber, $iOffset = 0)
	{
		// ?ppna
		if (!($this->open())) {
			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		if (!($sSort == "Posts")) $sSort = "Threads." . $sSort;
			
		// Konstruera och skicka query	
		$sQuery = "SELECT Threads.ID, Threads.UserID, Threads.Rubrik, Threads.Reads, Threads.Created, Threads.LastTimestamp,
				   Threads.LastUserID, Users_Thread.Name AS UserName, Users_Thread.Online, Users_Post.Name AS LastUserName,
				   Users_Post.Online AS LastOnline, COUNT(Posts.ID) AS Posts
				   FROM Threads 
				   LEFT JOIN Users AS Users_Thread ON Threads.UserID = Users_Thread.ID 
				   LEFT JOIN Users AS Users_Post ON Threads.LastUserID = Users_Post.ID 
				   LEFT JOIN Posts ON Threads.ID = Posts.ThreadID GROUP BY Threads.ID
				   ORDER BY ".$sSort." DESC, Threads.LastTimestamp DESC, Threads.Created DESC LIMIT ".$iOffset.", ".$iNumber;

		if (!($rResult = mysql_query($sQuery, $this->rMySQL))) {
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
			$this->close();
			return(FALSE); }
						
		// F?nga resultatet
		$a_oThreads = array();
		while ($a_mThread = mysql_fetch_array($rResult))
			$a_oThreads[$a_mThread['ID']] = new ListThread(new User($a_mThread['UserID'], stripslashes($a_mThread['UserName']), $a_mThread['Online']), $a_mThread['LastTimestamp'],
										   $a_mThread['Reads'], $a_mThread['Posts'], new User($a_mThread['LastUserID'], stripslashes($a_mThread['LastUserName']), $a_mThread['LastOnline']),
										   $a_mThread['ID'], stripslashes($a_mThread['Rubrik']), $a_mThread['Created']);
		// Returnera
		return($a_oThreads);
	}
	
	// Spara en profils data
	function saveUserPresentation (&$oUP)
	{
		// Kolla s? att vi f?tt en riktigt UP
		if (!(is_a($oUP, "UserPresentation"))) {
			trigger_error("28", E_USER_WARNING);
			return(FALSE); }
			
		// Konstruera query
		$oUser = $oUP->getUser();
		$sQuery = "UPDATE Users SET Age = ".$oUP->getAge().", Email = \"".addslashes($oUP->getEmail()).
				  "\", Homesite = \"".addslashes($oUP->getHomesite()).
				  "\", Other = \"".addslashes($oUP->getOther())."\", RealName = \"".addslashes($oUP->getRealName())."\", NumberPosts = ".$oUP->getNumberPostsInThread().", NumberThreads = ".$oUP->getNumberThreads().", Signature = \"".addslashes($oUP->getSignature())."\", Password = \"".$oUP->getPassword()."\" WHERE ID = ".$oUser->getID();
		
		// ?ppna
		if (!($this->open())) {
			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// Skicka
		if (!(mysql_query($sQuery, $this->rMySQL))) {
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
			$this->close();
			return(FALSE); }
			
		// St?ng
		if (!($this->close())) {
			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// Returnera
		return(TRUE);
	}
	
	// H?mta en profils data
	function loadUserPresentation (&$oUP)
	{
		// Kolla s? vi f?tt en UP
		if (!(is_a($oUP, "UserPresentation"))) {
			trigger_error("28", E_USER_WARNING);
			return(FALSE); }
			
		// ?ppna
		if (!($this->open())) {
			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// Skicka query
		$sQuery = "SELECT Users.Age, Users.Created, Users.LastLogin, Users.Logins, Users.Email, Users.Signature, Users.Password,
				   Users.Homesite, Users.Other, Users.RealName, Users.NumberThreads, Users.NumberPosts, Users.Logout,
				   COUNT(Posts.ID) AS Posts, COUNT(Messages_rec.ID) AS ReceivedMessages, 
				   COUNT(Messages_sen.ID) AS SentMessages, COUNT(Threads.ID) AS Threads, 
				   COUNT(Visits.ID) AS Visits FROM Users 
				   LEFT JOIN Posts ON Users.ID = Posts.UserID 
				   LEFT JOIN Messages AS Messages_rec ON Users.ID = Messages_rec.ToID
				   LEFT JOIN Messages AS Messages_sen ON Users.ID = Messages_sen.FromID
				   LEFT JOIN Threads ON Users.ID = Threads.UserID 
				   LEFT JOIN Visits ON Users.ID = Visits.VisitedID 
				   WHERE Users.ID = ".$oUP->getID()." GROUP BY Users.Age, Users.Created, Users.LastLogin,
				   Users.Logins, Users.Email, Users.Homesite, Users.Other, Users.RealName, Users.NumberThreads,
				   Users.NumberPosts";
		if (!($rResult = mysql_query($sQuery, $this->rMySQL))) {
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
			$this->close();
			return(FALSE); }
			
		// St?ng
		if (!($this->close())) {
			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// F?nga resultat
		if (!($a_mUser = mysql_fetch_array($rResult))) {
			trigger_error("30", E_USER_WARNING);
			return(FALSE); }
			
		// Fyll profilen med datan
		$oUP->iAge				  = $a_mUser['Age'];
		$oUP->iCreatedTimestamp   = $a_mUser['Created'];
		$oUP->iLastLoginTimestamp = $a_mUser['LastLogin'];
		$oUP->iNumberLogins		  = $a_mUser['Logins'];
		$oUP->iNumberPosts		  = $a_mUser['Posts'];
		$oUP->iNumberVisits		  = $a_mUser['Visits'];
		$oUP->iReceivedMessages	  = $a_mUser['ReceivedMessages'];
		$oUP->iSentMessages		  = $a_mUser['SentMessages'];
		$oUP->iStartedThreads	  = $a_mUser['Threads'];
		$oUP->sEmail			  = stripslashes($a_mUser['Email']);
		$oUP->sHomesiteURL		  = stripslashes($a_mUser['Homesite']);
		$oUP->sOther			  = stripslashes($a_mUser['Other']);
		$oUP->sRealName			  = stripslashes($a_mUser['RealName']);
		$oUP->sSignature		  = stripslashes($a_mUser['Signature']);
		$oUP->iNumberPostsInThread= $a_mUser['NumberPosts'];
		$oUP->iNumberThreads	  = $a_mUser['NumberThreads'];
		$oUP->sPassword			  = $a_mUser['Password'];
		$oUP->iLastLogout		= $a_mUser['Logout'];
		
		// Returnera
		return(TRUE);
	}
	
  	// Singletonmetod, anropas statiskt f?r att skapa instans
  	function &createInstance ()
  	{
  		static $oThis;
  		if (!($oThis))
  			$oThis = new ExternalStorage;
  		
  		return($oThis);
  	}
  	
  	// Markera att en profil f?tt en bes?kare
  	function visitProfile ($oVisitedUser, $oVisitingUser)
  	{
  		// Kolla s? att det ?r User-objekt
  		if (!((is_a($oVisitedUser, "User")) && (is_a($oVisitingUser, "User")))) {
  			trigger_error("6", E_USER_WARNING);
  			return(FALSE); }
  			
  		// ?ppna
  		if (!($this->open())) {
  			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
  			return(FALSE); }
  			
  		// Skicka query
  		if (!(mysql_query("INSERT INTO Visits (VisitedID, VisitingID) VALUES (".$oVisitedUser->getID.", ".$oVisitingUser->getID().")", $this->rMySQL))) {
  			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
  			$this->close();
  			return(FALSE); }
  			
  		// St?ng
  		if (!($this->close())) {
  			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
  			return(FALSE); }
  		
  		// Returnera
  		return(TRUE);
  	}
  	
  	// H?mta bes?kare till en profil
  	function getVisitors ($oUser, $iOffset = 0, $iNumber = 0)
  	{
  		// Kolla s? att vi fick en riktig User
  		if (!(is_a($oUser, "User"))) {
  			trigger_error("6", E_USER_WARNING);
  			return(FALSE); }
  			
  		// ?ppna
  		if (!($this->open())) {
  			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
  			return(FALSE); }
  			
  		// Skicka query
  		if ($iNumber)
			$iNumber = " LIMIT ".$iNumber.",".$iOffset;
			
		if (!($rResult = mysql_query("SELECT Visits.VisitingID AS ID, Users.Name FROM Visits LEFT OUTER JOIN Users ON Visits.VisitingID = Users.ID WHERE Visits.VisitedID = ".$oUser->getID()." ORDER BY Visits.ID DESC".$iNumber, $this->rMySQL))) {
			$this->close();
			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
			return(FALSE); }
			
		// F?nga resultat
		$a_oUsers = array();
		while ($a_mUser = mysql_fetch_array($rResult))
		{
			$a_oUsers[] = new User($a_mUser['ID'], stripslashes($a_mUser['Name']));
		}
		
		// Returnera
		return($a_oUsers);									
  	}
  	
  	// H?mta ID f?r anv?nare
  	function getUserID ($sName)
  	{
  		// ?ppna
  		if (!($this->open())) {
  			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
  			return(FALSE); }
  			
  		// Skicka query
  		if (!($rResult = mysql_query("SELECT ID FROM Users WHERE Name = \"".addslashes($sName)."\" LIMIT 1", $this->rMySQL))) {
  			$this->close();
  			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
  			return(FALSE); }
  			
  		// St?ng
  		if (!($this->close())) {
  			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
  			return(FALSE); }
  			
  		// F?nga
  		if (!($a_mUser = mysql_fetch_array($rResult))) {
  			trigger_error("30", E_USER_WARNING);
  			return(FALSE); }
  			
  		// Returnera
  		return($a_mUser[0]);
  	}
  	
  	// H?mta l?senord f?r anv?ndare
  	function getUserPassword ($iUserID)
  	{
  		// ?ppna
  		if (!($this->open())) {
  			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
  			return(FALSE); }
  			
  		// Skicka query
  		if (!($rResult = mysql_query("SELECT Password FROM Users WHERE ID = ".$iUserID." LIMIT 1", $this->rMySQL))) {
  			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
  			$this->close();
  			return(FALSE); }
  			
  		// St?ng
  		if (!($this->close())) {
  			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
  			return(FALSE); }
  			
  		// F?nga
  		if (!($a_mUser = mysql_fetch_array($rResult))) {
  			trigger_error("30", E_USER_WARNING);
  			return(FALSE); }
  			
  		// Returnera
  		return($a_mUser[0]);
  	}
  	
  	// Markera att en anv?ndare ?r inloggad
  	function markOnline ($iID)
  	{
  		// ?ppna
  		if (!($this->open())) {
  			trigger_error("17: ".$this->getErrorMsg, E_USER_WARNING);
  			return(FALSE); }
  			
  		// Skicka query
  		if (!(mysql_query("UPDATE Users SET Online = 1, LastLogin = ".time().", Logins = Logins + 1 WHERE ID = ".$iID, $this->rMySQL))) {
  			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
  			$this->close();
  			return(FALSE); }
  			
  		// St?ng
  		if (!($this->close())) {
  			trigger_error("27: ".$this->getErrorMsg, E_USER_WARNING);
  			return(FALSE); }
  			
  		// Returnera
  		return(TRUE);
  	}
  	
  	// Markera att en anv?ndare inte ?r inloggad
  	function markOffline ($iID)
  	{
  		// ?ppna
  		if (!($this->open())) {
  			trigger_error("17: ".$this->getErrorMsg, E_USER_WARNING);
  			return(FALSE); }
  			
  		// Skicka query
  		if (!(mysql_query("UPDATE Users SET Online = 0, Logout = ".$iTime = time()." WHERE ID = ".$iID, $this->rMySQL))) {
  			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
  			$this->close();
  			return(FALSE); }
  			
  		// St?ng
  		if (!($this->close())) {
  			trigger_error("27: ".$this->getErrorMsg, E_USER_WARNING);
  			return(FALSE); }
  			
  		// Returnera
  		return($iTime);
  	}
  	
  	//  Skapa ny anv?ndare
  	function createNewUser ($sName)
  	{
  		// ?ppna
  		if (!($this->open())) {
  			trigger_error("17: ".$this->getErrorMsg(), E_USER_WARNING);
  			return(FALSE); }
  			
  		// Kolla s? att det inte redan finns n?gon med det namnet
  		if (!($rResult = mysql_query("SELECT ID FROM Users WHERE Name = \"".addslashes($sName)."\"", $this->rMySQL))) {
  			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
  			$this->close();
  			return(FALSE); }
  			
  		// Fixa massa error om det fungerade
  		if (mysql_fetch_array($rResult)) {
  			// Anv?ndaren finns redan
  			$this->close();
  			trigger_error("42", E_USER_WARNING);
  			return(FALSE); }
  
  		// Skapa anv?ndaren
  		if (!($rResult = mysql_query("INSERT INTO Users (Name, Created) VALUES (\"".addslashes($sName)."\", ".time().")", $this->rMySQL))) {
  			trigger_error("20: ".mysql_error($this->rMySQL), E_USER_WARNING);
  			$this->close();
  			return(FALSE); }
  			
  		// F?nga ID
  		$iID = mysql_insert_id($this->rMySQL);
  		
  		// St?ng
  		if (!($this->close())) {
  			trigger_error("27: ".$this->getErrorMsg(), E_USER_WARNING);
  			return(FALSE); }
  			
  		// Returnera
  		return($iID); 	
  	}
  		
}

?>
