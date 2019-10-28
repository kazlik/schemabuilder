<?php
/**
 * Author: Marek Dočekal
 * Licence: WTFPL v2
 */

namespace Kazlik\SchemaBuilder;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Kazlik\SchemaBuilder\Config\ITableClassesConfig;
use Kazlik\SchemaBuilder\Table\ITableInfo;


class ApplyTableChangesService implements IApplyTableChangesService
{

	/** @var ITableClassesConfig */
	private $_tableClassesConfig;

	/** @var Connection */
	private $_connection;

	/**
	 * ApplyTableChangesService constructor.
	 *
	 * @param ITableClassesConfig $_tableClassesConfig
	 * @param Connection          $_connection
	 */
	public function __construct( ITableClassesConfig $_tableClassesConfig, Connection $_connection )
	{
		$this->_tableClassesConfig = $_tableClassesConfig;
		$this->_connection = $_connection;
	}


	public function applyAllChanges(): bool
	{
		$classes = $this->_tableClassesConfig->getClasses();
		/** @var Table[] $listTable */
		$listTable = [];
		foreach ( $classes as $class ) {
			$reflectionClass = new \ReflectionClass( $class );
			$implementsInterface = $reflectionClass->implementsInterface( ITableInfo::class );
			if ( !$implementsInterface ) {
				throw new \InvalidArgumentException( 'Class ' . $class . ' is not instance of ' . ITableInfo::class );
			}
			/** @var ITableInfo $tableInfo */
			$tableInfo = new $class;
			$listTable[ $tableInfo->create()->getName() ] = $tableInfo->create();
		}
		$comparator = new Comparator();
		$schemaManager = $this->_connection->getSchemaManager();
		$databasePlatform = $this->_connection->getDatabasePlatform();
		$listTableName = $schemaManager->listTableNames();
		$listSql = [];
		$existingTable = [];
		foreach ( $listTableName as $tableName ) {
			$existingTable[ $tableName ] = true;
			if ( !isset( $listTable[ $tableName ] ) ) {
				continue;
			}
			$newTable = $listTable[ $tableName ];
			$actualTable = $schemaManager->listTableDetails( $tableName );
			$tableDiff = $comparator->diffTable( $actualTable, $newTable );
			if ( $tableDiff ) {
				$listSql = array_merge( $listSql, $databasePlatform->getAlterTableSQL( $tableDiff ) );
			}
		}
		foreach ( $listTable as $Table ) {
			$tableName = $Table->getName();
			if ( isset( $existingTable[ $tableName ] ) ) {
				continue;
			}
			$schemaManager->createTable( $Table );
		}
		foreach ( $listSql as $sql ) {
			$this->_connection->query( $sql );
		}
		return true;
	}


}