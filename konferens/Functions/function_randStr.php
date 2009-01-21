<?PHP

// function_randStr.php
// Skriven av donald at design dot net, publicerad på http://www.php.net
// Modifierad av Eli Kaufman
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

function randStr ($iLen = 10)
{
	mt_srand((double)microtime()*10000);
	for ($i=0;$i<$iLen;$i++) {
		$x = mt_rand(1,3);
		$str .= (($x == 1) ? chr(mt_rand(48,57)) : (($x == 2) ? chr(mt_rand(65,90)) : chr(mt_rand(97,122))));
	}
	return($str);
}

?>