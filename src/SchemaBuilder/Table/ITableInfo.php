<?php
/**
 * Author: Marek Dočekal
 * Licence: WTFPL v2
 */

namespace Kazlik\Schemabuilder\Table;




use Doctrine\DBAL\Schema\Table;

interface ITableInfo
{
	public function create(): Table;
}