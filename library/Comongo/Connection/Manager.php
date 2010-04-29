<?php

class Comongo_Connection_Manager
{
	protected static $_connections = array();

	public static function setConnection($connection)
	{
		if (is_object($connection)) {
			if (!$connection instanceof Comongo_Connection) {
				throw new Comongo_Connection_Exception("The mongo extension is required: http://pecl.php.net/package/mongo");
			}
			$name = $connection->getName();
		} else if (is_array($connection)) {
			if (isset($connection['name'])) {
				$name = $connection['name'];
			} else {
				$name = Comongo_Connection::DEFAULT_NAME;
			}
		} else {
			throw new Comongo_Connection_Exception("The mongo extension is required: http://pecl.php.net/package/mongo");
		}

		self::$_connections[$name] = $connection;
	}

	public static function getConnection($name = Comongo_Connection::DEFAULT_NAME)
	{
		if (!isset(self::$_connections[$name])) {
			throw new Comongo_Connection_Exception("The mongo extension is required: http://pecl.php.net/package/mongo");
		}

		$connectionOrOptions = self::$_connections[$name];
		if (is_object($connectionOrOptions)) {
			$connection = $connectionOrOptions;
		} else {
			$options = $connectionOrOptions;
		    $connection = new self($options);
		}

		return self::$_connections[$name];
	}
}