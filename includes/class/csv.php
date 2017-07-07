<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*
	CSV-Klassen
	-----------
	stellt Klassen zur verfügung die einen Datenbankähnlichen zugriff
	auf CSV-Dateien ermöglichen.

	Features
	- separierte und festlängen CSV-Dateien werden verarbeitet
	- merhzeilige separierte CSV-Datein werden unterstützt
	- Datenbankähnlicher-Zugriff auf die Feldernamen und Felderdaten
	- Filter für bestimmte Felder mit einem definierten Wert,
	  d.h. es werden nur die Felder verwendet, die einem Filter entsprechen auch
	  mehrere Filter sind möglich, diese werden mit "AND"-verknüpft
	- Daten-Typ-Konvertierung pro Spalte
	  z.B.
	  "234,45" > NUMBER() > 234.45
	  "22041981" > DATE('DDMMYYYY', 'DD.MM.YYYY' ) > 22.04.1981
	  "0000006789" > DECIMAL(2) > 67.89

	--------------------------------------------------------------------------------------------------------------------------------------------
	Funktionenbeschreibungen siehe jeweilige Klasse und Funktion
	--------------------------------------------------------------------------------------------------------------------------------------------



	|--------------------|
	| ChangeLog-Legende  |
	|--------------------|
	| -- = Debugging     |
	| ++ = neues Feature |
	| RR = neues Release |
	| -> = Version       |
	|--------------------|

	ChangeLog
	--------------------------------------------------------------------------------------------------------------------------------------------
	01.04.03
	-- Bug in der Typ-Konvertierung für NUMBER() beseitigt
	++ Funktion dumpResult() hinzugefügt
	   Ermöglicht eine einfache Fehlerbeseitigung.
	   Erzeugt eine HTML-Tabelle mit allen Werten der CSV-Datei
	++ Die Typ-Konvertierung kann jetzt festgelegt werden, auch wenn
	   die Daten noch nicht geparst wurden, indem für die Funktion
	   	setFieldType("Name eines gültigen Feldes", "TYP")
	   	(
	   		vorher musste erst nachdem die Funktion parseCSV aufgerufen wurde
	   		mittels einer ID das Feld angesprochen werden.
	   	)
	-> 1.2.2

	11.12.02
	-- Einige Bugs im Filtermechanismus im Zusammenhang mit
	   Festlängen-CSV beseitigt
	-> 1.0.1

	23.10.03
	RR Die CSV-Class wurde releast
	-> 1.0.0



	--------------------------------------------------------------------------------------------------------------------------------------------
	geproggt by Waldemar Derr mail@onanyzer.net
*/




class CSV
{
	/*
		Das ist eine Base-Klasse für die CSV-Importe
	*/


	var $CSVFile;
	var $CSVData;
	var $CSVError;
	var $FieldNames;      //beinhaltet die Feldnamen der Felder. FieldNames[field_id] = Feldname
	var $Fields;         //beinhaltet ein array mit den ganzen zeilen und feldern Fields[line][field_id] = value
	var $FieldTypes;    //enthält die Typ-Definitionen für die Felder FieldTypes[field_id] = type
	var $fetchCursor;  //Interner Zähler der von den Fetch-Funktionen benutzt wird

	/*
		enthält ein Array mit den definierten Regeln

			$filter["FELD"] Der erste Schlüssel betrifft das Felt auf den der Filter bezogen ist
					["operator"] Kann alle möglichen PHP-Operatoren enthalten == != > < <>
					["match_value"] Ist der zu vergleichende Wert
	*/
	var $Filter;


	function __construct()
	{
		$this->CSVError = array();
		$this->CSVData = "";
		$this->Filter = array();
	}


	function setFile($file)
	{
		/*
			Bestimmt die CSV-Datei und setzt den Inhalt der Datei
			in die CSVData in einem String
		*/

		$this->CSVFile = $file;

		if(file_exists($this->CSVFile) && ($this->CSVFile != "none") && !empty($this->CSVFile))
		{
			$this->setData(join("",file($this->CSVFile)));
		}
		else
		{
			$this->CSVError[] = "Datei " . $file . " existiert nicht oder keine Lese-Rechte";
			return FALSE;
		}
	}


