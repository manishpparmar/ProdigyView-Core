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

class PVMVC extends PVStaticObject {

	/**
	 * Install or update a MVC into the database. MVCs are created and assigned an id by you, the developer.
	 *
	 * @param array $args The arguements that define an MVC
	 * 			-'mvc_unique_id' _string_: Required. This is an assigned id by the developer of the MVC.
	 * 			-'mvc_name' _string_: The name of the mvc.
	 * 			-'mvc_description' _string_: A description of the MVC
	 * 			-'mvc_author' _string_: The author of the MVC
	 * 			-'mvc_website' _string_: A website for the mvc
	 * 			-'mvc_license' _string_: The license for the MVC
	 * 			-'mvc_version' _double_: The version of this mvc
	 * 			-'mvc_directory' _string_: The directory of the MVC
	 * 			-'mvc_file' _string_: The main file to called when the mvc is made active
	 * 			-'mvc_object' _string_: An object associated with the MVC
	 * 			-'is_current_mvc' _boolean_: Determines if the mvc is the current one. Will be booted automatically is set to true
	 * 			-'auto_load' _boolean: Determines if the MVC will be automatically load in conjuction with initializeMVC()
	 *
	 * @return void
	 * @access public
	 */
	public static function installMVC($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::_getMVCDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		$is_current_mvc = ceil($is_current_mvc);
		$autoload_mvc = ceil($autoload_mvc);

		if (!PVValidator::isDouble($mvc_version) && !PVValidator::isInteger($mvc_version)) {
			$mvc_version = 0;
		}

		if (!empty($mvc_unique_id)) {

			$mvc_info = self::getMVCInfo($mvc_unique_id);

			if (empty($mvc_info)) {
				$query = "INSERT INTO " . PVDatabase::getMVCTableName() . "(mvc_unique_id, mvc_name, mvc_description, mvc_author,  mvc_website, mvc_license, mvc_version, mvc_directory, mvc_file, mvc_object, is_current_mvc, autoload_mvc) VALUES( '$mvc_unique_id', '$mvc_name', '$mvc_description', '$mvc_author', '$mvc_website', '$mvc_license', '$mvc_version', '$mvc_directory', '$mvc_file', '$mvc_object', '$is_current_mvc', '$autoload_mvc')";

			} else {
				$query = "UPDATE " . PVDatabase::getMVCTableName() . " SET  mvc_name='$mvc_name', mvc_description='$mvc_description' , mvc_author='$mvc_author',  mvc_website='$mvc_website', mvc_license='$mvc_license', mvc_version='$mvc_version', mvc_directory='$mvc_directory', mvc_file='$mvc_file', mvc_object='$mvc_object', is_current_mvc='$is_current_mvc', autoload_mvc='$autoload_mvc' WHERE mvc_unique_id='$mvc_unique_id' ";
			}

			PVDatabase::query($query);
			self::_notify(get_class() . '::' . __FUNCTION__, $args);
		}
	}//end installMVC

	/**
	 * Initalizes and MVC that is passed through. That will boot the MVC that is going to be used.
	 *
	 * @param string $mvc_unique_id The unique identification of the MVC to initiliaze.
	 *
	 * @return void
	 * @access public
	 */
	public static function initiliazeMVC($mvc_unique_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mvc_unique_id);

		$mvc_unique_id = self::_applyFilter(get_class(), __FUNCTION__, $mvc_unique_id, array('event' => 'args'));
		$mvc_info = self::getMVCInfo($mvc_unique_id);

		$mvc_file = PV_MVC . $mvc_info['mvc_directory'] . $mvc_info['mvc_file'];

