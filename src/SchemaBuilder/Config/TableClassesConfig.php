<?php
/**
 * Author: Marek Dočekal
 * Licence: WTFPL v2
 */

namespace Kazlik\Schemabuilder\Config;


class TableClassesConfig implements ITableClassesConfig
{
	/** @var array */
	protected $classes = [];

	/**
	 * TableClassesConfig constructor.
	 * @param array $classes
	 */
	public function __construct(array $classes)
	{
		$this->classes = $classes;
	}

	/**
	 * @return array
	 */
	public function getClasses(): array
	{
		return $this->classes;
	}


}