	function setData($string)
	{
		/*
			Sollte zum Import von CSV-Daten keine Datei benutzt werden, so
			kann hier der String übergeben werden.
		*/

		$this->CSVData = $string;


		//Löschung von gefüllten Arrays
		$this->Fields = array();
		$this->FieldTypes = array();
		$this->FieldNames = array();
	}



	/*
		Start DataHandler-Funktionen
	=======================================================================================================
	*/


	function CSVFetchRow()
	{
		/*
			liefert die gesamte Zeile als indiziertes Array
			und setzt den internen Zähler hoch.
			- ähnlich mysql_fetch_rows
		*/

		if($this->fetchCursor <= $this->CSVNumRows())
		{
			$r = $this->Fields[$this->fetchCursor];
			$this->fetchCursor++;
			return $r;
		}
		else
		{
			$this->CSVError[] = "Es existieren keine weiteren Datensätze";
			return FALSE;
		}
	}


	function CSVFetchArray($resultTyp = "BOTH")
	{
		/*
			liefert die gesamte Zeile als ein assoziatives und/oder numerisches Array
			$resultTyp bestimmt ob es ein assoziatives (ASSOC), numerisches (NUM) oder beides (BOTH)
			zurückgelifert wird
			- ähnlich mysql_fetch_array
		*/



		if($this->fetchCursor <= $this->CSVNumRows())
		{


			if( ($resultTyp == "NUM") || ($resultTyp == "BOTH") )
			{
				$r = $this->CSVFetchRow();

				if($resultTyp == "NUM") return $r;


				$this->fetchCursor--; //Weil diese var von der Funktion CSVFetchRow inkrementiert und im nächsten Fall "ASSOC" dann scheisse liefert
			}


			if( ($resultTyp == "ASSOC") || ($resultTyp == "BOTH") )
			{
				if(is_array($this->Fields[$this->fetchCursor]))
				{
					reset($this->Fields[$this->fetchCursor]);
					while(list($field_id, $field) = each($this->Fields[$this->fetchCursor]))
					{
						$r[$this->FieldNames[$field_id]] = $field;
					}
				}
			}

			$this->fetchCursor++;
			return $r;
		}
		else
		{
			$this->CSVError[] = "Es existieren keine weiteren Datensätze";
			return FALSE;
		}
	}


	function CSVFetchFieldNames()
	{
		/*
			liefert ein indiziertes Array
			mit allen Feldnamen in der richtigen Reihenfolge
		*/

		return $this->FieldNames;
	}


	function CSVFieldName($field_id)
	{
		/*
			liefert den Namen des Feldes mit der $feld_id
			- ähnlich mysql_field_name
		*/

		return $this->FieldNames[$field_id];
	}


	function CSVNumRows()
	{
		/*
			Liefert die anzahl der Datensätze
			- ähnlich mysql_num_rows
		*/


		return count($this->Fields);
	}


	function CSVNumFields()
	{
		/*
			Liefert die Anzahl der Felder
			- ähnlich mysql_num_fields
		*/

		return count($this->FieldNames);
	}


	function setCursor($pos)
	{
		/*
			Setzt den internen Zähler auf eine bestimmte Position.
		*/

		$this->fetchCursor = $pos;
	}


	function getCursor()
	{
		/*
			Gibt die aktuelle position des Zeigers zurück
		*/

		return $this->fetchCursor;
	}


	function resetCursor()
	{
		/*
			Setzt den internen Zähler auf den Anfang der Ergebnisse,
			d.h. der nächste Aufruf von CSVFetchRow oder CSVFetchArray
			liefert wieder den ersten Datensatz.
		*/


		$this->setCursor(0);
	}


