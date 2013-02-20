<?php

/**
 * @name SQLManager
 * @description Classe per Gestire il Database
 * @author StefanoV
 * @copyright 2010
 */

class SQLManager
{
	var $risorsa; // risorsa del db
	var $dieerror = true; // esce con il mysql error
	var $mailerror = ""; // invia una mail con l'errore della query.
	var $logfile = ""; // percorso dove salvare il log delle query
	var $logall = false; // decide se mostrare anche le query che hanno avuto successo
	
	function SQLManager($dieerror = true, $mailerror = "", $logfile = "", $logall = false)
	{
		$this->dieerror = $dieerror;
		$this->mailerror = $mailerror;
		$this->logfile = $logfile;
		$this->logall = $logall;
	}
	/*********************************** Funzioni Base ****************************************/
	
	/**
	 * Permette di usare una risorsa già aperta
	 *
	 * Param: $res (resource) - risorsa da utilizzare
	 */
	function usaRisorsa($res)
	{
		// se $res è settata, non vuota, ed è una risorsa
		if(isset($res) && !empty($res) && is_resource($res))
		{
			// applicala come risorsa globale
			$this->risorsa = $res;
		}
	}
	
	/**
	 * Connette lo script al Database MySQL
	 *
	 * Param: $host (string) - server del database
	 * Param: $user (string) - username del database
	 * Param: $pass (string) - password del database
	 * Param: $db (string) - nome del database
	 */
	function Open($host, $user, $pass, $db)
	{
		// se i campi sono inseriti
		if(empty($host) || empty($user) || empty($db))
			exit();
		
		// connetto al' host
		$ris = mysql_connect($host, $user, $pass) or $this->getErr("Errore di connessione all'host!");
		
		// seleziono il db
		mysql_select_db($db, $ris) or $this->getErr("Errore di selezione del database!");
		
		// setto la risorsa come globale della classe
		$this->risorsa = $ris;
	}
	
