<?

class Comongo_Collection
{
	/**
	 * @var Comongo_Database
	 */
	protected $_database;

	protected $_mongoCollection;

	protected $_documents = array();

	public function __construct(Comongo_Database $database, $name)
	{
		$this->_database = $database;

		try {
			$this->_mongoCollection = new MongoCollection($database->getMongoDb(), $name)
		} catch (MongoException $e) {
			throw new Comongo_Collection_Exception("Invalid collection: $name");
		}
	}

	public function getMongoCollection()
	{
		return $this->_mongoCollection;
	}
	
	public function drop()
	{
		// TODO: See response
		return $this->getMongoCollection()->drop();
	}
}

?>