	function getFieldID($search_field)
	{
		/*
			Liefert anhand des Feldnamens die Feld-ID
		*/

		if(!is_array($this->FieldNames)) return FALSE;

		foreach($this->FieldNames AS $field_id => $field_name)
		{
			//echo $search_field . " " . $field_name . " hallo<br>";
			if(trim($search_field) == trim($field_name))
			{
				return $field_id;
			}
		}

		return FALSE;
	}


	/*
		End DataHandler-Funktionen
	=======================================================================================================
	*/


	/*
		Filter-Funktionen
	=======================================================================================================
	*/




	function addFilter($field, $operator, $match_value)
	{
		/*
			Fügt einen Filter für ein bestimmtes Feld
		*/

		if(!$fieldID = $this->getFieldID($field)) $fieldID = $field;
		$field = $fieldID . "||" . count($this->Filter);

		$this->Filter[$field]["operator"] = $operator;
		$this->Filter[$field]["match_value"] = $match_value;


	}


	function applyFilter()
	{
		/*
			Wendet die gesetzten Filter an.
			Wird von der jeweiligen parseCSV Funktion aufgerufen.
		*/

		if(!count($this->Filter)) return FALSE;

		$results = array();
		$cc = 0;

		foreach($this->Filter AS $match_field => $match_values)
		{
			$match_field = substr($match_field, 0, strpos($match_field, "||"));

			//Bei Trennzeichen-CSV-Dateien steht im key anstatt der id der name des Feldes. Hier wird die id des feldes ermittelt
			if(!is_numeric($match_field) && (($fieldID = $this->getFieldID($match_field)) !== FALSE) ) $match_field = $fieldID;


			$operator = $match_values["operator"];
			$match_value = $match_values["match_value"];

			$results[$cc] = $this->Fields;

			foreach($this->Fields AS $line => $fields)
			{
				$filterResult = FALSE;

				switch($operator)
				{
					case "==": case "=":
						if($fields[$match_field] == $match_value) $filterResult = TRUE;

						break;

					case "!=" : case "!":
						if($fields[$match_field] != $match_value) $filterResult = TRUE;

						break;

					case "<": case "lt":
						if($fields[$match_field] < $match_value) $filterResult = TRUE;

						break;

					case ">": case "gt":
						if($fields[$match_field] > $match_value) $filterResult = TRUE;

						break;
				}


				if(!$filterResult)
				{
					/*
						Der definierte Filter trifft für diesen Datensatz zu,
						deswegen löschen wir einfach mal diese Zeile aus dem
						ErgebnisPuffer
					*/

					unset($results[$cc][$line]);
				}

			}

			$cc++;
		}


		//Die einzelnen Ergebnisse jedes Filters werden jetzt zusammengefasst
		$this->Fields = array();
		foreach($results AS $fields) $this->Fields = $this->array_merge_better($this->Fields, $fields);

		//Lückenlose (d.h. durchgehend nummeriert) Arrayzusammensetzung
		$r = "";
		foreach($this->Fields AS $fields) $r[] = $fields;
		$this->Fields = $r;


		/*
		//Debugging
		echo "<pre><b>Filter:</b>";
		print_r($this->Filter);
		echo "<b>Separierte Ergebnisse durch die jeweiligen Filter</b>";
		print_r($results);
		echo "<b>Feldernamen:</b>";
		print_r($this->FieldNames);
		echo "\n\n\n<b>Felder:</b>";
		print_r($this->Fields);
		echo "</pre>";
		*/




		return TRUE;

	}

	/*
		End Filter-Funktionen
	=======================================================================================================
	*/


	/*
		Typ-Umwandlung-Funktionen
	=======================================================================================================
	*/



