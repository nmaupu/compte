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
	var $db;			// "Pointeur" vers la base de données ouvertes
	

	// "Constructeur" permettant la connection à la base
	function ConnectionBD()
    	{
			$this->hote=getenv("DB_ADDR");
			$this->user=getenv("DB_USER");
			$this->pass=getenv("DB_PASSWORD");
			$this->nombd=getenv("DB_NAME");

    		// Connection à mysql
		$this->db=mysql_connect($this->hote,$this->user,$this->pass);
		if(!$this->db)
		{
			die("Connection à la base de données impossible !");
			exit();
		}
		
		// Sélection de la base de données
		$select=mysql_select_db($this->nombd,$this->db);
		if(!$select)
		{
			die("Base de données introuvable !");
			$this->fermer_connection();
			exit();
		}
	}

	// Pour fermer manuellement la connection à la base
	function fermer_connexion()
	{
		if($this->db)
		{
			@mysql_close($this->db);
		}
	}
}

// Connection à la base
function connect_bd()
{
	return new ConnectionBD();
}

?>
