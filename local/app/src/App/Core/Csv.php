<?php
namespace App\Core;

class Csv {

    /**
     * Return CSV as array, auto detect delimiter: http://stackoverflow.com/a/22540323
     */
    public static function read($fileName)
	{
		if(! is_file($fileName)) return false;
		//detect these delimeters
		$delA = array(";", ",", "|", "\t");
		$linesA = array();
		$resultA = array();
	
		$maxLines = 20; //maximum lines to parse for detection, this can be higher for more precision
		$lines = count(file($fileName));
		if ($lines < $maxLines) {//if lines are less than the given maximum
			$maxLines = $lines;
		}
	
		//load lines
		foreach ($delA as $key => $del) {
			$rowNum = 0;
			if (($handle = fopen($fileName, "r")) !== false) {
				$linesA[$key] = array();
				while ((($data = fgetcsv($handle, 1000, $del)) !== false) && ($rowNum < $maxLines)) {
					$linesA[$key][] = count($data);
					$rowNum++;
				}
	
				fclose($handle);
			}
		}
	
		//count rows delimiter number discrepancy from each other
		foreach ($delA as $key => $del) {
			//echo 'try for key=' . $key . ' delimeter=' . $del;
			$discr = 0;
			foreach ($linesA[$key] as $actNum) {
				if ($actNum == 1) {
					$resultA[$key] = 65535; //there is only one column with this delimeter in this line, so this is not our delimiter, set this discrepancy to high
					break;
				}
	
				foreach ($linesA[$key] as $actNum2) {
					$discr += abs($actNum - $actNum2);
				}
	
				//if its the real delimeter this result should the nearest to 0
				//because in the ideal (errorless) case all lines have same column number
				$resultA[$key] = $discr;
			}
		}

	
		//select the discrepancy nearest to 0, this would be our delimiter
		$delRes = 65535;
		foreach ($resultA as $key => $res) {
			if ($res < $delRes) {
				$delRes = $res;
				$delKey = $key;
			}
		}
	
		$delimeter = $delA[$delKey];
	
		//echo '$delimeter=' . $delimeter;
	
		//get rows
		$row = 0;
		$rowsA = array();
		if (($handle = fopen($fileName, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, $delimeter)) !== false) {
				$rowsA[$row] = Array();
				$num = count($data);
				for ($c = 0; $c < $num; $c++) {
					$rowsA[$row][] = trim($data[$c]);
				}
				$row++;
			}
			fclose($handle);
		}
	
		return $rowsA;
	}
}