	function setFieldType($field, $type="")
	{
		/*
			Setzt den Typ des Felds

			$field kann eine ID oder Name eines gültigen Feldes sein

			der Typ kann folgende Werte annehmen:

				DECIMAL(y) numerische Werte mit Anzahl y von Nachkommastellen z.b. "0000006789" mit DECIMAL(2) eine 67.89 liefern
				NUMBER()   konvertiert einen String in einen numerischen Wert z.B. "234,45" > 234.45

				DATE(aufbau, ausgabe)
				           ist ein Sonderfall und bekommt in der runden Klammer die Beschreibung über die Felder

						s = Sekunden
						m = Minuten
						h = Stunden
				           	D = Tag
				           	M = Monat
				           	Y = Jahr

				           Bsp.:
				           	"22041981" > DATE('DDMMYYYY', 'DD.MM.YYYY' ) > 22.04.1981
				           	             DATE('DDMMYYYY', 'YYYY/MM/DD' ) > 1981/04/22

		*/

		$this->FieldTypes[$field] = $type;
	}


	function _prepareFieldTypes()
	{
		/*
			!!!Interne Funktion bitte nicht benutzen!!!

			Konvertiert Namen von Feldern, die einer Typ-Konvertierung zugewiesen wurden,
			in IDs, dies ist nötig wenn die Typ-Konvertierung festgelegt werden, obwohl
			die CSV-Daten noch nicht geparst wurden.
		*/

		foreach($this->FieldTypes AS $field => $value)
		{
			if(is_string($field))
			{
				unset($this->FieldTypes[$field]);
				if(($fieldID = $this->getFieldID($field)) !== FALSE) $this->FieldTypes[$fieldID] = $value;
			}
		}
	}



	function convertFieldType()
	{
		/*
			Konvertiert alle gefetchten Felder in die definierten Typen
		*/

		if(!count($this->FieldTypes)) return FALSE;
		$this->_prepareFieldTypes();

		$valid_holder = array("m", "h", "D", "M", "Y"); //erlaubte Platzhalter in der Datum-Definition

		foreach($this->Fields AS $line_id => $line)
		{
			foreach($line AS $field_id => $value)
			{
				if(!isset($this->FieldTypes[$field_id]) || empty($this->FieldTypes[$field_id]) || empty($value)) continue;



				$convert_type = $this->FieldTypes[$field_id];
				$convert_arg  = substr($convert_type, strpos($convert_type, "(")+1, strlen($convert_type) - (strpos($convert_type, "(") + 2));

				if(strpos($convert_arg, ",")) //mehrere Argumente
				{
					$convert_arg = explode(",", $convert_arg);

					reset($convert_arg);

					while(list($key2, $value2) = each($convert_arg))
					{
						$value2 = trim($value2);
						if(substr($value2, 0, 1) == "'") $value2 = str_replace("'", "", $value2);

						$convert_arg[$key2] = $value2;
					}
				}

				$convert_value = "";

				switch(trim(strtolower(substr($convert_type, 0, strpos($convert_type, "(")))))
				{
					case "decimal": case "double":
						$convert_value = substr($value, 0, strlen($value) - $convert_arg) . "." . substr($value, $convert_arg * -1) ;
						$convert_value = doubleval($convert_value);
						break;

					case "number": case "int": case "integer":
						$convert_value = str_replace(",", ".", $value);
						$convert_value = doubleval($convert_value);
						break;

					case "date":
						$holder_define = $convert_arg[0]; //hier wurden die Platzhalter definiert
						if(isset($holder_values)) unset($holder_values);


						for($cc = 0; $cc < strlen($value); $cc++)
						{
							$sub_value = substr($value, $cc, 1);
							$holder = substr($holder_define, $cc, 1);

							if(in_array($holder, $valid_holder))
							{
								$holder_values[$holder] .= $sub_value;
							}
						}

						$holder_define = $convert_arg[1];

						for($cc = 0; $cc < strlen($holder_define); $cc++)
						{
							$holder = substr($holder_define, $cc, 1);

							if(in_array($holder, $valid_holder))
							{
								$convert_value .= substr($holder_values[$holder], 0, 1);
								$holder_values[$holder] = substr($holder_values[$holder], 1);
							}
							else
							{
								$convert_value .= $holder;
							}
						}

						break;
				}

				if($convert_value !== $value)  $this->Fields[$line_id][$field_id] = $convert_value;
			}
		}
	}



