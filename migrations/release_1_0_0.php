<?php

/**
*
* @package TopicDesc
* @copyright (c) 2020 DeaDRoMeO; hello-vitebsk.ru
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace deadromeo\topicdesc\migrations;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
    exit;
}

class release_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['td_version']) && version_compare($this->config['td_version'], '1.0.0', '>=');
	}
	static public function depends_on()
	{
			return array('\phpbb\db\migration\data\v310\dev');
	}
	public function update_schema()
	{
		if (!$this->db_tools->sql_column_exists($this->table_prefix . 'topics', 'topic_desc'))
		{
			return 	array(
				'add_columns' => array(
					$this->table_prefix . 'topics' => array(
						'topic_desc' => array('VCHAR_UNI', ''),
					),
				),
			);
		}
		return array(
		);
	}
	public function revert_schema()
	{
		return 	array(
			'drop_columns' => array(
				$this->table_prefix . 'topics' => array('topic_desc'),
			),
		);
	}
	public function update_data()
	{
		return array(
			array('config.add', array('td_version', '1.0.0')),
			array('permission.add', array('u_setdesc', true)),
		);
	}
		public function revert_data()
	{
		return array(
		array('config.remove', array('td_version')),
		array('permission.remove', array('u_setdesc')),
		);
	}
}
