<?php

class Comongo_Database
{
	/**
	 * @var Comongo_Connection
	 */
	protected $_connection;

	/**
	 * @var MongoDb
	 */
	protected $_mongoDb;

	public function __construct(Comongo_Connection $connection, $name)
	{
		$this->_connection = $connection;

		try {
			$this->_mongoDb = new MongoDb($connection->getMongo(), $name);
		} catch (MongoException $e) {
			throw new Comongo_Database_Exception("Invalid database: $name");
		}
	}
	
	public function getMongoDb()
	{
		return $this->_mongoDb;
	}

	public function getName()
	{
		return $this->getMongoDb()->__toString();
	}

	public function __toString()
	{
		return $this->getName();
	}

	public function getCollection($name)
	{
		
	}
	
	public function __get($name)
	{
		return $this->getCollection($name);
	}

	public function getCollections()
	{
		
	}
	
	public function authenticate($username, $password)
	{
		$response = $this->getMongoDb()->authenticate($username, $password);

		return (boolean)$response['ok'];
	}

	public function drop()
	{
		// TODO: See response
		return $this->getMongoDb()->drop();
	}
	
	public function repair()
	{
		// http://us.php.net/manual/en/mongodb.repair.php
	}
}