	/*
		End Typ-Umwandlung-Funktionen
	=======================================================================================================
	*/


	/*
		Exception-Funktionen
	=======================================================================================================
	*/

	function echoCSVError()
	{
		/*
			gibt alle Fehler aus
		*/

		foreach($this->CSVError AS $pos => $error_str)
		{
			echo "- " . ($pos+1) . ". " . $error_str . "<br>";
		}
	}


	function isOK($error_output = TRUE)
	{
		//Liefert TRUE or FALSE, ob alles ok ist. Der optionale Parameter gibt die fehler aus.

		if($error_output) $this->echoCSVError();
		return ((count($this->CSVError) > 0) ? FALSE : TRUE);
	}


	/*
		End Exception-Funktionen
	=======================================================================================================
	*/



	/*
		MISC
	=======================================================================================================
	*/

	function array_merge_better($a1,$a2)
	{
		if(!is_array($a1)) $a1 = array();
		if(!is_array($a2)) $a2 = array();

		// if(!is_array($a1) || !is_array($a2)) return false;
		$newarray = $a1;

		while (list($key, $val) = each($a2))
		{
			if (is_array($val) && is_array($newarray[$key]))
			{
				$newarray[$key] = $this->array_merge_better($newarray[$key], $val);
			}
			else
			{
				$newarray[$key] = $val;
			}
		}

		return $newarray;
	}


	function dumpResult()
	{
		/*
			Gibt eine HTML-Tabelle mit allen Werten der CSV-Datei aus
		*/

		$prevFetchCursor = $this->getCursor();
		$this->resetCursor();

		$fields = $this->CSVFetchFieldNames();
		echo "<table border='1'><tr>";

		foreach($fields AS $feld)
		{
			echo "<td><b>" . $feld . "</b></td>";
		}

		echo "</tr>";

		while($row = $this->CSVFetchArray("ASSOC"))
		{
			echo "<tr>";

			foreach($row AS $feld)
			{
				echo "<td>&nbsp;" . $feld . "</td>";
			}

			echo "</tr>";
		}

		echo "</table>";

		$this->setCursor($prevFetchCursor);
	}

	/*
		End - MISC
	=======================================================================================================
	*/
}



class CSVImport extends CSV
{
	var $FieldDelim;


	function __construct()
	{
		/*
			INIT-Funktion
		*/

		parent::CSV();
		$this->FieldDelim = ";";

	}


	function setDelim($delimiter)
	{
		/*
			Legt das Trennzeichen zwischen den Feldern fest
		*/

		$this->FieldDelim = $delimiter;
	}



