<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Main Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Settings_Main extends Controller_Swiftriver {
	
	// Active settings menu
	private $active;

	/**
	 * Access privileges for this controller and its children
	 */
	public $auth_required = 'admin';
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		$this->template->content = View::factory('pages/settings/layout')
			->bind('active', $this->active)
			->bind('settings_content', $this->settings_content);
	}
	
	/**
	 * List all the available settings
	 *
	 * @return  void
	 */
	public function action_index()
	{
		$this->settings_content = View::factory('pages/settings/main');
		$this->active = 'main';	
	}

	/**
	 * List all the Plugins
	 *
	 * @param	string $page - page uri
	 * @return	void
	 */
	public function action_plugins()
	{
		$this->settings_content = View::factory('pages/settings/plugins')
			->bind('plugins', $plugins)
			->bind('default_sort', $sort);

		$this->template->header->js = View::factory('pages/settings/js/plugins');

		$this->active = 'plugins';	
		
		// Process Plugins
		$this->_process_plugins();
		
		// save the data
		if ($_POST)
		{
			if ( isset($_POST['id']) 
				AND isset($_POST['action']) 
				AND is_numeric($_POST['id']) 
				AND in_array($_POST['action'], array(0, 1)) )
			{
				$plugin = ORM::factory('plugin', $_POST['id']);
				if ($plugin->loaded())
				{
					if ($_POST['action'] == 1)
					{
						$plugin->plugin_enabled = 1;
						$plugin->save();
						
						// Load this plugin into the system
						Kohana::modules(array_merge(Kohana::modules(), array(
							$plugin->plugin_path => PLUGINPATH.$plugin->plugin_path
						)));
						
						$check = Kohana::modules();
						// Was plugin loaded?
						if ( ! isset($check[$plugin->plugin_path]))
						{
							$plugin->plugin_enabled = 0;
							$plugin->save();
						}
						else
						{
							// Run default plugin functions
							$class = ucfirst($plugin->plugin_path);
							if (class_exists($class))
							{
								// Does an install exist?
								if (method_exists($class,'install'))
								{
									// Run the installer
									// declare it first to prevent T_PAAMAYIM_NEKUDOTAYIM
									$install = new $class;
									$install->install();
								}
							}
						}
					}
					else
					{
						$plugin->plugin_enabled = 0;
						$plugin->save();
					}
				}
			}
		}
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'plugin_name'; // set default sorting
		$dir = isset($_GET['dir']) ? 'DESC' : 'ASC'; // set order_by
		$plugins = ORM::factory('plugin')
			->order_by($sort, $dir)
			->find_all();
	}
	
	/**
	 * Private function to go through plugin directory and extract
	 * plugins in the system, then save them in the database
	 * so that they're available for activation in admin
	 *
	 * @return	void
	 */
	private function _process_plugins()
	{
		$configs = Swiftriver_Plugins::load_configs();
		
		// Sync the folder with the database
		foreach ($configs as $key => $value)
		{
			if ( ORM::factory('plugin')
				->where('plugin_path', '=', $key)
				->count_all() == 0 )
			{
				$plugin = ORM::factory('plugin');
				$plugin->plugin_path = $key;
				$plugin->plugin_name = $value['name'];
				$plugin->plugin_description = $value['description'];
				$plugin->save();
			}
		}
		
		// Remove Any Plugins not found in the plugins folder from the database
		foreach (ORM::factory('plugin')->find_all() as $plugin)
		{
			if ( ! array_key_exists($plugin->plugin_path, $configs))
			{
				$plugin->delete();
			}
		}
	}	
}