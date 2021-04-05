<?php
namespace Core;

use Database\Database as MysqlDatabase;
use Stan\Stan;

abstract class Model
{

    /**
     * Return Database Class
     *
     * @var Database
     */
    protected $db;

    /**
     * Model constructor.
     */
    public function __construct()
    {
		$this->db = MysqlDatabase::get();
    }
}
