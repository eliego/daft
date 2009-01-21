<?PHP

// class_Thread.php
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

class Thread extends MiniThread
{
	// Membervars:
	// Anv�ndarobjektet som skapade
	var $oUser;

	// Tidst�mpel d� tr�den skapades
	var $iCreatedTimestamp;

	// Antal som l�st
	var $iReads;
	
	// Antal poster
	var $iPosts;
	
	// Instans av datak�lla-klassen
	var $oData;
	
	// Senaste felkod
	var $iErrorNo;
	
	// Senaste felmeddelande
	var $sErrorMsg;

	// Metoder:	
	// Konstruktor
	function Thread ($mRubrikOrID, $oUser = NULL)
	{
		// Initiera membervas
		$this->oUser = "";
		$this->iCreatedTimestamp = 0;
		$this->iReads = 0;
		$this->iPosts = 0;
		$this->oData =& ExternalStorage::createInstance();
		$this->iErrorNo = 0;
		$this->sErrorMsg = "";
		$this->sRubrik = "";
		$this->iThreadID = 0;
		
		if (is_numeric($mRubrikOrID))
		{
			// Vi ska ladda en sparad tr�d
			// St�ll in ID't
			$this->iThreadID = $mRubrikOrID;
			
			// Ladda fr�n databasen
			if (!($this->oData->loadThread($this))) {
				trigger_error("8: ".$this->oData->getErrorMsg(), E_USER_WARNING);
				return(FALSE); }
		} else {
			// Skapa en ny tr�d
			// Kolla s� att vi har en giltig user
			if (!(is_a($oUser, "User"))) {
				trigger_error("6", E_USER_WARNING);
				return(FALSE); }
				
			// Tryck in i membervarsen
			$this->sRubrik = $mRubrikOrID;
			$this->oUser = $oUser;
			$this->iCreatedTimestamp = time();
			
			// Spara i databasen		
			if (!($this->iThreadID = $this->oData->saveThread($this))) {
				trigger_error("7: ".$this->oData->getErrorMsg(), E_USER_WARNING);
				return(FALSE); }
		}
			
			// Returnera
			return(NULL);
	}
	
	// Konvertera till MiniThread
	function getMiniThread ()
	{
		return(new MiniThread($this->iThreadID, $this->sRubrik));
	}
	
	// L�gg till ny post i tr�den
	function addPost ($oPost)
	{
		// Kolla s� att det �r ett giltigt objekt
		if (!(is_a($oPost, "Post"))) {
			trigger_error("9", E_USER_WARNING);
			return(FALSE); }
			
		// S�tt Thread
		$oPost->setThread($this->getMiniThread());
		
		// Spara post
		if (!($oPost->saveData())) {
			trigger_error("2: ".$oPost->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// Uppdatera antal poster
		$this->iPosts++;
			
		// Returnera
		return(TRUE);
	}
	
	// H�mta poster
	function getPosts ($iIDOffset = 0, $iCount = 0)
	{
		// H�mta posterna
		if (FALSE === ($a_oPosts = $this->oData->loadPosts($this->getMiniThread(), $iIDOffset, $iCount))) {
			trigger_error("23: ".$this->oData->getErrorMsg());
			return(FALSE); }
			
		// Returnera
		return($a_oPosts);
	}
	
	// H�mta anv�ndare
	function getUser ()
	{
		return($this->oUser);
	}
	
	// H�mta tidst�mpel
	function getCreatedTimestamp ()
	{
		return($this->iCreatedTimestamp);
	}
	
	// H�mta antal som l�st
	function getRead ()
	{
		return($this->iReads);
	}
	
	// Markera att tr�den blivit l�st
	function read()
	{
		// Markera i databasen
		if (!($this->oData->readThread($this->getMiniThread()))) {
			trigger_error("24: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// Markera lokalt
		$this->iReads++;
		
		// Returnera
		return(TRUE);
	}
	
	// H�mta antal poster
	function getNumberPosts ()
	{
		return($this->iPosts);
	}
	
	// H�mta senaste felkod
	function getErrorNo ()
	{
		return($this->iErrCode);
	}
	
	// H�mta senaste felmeddelande
	function getErrorMsg ()
	{
		return($this->sErrMsg);
	}
	
	// Felhanterare
	function errorHandler ($iErrorNo, $sErrorMsg)
	{
		// Tryck in i membervarsen
		$this->iErrCode = $iErrorNo;
		$this->sErrMsg = $sErrorMsg;
		return(NULL);
	}
		
}