<?PHP

// class_Post.php
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


class Post
{
	// Membervars:
	// Timestamp n�r jag skapades
	var $iCreatedTimestamp;
	
	// User som skapade mig
	var $oUser;
	
	// Sj�lva texten
	var $sText;
	
	// Tr�den jag tillh�r
	var $iThread;
	
	// Mitt ID
	var $iPostID;
	
	// Felkod
	var $iErrCode;
	
	// Felmeddelande
	var $sErrMsg;
	
	// Instans av dataklass
	var $oData;
	
	// Metoder:
	// Konstruktor
	function Post ($oUser, $sText, $iThreadID)
	{
		// Tryck in grejset i membervarsen
		$this->sText = $sText;
		$this->oUser = $oUser;
		$this->iPostID = 0;
		$this->iErrCode = 0;
		$this->sErrMsg = "";
		$this->oData =& ExternalStorage::createInstance();
		$this->iThread = $iThreadID;
		
		// Skapa timestamp
		$this->iCreatedTimestamp = time();
		
		// Vi �r f�rdiga
		return(NULL);
	}
	
	// H�mta anv�ndare
	function getUser ()
	{
		return($this->oUser);
	}
	
	// H�mta text
	function getText ()
	{
		return($this->sText);
	}
	
	// H�mta timestamp
	function getCreatedTimestamp ()
	{
		return($this->iCreatedTimestamp);
	}
	
	// Spara mig sj�lv
	function saveData ()
	{	
		// Spara mig sj�lv
		if (!($this->iPostID = $this->oData->savePost($this))) {
			trigger_error("2: ".$this->oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
			
		// Returnera
		return(TRUE);
	}
	
	// H�mta PostID
	function getPostID ()
	{
		return($this->iPostID);
	}

	// H�mta ThreadID
	function getThread ()
	{
		return($this->iThread);
	}
	
	// S�tt Thread
	function setThread ($oThread)
	{
		$this->oThread = $oThread;
		return(TRUE);
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
	function errorHandler ($iErrCode, $sErrMsg)
	{
		// Stoppa in i membervarsen
		$this->iErrCode = $iErrCode;
		$this->sErrMsg = $sErrMsg;
		return(NULL);
	}
	

}

?>