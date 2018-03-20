<?php

//
// CLASSE DE CONNEXION A UNE BASE DE DONNEES MYSQL
//
class ConnectionBD
{
	var $hote;
	var $user;
	var $pass;
	var $nombd;
	var $db;			// "Pointeur" vers la base de donn�es ouvertes
	

	// "Constructeur" permettant la connection � la base
	function ConnectionBD()
    	{
			$this->hote=getenv("DB_ADDR");
			$this->user=getenv("DB_USER");
			$this->pass=getenv("DB_PASSWORD");
			$this->nombd=getenv("DB_NAME");

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
