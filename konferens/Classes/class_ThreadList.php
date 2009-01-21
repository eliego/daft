<?PHP

// class_ThreadList.php
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

class ThreadList
{
	// Membervars:
	// Senaste felkod
	var $iErrorNo;
	
	// Senaste felmeddelande
	var $sErrorMsg;
	
	// Metoder:
	// Konstruktor
	function ThreadList ()
	{
		// Initiera membervars
		$this->iErrorNo = 0;
		$this->sErrorMsg = "";
	}
	
	// H?mta array med ListThreads
	function getThreads ($sSort, $iNumber, $iOffset = 0, $iTimestamp = 0, $a_iOldThreads = array())
	{
		// H?mta arrayen fr?n datak?llan
		$oData =& ExternalStorage::createInstance();
	
		if (FALSE === ($a_oListThreads = $oData->getLatestListThreads($sSort, $iNumber, $iOffset))) {
			trigger_error("11: ".$oData->getErrorMsg(), E_USER_WARNING);
			return(FALSE); }
		
		foreach (array_keys($a_oListThreads) AS $iThread)
		{
			if ($a_oListThreads[$iThread]->getLastPostTimestamp() >= $iTimestamp)
				$a_oListThreads[$iThread]->setNew(TRUE);
		}
		
		foreach (array_keys($a_iOldThreads) AS $key)
		{
			if ($a_oListThreads[$key]) {
				if ($a_iOldThreads[$key] == $a_oListThreads[$key]->getNumberPosts())
					$a_oListThreads[$key]->setNew(FALSE);
				else 
					unset($a_iOldThreads[$key]);
			}
		}
		
		// Returnera arrayen
		return($a_oListThreads);
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
	
	// Felhanterare
	function errorHandler ($iErrorNo, $sErrorMsg)
	{
		// Tryck in i membervars
		$this->iErrorNo = $iErrorNo;
		$this->sErrorMsg = $sErrorMsg;
		
		// Returnera
		return(NULL);
	}
}

?>