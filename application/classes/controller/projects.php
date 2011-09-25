<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Projects Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.swiftly.org
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Projects extends Controller_Sweeper {
	
	/**
	 * @return	void
	 */
	public function before()
	{
		// Execute parent::before first
		parent::before();
		
		$this->template->header->page_title = __('Projects');
		$this->template->header->tab_menu = View::factory('pages/projects/menu');
		$this->template->header->tab_menu->active = "";
	}
	
	/**
	 * List all the Projects
	 *
	 * @param	string $page - page uri
	 * @return	void
	 */
	public function action_index($page = NULL)
	{
		$this->template->content = View::factory('pages/projects/overview')
			->bind('projects', $result)
			->bind('total', $total)
			->bind('paging', $pagination)
			->bind('default_sort', $sort);
		
		// Projects
		$projects = ORM::factory('project');
		// Get the total count for the pagination
		$total = $projects->count_all();
		
		// Create a paginator
		$pagination = new Pagination(array(
			'total_items' => $total, 
			'items_per_page' => 20,
			'auto_hide' => false
		));
		
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'project_title'; // set default sorting
		$dir = isset($_GET['dir']) ? 'DESC' : 'ASC'; // set order_by
		$result = $projects->limit($pagination->items_per_page)
			->offset($pagination->offset)
			->order_by($sort, $dir)
			->find_all();
	}
	
	/**
	 * Add/Edit a Project
	 *
	 * @return	void
	 */
	public function action_edit()
	{
		$id = $this->request->param('id');
		$this->template->content = View::factory('pages/projects/edit')
			->bind('post', $post)
			->bind('errors', $errors);
		$this->template->header->tab_menu->active = 'edit';
		
		// save the data
		if ($_POST)
		{
			$project = ORM::factory('project', $id);
			$post = $project->validate($_POST);
			if ($post->check())
			{
				$project->project_title = $post['project_title'];
				$project->project_description = $post['project_description'];
				$project->save();
				
				// Always redirect after a successful POST to prevent refresh warnings
				$this->request->redirect('projects');
			}
			else
			{
				//validation failed, get errors
				$errors = $post->errors('projects');
			}
		}
		else
		{
			if (is_numeric($id))
			{
				$project = ORM::factory('project', $id);
				$post = $project->as_array();
			}
		}	
	}
}