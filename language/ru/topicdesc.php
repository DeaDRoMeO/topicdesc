<?php

/**
*
* @package TopicDesc
* @copyright (c) 2020 DeaDRoMeO; hello-vitebsk.ru
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'DESC'					=> 'Описание темы:',
	'DESC_EXP'					=> 'Максимально <strong>255</strong> символов.',	
));
