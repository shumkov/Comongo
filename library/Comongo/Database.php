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
		$this->_mongoDb = new MongoDb($connection->getMongo(), $name);
	}

	public function getName()
	{
		return $this->_mongoDb->__toString();
	}

	public function getCollections()
	{
		
	}

	public function drop()
	{
		
	}
}