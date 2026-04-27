<?php
include_once("AccessBDD.php");

/**
 * Classe de construction des requêtes SQL
 * hérite de AccessBDD qui contient les requêtes de base
 */
class MyAccessBDD extends AccessBDD {
	    
    /**
     * constructeur qui appelle celui de la classe mère
     */
    public function __construct(){
        try{
            parent::__construct();
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * demande de recherche
     */	
    protected function traitementSelect(string $table, ?array $champs) : ?array{
        switch($table){ 
            case "abonnement/expiresbientot" :
                return $this->selectAbonnementsExpiresBientot();
            case "livre" :
                return $this->selectAllLivres();
            case "dvd" :
                return $this->selectAllDvd();
            case "revue" :
                return $this->selectAllRevues();
            case "exemplaire" :
                return $this->selectExemplairesRevue($champs);
            case "commandedocument" :
                return $this->selectCommandesDocument($champs);
            case "abonnement" :
                return $this->selectCommandesRevue($champs);
            case "genre" :
            case "public" :
            case "rayon" :
            case "etat" :
            case "suivi" :
                return $this->selectTableSimple($table);
            case "" :
                // return $this->uneFonction(parametres);
            default:
                return $this->selectTuplesOneTable($table, $champs);
        }	
    }

    /**
     * demande d'ajout (insert)
     */	
    protected function traitementInsert(string $table, ?array $champs) : ?int{
        switch($table){
            case "commandedocument" :
                return $this->insertCommandeDocument($champs);
            case "abonnement" :
                return $this->insertCommandeRevue($champs);
            default:                    
                return $this->insertOneTupleOneTable($table, $champs);
        }
    }
    
    /**
     * demande de modification (update)
     */	
    protected function traitementUpdate(string $table, ?string $id, ?array $champs) : ?int{
        switch($table){
            case "commandedocument" :
                return $this->updateSuiviCommande($id, $champs);
            default:                    
                return $this->updateOneTupleOneTable($table, $id, $champs);
        }	
    }  
    
    /**
     * demande de suppression (delete)
     */	
    protected function traitementDelete(string $table, ?array $champs) : ?int{
        switch($table){
            case "commandedocument" :
                return $this->deleteCommandeDocument($champs);
            case "abonnement" :
                return $this->deleteTuplesOneTable($table, $champs);
            default:                    
                return $this->deleteTuplesOneTable($table, $champs);
        }
    }	    
        
    /**
     * récupère les tuples d'une seule table
     */
    private function selectTuplesOneTable(string $table, ?array $champs) : ?array{
        if(empty($champs)){
            $requete = "select * from $table;";
            return $this->conn->queryBDD($requete);  
        }else{
            $requete = "select * from $table where ";
            foreach ($champs as $key => $value){
                $requete .= "$key=:$key and ";
            }
            $requete = substr($requete, 0, strlen($requete)-5);	          
            return $this->conn->queryBDD($requete, $champs);
        }
    }	

    /**
     * demande d'ajout (insert) d'un tuple dans une table
     */	
    private function insertOneTupleOneTable(string $table, ?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        $requete = "insert into $table (";
        foreach ($champs as $key => $value){
            $requete .= "$key,";
        }
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ") values (";
        foreach ($champs as $key => $value){
            $requete .= ":$key,";
        }
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ");";
        return $this->conn->updateBDD($requete, $champs);
    }

    /**
     * demande de modification (update) d'un tuple dans une table
     */	
    private function updateOneTupleOneTable(string $table, ?string $id, ?array $champs) : ?int {
        if(empty($champs)){
            return null;
        }
        if(is_null($id)){
            return null;
        }
        $requete = "update $table set ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key,";
        }
        $requete = substr($requete, 0, strlen($requete)-1);				
        $champs["id"] = $id;
        $requete .= " where id=:id;";		
        return $this->conn->updateBDD($requete, $champs);	        
    }
    
    /**
     * demande de suppression (delete) d'un ou plusieurs tuples dans une table
     */
    private function deleteTuplesOneTable(string $table, ?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        $requete = "delete from $table where ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key and ";
        }
        $requete = substr($requete, 0, strlen($requete)-5);   
        return $this->conn->updateBDD($requete, $champs);	        
    }
 
    /**
     * récupère toutes les lignes d'une table simple (qui contient juste id et libelle)
     */
    private function selectTableSimple(string $table) : ?array{
        $requete = "select * from $table order by libelle;";		
        return $this->conn->queryBDD($requete);	    
    }
    
    /**
     * récupère toutes les lignes de la table Livre et les tables associées
     */
    private function selectAllLivres() : ?array{
        $requete = "Select l.id, l.ISBN, l.auteur, d.titre, d.image, l.collection, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from livre l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";		
        return $this->conn->queryBDD($requete);
    }	

