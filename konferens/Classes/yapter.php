<?php
/*****************************************************************************
  $Id: yapter.php,v 1.9 2003/08/05 11:06:56 nvie Exp $

  Yapter 2.13b1 - Yet Another PHP Template Engine ®
  Copyright (C) 2001-2002 Vincent Driessen

  This library is free software; you can redistribute it and/or
  modify it under the terms of the GNU Lesser General Public
  License as published by the Free Software Foundation; either
  version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this library; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

  For more information, visit http://yapter.sf.net/ or contact us at
  nvie@users.sourceforge.net
  The full terms of the GNU Lesser General Public License that apply to Yapter
  can be found at http://yapter.sf.net/LICENSE
 *****************************************************************************/

// Define Yapter's warning levels
define("E_YAPTER_NONE",    0);		// Be completely silent
define("E_YAPTER_ERROR",   1);		// Report only errors
define("E_YAPTER_WARNING", 2);		// Report errors and warnings
define("E_YAPTER_ALL",     3);		// Report errors, warnings and notices

class Template {
	var $_ROOT = "__DOCUMENT_ROOT";
	var $parseUnknownVars = false;	// Defines whether unknown variables should be removed or left alone
	var $blox = array();		// $blox[blockname]["content"]  holds the template's content
					// $blox[blockname]["numlines"] holds the number of lines in the block
					// $blox[blockname]["parsed"]   holds a string with the parsed data
					// $blox[$_ROOT]                always holds the main template
	var $blockDefs = array();	// Keeps track of all block-definitions from which multiple blocks...
					// ...can be created instances of

	var $vars = array();		// This array contains all variables. All are accessible from all blocks.

	var $log;			// Keep a log of all actions
	var $prelog;
	var $warningLevel;		// The level of verbosity Yapter complies with (see the E_* defines above)
	var $startTime;			// Holds the start time of the script, so that it can compare it to the...
					// ...end time to calculate the execution time. (For debugging purposes only.)
	
	var $missing_list;		// List of variable names that are declared but never set.

	/////////////////////////////////////////////////////////////////////

	function error($msg) {
		if ($this->warningLevel >= E_YAPTER_ERROR)
			die("<br />\n<b>Yapter error</b>: $msg<br />");	// Die here!
	}

	function warning($msg) {
		if ($this->warningLevel >= E_YAPTER_WARNING)
			echo "<br />\n<b>Yapter warning</b>: $msg<br />";
	}

	function notice($msg) {
		if ($this->warningLevel >= E_YAPTER_ALL)
			echo "<br />\n<b>Yapter notice</b>: $msg<br />";
	}

	function warn_var_not_set($varname) {
		if (!in_array($varname, $this->missing_list)) {
			$this->missing_list[] = $varname;	// Add it to the list...

			// ...and print a warning once.
			$this->warning("Variable <b>".htmlspecialchars($varname)."</b> found, but never assigned a value. (This message is shown only once for each variable.)");
		}
	}

	/////////////////////////////////////////////////////////////////////

	function Template($file, $level = E_YAPTER_ALL) {
		if (is_bool($level)) {
			//
			// Rationale:
			// =========
			// Older Yapter versions had the possibility of turning
			// on the so called "debug mode" with a bool parameter
			// as the second argument to this constructor.
			// However, since debug mode was dropped and the
			// warning level support was built in, it was a logical
			// step of replacing the second parameter.
			//
			// However, to prevent people from making mistakes,
			// we'll check if the user passed a boolean parameter.
			// If so, he or she is probably using debug mode, and
			// we'll issue a notice in these cases.
			//
			// Thanks to Ivo Koster.
			// 
			$this->notice("Debug mode is not supported anymore since Yapter version 2.12.");
			$this->warningLevel = E_YAPTER_ALL;
		}
		else
			$this->warningLevel = $level;

		$this->startTime = $this->getmicrotime();
		$this->addBlockFromFile($this->_ROOT, $file);
		$this->missing_list = array();
	}

	/* setParseMode(): specifies to parse unknown variables or not */
	function setParseMode($parseUnknownVars) {
		$this->parseUnknownVars = $parseUnknownVars;
	}

	/* setWarningLevel(): sets the level of verbosity which Yapter should obey */
	function setWarningLevel($level) {
		$this->warningLevel = $level;
	}

	/* addBlock(): adds a new block to the blox-array */
	function addBlock($blockname, $content) {
		$this->blox[$blockname]["content"] = $content;
		$this->blox[$blockname]["numlines"] = sizeof($this->blox[$blockname]["content"]);
		$this->blox[$blockname]["parsed"] = "";
		$this->prepare($blockname);
	}

