<?PHP

// class_UserPresentation.php
// Skriven av Eli Kaufman f�r Daft
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

// S�kv�g till mailtemplat
define("TEMPLATE_PATH", "/home/daft/Templates/Mail.tpl");

class UserPresentation extends User
{
	// Membervars:	
	// Riktigt namn
	var $sRealName;
	
	// E-post adress
	var $sEmail;
	
	// �lder
	var $iAge;
	
	// Hemsida
	var $sHomesiteURL;
	
	// 'Annan'
	var $sOther;
	
	// Antal inloggningar
	var $iNumberLogins;
	
	// Antal startade tr�dar
	var $iStartedThreads;
	
	// Antal bes�k
	var $iNumberVisits;
	
	// Antal postningar
	var $iNumberPosts;
	
	// Antal skickade meddelanden
	var $iSentMessages;
	
	// Antal mottagna meddelanden
	var $iReceivedMessages;
	
	// Senast inloggad
	var $iLastLoginTimestamp;
	
	// Instans till datak�llan
	var $oData;
	
	// Senaste felkod
	var $iErrorNo;
	
	// Senaste felmeddelande
	var $sErrorMsg;
	
	// Skapad
	var $iCreatedTimestamp;
	
	// Antal tr�dar
	var $iNumberThreads;
	
	// Antal poster
	var $iNumberPostsInThread;
	
	// Signatur
	var $sSignature;
	
	// L�senord
	var $sPassword;
	
	// Senast utloggad
	var $iLastLogout;
	
