<?php
/**
 * Created on Mar 3, 2007
 * Filename session.php
 */
class Session {
	private $ses_id;
	private $db;
	private $db_conn;
	private $table;
	private $ses_life;
	private $ses_start;
	private $fingerprintKey = 'sdfkj43545lkjlkmndsf89a*(&(Nhnkj2h349*&(';
	private $threshold = 25;
	static private $fingerprintChecks = 0;

	/**
	* Class Constructor
	*
	* @param $db string
	* @param $table string
	* @return NULL
	*/
	public function __construct($db, $table = 'session'){
		$this->db = $db;
		$this->table = $table;
		
		$this->db_conn = new DB;
		$this->db_conn->Connect();
	}
	
	/**
	* Open Session
	*
	* @param $path string
	* @param $name string
	* @return NULL
	*/
	public function open($path, $name){
		$this->ses_life = ini_get('session.gc_maxlifetime');
	}
	
	/**
	* Close Session
	*
	* @param $ses_id string
	* @return NULL
	*/
	public function close(){
		$this->gc();
	}
	
	/**
	* Read Session from DB
	*
	* @param $ses_id string
	* @return string
	*/
	public function read($ses_id){
		//global $db;
		
		$session_sql = 'SELECT * FROM `' . $this->table . '` WHERE ses_id = \'' . $ses_id . '\' LIMIT 1';
		$session_res = $this->db_conn->Query($session_sql, $this->db, false);
		if (!$session_res){
			return '';
		}
		$session_num = $this->db_conn->NumRows($session_res);
		if ($session_num > 0){
			$session_row = $this->db_conn->FetchArray($session_res);
			$ses_data = unserialize($session_row['ses_value']);
			$this->ses_start = $session_row['ses_start'];
			$this->ses_id = $ses_id;
			return $ses_data;
		}else{
			return '';
		}
	}
	
	/**
	* Write Session data to DB
	*
	* @param $ses_id string
	* @param $data string
	* @return bool
	*/
	public function write($ses_id, $data) {
		//global $db;
		
		if(!isset($this->ses_start))
			$this->ses_start = time();

		$session_sql = 'SELECT * FROM `' . $this->table . '` WHERE `ses_id` = \'' . $ses_id . '\' LIMIT 1';
		$res = $this->db_conn->Query($session_sql, $this->db, false);
		
		if($this->db_conn->NumRows($res) == 0) {
			$type = 'insert';
			$extra = '';
			$info = array('ses_id' => $ses_id, 'last_access' => time(), 'ses_start' => $this->ses_start, 'ses_value' => serialize($data));
		}else{
			$type = 'update';
			$extra = '`ses_id` = \'' . $ses_id . '\'';
			$info = array('last_access' => time(), 'ses_value' => serialize($data));
		}

		// Do the Operation
		$session_res = $this->db_conn->Perform($this->table, $info, $type, $extra, $this->db, false);
		if (!$session_res)
			return false;
		
		return true;
	}
	
	/**
	* Destroy the session
	*
	* @param $ses_id string
	* @return null
	*/
	public function destroy($ses_id){
		//global $db;
		
		$session_sql = 'DELETE FROM `' . $this->table . '` WHERE `ses_id` = \'' . $ses_id . '\' LIMIT 1';
		$res = $this->db_conn->Query($session_sql, $this->db, false);
		
		return true;
	}
	
	/**
	* Garbase Collector
	*
	* @return bool
	*/
	public function gc(){
		//global $db;
		
		$ses_life = time() - $this->ses_life;
		$session_sql = 'DELETE FROM `' . $this->table . '` WHERE `last_access` < ' . $ses_life . '';
		$session_res = $this->db_conn->Query($session_sql, $this->db, false);

		if (!$session_res)
			return false;
			
		return true;
	}
}
?>