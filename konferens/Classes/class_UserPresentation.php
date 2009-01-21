<?PHP

// class_UserPresentation.php
// Skriven av Eli Kaufman för Daft
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// Sökväg till mailtemplat
define("TEMPLATE_PATH", "/home/daft/Templates/Mail.tpl");

class UserPresentation extends User
{
	// Membervars:	
	// Riktigt namn
	var $sRealName;
	
	// E-post adress
	var $sEmail;
	
	// Ålder
	var $iAge;
	
	// Hemsida
	var $sHomesiteURL;
	
	// 'Annan'
	var $sOther;
	
	// Antal inloggningar
	var $iNumberLogins;
	
	// Antal startade trådar
	var $iStartedThreads;
	
	// Antal besök
	var $iNumberVisits;
	
	// Antal postningar
	var $iNumberPosts;
	
	// Antal skickade meddelanden
	var $iSentMessages;
	
	// Antal mottagna meddelanden
	var $iReceivedMessages;
	
	// Senast inloggad
	var $iLastLoginTimestamp;
	
	// Instans till datakällan
	var $oData;
	
	// Senaste felkod
	var $iErrorNo;
	
	// Senaste felmeddelande
	var $sErrorMsg;
	
	// Skapad
	var $iCreatedTimestamp;
	
	// Antal trådar
	var $iNumberThreads;
	
	// Antal poster
	var $iNumberPostsInThread;
	
	// Signatur
	var $sSignature;
	
	// Lösenord
	var $sPassword;
	
	// Senast utloggad
	var $iLastLogout;
	
