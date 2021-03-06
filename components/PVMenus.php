<?php
/*
 *Copyright 2011 ProdigyView LLC. All rights reserved.
 *
 *Redistribution and use in source and binary forms, with or without modification, are
 *permitted provided that the following conditions are met:
 *
 *   1. Redistributions of source code must retain the above copyright notice, this list of
 *      conditions and the following disclaimer.
 *
 *   2. Redistributions in binary form must reproduce the above copyright notice, this list
 *      of conditions and the following disclaimer in the documentation and/or other materials
 *      provided with the distribution.
 *
 *THIS SOFTWARE IS PROVIDED BY ProdigyView LLC ``AS IS'' AND ANY EXPRESS OR IMPLIED
 *WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 *FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL ProdigyView LLC OR
 *CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 *CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 *SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 *ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 *ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *The views and conclusions contained in the software and documentation are those of the
 *authors and should not be interpreted as representing official policies, either expressed
 *or implied, of ProdigyView LLC.
 */

class PVMenus extends PVStaticObject {

	/**
	 * Create a menu container. The menu container will hold the menu items.
	 *
	 * @param array $args Arguements passed that define the menu
	 * 			-'menu_name' _string_: The name of the menu
	 * 			-'menu_type' _string_: The type of menu being created
	 * 			-'menu_tag_id' _string_: The id of the tag as an attribute
	 * 			-'menu_css' _string_: The css for the mnsu
	 * 			-'content_id' _id_: Content this menu is related too
	 * 			-'user_id' _id_: The id of the user the menu is associated with
	 * 			-'app_id' _id_: The aplication the menu is associated with
	 * 			-'menu_unique_id' _string_: A assigned unique identifer for the menu
	 * 			-'menu_class' _string_: The class attributes for the menu
	 * 			-'menu_description' _string_: A description of the menu
	 * 			-'menu_enabled' _boolean_: Determines if the menu is enabled
	 *
	 * @return id $menu_id The id of the menu created
	 * @access public
	 */
	public static function createMenu($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getMenuDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);

		if (is_array($args)) {
			extract($args);
		}

		$menu_order = ceil($menu_order);
		$menu_enabled = ceil($menu_enabled);

		if (empty($menu_unique_id)) {
			$menu_unique_id = PVTools::generateRandomString(20);
		}

		$query = "INSERT INTO " . PVDatabase::getMenuTableName() . "(menu_name, menu_type, menu_tag_id, menu_css, menu_order, content_id, user_id, app_id, menu_unique_id, menu_class, menu_description, menu_enabled) VALUES( '$menu_name', '$menu_type', '$menu_tag_id', '$menu_css', '$menu_order', '$content_id', '$user_id', '$app_id', '$menu_unique_id', '$menu_class', '$menu_description', '$menu_enabled')";
		$menu_id = PVDatabase::return_last_insert_query($query, 'menu_id', PVDatabase::getMenuTableName());

		self::_notify(get_class() . '::' . __FUNCTION__, $menu_id, $args);
		$menu_id = self::_applyFilter(get_class(), __FUNCTION__, $menu_id, array('event' => 'return'));