    /**
     * récupère toutes les lignes de la table DVD et les tables associées
     */
    private function selectAllDvd() : ?array{
        $requete = "Select l.id, l.duree, l.realisateur, d.titre, d.image, l.synopsis, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from dvd l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";	
        return $this->conn->queryBDD($requete);
    }	

    /**
     * récupère toutes les lignes de la table Revue et les tables associées
     */
    private function selectAllRevues() : ?array{
        $requete = "Select l.id, l.periodicite, d.titre, d.image, l.delaiMiseADispo, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from revue l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";
        return $this->conn->queryBDD($requete);
    }	

    /**
     * récupère tous les exemplaires d'une revue
     */
    private function selectExemplairesRevue(?array $champs) : ?array{
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('id', $champs)){
            return null;
        }
        $champNecessaire['id'] = $champs['id'];
        $requete = "Select e.id, e.numero, e.dateAchat, e.photo, e.idEtat ";
        $requete .= "from exemplaire e join document d on e.id=d.id ";
        $requete .= "where e.id = :id ";
        $requete .= "order by e.dateAchat DESC";
        return $this->conn->queryBDD($requete, $champNecessaire);
    }

    /**
     * récupère toutes les commandes d'un livre ou DVD
     */
    private function selectCommandesDocument(?array $champs) : ?array{
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('id', $champs)){
            return null;
        }
        $champNecessaire['id'] = $champs['id'];
        $requete = "SELECT c.id, c.dateCommande, c.montant, cd.nbExemplaire, ";
        $requete .= "cd.idLivreDvd, cd.idSuivi, s.libelle as suivi ";
        $requete .= "FROM commande c ";
        $requete .= "JOIN commandedocument cd ON c.id = cd.id ";
        $requete .= "JOIN suivi s ON cd.idSuivi = s.id ";
        $requete .= "WHERE cd.idLivreDvd = :id ";
        $requete .= "ORDER BY c.dateCommande DESC";
        return $this->conn->queryBDD($requete, $champNecessaire);
    }

    /**
     * insère une commande document (dans commande ET commandedocument)
     */
    private function insertCommandeDocument(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        $requete = "INSERT INTO commande (id, dateCommande, montant) ";
        $requete .= "VALUES (:Id, :DateCommande, :Montant)";
        $result = $this->conn->updateBDD($requete, $champs);
        if($result === null){
            return null;
        }
        $requete = "INSERT INTO commandedocument (id, nbExemplaire, idLivreDvd, idSuivi) ";
        $requete .= "VALUES (:Id, :NbExemplaire, :IdLivreDvd, :IdSuivi)";
        return $this->conn->updateBDD($requete, $champs);
    }

    /**
     * modifie le suivi d'une commande document
     */
    private function updateSuiviCommande(?string $id, ?array $champs) : ?int{
        if(empty($champs) || is_null($id)){
            return null;
        }
        $idSuivi = null;
        foreach($champs as $key => $value){
            if(strtolower($key) === 'idsuivi'){
                $idSuivi = $value;
                break;
            }
        }
        if(is_null($idSuivi)){
            return null;
        }
        $requete = "UPDATE commandedocument SET idSuivi = :idSuivi WHERE id = :id";
        return $this->conn->updateBDD($requete, ['idSuivi' => $idSuivi, 'id' => $id]);
    }

    /**
     * supprime une commande document
     */
    private function deleteCommandeDocument(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        $requete = "DELETE FROM commande WHERE id = :id";
        return $this->conn->updateBDD($requete, $champs);
    }

    /**
     * récupère tous les abonnements d'une revue
     */
    private function selectCommandesRevue(?array $champs) : ?array{
        if(empty($champs) || !array_key_exists('id', $champs)){
            return null;
        }
        $requete = "SELECT a.id, a.dateCommande, a.montant, a.dateFinAbonnement, a.idRevue ";
        $requete .= "FROM abonnement a ";
        $requete .= "WHERE a.idRevue = :id ";
        $requete .= "ORDER BY a.dateCommande DESC";
        return $this->conn->queryBDD($requete, ['id' => $champs['id']]);
    }

    /**
     * insère un abonnement
     */
    private function insertCommandeRevue(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        $requete = "INSERT INTO abonnement (id, dateCommande, montant, dateFinAbonnement, idRevue) ";
        $requete .= "VALUES (:Id, :DateCommande, :Montant, :DateFinAbonnement, :IdRevue)";
        return $this->conn->updateBDD($requete, $champs);
    }

    /**
     * récupère les abonnements qui expirent dans moins de 30 jours
     */
    private function selectAbonnementsExpiresBientot() : ?array{
        $requete = "SELECT a.id, a.dateCommande, a.montant, a.dateFinAbonnement, a.idRevue, d.titre ";
        $requete .= "FROM abonnement a ";
        $requete .= "JOIN document d ON a.idRevue = d.id ";
        $requete .= "WHERE a.dateFinAbonnement BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) ";
        $requete .= "ORDER BY a.dateFinAbonnement ASC";
        return $this->conn->queryBDD($requete);
    }
}