	// Metoder:
	// Konstruktor
	function UserPresentation ($oUser, $bCreateNew = FALSE)
	{	
		// Kolla så vi fått en riktig användare
		if (!(is_a($oUser, "User")))
			return(FALSE);
		
		// Tryck in i membervarsen
		$this->iID = $oUser->getID();
		$this->sName = $oUser->getName();
		$this->iAge = 0;
		$this->iCreatedTimestamp = 0;
		$this->sEmail = "";
		$this->sHomesiteURL = "";
		$this->sOther = "";
		$this->sRealName = "";
		$this->iNumberLogins = 0;
		$this->iNumberPosts = 0;
		$this->iSentMessages = 0;
		$this->iReceivedMessages = 0;
		$this->iStartedThreads = 0;
		$this->oData =& ExternalStorage::createInstance();
		$this->iErrorNo = 0;
		$this->sErrorMsg = "";
		$this->iNumberVisits = 0;
		$oConfiguration =& Configuration::createInstance();
		$this->iNumberThreads = 0;
		$this->iNumberPostsInThread = 0;
		$this->sSignature = "";
		$this->sPassword = "";
		
		// Skapa användare om det behövs
		if ($bCreateNew)
		{
			if (!($this->iID = $this->oData->createNewUser($this->sName))) {
				trigger_error("39: ".$this->getErrorMsg(), E_USER_WARNING);
				return(FALSE); }
		}
		
		// Fixa ID om vi inte fått nåt
		if (!($this->iID)) {
			if (!($this->iID = $this->oData->getUserID($this->sName))) {
				trigger_error("38: ".$this->oData->getErrorMsg(), E_USER_WARNING);
				return(FALSE); }}
		
		// Ladda data
		if (!($this->oData->loadUserPresentation($this))) {
			trigger_error("13: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }

		// Returnera
		return(NULL);
	}
	
	// Spara datan till extern källa
	function saveData ()
	{						
		// Skicka mig själv för nerfrysning
		if (!($this->oData->saveUserPresentation($this))) {
			trigger_error("12: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
		
		// Returnera sant
		return(TRUE);
	}
	
	// Hämta användare
	function getUser ()
	{
		return(new User($this->iID, $this->sName));
	}
	
	// Hämta array med trådar som jag postat, i MiniThreads
	function getPostedThreads ($iOffset = 0, $iNumber = 0)
	{			
		if (!($a_oMiniThreads = $this->oData->getMiniThreadsByUser($this->getUser(), $iOffset, $iNumber))) {
			trigger_error("14: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
		
		// Returnera arrayen
		return($a_oMiniThreads);
	}
	
	// Hämta array med trådar jag gjort ett inlägg i, i MiniThreads
	function getThreadsWhereIPosted ($iOffset = 0, $iNumber = 0)
	{			
		if (!($a_oMiniThreads = $this->oData->getMiniThreadsByUserHasPosted($this->getUser(), $iOffset, $iNumber))) {
			trigger_error("15: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
		
		// Returnera arrayen
		return($a_oMiniThreads);
	}
	
	// Hämta de senaste besökarna
	function getVisitors ($iOffset = 0, $iNumber = 0)
	{
		if (!($a_oVisitors = $this->oData->getVisitors($this->getUser(), $iOffset, $iNumber))) {
			trigger_error("29: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		return($a_oVisitors);
	}
	
	// Markera att jag blivit besökt
	function visit ($oUser)
	{			
		// Kolla så att det är en riktig användare
		if (!(is_a($oUser, "User"))) {
			trigger_error("6", E_USER_WARNING);
			return(FALSE); }
			
		// Tryck in användaren i databasen
		if (!($this->oData->visitProfile($this->getUser(), $oUser))) {
			trigger_error("30: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// Uppdatera lokala räknaren
		$this->iNumberVisits++;
		
		// Returnera sant
		return(TRUE);
	}
	
	// Hämta ålder
	function getAge ()
	{			
		return($this->iAge);
	}
	
	// Sätt ålder
	function setAge ($iAge)
	{
		$this->iAge = $iAge;
		return(TRUE);
	}
	
	// Hämta antal besök
	function getNumberVisists ()
	{			
		return($this->iNumberVisits);
	}
	
	// Hämta email
	function getEmail ()
	{			
		return($this->sEmail);
	}
	
	// Sätt email
	function setEmail ($sEmail)
	{
		$this->sEmail = $sEmail;
		return(TRUE);
	}
	
	// Hämta hemsida
	function getHomesite ()
	{			
		return($this->sHomesiteURL);
	}
	
	// Sätt hemsida
	function setHomesite ($sHomesite)
	{
		$this->sHomesiteURL = $sHomesite;
		return(TRUE);
	}
	
	// Hämta annan
	function getOther ()
	{			
		return($this->sOther);
	}
	
	// Sätt annan
	function setOther ($sOther)
	{
		$this->sOther = $sOther;
		return(TRUE);
	}
	
	// Hämta riktigt namn
	function getRealName ()
	{			
		return($this->sRealName);
	}
	
	// Sätt riktigt namn
	function setRealName ($sName)
	{
		$this->sRealName = $sName;
		return(TRUE);
	}

	// Hämta antal logins
	function getNumberLogins ()
	{			
		return($this->iNumberLogins);
	}
	
	// Hämta antal poster
	function getNumberPosts ()
	{			
		return($this->iNumberPosts);
	}
	
	// Hämta antal skickade meddelanden
	function getNumberSentMessages ()
	{			
		return($this->iSentMessages);
	}
	
	// Hämta antal mottagna meddelanden
	function getNumberReceivedMessages ()
	{			
		return($this->iReceivedMessages);
	}
	
	// Hämta antal startade trådar
	function getNumberStartedThreads ()
	{			
		return($this->iStartedThreads);
	}
	
	// Hämta senaste felkod
	function getErrorNo ()
	{
		return($this->iErrorNo);
	}
	
	// Hämta senaste felmeddelande
	function getErrorMsg ()
	{
		return($this->sErrorMsg);
	}
	
	// Felhanterare
	function errorHandler ($iErrorNo, $sErrorMsg)
	{
		// Tryck in skiten i membervarsen
		$this->iErrorNo = $iErrorNo;
		$this->sErrorMsg = $sErrorMsg;
		
		// Returnera
		return(NULL);
	}
	
	// Hämta tidpunkt för skapande
	function getCreatedTimestamp ()
	{
		return($this->iCreatedTimestamp);
	}
	
	// Hämta antal trådar
	function getNumberThreads ()
	{
		return($this->iNumberThreads);
	}
	
	// Sätt antal trådar
	function setNumberThreads ($iNumberThreads)
	{
		$this->iNumberThreads = $iNumberThreads;
	}
	
	// Hämta antal poster
	function getNumberPostsInThread ()
	{
		return($this->iNumberPostsInThread);
	}
	
	// Sät antal poster
	function setNumberPostsInThread ($iNumberPosts)
	{
		$this->iNumberPostsInThread = $iNumberPosts;
		return(TRUE);
	}
	
		
	// Logga in
	function logOn ($sPassword)
	{
		// Databasinstans
		$oData =& ExternalStorage::createInstance();
		
		// Jämför lösenord
		if (!(md5($sPassword) == $this->sPassword)) {
			trigger_error("33", E_USER_WARNING);
			return(FALSE); }
			
		// Märk som inloggad
		if (!($oData->markOnline($this->iID))) {
			trigger_error("34: ".$oData->getErrorMsg, E_USER_WARNING);
			return(FALSE); }
			
		// Lokalt
		$this->bOnline = TRUE;
		$this->iNumberLogins++;
			
		// Returnera
		return(TRUE);
	}
	
	// Hämta senast inloggad
	function getLastLogin ()
	{
		return($this->iLastLoginTimestamp);
	}
	
	// H?mta senast utloggad
	function getLastLogout ()
	{
		return($this->iLastLogout);
	}
	
	// Sätt signatur
	function setSignature ($sSignature)
	{
		$this->sSignature = $sSignature;
		return(TRUE);
	}
	
	// Hämta signatur
	function getSignature ()
	{
		return($this->sSignature);
	}
	
	// Sätt password
	function setPassword ($sPassword)
	{
		$this->sPassword = md5($sPassword);
		return(TRUE);
	}
	
	// Skapa lösenord och skicka
	function createPasswordAndSend ()
	{
		// Konfiguration
		$oConfiguration =& Configuration::createInstance();
		
		// Templat
		$oTemplate = new Template(TEMPLATE_PATH);
		$oTemplate->setParseMode(TRUE);
		
		// Skapa password
		$this->setPassword($sPassword = randStr($oConfiguration->getCustomValue("PasswordLen")));
		
		// Definiera
		$oTemplate->set("UserName", $this->sName);
		$oTemplate->set("Password", $sPassword);
		
		// Hämta output
		$oTemplate->parse();
		$sData = $oTemplate->getContents();
		
		// Maila
		if (!(mail($this->sEmail, $oConfiguration->getCustomValue("MailSubject"), $sData, "From: ".$oConfiguration->getCustomValue("MailFrom")." <noreply@at.all>"))) {
			trigger_error("41", E_USER_WARNING);
			return(FALSE); }
			
		// Returnera
		return(TRUE);
	}
	
	// Logga ut
	function logOff ()
	{
		if (!($iTime = $this->oData->markOffline($this->iID))) {
			trigger_error("34: ".$oData->getErrorMsg, E_USER_WARNING);
			return(FALSE); }
			
		// Uppdatera lokalt
		$this->bOnline = FALSE;
		$this->iLastLoginTimestamp = $iTime;
		
		// Returnera
		return(TRUE);
	}
	
	// Hämta lösenord
	function getPassword ()
	{
		return($this->sPassword);
	}
}

?>