<?php

//
// CLASSE DE CONNEXION A UNE BASE DE DONNEES MYSQL
//
class ConnectionBD
{
	var $hote=getenv("DB_ADDR");
	var $user=getenv("DB_USER");
	var $pass=getenv("DB_PASSWORD");
	var $nombd=getenv("DB_NAME");
	var $db;			// "Pointeur" vers la base de donn�es ouvertes
	

	// "Constructeur" permettant la connection � la base
	function ConnectionBD()
    	{
    		// Connection � mysql
		$this->db=mysql_connect($this->hote,$this->user,$this->pass);
		if(!$this->db)
		{
			die("Connection � la base de donn�es impossible !");
			exit();
		}
		
		// S�lection de la base de donn�es
		$select=mysql_select_db($this->nombd,$this->db);
		if(!$select)
		{
			die("Base de donn�es introuvable !");
			$this->fermer_connection();
			exit();
		}
	}

	// Pour fermer manuellement la connection � la base
	function fermer_connexion()
	{
		if($this->db)
		{
			@mysql_close($this->db);
		}
	}
}

// Connection � la base
function connect_bd()
{
	return new ConnectionBD();
}

?>