	/**
	 * Libera le risorse della risorsa risultante dalla query
	 *
	 * Param: $query (resource link) - la risorsa ottenuta dalla funzione doQuery
	 */
	function Free($query)
	{
		if(mysql_free_result($query))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Restituisce l'ID generato dall' ultima query INSERT
	 */
	function lastID()
	{
		return @mysql_insert_id();
	}
	
	/**
	 * Restituisce le righe contate nella query
	 *
	 * Param: $query (resource link) - la risorsa ottenuta dalla funzione doQuery
	 */
	function Count($query)
	{
		return @mysql_num_rows($query);
	}
	
	/**
	 * Restituisce true se trova almeno un record
	 *
	 * Param: $query (resource link) - la risorsa ottenuta dalla funzione doQuery
	 */
	function Found($query)
	{
		if($this->Count($query) != 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Mostra l'errore o manda la mail con l'errore
	 *
	 * Param: $errore (string) - stringa di errore restituita da MySQL
	 * Param: $mErr (string) - stringa di errore da mostrare scelta da noi
	 */
	private function getErr($errore = "")
	{
		
		if(empty($errore)) $errore = mysql_error();
		
		// se bisogna mandare la mail
		if(!empty($this->mailerror))
		{
			// testo mail
			$testo = "Si è verificato un errore: ".$errore." \r\n \r\n Indirizzo del file chiamato: ".$_SERVER['REQUEST_URI'];
			
			// destinatario
			$to = $this->mailerror;
			
			// soggetto
			$subject = "SQLManager: Errore query.";
			
			// headers
			$headers = "From: $to\r\n";
			$headers .= "Reply-To: $to\r\n";
			$headers .= "Return-Path: $to\r\n";
			
			// manda la mail
			if (!mail($to, $subject, $testo, $headers))
			{
				// se non va a buon fine, mostra l'errore
			   die("Errore durante l'invio della Segnalazione!");
			}
			
		}
		
		if(!empty($this->logfile))
		{
			$fp = @fopen($this->logfile, "a");
			@fwrite($fp, $tipo . date("d/m/Y H:i:s")." - Si è verificato un errore: $errore\r\n");
			@fclose($fp);
		}
		
		if($this->dieerror)
		{
			// mostra errore personale
			die($errore);
		}
	}
	
	/**
	 * Esegue la query al database
	 *
	 * Param: $query (string) - la query da eseguire al database
	 * Param: $manualError (string) - errore personalizzato in caso di fallimento della query
	 */
	function Query($query, $manualError = "")
	{
		//$query = stripslashes($query);
		//$query = addslashes($query);

        /*$fp = @fopen($this->logfile, "a");
				@fwrite($fp, $tipo . date("d/m/Y H:i:s")." - errore: $query\r\n");
				@fclose($fp);*/
		// eseguo la query
		$rs = mysql_query($query) or $this->getErr($manualError);
		
		// se è andata bene
		if($rs)
		{
			if(!empty($this->logfile) && $this->logall)
			{
				$fp = @fopen($this->logfile, "a");
				@fwrite($fp, $tipo . date("d/m/Y H:i:s")." - Query eseguita correttamente: $query\r\n");
				@fclose($fp);
			}
			// restituisco il link di risorsa
			return $rs;
		}
	}
	
	/**
	 * Ottiene i dati come oggetti (da usare come mysql_fetch_object)
	 *
	 * Param: $query (resource link) - la risorsa ottenuta dalla funzione doQuery
	 */
	function getObject($query)
	{
		// ottiene le righe come oggetto
		$rig = @mysql_fetch_object($query);
		
		// restituisce il tutto
		return $rig;
	}
	
	/**
	 * Chiude la connessione al Database
	 */
	function Close()
	{
		mysql_close($this->risorsa);
	}
	
	/*********************************** Funzioni Utili ****************************************/
	
	/**
	 * Ottiene i dati ottenuti da una query SELECT in un array
	 *
	 * Param: $query (resource link) - la risorsa ottenuta dalla funzione doQuery
	 * Param: $associativo (boolean) - determina se creare un sotto-array associativo per ogni riga, oppure no
	 */
	function getArray($query, $associativo = true)
	{
		// dichiaro e svuoto l'array
		$arrayCampi = array();
		
		// ciclo i nomi dei campi nella SELECT e li metto in array
		for($i = 0; $i < @mysql_num_fields($query); $i++)
		{
			$arrayCampi[] = @mysql_fetch_field($query)->name;
		}
		
		// dichiarazione e svuotamento array $dati
		$dati = array();
		
		// ciclo per ottenere i valori associativi in $linea
		while($linea = @mysql_fetch_array($query, MYSQL_ASSOC))
		{
			
			// dichiaro e svuoto l'array $par
			$par = array();
			
			// ciclo i nomi passati nell'array $arrayCampi
			foreach($arrayCampi as $nomi)
			{
				// se è impostato l'associativo
				if($associativo)
				{
					// mette in $par i valori come array associativo
					$par[$nomi] = $linea[$nomi];
				}
				else // ... altrimenti ... 
				{
					// mette in $par i valori come array numerato
					$par[] = $linea[$nomi];
				}
				
			}
			
			// aggiunge all'array $dati, l'array $par
			$dati[] = $par;
		}
		
		// restituisce i dati
		return $dati;
	}
	
	/**
	 * Muove il puntatore interno ad una riga
	 *
	 * Param: $query (resource) - la risorsa della query
	 * Param: $riga (int) - la riga da cui iniziare - Default: 0
	 */
	function dataSeek($query, $riga = 0)
	{
		return @mysql_data_seek($query, $riga);
	}
	
	/**
	 * Inserisce un array (chiave => valore) nel database
	 *
	 * Param: $table (string) - la tabella del database
	 * Param: $array (array) - l'array da cui prendere i valori
	 */
	function insertArray($table, $array)
	{
		$keys = array_keys($array);
		
		$values = array_values($array);
		
		$sql = 'INSERT INTO ' . $table . '(' . implode(', ', $keys) . ') VALUES ("' . implode('", "', $values) . '")';
		
		return($this->Query($sql));
	}
	
	/**
	 * Resetta l'ultimo ID autoincrement
	 *
	 * Param: $table (string) - la tabella del database
	 */
	function resetIncrement($table)
	{
		$get = $this->Query("SELECT MAX(id) as mxid FROM $table");
		
		if($this->Found($get))
		{
			$max = $this->getObject($get);
			
			$mxid = (int)$max->mxid;
			
			$mxid++;
			
			$this->Query("ALTER TABLE $table AUTO_INCREMENT = $mxid");
		}
	}
	
	/**
	 * Ottiene un campo specifico
	 *
	 * Param: $campo (string) - il campo da restituire
	 * Param: $table (string) - la tabella da cui estrarre il campo
	 * Param: $where (string) - la clausula where
	 */
	function getField($campo, $table, $where)
	{
		$query = $this->Query("SELECT $campo FROM $table WHERE $where LIMIT 1");
		
		$risultato = $this->getObject($query);

		return $risultato->$campo;
	}

    function clean_input($in)
  {
    $in=stripslashes($in);
    return str_replace(array("<",">",'"',"'","\n"), array("&lt;","&gt;","&quot;","&#39;","<br />"), $in);
  }

}

?>