	/* addBlockFromFile(): adds a new block, filling it with the specified's file contents */
	function addBlockFromFile($blockname, $file) {
		$content = @file($file) or $this->error("Cannot open template file <b>$file</b>!");
		if ($blockname != $this->_ROOT) {
			$this->addBlockDef($blockname, $content);
		}
		$this->addBlock($blockname, $content);
	}

	/* addBlockDef(): adds a block definition to the block-definition array from which other blocks can be copied */
	function addBlockDef($blockdef, $content) {
		$this->blockDefs[$blockdef] = $content;
	}

	/* addBlockFromDef(): copies a block from the block definition array */
	function addBlockFromDef($blockname, $blockdef) {
		$content = $this->blockDefs[$blockdef];
		$this->addBlock($blockname, $content);
	}

	/* prepare(): handles subprocessing of templates found in the main template file */
	function prepare($blockname) {
		$block = &$this->blox[$blockname];
		for ($i = 0; $i < $block["numlines"]; $i++) {
			if (isset($block["content"][$i])) {
				$line = $block["content"][$i];
			} else {
				continue;
			}

			// Try to find a tag-definition on this line
			if (preg_match("/\[(INCLUDE|BLOCK|END|REUSE|SET) ([A-Za-z0-9_.\/-]+)( AS ([A-Za-z0-9_-]+))?]/", $line, $matches)) {
				$type = $matches[1];
				$name = (!empty($matches[4])) ? $matches[4] : $matches[2];
				if ($type == "END" && $matches[2] == $currblockdef) {
					if (isset($matches[4])) {
						$this->error("Given \"AS\"-parameter not allowed in END-tags!");
					}

					// End the current block definition: add the block to the blox-array
					$this->addBlockDef($currblockdef, $currblockcontents);
					$this->addBlockFromDef($currblockname, $currblockdef);

					// Now, try to remove the block from the template definition, replacing it with a var
					for ($j = $i; $j >= $currblockstart; $j--) {
						if ($j == $currblockstart && $currblocktype == "BLOCK") {
							$block["content"][$j] = "\{$currblockname}\n";
						} else {
							unset($block["content"][$j]);
						}
					}

					// unset these thingies for further preparing
					unset($currblocktype);
					unset($currblockstart);
					unset($currblockname);
					unset($currblockdef);
					unset($currblockcontents);

				} elseif (($type == "SET" || $type == "BLOCK") && !isset($currblockname)) {

					if ($type == "BLOCK") {

						// Start block definition
						$currblocktype  = $type;
						$currblockstart = $i;
						$currblockname  = $name;
						$currblockdef   = $matches[2];

					} else {		// SET-tag

						// Start block definition
						if (isset($matches[4])) {
							$this->error("Given \"AS\"-parameter not allowed in SET-tags!");
						}
						$currblocktype  = $type;
						$currblockstart = $i;
						$currblockname  = $matches[2];
						$currblockdef   = $matches[2];

					}

				} elseif ($type == "INCLUDE" && !isset($currblockname)) {

					// Make this line a variable...
					$block["content"][$i] = "\{$name}\n";

					// ...and include the given file...
					$this->addBlockFromFile($name, $matches[2]);

				} elseif ($type == "REUSE" && !isset($currblockname)) {

					if (!isset($matches[4])) {
						$this->error("Missing \"AS\"-parameter in [REUSE <b>$name</b>] tag!");
					}

					// Make this line a variable...
					$block["content"][$i] = "\{$matches[4]}\n";

					// ...and get this REUSE value from the block definition list...
					$this->addBlockFromDef($matches[4], $matches[2]);

				} elseif ($currblockname != $name) {
					if ($currblockname) {
						$currblockcontents[] = $line;
					}
				}
			} else {
				// No tag-definition... just normal text so do nothing here
				if (!empty($currblockname)) {
					$currblockcontents[] = $line;
				}
			}
		}
	}

