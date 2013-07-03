<?php
class Reader {
	public static function getInstance($feed) {
		if (include_once 'ext/media/' . $feed['category'] . 'Reader.php') {
            		$classname = $feed['category'].'Reader';
			$reader = new $classname($feed['url']);
			$reader->init();
            		return $reader;
        	} else {
            		throw new Exception('Reader not found');
        	}
	}	
}
?>