		return $menu_id;
	}//end createMenu

	/**
	 * Update a menu based on passed elements. The menu id is required to successfully update a menu.
	 *
	 * @param array $args Arguements passed that define the menu
	 * 			-'menu_id' _id_: Required. Updates the menu based on the id.
	 * 			-'menu_name' _string_: The name of the menu
	 * 			-'menu_type' _string_: The type of menu being created
	 * 			-'menu_tag_id' _string_: The id of the tag as an attribute
	 * 			-'menu_css' _string_: The css for the mnsu
	 * 			-'content_id' _id_: Content this menu is related too
	 * 			-'user_id' _id_: The id of the user the menu is associated with
	 * 			-'app_id' _id_: The aplication the menu is associated with
	 * 			-'menu_unique_id' _string_: A assigned unique identifer for the menu
	 * 			-'menu_class' _string_: The class attributes for the menu
	 * 			-'menu_description' _string_: A description of the menu
	 * 			-'menu_enabled' _boolean_: Determines if the menu is enabled
	 *
	 * @return voids
	 * @access public
	 */
	public static function updateMenu($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getMenuDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);

		if (is_array($args)) {
			extract($args);
		}

		if (!empty($menu_id)) {

			$menu_order = ceil($menu_order);
			$menu_enabled = ceil($menu_enabled);

			$query = "UPDATE " . PVDatabase::getMenuTableName() . " SET menu_name='$menu_name', menu_type='$menu_type' , menu_tag_id='$menu_tag_id' , menu_css='$menu_css' , menu_order='$menu_order' , content_id='$content_id', user_id='$user_id', app_id='$app_id', menu_unique_id='$menu_unique_id', menu_class='$menu_class', menu_description='$menu_description', menu_enabled='$menu_enabled' WHERE menu_id='$menu_id' ";
			PVDatabase::query($query);

			self::_notify(get_class() . '::' . __FUNCTION__, $args);
		}//end

	}//end updateMenu

	/**
	 * Search for a menu in the database based on the parameters based that describe the menu. The parameters passed should
	 * follow the PV Standard Search Query arguements
	 *
	 * @param array $args Arguements passed that define the menu
	 * 			-'menu_id' _id_: The id of the menu
	 * 			-'menu_name' _string_: The name of the menu
	 * 			-'menu_type' _string_: The type of menu being created
	 * 			-'menu_tag_id' _string_: The id of the tag as an attribute
	 * 			-'menu_css' _string_: The css for the mnsu
	 * 			-'content_id' _id_: Content this menu is related too
	 * 			-'user_id' _id_: The id of the user the menu is associated with
	 * 			-'app_id' _id_: The aplication the menu is associated with
	 * 			-'menu_unique_id' _string_: A assigned unique identifer for the menu
	 * 			-'menu_class' _string_: The class attributes for the menu
	 * 			-'menu_description' _string_: A description of the menu
	 * 			-'menu_enabled' _boolean_: Determines if the menu is enabled
	 *
	 * @return array $menus Returns an array of menus
	 * @access public
	 */
	public static function getMenuList($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getMenuDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));

		$custom_where = $args['custom_where'];
		$custom_join = $args['custom_join'];
		$args = PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);

		$first = 1;

		$content_array = array();
		$table_name = PVDatabase::getMenuTableName();
		$db_type = PVDatabase::getDatabaseType();

		$WHERE_CLAUSE = '';

		if (!empty($menu_id)) {

			$menu_id = trim($menu_id);

			if ($first == 0 && ($menu_id[0] != '+' && $menu_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($menu_id[0] == '+' || $menu_id[0] == ',') && $first == 1) {
				$app_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($menu_id, 'menu_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($menu_name) || $menu_name === '0') {

			$menu_name = trim($menu_name);

			if ($first == 0 && ($menu_name[0] != '+' && $menu_name[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($menu_name[0] == '+' || $menu_name[0] == ',') && $first == 1) {
				$menu_name[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($menu_name, 'menu_name');

			$first = 0;
		}//end not empty app_id

		if (!empty($menu_type)) {

			$menu_type = trim($menu_type);

			if ($first == 0 && ($menu_type[0] != '+' && $menu_type[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($menu_type[0] == '+' || $menu_type[0] == ',') && $first == 1) {
				$menu_type[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($menu_type, 'menu_type');

			$first = 0;
		}//end not empty app_id

		if (!empty($menu_tag_id)) {

			$menu_tag_id = trim($menu_tag_id);

			if ($first == 0 && ($menu_tag_id[0] != '+' && $menu_tag_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($menu_tag_id[0] == '+' || $menu_tag_id[0] == ',') && $first == 1) {
				$menu_tag_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($menu_tag_id, 'menu_tag_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($menu_css)) {

			$menu_css = trim($menu_css);

			if ($first == 0 && ($menu_css[0] != '+' && $menu_css[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($menu_css[0] == '+' || $menu_css[0] == ',') && $first == 1) {
				$menu_css[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($menu_css, 'menu_css');

			$first = 0;
		}//end not empty app_id

		if (!empty($menu_order)) {

			$menu_order = trim($menu_order);

			if ($first == 0 && ($menu_order[0] != '+' && $menu_order[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($menu_order[0] == '+' || $menu_order[0] == ',') && $first == 1) {
				$menu_order[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($menu_order, 'menu_order');

			$first = 0;
		}//end not empty app_id

		if (!empty($content_id)) {

			$content_id = trim($content_id);

			if ($first == 0 && ($content_id[0] != '+' && $content_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($content_id[0] == '+' || $content_id[0] == ',') && $first == 1) {
				$content_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($content_id, 'content_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($user_id)) {

			$user_id = trim($user_id);

			if ($first == 0 && ($user_id[0] != '+' && $user_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($user_id[0] == '+' || $user_id[0] == ',') && $first == 1) {
				$user_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($user_id, 'user_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($app_id)) {

			$app_id = trim($app_id);

			if ($first == 0 && ($app_id[0] != '+' && $app_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($app_id[0] == '+' || $app_id[0] == ',') && $first == 1) {
				$app_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($app_id, 'app_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($menu_unique_id)) {

			$menu_unique_id = trim($menu_unique_id);

			if ($first == 0 && ($menu_unique_id[0] != '+' && $menu_unique_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($menu_unique_id[0] == '+' || $menu_unique_id[0] == ',') && $first == 1) {
				$menu_unique_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($menu_unique_id, 'menu_unique_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($menu_class)) {

			$menu_class = trim($menu_class);

			if ($first == 0 && ($menu_class[0] != '+' && $menu_class[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($menu_class[0] == '+' || $menu_class[0] == ',') && $first == 1) {
				$item_title[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($menu_class, 'menu_class');

			$first = 0;
		}//end not empty app_id

		if (!empty($menu_description)) {

			$menu_description = trim($menu_description);

			if ($first == 0 && ($menu_description[0] != '+' && $menu_description[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($menu_description[0] == '+' || $menu_description[0] == ',') && $first == 1) {
				$menu_description[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($menu_description, 'menu_description');

			$first = 0;
		}//end not empty app_id

		$JOINS = '';

		if (!empty($custom_where)) {
			$WHERE_CLAUSE .= ' ' . $custom_where . ' ';
		}

		if (!empty($custom_join)) {
			$JOINS .= ' ' . $custom_join . ' ';
		}

		if (!empty($WHERE_CLAUSE)) {
			$WHERE_CLAUSE = ' WHERE ' . $WHERE_CLAUSE;
		}

		if (!empty($distinct)) {
			$PREFIX_ARGS .= " DISTINCT $distinct, ";
		}

		if (!empty($limit) && $db_type == 'mssql' && !$paged) {
			$PREFIX_ARGS .= " TOP $limit ";
		}

		if ($paged) {
			$page_results = PVDatabase::getPagininationOffset($table_name, $JOINS, $WHERE_CLAUSE, $current_page, $results_per_page, $order_by);

			if ($db_type == 'mysql' || $db_type == 'postgresql') {
				$limit = ' ' . $page_results['limit_offset'];
			} else if ($db_type == 'mssql') {
				$WHERE_CLAUSE .= ' ' . $page_results['limit_offset'];
				$table_name = $page_results['from_clause'];
			}
		}

		if (!empty($group_by)) {
			$WHERE_CLAUSE .= " GROUP BY $group_by";
		}

		if (!empty($having)) {
			$WHERE_CLAUSE .= " HAVING $having";
		}

		if (!empty($order_by)) {
			$WHERE_CLAUSE .= " ORDER BY $order_by";
		}

		if (!empty($limit) && !$paged && ($db_type == 'mysql' || $db_type == 'postgresql')) {
			$WHERE_CLAUSE .= " LIMIT $limit";
		}

		if ($paged) {
			$WHERE_CLAUSE .= " $limit";
		}

		if (empty($custom_select)) {
			$custom_select = '*';
		}

		$query = "$prequery SELECT $prefix_args $custom_select FROM $table_name $JOINS $WHERE_CLAUSE";
		$result = PVDatabase::query($query);

		while ($row = PVDatabase::fetchArray($result)) {
			if ($paged) {
				$row['current_page'] = $page_results['current_page'];
				$row['last_page'] = $page_results['last_page'];
				$row['total_pages'] = $page_results['total_pages'];
			}

			array_push($content_array, $row);
		}//end while

		$content_array = PVDatabase::formatData($content_array);
		self::_notify(get_class() . '::' . __FUNCTION__, $content_array, $args);
		$content_array = self::_applyFilter(get_class(), __FUNCTION__, $content_array, array('event' => 'return'));

		return $content_array;

	}//end getMeniUtemList

	/**
	 * Returns a menu's data based on the id of the menu.
	 *
	 * @param id $menu_id The id of the menu
	 *
	 * @return array $menu Returns the menu's data
	 * @access public
	 */
	public static function getMenu($menu_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $menu_id);

		$menu_id = self::_applyFilter(get_class(), __FUNCTION__, $menu_id, array('event' => 'args'));
		$menu_id = PVDatabase::makeSafe($menu_id);

		if (!empty($menu_id)) {
			$query = "SELECT * FROM " . PVDatabase::getMenuTableName() . " WHERE menu_id='$menu_id' ";

			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);

			$row = PVDatabase::formatData($row);
			self::_notify(get_class() . '::' . __FUNCTION__, $row, $menu_id);
			$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

			return $row;
		}

	}//end

	/**
	 * Returns a menu's data based on the assigned unique id of the menu.
	 *
	 * @param id $menu_id The assigned unique id of the menu
	 *
	 * @return array $menu Returns the menu's data
	 * @access public
	 */
	public static function getMenuByUniqueID($menu_unique_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $menu_unique_id);

		$menu_unique_id = self::_applyFilter(get_class(), __FUNCTION__, $menu_unique_id, array('event' => 'args'));
		$menu_unique_id = PVDatabase::makeSafe($menu_unique_id);

		if (!empty($menu_unique_id)) {
			$query = "SELECT * FROM " . PVDatabase::getMenuTableName() . " WHERE menu_unique_id='$menu_unique_id' ";

			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);

			$row = PVDatabase::formatData($row);
			self::_notify(get_class() . '::' . __FUNCTION__, $row, $menu_unique_id);
			$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

			return $row;
		}

	}//end

	/**
	 * Delete a menu from the database.
	 *
	 * @param id $menu_id The id of the mneu to be tdeleted
	 * @param boolean $delete_menu_items By default is set to true. Items with this menu will be deleted as well
	 *
	 * @return void
	 * @access public
	 */
	public static function deleteMenu($menu_id, $delete_menu_items = TRUE) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $menu_id, $delete_menu_items);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('menu_id' => $menu_id, 'delete_menu_items' => $delete_menu_items), array('event' => 'args'));
		$menu_id = $filtered['menu_id'];
		$delete_menu_items = $filtered['delete_menu_items'];

		$menu_id = PVDatabase::makeSafe($menu_id);

		if (!empty($menu_id)) {
			$query = "DELETE FROM " . PVDatabase::getMenuTableName() . " WHERE menu_id='$menu_id' ";
			PVDatabase::query($query);

			if ($delete_menu_items) {
				$query = "DELETE FROM " . PVDatabase::getMenuItemsTableName() . " WHERE menu_id='$menu_id'";
				PVDatabase::query($query);
			}

			self::_notify(get_class() . '::' . __FUNCTION__, $menu_id, $delete_menu_items);
		}//end !empty(menu_id)
	}//end deleteMenu

	/**
	 * Create a menu item that is associated with a menu
	 *
	 * @param array $args Arguements that define the fields in the menu item
	 * 		'menu_id' _id_: The id of the menu the menu item will belong too
	 * 		'parent_id' _id_: The id of the parent menu item
	 * 		'item_name' _string_: The name of the menu item
	 * 		'item_description' _string_: The description of the menu item
	 * 		'item_url' _string_: The url of the menu item
	 * 		'item_params' _string_: Parameters for the item
	 * 		'item_css' _string_: Css for the menu item
	 * 		'item_ordering' _int_: The order of the item, in the menu
	 * 		'item_enabled' _boolean_: If the item is enabled
	 * 		'item_title' _string_: The title of the menu tiem
	 * 		'item_permissions' _string_: The permissions allowed to view this item
	 * 		'item_id_tag' _string_: The tag id attribute of the menu item
	 *
	 * @return id $item_id The of the item tag
	 * @access public
	 */
	public static function createMenuItem($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getMenuItemDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);

		extract($args);

		$item_ordering = ceil($item_ordering);
		$item_enabled = ceil($item_enabled);

		$query = "INSERT INTO " . PVDatabase::getMenuItemsTableName() . "(menu_id, parent_id,item_name, item_description, item_url, item_params, item_css, item_ordering, item_enabled, item_title, item_permissions, item_id_tag) VALUES( '$menu_id' , '$parent_id' , '$item_name' , '$item_description', '$item_url', '$item_params', '$item_css', '$item_ordering', '$item_enabled', '$item_title', '$item_permissions' , '$item_id_tag' ) ";
		$item_id = PVDatabase::return_last_insert_query($query, 'item_id', PVDatabase::getMenuItemsTableName());

		self::_notify(get_class() . '::' . __FUNCTION__, $item_id, $args);
		$item_id = self::_applyFilter(get_class(), __FUNCTION__, $item_id, array('event' => 'return'));

		return $item_id;
	}//end createmenuitem

	/**
	 * Update a menu item. Requires the item_id to update.
	 *
	 * @param array $args Arguements that define the fields in the menu item
	 * 		'item_id' _id_: The id of the item. Required for updating the item
	 * 		'menu_id' _id_: The id of the menu the menu item will belong too
	 * 		'parent_id' _id_: The id of the parent menu item
	 * 		'item_name' _string_: The name of the menu item
	 * 		'item_description' _string_: The description of the menu item
	 * 		'item_url' _string_: The url of the menu item
	 * 		'item_params' _string_: Parameters for the item
	 * 		'item_css' _string_: Css for the menu item
	 * 		'item_ordering' _int_: The order of the item, in the menu
	 * 		'item_enabled' _boolean_: If the item is enabled
	 * 		'item_title' _string_: The title of the menu tiem
	 * 		'item_permissions' _string_: The permissions allowed to view this item
	 * 		'item_id_tag' _string_: The tag id attribute of the menu item
	 *
	 * @return void
	 * @access public
	 */
	public static function updateMenuItem($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getMenuItemDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		$item_ordering = ceil($item_ordering);
		$item_enabled = ceil($item_enabled);

		$query = "UPDATE " . PVDatabase::getMenuItemsTableName() . " SET  parent_id='$parent_id', item_name='$item_name', item_description='$item_description',  item_url='$item_url' , item_params='$item_params' , item_css='$item_css' , item_ordering='$item_ordering' ,  item_enabled='$item_enabled' , item_title='$item_title' , item_permissions='$item_permissions' ,  item_id_tag='$item_id_tag', menu_id='$menu_id' WHERE item_id='$item_id' ";
		PVDatabase::query($query);
		self::_notify(get_class() . '::' . __FUNCTION__, $args);
	}//end updateMenuItem

	/**
	 * Search for items in a list of items.Uses the PV Standard Search Query
	 *
	 * @param array $args Arguements that define the fields in the menu item
	 * 		'item_id' _id_: The id of the item. Required for updating the item
	 * 		'menu_id' _id_: The id of the menu the menu item will belong too
	 * 		'parent_id' _id_: The id of the parent menu item
	 * 		'item_name' _string_: The name of the menu item
	 * 		'item_description' _string_: The description of the menu item
	 * 		'item_url' _string_: The url of the menu item
	 * 		'item_params' _string_: Parameters for the item
	 * 		'item_css' _string_: Css for the menu item
	 * 		'item_ordering' _int_: The order of the item, in the menu
	 * 		'item_enabled' _boolean_: If the item is enabled
	 * 		'item_title' _string_: The title of the menu tiem
	 * 		'item_permissions' _string_: The permissions allowed to view this item
	 * 		'item_id_tag' _string_: The tag id attribute of the menu item
	 *
	 * @return array $items Returns an array of item
	 * @access public
	 */
	public static function getMenuItemList($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::getMenuItemDefaults();
		$args += self::_getSqlSearchDefaults();
		$args += array('join_menu' => false);
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$custom_where = $args['custom_where'];
		$custom_join = $args['custom_join'];
		extract($args, EXTR_SKIP);

		$first = 1;

		$content_array = array();
		$table_name = PVDatabase::getMenuItemsTableName();
		$db_type = PVDatabase::getDatabaseType();

		$WHERE_CLAUSE = '';

		if (!empty($menu_id) || $menu_id === '0') {

			$menu_id = trim($menu_id);

			if ($first == 0 && ($menu_id[0] != '+' && $menu_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($menu_id[0] == '+' || $menu_id[0] == ',') && $first == 1) {
				$app_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($menu_id, 'menu_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_id) || $item_id === '0') {

			$item_id = trim($item_id);

			if ($first == 0 && ($item_id[0] != '+' && $item_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_id[0] == '+' || $item_id[0] == ',') && $first == 1) {
				$item_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_id, 'item_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($parent_id) || $parent_id === '0') {

			$parent_id = trim($parent_id);

			if ($first == 0 && ($parent_id[0] != '+' && $parent_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($parent_id[0] == '+' || $parent_id[0] == ',') && $first == 1) {
				$parent_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($parent_id, 'parent_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_name)) {

			$item_name = trim($item_name);

			if ($first == 0 && ($item_name[0] != '+' && $item_name[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_name[0] == '+' || $item_name[0] == ',') && $first == 1) {
				$item_name[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_name, 'item_name');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_description)) {

			$item_description = trim($item_description);

			if ($first == 0 && ($item_description[0] != '+' && $item_description[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_description[0] == '+' || $item_description[0] == ',') && $first == 1) {
				$item_description[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_description, 'item_description');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_url)) {

			$item_url = trim($item_url);

			if ($first == 0 && ($item_url[0] != '+' && $item_url[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_url[0] == '+' || $item_url[0] == ',') && $first == 1) {
				$item_url[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_url, 'item_url');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_params)) {

			$item_params = trim($item_params);

			if ($first == 0 && ($item_params[0] != '+' && $item_params[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_params[0] == '+' || $item_params[0] == ',') && $first == 1) {
				$item_params[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_params, 'item_params');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_css)) {

			$item_css = trim($item_css);

			if ($first == 0 && ($item_css[0] != '+' && $item_css[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_css[0] == '+' || $item_css[0] == ',') && $first == 1) {
				$item_css[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_css, 'item_css');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_ordering)) {

			$item_ordering = trim($item_ordering);

			if ($first == 0 && ($item_ordering[0] != '+' && $item_ordering[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_ordering[0] == '+' || $item_ordering[0] == ',') && $first == 1) {
				$item_ordering[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_ordering, 'item_ordering');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_enabled)) {

			$item_enabled = trim($item_enabled);

			if ($first == 0 && ($item_enabled[0] != '+' && $item_enabled[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_enabled[0] == '+' || $item_enabled[0] == ',') && $first == 1) {
				$item_enabled[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_enabled, 'item_enabled');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_title)) {

			$item_title = trim($item_title);

			if ($first == 0 && ($item_title[0] != '+' && $item_title[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_title[0] == '+' || $item_title[0] == ',') && $first == 1) {
				$item_title[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_title, 'item_title');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_permissions)) {

			$item_permissions = trim($item_permissions);

			if ($first == 0 && ($item_permissions[0] != '+' && $item_permissions[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_permissions[0] == '+' || $item_permissions[0] == ',') && $first == 1) {
				$item_permissions[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_permissions, 'item_permissions');

			$first = 0;
		}//end not empty app_id

		if (!empty($item_id_tag)) {

			$item_id_tag = trim($item_id_tag);

			if ($first == 0 && ($item_id_tag[0] != '+' && $item_id_tag[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($item_id_tag[0] == '+' || $item_id_tag[0] == ',') && $first == 1) {
				$item_id_tag[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($item_id_tag, 'item_id_tag');

			$first = 0;
		}//end not empty app_id

		$JOINS = '';

		if ($join_menu) {
			$JOINS .= ' JOIN ' . PVDatabase::getMenuTableName() . ' ON ' . PVDatabase::getMenuTableName() . '.menu_id=' . PVDatabase::getMenuItemsTableName() . '.menu_id';
		}

		if (!empty($custom_where)) {
			$WHERE_CLAUSE .= ' ' . $custom_where . ' ';
		}

		if (!empty($custom_join)) {
			$JOINS .= ' ' . $custom_join . ' ';
		}

		if (!empty($WHERE_CLAUSE)) {
			$WHERE_CLAUSE = ' WHERE ' . $WHERE_CLAUSE;
		}

		if (!empty($distinct)) {
			$PREFIX_ARGS .= " DISTINCT $distinct, ";
		}

		if (!empty($limit) && $db_type == 'mssql' && !$paged) {
			$PREFIX_ARGS .= " TOP $limit ";
		}

		if ($paged) {
			$page_results = PVDatabase::getPagininationOffset($table_name, $JOINS, $WHERE_CLAUSE, $current_page, $results_per_page, $order_by);

			if ($db_type == 'mysql' || $db_type == 'postgresql') {
				$limit = ' ' . $page_results['limit_offset'];
			} else if ($db_type == 'mssql') {
				$WHERE_CLAUSE .= ' ' . $page_results['limit_offset'];
				$table_name = $page_results['from_clause'];
			}
		}

		if (!empty($group_by)) {
			$WHERE_CLAUSE .= " GROUP BY $group_by";
		}

		if (!empty($having)) {
			$WHERE_CLAUSE .= " HAVING $having";
		}

		if (!empty($order_by)) {
			$WHERE_CLAUSE .= " ORDER BY $order_by";
		}

		if (!empty($limit) && !$paged && ($db_type == 'mysql' || $db_type == 'postgresql')) {
			$WHERE_CLAUSE .= " LIMIT $limit";
		}

		if ($paged) {
			$WHERE_CLAUSE .= " $limit";
		}

		if (empty($custom_select)) {
			$custom_select = '*';
		}

		$query = "$prequery SELECT $prefix_args $custom_select FROM $table_name $JOINS $WHERE_CLAUSE";

		$result = PVDatabase::query($query);

		while ($row = PVDatabase::fetchArray($result)) {
			if ($paged) {
				$row['current_page'] = $page_results['current_page'];
				$row['last_page'] = $page_results['last_page'];
				$row['total_pages'] = $page_results['total_pages'];
			}

			array_push($content_array, $row);
		}//end while

		$content_array = PVDatabase::formatData($content_array);
		self::_notify(get_class() . '::' . __FUNCTION__, $content_array, $args);
		$content_array = self::_applyFilter(get_class(), __FUNCTION__, $content_array, array('event' => 'return'));

		return $content_array;
	}//end getMeniUtemList

	/**
	 * Returns the data for a specified item.
	 *
	 * @param id $item_id The id of the item being returned
	 *
	 * @return array $item Data about the item
	 * @access public
	 */
	public static function getMenuItem($item_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $item_id);

		$item_id = self::_applyFilter(get_class(), __FUNCTION__, $item_id, array('event' => 'args'));
		$item_id = PVDatabase::makeSafe($item_id);

		$query = "SELECT * FROM " . PVDatabase::getMenuItemsTableName() . " WHERE item_id='$item_id' ";
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$row = PVDatabase::formatData($row);

		self::_notify(get_class() . '::' . __FUNCTION__, $row, $item_id);
		$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

		return $row;
	}//end updateMenuItem

	/**
	 * Deletes an item from the database.
	 *
	 * @param id $item_id The id of the time to be deleted
	 *
	 * @return void
	 * @access public
	 */
	public static function deleteMenuItem($item_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $item_id);

		$item_id = self::_applyFilter(get_class(), __FUNCTION__, $item_id, array('event' => 'args'));
		$item_id = PVDatabase::makeSafe($item_id);
		$query = "DELETE FROM " . PVDatabase::getMenuItemsTableName() . " WHERE item_id='$item_id' ";

		PVDatabase::query($query);
		self::_notify(get_class() . '::' . __FUNCTION__, $item_id);
	}//end updateMenuItem

	/**
	 * @todo remove???
	 */
	public static function generateListMenu($menu_unique_id, $args) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $menu_unique_id, $args);

		if (!empty($menu_unique_id)) {

			$menu_unique_id = PVDatabase::makeSafe($menu_unique_id);

			if (PVValidator::isInteger($menu_unique_id)) {
				$query = "SELECT * FROM " . PVDatabase::getMenuTableName() . " WHERE menu_id='$menu_unique_id' ";
			} else {
				$query = "SELECT * FROM " . PVDatabase::getMenuTableName() . " WHERE menu_unique_id='$menu_unique_id' ";
			}

			$result = PVDatabase::query($query);

			$menu_info = PVDatabase::fetchArray($result);

			if (!empty($menu_info)) {

			}//end !empty($menu_info)

		}//end !empty(menu_unique_id)

	}//end displayListMenu

	private static function getMenuDefaults() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$defaults = array(
			'menu_id' => 0, 
			'menu_name' => '', 
			'menu_type' => '', 
			'menu_tag_id' => '', 
			'menu_css' => '', 
			'menu_order' => 0, 
			'content_id' => 0, 
			'user_id' => 0, 
			'app_id' => 0, 
			'menu_unique_id' => '', 
			'menu_class' => '', 
			'menu_description' => '', 
			'menu_enabled' => 0
		);

		$defaults = self::_applyFilter(get_class(), __FUNCTION__, $defaults, array('event' => 'return'));
		return $defaults;
	}

	private static function getMenuItemDefaults() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$defaults = array(
			'menu_id' => 0, 
			'item_id' => 0, 
			'parent_id' => 0, 
			'item_name' => '', 
			'item_description' => '', 
			'item_url' => '', 
			'item_params' => '', 
			'item_css' => '', 
			'item_ordering' => 0, 
			'item_enabled' => 0, 
			'item_title' => '', 
			'item_permissions' => '', 
			'item_id_tag' => ''
		);

		$defaults = self::_applyFilter(get_class(), __FUNCTION__, $defaults, array('event' => 'return'));
		return $defaults;
	}

}//end class
