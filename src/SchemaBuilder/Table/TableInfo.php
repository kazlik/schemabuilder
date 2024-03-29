<?php
/**
 * Author: Marek Dočekal
 * Licence: WTFPL v2
 */

namespace Kazlik\SchemaBuilder\Table;


use Doctrine\DBAL\Schema\Table;

abstract class TableInfo implements ITableInfo
{

	const _TYPE_TARRAY = 'array';
	const _TYPE_SIMPLE_ARRAY = 'simple_array';
	const _TYPE_JSON_ARRAY = 'json_array';
	const _TYPE_JSON = 'json';
	const _TYPE_BIGINT = 'bigint';
	const _TYPE_BOOLEAN = 'boolean';
	const _TYPE_DATETIME = 'datetime';
	const _TYPE_DATETIME_IMMUTABLE = 'datetime_immutable';
	const _TYPE_DATETIMETZ = 'datetimetz';
	const _TYPE_DATETIMETZ_IMMUTABLE = 'datetimetz_immutable';
	const _TYPE_DATE = 'date';
	const _TYPE_DATE_IMMUTABLE = 'date_immutable';
	const _TYPE_TIME = 'time';
	const _TYPE_TIME_IMMUTABLE = 'time_immutable';
	const _TYPE_DECIMAL = 'decimal';
	const _TYPE_INTEGER = 'integer';
	const _TYPE_OBJECT = 'object';
	const _TYPE_SMALLINT = 'smallint';
	const _TYPE_STRING = 'string';
	const _TYPE_TEXT = 'text';
	const _TYPE_BINARY = 'binary';
	const _TYPE_BLOB = 'blob';
	const _TYPE_FLOAT = 'float';
	const _TYPE_GUID = 'guid';
	const _TYPE_DATEINTERVAL = 'dateinterval';

	/** @var Table */
	private $_table;

	/** @var array */
	private $_foreignKeys = [];


    public function getTable(): Table
    {
        if ( $this->_table === null ) {
            $this->createTable();
        }
        return $this->_table;
    }


    public function getForeignKeys(): array
    {
        return $this->_foreignKeys;
    }


    abstract protected function create(): void;


	protected function _createTable(): Table
	{
        $this->_table = new Table( $this->_getTableName() );
        return $this->_table;
	}


	abstract protected function _getTableName(): string;


	protected function _addPrimaryKeyColumn( Table $table, string $columnName = 'id' ): void
	{
		$table->addColumn( $columnName, 'integer' )->setAutoincrement( true )->setUnsigned( true );
		$table->setPrimaryKey( [ $columnName ] );
	}


	protected function _addForeignKey( string $foreignTableClass,
	                                   array $localColumnNames,
	                                   array $foreignColumnNames,
	                                   array $options = [],
	                                   $constraintName = null ): void
	{
        $this->_foreignKeys[] = [
            $foreignTableClass,
            $localColumnNames,
            $foreignColumnNames,
            $options,
            $constraintName,
        ];
	}


	protected function _addLocalForeignKey( Table $table,
	                                        array $localColumnNames,
	                                        array $foreignColumnNames,
	                                        array $options = [],
	                                        $constraintName = null )
	{
		$table->addForeignKeyConstraint( $table, $localColumnNames, $foreignColumnNames, $options, $constraintName );
	}


}