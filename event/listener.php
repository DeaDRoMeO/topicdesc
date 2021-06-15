<?php

/**
*
* @package TopicDesc
* @copyright (c) 2020 DeaDRoMeO; hello-vitebsk.ru
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace deadromeo\topicdesc\event;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
    exit;
}

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;

public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->db = $db;
		$this->auth = $auth;
		$this->template = $template;
		$this->user = $user;
}
	static public function getSubscribedEvents()
	{
		return array(
		'core.posting_modify_template_vars'			=> 'modify_posting_vars',
		'core.modify_submit_post_data'				=> 'posting_data',	
		'core.posting_modify_submit_post_after'						=> 'setdesc',
		'core.viewforum_modify_topics_data' => 'low',		
		'core.viewforum_modify_topicrow'						=> 'dispdesc',	
		'core.search_modify_tpl_ary'						=> 'dispdescs',
		'core.permissions'						=> 'add_perm',
		);
	}
	public function add_perm($event)
	{
		$permissions = $event['permissions'];
		$permissions['u_setdesc'] = array('lang' => 'ACL_U_SETDESC', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}
public function posting_data($event)
	{
		$post_data = $event['post_data'];
		$post_data['topic_desc'] = utf8_normalize_nfc(request_var('topic_desc', '', true));
		$event['post_data'] = $post_data;
	}
	public function setdesc($event)
	{
		global $post_data;
		$data = $event['data'];
		$post_id = $data['post_id'];
		$topic_id = $data['topic_id'];
		$mode = $event['mode'];
		$post_data['topic_desc'] = utf8_normalize_nfc(request_var('topic_desc', '', true));
		if (($mode == 'edit' && $post_id == $data['topic_first_post_id']) || $mode == 'post')
		{
		
			$sql = 'UPDATE  ' . TOPICS_TABLE . '
			SET topic_desc = "' . $post_data['topic_desc'] . '"
			WHERE topic_id = ' . $topic_id . '';				
				$this->db->sql_query($sql);
			
 
		}
	}
	public function modify_posting_vars($event)
	{
		$post_data = $event['post_data'];
		$mode = $event['mode'];
		$post_id = $event['post_id'];
		$this->user->add_lang_ext('deadromeo/topicdesc', 'topicdesc');
		$topic_desc = '';
		if ((($mode == 'edit' && $post_id == $post_data['topic_first_post_id']) || $mode == 'post')
			)
		{
		
			$topic_desc = (isset($post_data['topic_desc'])) ? $post_data['topic_desc'] : '';
		}

		
		$this->template->assign_vars(array(
		'S_FIRSTP'	=> 	($mode == 'edit' && $post_id == $post_data['topic_first_post_id'] || $mode == 'post') ? true : false,
		'S_SETDESC'	=> (bool) ($this->auth->acl_get('u_setdesc')),
			'TOPIC_DESC'		=> $topic_desc,
		));
	}
	
	public function low($event)
	{
		
			$rowset = $event['rowset'];
			foreach ($event['topic_list'] as $topic_id)
			{
				$row = &$rowset[$topic_id];
				if (!isset($topic_ids))
				{
					$topic_ids = array();
				}
				$topic_ids[] = ($row['topic_desc']) ? $topic_id : '';
				unset($rowset[$topic_id]);
			}
				if (isset($topic_ids))
				{
				$sql = 'SELECT topic_desc, topic_id
					FROM ' . TOPICS_TABLE . '
					WHERE  ' . $this->db->sql_in_set('topic_id', $topic_ids) . '';
				$result = $this->db->sql_query($sql);

				while($row = $this->db->sql_fetchrow($result))
				{				
						$this->topic_desc[$row['topic_id']] = $row['topic_desc'];
				}
				$this->db->sql_freeresult($result);	
		}
	}
	public function dispdesc($event)
	{
		$row = $event['row'];
		if (!empty($this->topic_desc[$row['topic_id']]))
		{
			$topic_row = array(
				'TOPIC_DESC'	=> $this->topic_desc[$row['topic_id']],
			);
			$event['topic_row'] += $topic_row;
		}
	
	}
	public function dispdescs($event)
	{
			$row = $event['row'];
			$tpl_ary = $event['tpl_ary'];
			$topic_id = $row['topic_id'];
		$sql = 'SELECT topic_desc
				FROM ' . TOPICS_TABLE . '
				WHERE topic_id = ' . $topic_id . '';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$tpl_ary = array_merge($tpl_ary, array(
				'TOPIC_DESC' => $row['topic_desc'],
			));
			$event['tpl_ary'] = $tpl_ary;
			}
			$this->db->sql_freeresult($result);
	
	
	}
}