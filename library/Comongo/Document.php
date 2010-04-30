<?php

class Comongo_Document
{
	/**
	 * @var Comongo_Collection
	 */
	protected $_collection;

	protected $_attributes = array();

	public function __construct(Comongo_Collection $collection)
	{
		$this->_collection = $collection;
	}
}

?>