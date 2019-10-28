<?php
/**
 * Author: Marek Dočekal
 * Licence: WTFPL v2
 */

namespace Kazlik\Schemabuilder;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Kazlik\Schemabuilder\Config\ITableClassesConfig;
use Kazlik\Schemabuilder\Table\ITableInfo;


class ApplyTableChangesService implements IApplyTableChangesService
{

	/** @var ITableClassesConfig */
	private $_TableClassesConfig;

	/** @var Connection */
	private $_Connection;

	/**
	 * ApplyTableChangesService constructor.
	 *
	 * @param ITableClassesConfig $_TableClassesConfig
	 * @param Connection          $_Connection
	 */
	public function __construct( ITableClassesConfig $_TableClassesConfig, Connection $_Connection )
	{
		$this->_TableClassesConfig = $_TableClassesConfig;
		$this->_Connection = $_Connection;
	}


	public function applyAllChanges(): bool
	{
		$classes = $this->_TableClassesConfig->getClasses();
		/** @var Table[] $listTable */
		$listTable = [];
		foreach ( $classes as $class ) {
			$ReflectionClass = new \ReflectionClass( $class );
			$implementsInterface = $ReflectionClass->implementsInterface( ITableInfo::class );
			if ( !$implementsInterface ) {
				throw new \InvalidArgumentException( 'Class ' . $class . ' is not instance of ' . ITableInfo::class );
			}
			/** @var ITableInfo $TableInfo */
			$TableInfo = new $class;
			$listTable[ $TableInfo->create()->getName() ] = $TableInfo->create();
		}
		$Comparator = new Comparator();
		$SchemaManager = $this->_Connection->getSchemaManager();
		$DatabasePlatform = $this->_Connection->getDatabasePlatform();
		$listTableName = $SchemaManager->listTableNames();
		$listSql = [];
		$existingTable = [];
		foreach ( $listTableName as $tableName ) {
			$existingTable[ $tableName ] = true;
			if ( !isset( $listTable[ $tableName ] ) ) {
				continue;
			}
			$NewTable = $listTable[ $tableName ];
			$ActualTable = $SchemaManager->listTableDetails( $tableName );
			$TableDiff = $Comparator->diffTable( $ActualTable, $NewTable );
			if ( $TableDiff ) {
				$listSql = array_merge( $listSql, $DatabasePlatform->getAlterTableSQL( $TableDiff ) );
			}
		}
		foreach ( $listTable as $Table ) {
			$tableName = $Table->getName();
			if ( isset( $existingTable[ $tableName ] ) ) {
				continue;
			}
			$SchemaManager->createTable( $Table );
		}
		foreach ( $listSql as $sql ) {
			$this->_Connection->query( $sql );
		}
		return true;
	}


}