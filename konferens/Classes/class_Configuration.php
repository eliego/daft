<?PHP

// class_Configuration.php
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  US

// Definiera konfigurationsfil
define("CONFIGFILE", "/home/daft/konferens/config.ini");

class Configuration
{
	// Membervars:
	// Array med värden från konfigurationsfilen
	var $a_mConfig;
	
	// Metoder:
	// Konstruktor
	function Configuration ()
	{
		// Se till så att klassen inte går att instansiera utrifrån, och att konstruktorn inte går att kalla
		if (!(is_a($this, "Configuration")))
			trigger_error("Configuration-klassen får bara singletons-instansieras!", E_USER_ERROR);
		
		// Kolla så att filen finns
		if (!(is_readable(CONFIGFILE)))
			trigger_error("Den angivna konfigurationsfilen gick inte att läsa", E_USER_ERROR);
			
		// Ladda in filen
		$this->a_mConfig = parse_ini_file(CONFIGFILE, TRUE);
		
		// Returnera
		return(NULL);
	}
	
	// Skapa singleton-instans
	function &createInstance ()
	{
		static $oThis;
  		if (!($oThis))
  			$oThis = new Configuration;
  		
  		return($oThis);
	}
	
	// Hämta felmeddelande
	function getErrorMsg ($iErrCode)
	{		
		return($this->a_mConfig['errormsgs'][$iErrCode]);
	}
	
	// Hämta annat värde
	function getCustomValue ($sKey)
	{
		return($this->a_mConfig['others'][$sKey]);
	}
	
	// Hämta MySQL-värden
	function getMysqlHost ()
	{
		return($this->a_mConfig['mysql']['host']);
	}
	
	function getMysqlUser ()
	{
		return($this->a_mConfig['mysql']['user']);
	}
	
	function getMysqlPass ()
	{
		return($this->a_mConfig['mysql']['pass']);
	}
	
	function getMysqlDb ()
	{
		return($this->a_mConfig['mysql']['db']);
	}
}

?>