<?php
/**
 * iZAP Videos plugin by iionly
 * (based on version 3.71b of the original izap_videos plugin for Elgg 1.7)
 * Contact: iionly@gmx.de
 * https://github.com/iionly
 *
 * Original developer of the iZAP Videos plugin:
 * @package Elgg videotizer, by iZAP Web Solutions
 * @license GNU Public License version 2
 * @Contact iZAP Team "<support@izap.in>"
 * @Founder Tarun Jangra "<tarun@izap.in>"
 * @link http://www.izap.in/
 *
 */

define('IN_PROCESS', 1);
define('PENDING', 2);

class izapQueue {
	// Sqlite connection string
	private $db_connection;

	/*
	 * If set to false (e.g. due to PDOException), all queues and arrays will behave like
	 * empty arrays, and no PDO queries will be made
	 */
	public static $pdoSupport = true;
	function  __construct() {
		// Installing and connecting to database
		try{
			$this->db_connection = $this->connect();
			$this->setup();
		}
		catch(PDOException $e) {
			if (self::$pdoSupport && elgg_is_admin_logged_in()) {
				self::$pdoSupport = false;
				register_error(elgg_echo("izap_videos:error:sqliteDrivers"));
				izapAdminSettings_izap_videos('izapVideoOptions', array('OFFSERVER', 'EMBED'), true);
			}
		}
	}

	private function connect() {
		$queue_db_location = elgg_get_data_path() . '/izap_queue_db';
		$queue_db_file = 'queue.db';
		if (!file_exists($queue_db_location . '/' . $queue_db_file)) {
			mkdir($queue_db_location);
		}
		return new PDO('sqlite:' . $queue_db_location . '/' . $queue_db_file);
	}


	// Initial setup in the database
	public function setup() {
		$queue_db = $this->db_connection;
		try{
			$queue_db->query("CREATE TABLE video_queue(
				guid INTEGER PRIMARY KEY ASC,
				main_file VARCHAR(255),
				title VARCHAR(255),
				url VARCHAR(255),
				access_id INTEGER,
				owner_id INTERGER,
				conversion INTEGER DEFAULT ".PENDING.",
				timestamp TIMESTAMP)");
			$queue_db->query("CREATE TABLE video_trash(
				guid INTEGER PRIMARY KEY ASC,
				main_file VARCHAR(255),
				title VARCHAR(255),
				url VARCHAR(255),
				access_id INTEGER,
				owner_id INTERGER,
				timestamp TIMESTAMP)");
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getCode();
		}
	}

	// Put items in the queue
	public function put($video, $file, $defined_access_id) {
		$queue_db = $this->db_connection;
		return $queue_db->exec("INSERT INTO video_queue (guid, main_file, title, url, access_id, owner_id, timestamp)
			VALUES('".$video->guid."',
				'".$file."',
				'".$video->title."',
				'".$video->getURL()."',
				'".$defined_access_id."',
				'".$video->owner_guid."',
				strftime('%s','now'))"
		);
	}

	// Put queued video in trash
	public function move_to_trash($guid) {
		$queue_db = $this->db_connection;
		$item_to_move = $this->get($guid);
		$queue_db->exec("INSERT INTO video_trash (guid, main_file, title, url, access_id, owner_id, timestamp)
			VALUES('".$item_to_move[0]['guid']."',
				'".$item_to_move[0]['main_file']."',
				'".$item_to_move[0]['title']."',
				'".$item_to_move[0]['url']."',
				'".$item_to_move[0]['access_id']."',
				'".$item_to_move[0]['owner_id']."',
				strftime('%s','now'))"
		);

		return $this->delete($guid);
	}

	public function restore($guid) {
		$queue_db = $this->db_connection;
		$item_to_restore = $this->get_from_trash($guid);
		$queue_db->exec("INSERT INTO video_queue (guid, main_file, title, url, access_id, owner_id, timestamp)
			VALUES('".$item_to_restore[0]['guid']."',
				'".$item_to_restore[0]['main_file']."',
				'".$item_to_restore[0]['title']."',
				'".$item_to_restore[0]['url']."',
				'".$item_to_restore[0]['access_id']."',
				'".$item_to_restore[0]['owner_id']."',
				strftime('%s','now'))"
		);
		return $this->delete_from_trash($guid);
	}

