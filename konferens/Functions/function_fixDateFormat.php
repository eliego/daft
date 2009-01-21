<?PHP

// function_fixDateFormat.php
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

function fixDateFormat ($iTimestamp, $iVersion = 1)
{
	if (!($iTimestamp))
		return("");
		
	$oConfiguration =& Configuration::createInstance();
	$iRest = time() % 86400;
	if (($iTime = time()) - $iTimestamp < $iRest)
		return(date($oConfiguration->getCustomValue("DateFormatToday".$iVersion), $iTimestamp));
	elseif (($iTime - $iTimestamp < ($iRest + 86400)))
		return(date($oConfiguration->getCustomValue("DateFormatYesterday".$iVersion), $iTimestamp));
	else
		return(date($oConfiguration->getCustomValue("DateFormat".$iVersion), $iTimestamp));
}

?>