<?php

abstract class Comongo_Document_Attribute_Abstract
{
	protected $_name;
	protected $_value;

	public function __construct($name, $value = null)
	{
		$this->_name  = $name;
		$this->_value = $value;
	}
}

?>