	function parseCSV()
	{
		/*
			Parst das komplette CSV-Data durch
			und erstellt die Arrays:
				Fields mit den Datensätzen
				FieldNames mit den Feldnamen
		*/

		if($this->CSVData)
		{
			$akt_line  = 0;
			$akt_field = 0;
			$akt_field_value = "";
			$last_char = "";
			$quote = 0;
			$field_input = 0;
			$head_complete = 0;

			$end_cc = strlen($this->CSVData);

			for($cc = 0; $cc < $end_cc; $cc++)
			{
				$akt_char = substr($this->CSVData,$cc,1);

				if(($akt_char == "\"") && ($last_char != "\\")) //Abschliessung des eingeschlossenen Feldes. beschreibung siehe unten
				{
					$quote = !$quote;
					$akt_char = "";
				}

				if(!$quote)
				{
					if($akt_char == $this->FieldDelim) //Trennzeichen
					{
						$field_input = !$field_input;
						$akt_char = "";
						$akt_field++;
						$akt_field_value = "";
					}
					elseif(($akt_char == "\\") && $field_input) //Escape-Zeichen
					{
						$field_input++;
						$quote++;
					}
					elseif($akt_char == "\"") //Anführungszeichen kennzeichenen ein eingeschlossenes Feld, d.h. dieses Feld kann das Trennzeichen als Text enthalten und mehrzeilig sein.
					{
						$quote--;

						if($field_input)
							$field_input--;
						else
							$field_input++;
					}
					elseif($akt_char == "\n") //Neuer Datensatz
					{

						if($head_complete && (($akt_field+1) > $this->CSVNumFields()))
						{
							$this->CSVError[] = "Fehler in <b>Zeile " . ($akt_line + 2) . "</b>";
						}

						$akt_line++;
						$akt_field = 0;
						if(!$head_complete) $akt_line = 0;
						$head_complete = 1;
						$akt_char = "";
						$akt_field_value = "";
					}
				}


				$last_char = $akt_char;
				if($akt_char == "\\") $akt_char = "";
				$akt_field_value .= $akt_char;



				if($head_complete)
				{
					$this->Fields[$akt_line][$akt_field] = trim($akt_field_value); //Felder befüllung
				}
				else
				{
					$this->FieldNames[$akt_field] = trim($akt_field_value); //Feldernamen befüllung
				}

			}


			if(!$akt_field) //Leeren Abschluss-Datensatz entfernen
			{
				unset($this->Fields[$akt_line]);
			}

			parent::convertFieldType();
			parent::applyFilter();
			$this->fetchCursor = 0;

			/*
			//Debugging
			echo "<pre><b>Feldernamen:</b>";
			print_r($this->FieldNames);
			echo "\n\n\n<b>Felder:</b>";
			print_r($this->Fields);
			echo "</pre>";
			*/


		}
		else
		{
			$this->CSVError[] = "Das CSV-Data ist nicht gefüllt";
			return FALSE;
		}
	}

}



class CSVFixImport extends CSV
{
	/*
		Erweitert die CSVImport-Klasse um die Möglichgkeit CSV-Dateien
		mit Festlängen-Werten zu importieren anstatt separierte Werte.
	*/

	var $FieldLengths; //Array mit den Längen der Felder


	function __construct()
	{
		parent::CSV();  //Konstruktor der Eltern-Klasse aufrufen
		$this->FieldLengths = array();
	}


	function addCSVField($name, $length, $type = "")
	{
		/*
			Fügt ein neues Feld hinzu, diese Längen werden beim
			importieren der Datei berücksichtigt. Die übergebenen
			Felder müssen in der gleichen Reihenfolge wie sie in der
			Datei stehen übergeben werden.

			$type ist optional und legt den Typ des Feldes fest,
				Beschreibung siehe Funktion setFieldType() in der Klasse CSV
		*/

		$cursor = count($this->FieldNames);

		if(!$name) $name = "Feld " . ($cursor + 1);

		$this->FieldNames[$cursor] = $name;
		$this->FieldLengths[$cursor] = $length;
		$this->setFieldType($cursor, $type);
	}


	function setFile($file)
	{
		parent::setFile($file);
		$this->CSVData = explode("\n", trim($this->CSVData));
	}


	function parseCSV()
	{
		/*
			Fetcht die Datensätze in den Puffer
		*/


		if($this->CSVData)
		{
			if(!count($this->FieldLengths))
			{
				$this->CSVError[] = "Die Felder wurden nicht definiert";
				return FALSE;
			}


			$currentLine = 0;

			foreach($this->CSVData AS $line)
			{
				$currentField = 0;
				$currentStringPos = 0;

				foreach($this->FieldLengths AS $FieldLength)
				{
					$value = trim(substr($line, $currentStringPos, $FieldLength));

					$this->Fields[$currentLine][$currentField] = $value;
					$currentStringPos += $FieldLength;
					$currentField++;
				}

				$currentLine++;
			}


			parent::convertFieldType();
			parent::applyFilter();
			$this->fetchCursor = 0;
		}
		else
		{
			$this->CSVError[] = "Keine zu importierende Daten gesetzt";
			return FALSE;
		}
	}
}

?>