	public function get($guid = false) {
		if($guid) {
			return $this->db_connection->query("SELECT * FROM video_queue WHERE guid = {$guid} ORDER BY timestamp", PDO::FETCH_ASSOC)->fetchall();
		} else {
			return $this->db_connection->query("SELECT * FROM video_queue  ORDER BY timestamp", PDO::FETCH_ASSOC)->fetchall();
		}
	}

	public function get_from_trash($guid = false) {
		if($guid) {
			return $this->db_connection->query("SELECT * FROM video_trash WHERE guid = {$guid}  ORDER BY timestamp", PDO::FETCH_ASSOC)->fetchall();
		} else {
			return $this->db_connection->query("SELECT * FROM video_trash  ORDER BY timestamp", PDO::FETCH_ASSOC)->fetchall();
		}
	}

	public function delete($guid = false, $also_media = false) {
		$queue_db = $this->db_connection;
		if($also_media) {
			$this->delete_related_media($guid);
		}
		return $queue_db->exec(($guid)?"DELETE FROM video_queue WHERE guid = {$guid}":'DELETE FROM video_queue');
	}

	public function delete_from_trash($guid = false, $also_media = false) {
		$queue_db = $this->db_connection;
		if($also_media) {
			$this->delete_related_media($guid);
		}
		return $queue_db->exec(($guid)?"DELETE FROM video_trash WHERE guid = {$guid}":'DELETE FROM video_trash');
	}

	// Fetch all records from queue and change their flags to conversion in process
	public function fetch_videos($guid = false) {
		$select = $this->db_connection->query("SELECT * FROM video_queue WHERE conversion = ".PENDING." ORDER BY timestamp", PDO::FETCH_ASSOC)->fetchall();
		if(count($select)) {
			foreach($select as $row) {
				$guid_array[]=$row->guid;
			}
		}
		return $select;
	}

	public function count() {
		$select = $this->db_connection->query("SELECT count(*) AS count FROM video_queue", PDO::FETCH_ASSOC)->fetch();
     return $select['count'];
	}

	public function count_trash() {
		$select = $this->db_connection->query("SELECT count(*) AS count FROM video_trash", PDO::FETCH_ASSOC)->fetch();
		return $select['count'];
	}

	public function change_conversion_flag($guid) {
		$update_sql = "UPDATE video_queue SET conversion = ".IN_PROCESS."
			WHERE conversion = ".PENDING."
			AND guid = :guid";
		$stmt = $this->db_connection->prepare($update_sql);
		$stmt->bindParam(':guid', $guid, PDO::PARAM_STR);
		return $stmt->execute();
	}

	public function check_process() {
		$select = $this->db_connection->query("SELECT count(*) AS count FROM video_queue WHERE conversion = ".IN_PROCESS, PDO::FETCH_ASSOC)->fetch();
		return $select['count'];
	}

	// This function will delete all related media and kill the process
	public function delete_related_media($guid) {
		$item_of_queue = $this->db_connection->query("SELECT * FROM video_queue WHERE guid = {$guid}", PDO::FETCH_ASSOC)->fetch();
		$item_of_trash = $this->db_connection->query("SELECT * FROM video_trash WHERE guid = {$guid}", PDO::FETCH_ASSOC)->fetch();
		$queue_elements = $item_of_queue?$item_of_queue:$item_of_trash;
		if (!$queue_elements) { // Neither trash nor queue has any element to delete
			return true;
		}
		$removeChar = -1 * (strlen(end(explode('.', $queue_elements['main_file']))) + 1);
		$tmpVideoFile = substr($queue_elements['main_file'], 0, $removeChar) . '_c.flv';
		//creating path to delete thumb from uploaded folder instead of tmp
		$tmpImageFile = preg_replace("/tmp/", 'izap_videos/uploaded', substr($queue_elements['main_file'], 0, $removeChar) . '_i.png');
		@unlink($queue_elements['main_file']);
		@unlink($tmpVideoFile);
		@unlink($tmpImageFile);
		return true;
	}
}