	/* parse(): parses the specified block, filling variables and nested blockdefs */
	function parse($blockname = "") {
		if (!$blockname) $blockname = $this->_ROOT;
		$block = &$this->blox[$blockname];
		$parsed = $block["content"];

		// Loop through all the lines of the template and parse variables one-by-one
		for ($i = 0; $i < $block["numlines"]; $i++) {
			if (!isset($parsed[$i])) {
				continue;
			}
			$line = $parsed[$i];

			// Look for variables in this line, processing it character-by-character
			unset($start);
			unset($buffer);
			for ($j = 0; $j < strlen($line); $j++) {
				$char = $line[$j];
				if (!isset($start) && $char == '{') {
					$start = $j;
				} elseif (isset($start) && $char == '}') {
					// The sequence {} is not a valid variable value
					if (!isset($buffer)) {
						unset($start);
						unset($buffer);
						continue;
					} else {
						// Gotcha! Now replace this variable with its contents
						// First, check to see if it's a variable or a block that has to be parsed
						if (isset($this->vars[$buffer])) {
							$value = $this->vars[$buffer];
						} elseif (isset($this->blox[$buffer])) {
							if ($this->blox[$buffer]["parsed"]) {
								// The value must be filled with the parsed data from the $buffer block
								$value = @implode("", $this->blox[$buffer]["parsed"]);
							} else {
								// Make the recursive call now
								$value = @implode("", $this->parse($buffer));
							}
						} else {
							// No variable or block name found by the name of $buffer

							// First, issue a warning!
							$this->warn_var_not_set($buffer);

							if ($this->parseUnknownVars) {
								// Unable to find variable, replace this one with an empty
								// string silently.
								$value = "";
							} else {
								// Unable to find variable, leave this one alone...
								unset($start);
								unset($buffer);
								continue;
							}
						}
						$part1 = substr($line, 0, $start);
						$part2 = substr($line, $start + strlen($buffer) + 2);
						$line = $part1 . $value . $part2;
						$j += strlen($value) - (strlen($buffer) + 2);
						unset($start);
						unset($buffer);
					}
				} elseif (isset($start)) {
					// Check to see $char is a proper character (range: [A-Za-z0-9_.-])
					if (($char >= 'a' && $char <= 'z') || ($char >= '0' && $char <= '9') || ($char >= 'A' && $char <= 'Z') || ($char == '_') || ($char == '.') || ($char == '-')) {
						if (!empty($buffer)) {
							$buffer .= $char;
						}
						else {
							$buffer = $char;
						}
					} else {
						unset($start);
						unset($buffer);
					}
				}
			}
			$parsed[$i] = $line;
		}

		$this->blox[$blockname]["parsed"] = array_merge($this->blox[$blockname]["parsed"], $parsed);
		return $this->blox[$blockname]["parsed"];
	}

	/* set(): assigns a value to a variabele inside curly brackets ("{" and "}") */
	function set($varname, $value) {
		$this->vars[$varname] = $value;
	}

	/* setVars(): assigns values to variables for each element in the given array
	   Contributed by: Ramiz
	 */
	function setVars($variables) {
		foreach($variables as $varname => $value)
			$this->vars[$varname] = $value;
	}

	/* setFile(): assigns the contents of a file to a variabele inside curly brackets ("{" and "}") */
	function setFile($varname, $filename) {
		$value = implode("", file($filename));
		$this->set($varname, $value);
	}

	/* getVar(): returns the value of the "varname" variable */
	function getVar($varname) {
		if ($this->vars[$varname]) {
			return $this->vars[$varname];
		} else {
			return "";
		}
	}

	/* getBlock(): returns the content of the "blockname" block */
	function getBlockContent($blockname) {
		if ($this->$blox[$blockname]["content"]) {
			return @implode("", $this->$blox[$blockname]["content"]);
		} else {
			return "";
		}
	}

	/* replace(): replaces the content of one block by another */
	function replace($block, $byblock) {
		$this->blox[$block]["content"] = $this->blox[$byblock]["content"];
		$this->blox[$block]["numlines"] = $this->blox[$byblock]["numlines"];
	}

	/* clear(): resets the parsed data to a null-string again and defines the block as "unparsed" */
	function clear($blockname) {
		$this->blox[$blockname]["parsed"] = "";
		unset($this->vars[$blockname]);	// often, a variabele is set whenever a block should be discarded...
						// ...now reset such a variable to make sure the block is not overriden
	}

	/* getContents(): gets the final contents to be outputted on the screen */
	function getContents($blockname = "") {
		if ($blockname == "") $blockname = $this->_ROOT;
		$parsed = $this->blox[$blockname]["parsed"];
		if ($parsed) {
			return implode("", $parsed);
		} else {
			return "";
		}
	}

	/* spit(): ouputs contents to screen */
	function spit() {
		echo $this->getContents();
	}

	function getmicrotime() {
		/* I got this getmicrotime()-function from the PHP.net website, but it seems to be
		   buggy, while it sometimes displays a negative execution time when you substract
		   the current time with the starting time of the script... I only noticed it at
		   my Windows localhost machine, not on Linux servers. Is anybody familiar with this
		   behaviour? Any information about this is welcome at nvie@users.sourceforge.net
		   for your co-operation. */
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	function execTime() {
		return round($this->getmicrotime() - $this->startTime, 5);
	}

	function executionTime() {
		echo "<p>\n\nThe execution time is <b>" . $this->execTime() . "</b> seconds.<br>\n";
	}
}
?>
