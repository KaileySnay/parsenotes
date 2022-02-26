<?php
/**
 *
 * Parse User Notes extension for the phpBB Forum Software package
 *
 * @copyright (c) 2022, Kailey Snay, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kaileysnay\parsenotes\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parse User Notes event listener
 */
class main_listener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return [
			'core.add_log'	=> 'add_log',
		];
	}

	public function add_log($event)
	{
		if ($event['mode'] == 'user')
		{
			$sql_ary = $event['sql_ary'];

			unset($event['sql_ary']);
			$text = unserialize($sql_ary['log_data'])[0];
			$uid = $bitfield = $options = ''; // Will be modified by generate_text_for_storage
			$allow_bbcode = $allow_urls = $allow_smilies = true;
			generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			$parsed = generate_text_for_display($text, $uid, $bitfield, $options);
			$sql_ary['log_data'] = serialize([$parsed]);

			$event['sql_ary'] = $sql_ary;
		}
	}
}
