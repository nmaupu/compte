<?php

//
// CLASSE DE CONNECTION A UNE BASE DE DONNEES MYSQL
//
class ConnectionBD
{
	var $hote="db";		// Machine qui h�berge le serveur mysql
	var $user="compte";	// Utilisateur autoris� � se connecter � la base
	var $pass="FH_9qj-T"; 		// Mot de passe de connection � la base
	var $nombd="compte";		// Nom de la base de donn�es
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
