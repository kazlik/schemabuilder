<?php
/**
 * Author: Marek Dočekal
 * Licence: WTFPL v2
 */

namespace Kazlik\Schemabuilder\Config;


interface ITableClassesConfig
{
	/**
	 * @return array
	 */
	public function getClasses(): array;
}