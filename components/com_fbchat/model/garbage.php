<?php
//namespace components\com_fbchat\model; 
/**
 * Classe che gestisce il garbage collector dei messaggi obsoleti nel database 
 * @package FBChat::components::com_fbchat
 * @subpackage model
 * @author 2Punti - Marco Biagioni
 * @version $Id: garbage.php 42 01/01/2011 16:31:20Z marco $     
 * @copyright (C) 2011 - 2PUNTI SRL
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html  
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class garbage{
	
	/**
	 * Memorizza se il garbage è attivato 
	 * @access private
	 * @var Boolean
	 */
	private $enabled;
	/**
	 * Rappresenta il massimo tempo oltre cui considerare un messaggio obsoleto 
	 * @access private
	 * @var int
	 */
	private $maxLifeTime;
	/**
	 * Decide la probabilità che il garbage collector venga avviato
	 * @access private
	 * @var int
	 */
	private $probability;
	/**
	 * @property Boolean $divisor - Il divisore probabilistico 
	 * @access private
	 * @var int
	 */
	private $divisor;
	/**
	 * Memorizza un reference all'oggetto database
	 * @access private
	 * @var Object &
	 */
	private $DBO;
	
	/**
	 * Setta i parametri di configurazione nelle private properties
	 * @param Object& $configParams
	 * @return Boolean
	 */ 
	public function __construct(&$configParams){
		/** 
		 * Inizializzazione oggetto garbage collector 
		 */
		$this->DBO = &JFactory::getDBO();
		$this->probability = (int)$configParams->get('probability');
		$this->maxLifeTime = (int)$configParams->get('maxlifetime');
		$this->enabled = (int)$configParams->get('enabled');
		$this->divisor = 100;
	}
	
	/**
	 * Esegue il garbage collector process dando avvio solo se il calcolo probabilistico ha esito positivo 
	 * @return Boolean
	 */ 
	public function execGC(){
		$match = $this->probabilityFn();
		if($match && $this->enabled){
			$query = $this->buildQuery();
			$this->DBO->setQuery($query);
			if(!$this->DBO->Query()){
				$errorMsg = $this->DBO->getErrorMsg();
				return $errorMsg;
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Esegue il calcolo probabilistico vero e proprio
	 * @return Boolean
	 */
	private function probabilityFn(){
		$randomNumber = rand(0, $this->divisor);
		if(0 < $randomNumber && $randomNumber <= $this->probability){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Costruisce la query di DELETE dal DB in caso di match
	 * @return String
	 */
	private function buildQuery(){
		$time_attuale = time();
		$soglia = $time_attuale - $this->maxLifeTime;
		//Assicuriamoci di cancellare messsaggi letti
		$query = "DELETE FROM #__fbchat WHERE (" . $this->DBO->nameQuote('sent') . "< " . $this->DBO->Quote($soglia) .
												 " AND " . $this->DBO->nameQuote('read') . " = 1)";
		return $query;
	}
}

?>