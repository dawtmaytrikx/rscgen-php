#!/usr/bin/php
<?php
/*
 * Command line RSCollection generator written by dawt.
 * Usage: php rscgen.php
 * 		  then follow instructions.
 * 
 * License: CC-BY-NC (http://creativecommons.org/licenses/by-nc/3.0/)
 */
	echo "Your file will be written by entering an empty line.\n".
		 "What would you like this collection to be called? (extension will be added automatically)".
		 "... no need to enter anything, if you're only adding 1 file!\n> ";
	$handle = fopen ("php://stdin","r");
	$title = trim(fgets($handle, 4096)).".rscollection";
	fclose($handle);
	echo "Goodie! This file will be called $title.\n\n";
	echo "Post the lines from your .rsfb/.rsfc file, or retroshare links to create a .rscollection file!\n> ";
	$handle = fopen ("php://stdin","r");
	$space = " ";
	$rsc = "<!DOCTYPE RsCollection>\r\n<RsCollection>\r\n";
	$counter = 0;
	$single = 0;
	if ($handle) {
		while (($line = fgets($handle, 4096)) !== false) {
			if ($line == "\n") {
				if ($counter !== 0) { die("\nSomething went wrong! Make sure the number of 'd's matches the number of '-'s!\n"); }
				if ($single === 1) { $title = substr($name, 0, -(strlen(strrchr($name, ".")))).".rscollection"; }
				$filehandle = fopen($title, "w");
				fwrite($filehandle, $rsc."</RsCollection>");
				fclose($filehandle);
				fclose($handle);
				echo "\n$title successfully created!\n";
				exit(0);
			}
			$line = trim($line);
			if (strpos($line, 'retroshare://') === false) {
				$file = explode("|", $line);
				$type = substr($file[0], 0, 1);
				$name = substr($file[0], 1);
				if (($type == "d") && (strpos($file[1], '/') === 0)) {
					$rsc .= "$space<Directory name=\"$name\">\r\n";
					$space .= " ";
					$counter++;
				} elseif ($type == "-") {
					$space = substr($space, 0, -1);
					$rsc .= "$space</Directory>\r\n";
					$counter--;
				} elseif ($type == "f") {
					$size = $file[2];
					$hash = $file[1];
					$rsc .= "$space<File size=\"$size\" sha1=\"$hash\" name=\"$name\"/>\r\n";
					$single++;
				}
			} elseif (strpos($line, 'retroshare://') === 0) {
				$file = parse_url($line, PHP_URL_QUERY);
				parse_str($file, $output);
				$name = $output['name'];
				$size = $output['size'];
				$hash = $output['hash'];
				$rsc .= "$space<File size=\"$size\" sha1=\"$hash\" name=\"$name\"/>\r\n";
				$single++;
			}
		}
		fclose($handle);
	}
	exit(0);
?>