		if (file_exists($mvc_file)) {
			include ($mvc_file);
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $mvc_unique_id);
	}//end initiliazeMVC

	/**
	 * Retreives the data associated with an MVC.
	 *
	 * @param string $mvc_unique_id The unique id of the MVC
	 *
	 * @return array $mvc Data pertaining to the MVC
	 * @access void
	 */
	public static function getMVCInfo($mvc_unique_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mvc_unique_id);

		$mvc_unique_id = self::_applyFilter(get_class(), __FUNCTION__, $mvc_unique_id, array('event' => 'args'));
		$mvc_unique_id = PVDatabase::makeSafe($mvc_unique_id);

		$query = "SELECT * FROM " . PVDatabase::getMVCTableName() . " WHERE mvc_unique_id='$mvc_unique_id'";
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);

		$row = PVDatabase::formatData($row);
		self::_notify(get_class() . '::' . __FUNCTION__, $row, $mvc_unique_id);
		$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

		return $row;
	}//end getMVCInfo

	/**
	 * Search for MVCs in the database based upon fields that make up that MVC. Uses the PV Standard Seeach Query.
	 *
	 * @param array $args The arguements that define an MVC when searching
	 * 			-'mvc_unique_id' _string_: Required. This is an assigned id by the developer of the MVC.
	 * 			-'mvc_name' _string_: The name of the mvc.
	 * 			-'mvc_description' _string_: A description of the MVC
	 * 			-'mvc_author' _string_: The author of the MVC
	 * 			-'mvc_website' _string_: A website for the mvc
	 * 			-'mvc_license' _string_: The license for the MVC
	 * 			-'mvc_version' _double_: The version of this mvc
	 * 			-'mvc_directory' _string_: The directory of the MVC
	 * 			-'mvc_file' _string_: The main file to called when the mvc is made active
	 * 			-'mvc_object' _string_: An object associated with the MVC
	 * 			-'is_current_mvc' _boolean_: Determines if the mvc is the current one. Will be booted automatically is set to true
	 * 			-'auto_load' _boolean: Determines if the MVC will be automatically load in conjuction with initializeMVC()
	 *
	 * @return array $mvcs Returns an array of MVCs found
	 * @access public
	 */
	public static function getMVCList($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::_getMVCDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$custom_where = $args['custom_where'];
		$custom_join = $args['custom_join'];
		$args = PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);

		$first = 1;

		$content_array = array();
		$table_name = PVDatabase::getMVCTableName();
		$db_type = PVDatabase::getDatabaseType();

		$WHERE_CLAUSE = '';

		if (!empty($mvc_unique_id) || $mvc_unique_id === '0') {

			$mvc_unique_id = trim($mvc_unique_id);

			if ($first == 0 && ($mvc_unique_id[0] != '+' && $mvc_unique_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($mvc_unique_id[0] == '+' || $mvc_unique_id[0] == ',') && $first == 1) {
				$mvc_unique_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($mvc_unique_id, 'mvc_unique_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($mvc_name) || $mvc_name === '0') {

			$mvc_name = trim($mvc_name);

			if ($first == 0 && ($mvc_name[0] != '+' && $mvc_name[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($mvc_name[0] == '+' || $mvc_name[0] == ',') && $first == 1) {
				$mvc_name[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($mvc_name, 'mvc_name');

			$first = 0;
		}//end not empty app_id

		if (!empty($mvc_description) || $mvc_description === '0') {

			$mvc_description = trim($mvc_description);

			if ($first == 0 && ($mvc_description[0] != '+' && $mvc_description[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($mvc_description[0] == '+' || $mvc_description[0] == ',') && $first == 1) {
				$mvc_description[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($mvc_description, 'mvc_description');

			$first = 0;
		}//end not empty app_id

		if (!empty($mvc_author)) {

			$mvc_author = trim($mvc_author);

			if ($first == 0 && ($mvc_author[0] != '+' && $mvc_author[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($mvc_author[0] == '+' || $mvc_author[0] == ',') && $first == 1) {
				$mvc_author[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($mvc_author, 'mvc_author');

			$first = 0;
		}//end not empty app_id

		if (!empty($mvc_website)) {

			$mvc_website = trim($mvc_website);

			if ($first == 0 && ($mvc_website[0] != '+' && $mvc_website[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($mvc_website[0] == '+' || $mvc_website[0] == ',') && $first == 1) {
				$mvc_website[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($mvc_website, 'mvc_website');

			$first = 0;
		}//end not empty app_id

		if (!empty($mvc_license)) {

			$mvc_license = trim($mvc_license);

			if ($first == 0 && ($mvc_license[0] != '+' && $mvc_license[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($mvc_license[0] == '+' || $mvc_license[0] == ',') && $first == 1) {
				$mvc_license[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($mvc_license, 'mvc_license');

			$first = 0;
		}//end not empty app_id

		if (!empty($mvc_version)) {

			$mvc_version = trim($mvc_version);

			if ($first == 0 && ($mvc_version[0] != '+' && $mvc_version[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($mvc_version[0] == '+' || $mvc_version[0] == ',') && $first == 1) {
				$mvc_version[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($mvc_version, 'mvc_version');

			$first = 0;
		}//end not empty app_id

		if (!empty($mvc_directory)) {

			$mvc_directory = trim($mvc_directory);

			if ($first == 0 && ($mvc_directory[0] != '+' && $mvc_directory[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($mvc_directory[0] == '+' || $mvc_directory[0] == ',') && $first == 1) {
				$mvc_directory[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($mvc_directory, 'mvc_directory');

			$first = 0;
		}//end not empty app_id

		if (!empty($mvc_file)) {

			$mvc_file = trim($mvc_file);

			if ($first == 0 && ($mvc_file[0] != '+' && $mvc_file[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($mvc_file[0] == '+' || $mvc_file[0] == ',') && $first == 1) {
				$mvc_file[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($mvc_file, 'mvc_file');

			$first = 0;
		}//end not empty app_id

		if (!empty($mvc_object)) {

			$mvc_object = trim($mvc_object);

			if ($first == 0 && ($mvc_object[0] != '+' && $mvc_object[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($mvc_object[0] == '+' || $mvc_object[0] == ',') && $first == 1) {
				$mvc_object[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($mvc_object, 'mvc_object');

			$first = 0;
		}//end not empty app_id

		if (!empty($is_current_mvc)) {

			$is_current_mvc = trim($is_current_mvc);

			if ($first == 0 && ($is_current_mvc[0] != '+' && $is_current_mvc[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($is_current_mvc[0] == '+' || $is_current_mvc[0] == ',') && $first == 1) {
				$is_current_mvc[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($is_current_mvc, 'is_current_mvc');

			$first = 0;
		}//end not empty app_id

		if (!empty($autoload_mvc)) {

			$autoload_mvc = trim($autoload_mvc);

			if ($first == 0 && ($autoload_mvc[0] != '+' && $autoload_mvc[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($autoload_mvc[0] == '+' || $autoload_mvc[0] == ',') && $first == 1) {
				$autoload_mvc[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($autoload_mvc, 'autoload_mvc');

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
	 * Removes an MVC from the database and also deletes the directory pertaining to that mvc.
	 *
	 * @param string $mvc_unique_id The assigned id of the MVC to delete
	 *
	 * @return void
	 * @access public
	 */
	public static function deleteMVC($mvc_unique_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mvc_unique_id);

		$mvc_unique_id = self::_applyFilter(get_class(), __FUNCTION__, $mvc_unique_id, array('event' => 'args'));

		if (!empty($mvc_unique_id)) {
			$mvc_info = self::getMVCInfo($mvc_unique_id);

			PVFileManager::deleteDirectory(PV_MVC . $mvc_info['mvc_directory']);

			$mvc_unique_id = PVDatabase::makeSafe($mvc_unique_id);

			$query = "DELETE FROM " . PVDatabase::getMVCTableName() . " WHERE mvc_unique_id='$mvc_unique_id'";
			$result = PVDatabase::query($query);

			PVDatabase::fetchArray($result);
			self::_notify(get_class() . '::' . __FUNCTION__, $mvc_unique_id);
		}
	}//end deleteMVC

	protected static function _getMVCDefaults() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$defaults = array(
			'mvc_unique_id' => '', 
			'mvc_name' => '', 
			'mvc_description' => '', 
			'mvc_author' => '', 
			'mvc_website' => '', 
			'mvc_license' => '', 
			'mvc_version' => 0, 
			'mvc_directory' => '', 
			'mvc_file' => '', 
			'mvc_object' => '', 
			'is_current_mvc' => 0, 
			'autoload_mvc' => 0
		);

		$defaults = self::_applyFilter(get_class(), __FUNCTION__, $defaults, array('event' => 'return'));
		return $defaults;
	}

}//end class