	// Metoder:
	// Konstruktor
	function UserPresentation ($oUser, $bCreateNew = FALSE)
	{	
		// Kolla s� vi f�tt en riktig anv�ndare
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
		
		// Skapa anv�ndare om det beh�vs
		if ($bCreateNew)
		{
			if (!($this->iID = $this->oData->createNewUser($this->sName))) {
				trigger_error("39: ".$this->getErrorMsg(), E_USER_WARNING);
				return(FALSE); }
		}
		
		// Fixa ID om vi inte f�tt n�t
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
	
	// Spara datan till extern k�lla
	function saveData ()
	{						
		// Skicka mig sj�lv f�r nerfrysning
		if (!($this->oData->saveUserPresentation($this))) {
			trigger_error("12: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
		
		// Returnera sant
		return(TRUE);
	}
	
	// H�mta anv�ndare
	function getUser ()
	{
		return(new User($this->iID, $this->sName));
	}
	
	// H�mta array med tr�dar som jag postat, i MiniThreads
	function getPostedThreads ($iOffset = 0, $iNumber = 0)
	{			
		if (!($a_oMiniThreads = $this->oData->getMiniThreadsByUser($this->getUser(), $iOffset, $iNumber))) {
			trigger_error("14: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
		
		// Returnera arrayen
		return($a_oMiniThreads);
	}
	
	// H�mta array med tr�dar jag gjort ett inl�gg i, i MiniThreads
	function getThreadsWhereIPosted ($iOffset = 0, $iNumber = 0)
	{			
		if (!($a_oMiniThreads = $this->oData->getMiniThreadsByUserHasPosted($this->getUser(), $iOffset, $iNumber))) {
			trigger_error("15: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
		
		// Returnera arrayen
		return($a_oMiniThreads);
	}
	
	// H�mta de senaste bes�karna
	function getVisitors ($iOffset = 0, $iNumber = 0)
	{
		if (!($a_oVisitors = $this->oData->getVisitors($this->getUser(), $iOffset, $iNumber))) {
			trigger_error("29: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		return($a_oVisitors);
	}
	
	// Markera att jag blivit bes�kt
	function visit ($oUser)
	{			
		// Kolla s� att det �r en riktig anv�ndare
		if (!(is_a($oUser, "User"))) {
			trigger_error("6", E_USER_WARNING);
			return(FALSE); }
			
		// Tryck in anv�ndaren i databasen
		if (!($this->oData->visitProfile($this->getUser(), $oUser))) {
			trigger_error("30: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// Uppdatera lokala r�knaren
		$this->iNumberVisits++;
		
		// Returnera sant
		return(TRUE);
	}
	
	// H�mta �lder
	function getAge ()
	{			
		return($this->iAge);
	}
	
	// S�tt �lder
	function setAge ($iAge)
	{
		$this->iAge = $iAge;
		return(TRUE);
	}
	
	// H�mta antal bes�k
	function getNumberVisists ()
	{			
		return($this->iNumberVisits);
	}
	
	// H�mta email
	function getEmail ()
	{			
		return($this->sEmail);
	}
	
	// S�tt email
	function setEmail ($sEmail)
	{
		$this->sEmail = $sEmail;
		return(TRUE);
	}
	
	// H�mta hemsida
	function getHomesite ()
	{			
		return($this->sHomesiteURL);
	}
	
	// S�tt hemsida
	function setHomesite ($sHomesite)
	{
		$this->sHomesiteURL = $sHomesite;
		return(TRUE);
	}
	
	// H�mta annan
	function getOther ()
	{			
		return($this->sOther);
	}
	
	// S�tt annan
	function setOther ($sOther)
	{
		$this->sOther = $sOther;
		return(TRUE);
	}
	
	// H�mta riktigt namn
	function getRealName ()
	{			
		return($this->sRealName);
	}
	
	// S�tt riktigt namn
	function setRealName ($sName)
	{
		$this->sRealName = $sName;
		return(TRUE);
	}

	// H�mta antal logins
	function getNumberLogins ()
	{			
		return($this->iNumberLogins);
	}
	
	// H�mta antal poster
	function getNumberPosts ()
	{			
		return($this->iNumberPosts);
	}
	
	// H�mta antal skickade meddelanden
	function getNumberSentMessages ()
	{			
		return($this->iSentMessages);
	}
	
	// H�mta antal mottagna meddelanden
	function getNumberReceivedMessages ()
	{			
		return($this->iReceivedMessages);
	}
	
	// H�mta antal startade tr�dar
	function getNumberStartedThreads ()
	{			
		return($this->iStartedThreads);
	}
	
	// H�mta senaste felkod
	function getErrorNo ()
	{
		return($this->iErrorNo);
	}
	
	// H�mta senaste felmeddelande
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
	
	// H�mta tidpunkt f�r skapande
	function getCreatedTimestamp ()
	{
		return($this->iCreatedTimestamp);
	}
	
	// H�mta antal tr�dar
	function getNumberThreads ()
	{
		return($this->iNumberThreads);
	}
	
	// S�tt antal tr�dar
	function setNumberThreads ($iNumberThreads)
	{
		$this->iNumberThreads = $iNumberThreads;
	}
	
	// H�mta antal poster
	function getNumberPostsInThread ()
	{
		return($this->iNumberPostsInThread);
	}
	
	// S�t antal poster
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
		
		// J�mf�r l�senord
		if (!(md5($sPassword) == $this->sPassword)) {
			trigger_error("33", E_USER_WARNING);
			return(FALSE); }
			
		// M�rk som inloggad
		if (!($oData->markOnline($this->iID))) {
			trigger_error("34: ".$oData->getErrorMsg, E_USER_WARNING);
			return(FALSE); }
			
		// Lokalt
		$this->bOnline = TRUE;
		$this->iNumberLogins++;
			
		// Returnera
		return(TRUE);
	}
	
	// H�mta senast inloggad
	function getLastLogin ()
	{
		return($this->iLastLoginTimestamp);
	}
	
	// H?mta senast utloggad
	function getLastLogout ()
	{
		return($this->iLastLogout);
	}
	
	// S�tt signatur
	function setSignature ($sSignature)
	{
		$this->sSignature = $sSignature;
		return(TRUE);
	}
	
	// H�mta signatur
	function getSignature ()
	{
		return($this->sSignature);
	}
	
	// S�tt password
	function setPassword ($sPassword)
	{
		$this->sPassword = md5($sPassword);
		return(TRUE);
	}
	
	// Skapa l�senord och skicka
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
		
		// H�mta output
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
	
	// H�mta l�senord
	function getPassword ()
	{
		return($this->sPassword);
	}
}

?>