<?php

class Comongo_Connection
{
    const DEFAULT_NAME = 'default';
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAILT_PORT = 27017;
	const DEFAULT_DATABASE = 'admin';

    /**
     * @var Mongo
     */
    protected $_mongo;

    protected $_isConnected = false;

	protected static $_databases = array();

    protected $_options = array(
        'name'       => self::DEFAULT_NAME,
        'servers'    => array(
            array('host' => self::DEFAULT_HOST, 'port' => self::DEFAILT_PORT)
        ),
        'username'   => null,
        'password'   => null,
        'database'   => self::DEFAULT_DATABASE,
        'connect'    => true,
        'persistent' => null,
        'timeout'    => null,
    );

    public function __constrcut($options = array())
    {
        if (!extension_loaded('mongo')) {
            throw new Comongo_Connection_Exception("The mongo extension is required: http://pecl.php.net/package/mongo");
        }

        // Parse connection string to options
        if (is_string($options)) {
            $options = self::parseConnectionString($options);
        }

        $options = array_merge($this->_options, $options);
        $this->setOptions($options);

        // Prepare connection string
        $connectionString = "mongodb://";

        // Username and password
        if (isset($this->_options['username'])) {
            $connectionString .= $this->_options['username'];
            if (isset($this->_options['password'])) {
                $connectionString .= ":{$this->_options['password']}";
            }
            $connectionString .= "@";
        }

        // Servers
        $first = true;
        foreach ($this->_options['servers'] as $server) {
            if (!isset($server['host'])) {
                $server['host'] = self::DEFAULT_HOST;
            }
            if (!isset($server['port'])) {
                $server['port'] = self::DEFAULT_PORT;
            }

            if (!$first) {
                $connectionString .= ','
            }

            $connectionString .= "{$server['host']}:{$server['port']}";

            $first = false;
        }

        if (isset($this->_options['database'])) {
            $connectionString .= "/{$this->_options['database']}";
        }

        // Get Mongo options
        foreach (array('connect', 'persistent', 'timeout') as $name) {
            if (isset($this->_options[$name])) {
                $mongoOptions[$name] = $this->_options[$name];
            }
        }

        $this->_mongo = new Mongo($connectionString, $mongoOptions);

        Comongo_Connection_Manager::setConnection($this);
    }

	public function getMongo()
	{
		return $this->_mongo;
	}

    /**
     * Set options array
     * 
     * @param array $options Options (see $_options description)
     * @return Comongo_Connection
     */
    public function setOptions(array $options)
    {
        foreach($options as $name => $value) {
            if (method_exists($this, "set$name")) {
                call_user_func(array($this, "set$name"), $value);
            } else {
                $this->setOption($name, $value);
            }
        }

        return $this;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set option
     * 
     * @throws Rediska_Exception
     * @param string $name Name of option
     * @param mixed $value Value of option
     * @return Rediska
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->_options)) {
            throw new Comongo_Connection_Exception("Unknown option '$name'");
        }

        $this->_options[$name] = $value;

        return $this;
    }

    /**
     * Get option
     * 
     * @throws Rediska_Exception 
     * @param string $name Name of option
     * @return mixed
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->_options)) {
            throw new Comongo_Connection_Exception("Unknown option '$name'");
        }

        return $this->_options[$name];
    }
    
    public function setName($name)
    {
        $this->_options['name'] = $name;
        
        return $this;
    }
    
    public function getName()
    {
        return $this->_options['name'];
    }

	public function __toString()
	{
		return $this->getName();
	}

    public function connect()
    {
        if ($this->isConnected()) {
            return false;
        }

        try {
            $this->getMongo()->connect();
            
            return true;
        } catch (MongoConnectionException $e) {
            throw new Comongo_Connection_Exception("The mongo extension is required: http://pecl.php.net/package/mongo");
        } 
    }

    public function disconnect()
    {
        if (!$this->isConnected()) {
            return false;
        }

        $this->getMongo()->close();

        return true;
    }

    public function isConnected()
    {
        return $this->_isConnected;
    }

	public function getDatabase($name = null)
	{
		if (null === $name) {
			$name = $this->_options['database'];
		}

		// TODO: Если создать через конструктор то в кеш не попадет, возможно это не круто.
		if (!isset(self::$_databases[$name])) {
			$this->_databases[$name] = new Comongo_Database($this, $name);
		}

		return $this->_databases[$name];
	}
	

	public function __get($name)
	{
		return $this->getDatabase($name);
	}

	public function getDatabases()
	{
		
	}

    public static function parseConnectionString($connectionString)
    {
        //mongodb://[username:password@]host1[:port1][,host2[:port2:],...]/db
        $parsed = @parse_url($connectionString);

        if ($parsed == false || $parsed['scheme'] != 'mongodb' || $parsed['host'] == null) {
            throw new Comongo_Connection_Exception("Invalid connection string: $connectionString");
        }

        $options = array();
        foreach(array('user' => 'username', 'pass' => 'password', 'path' => 'database') as $parsedName => $optionsName) {
            if (isset($parsed[$parsedName])) {
                $options[$optionsName] = $parsed[$parsedName];
            }
        }

        throw new Comongo_Connection_Exception("Must implement");

        // TODO: Посмотреть как распарсит список хотсов с портами и без и в зависимости от этого сделать условие
        //       и выбрать данные
        if (strpos($parsed['host'], ',') !== false || (isset($parsed['port']) && strpos($parsed['port'], ',') !== false)) {
            // TODO: Implement!
        } else {
            $options['servers'] = array(
                array('host' => self::DEFAULT_HOST, 'port' => self::DEFAILT_PORT)
            )
        }

        return $options;
    }
}

?>