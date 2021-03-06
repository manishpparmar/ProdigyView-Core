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
class PVPoints extends PVStaticObject {

	/**
	 * Adds a point's value to the database.
	 *
	 * @param array $args Arguements that define a point
	 * 			-'user_id' _id_: The id of the user associated with this point
	 * 			-'content_id' _id_: The id of the content associated with this point
	 * 			-'comment_id' _id_: The id of the comment associated with this point
	 * 			-'app_id' _id_: The id of the application associated this point
	 * 			-'point_value' _double_: The value of this point. Can be a decimal or int.
	 * 			-'point_type' _string_: The type of point this is
	 * 			-'point_date' _date/time_: The date and time of this point
	 *
	 * @return id $point_id The id of this point
	 * @access public
	 */
	public static function addPoint($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::_getPointsDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		if (empty($point_date)) {
			$point_date = date("Y-m-d H:i:s", time());
		}

		$user_ip = $_SERVER['REMOTE_ADDR'];

		$query = "INSERT INTO " . PVDatabase::getPointsTableName() . "(user_id, content_id, comment_id, app_id, point_value, point_type, user_ip, point_date) VALUES( '$user_id', '$content_id', '$comment_id', '$app_id', '$point_value', '$point_type', '$user_ip', '$point_date' ) ";
		$point_id = PVDatabase::return_last_insert_query($query, 'point_id', PVDatabase::getPointsTableName());

		$point_id = self::_applyFilter(get_class(), __FUNCTION__, $point_id, array('event' => 'return'));
		self::_notify(get_class() . '::' . __FUNCTION__, $point_id, $args);

		return $point_id;
	}//end addUserPoster

	/**
	 * Adds a unique point to the system by checking of a similiar point already exist. Similiarity of a point
	 * is checked on the followingg fields: 'content_id', 'user_id', 'app_id', 'comment_id', 'point_type', user_ip.
	 *
	 * @param array $args Arguements that define a point
	 * 			-'user_id' _id_: The id of the user associated with this point
	 * 			-'content_id' _id_: The id of the content associated with this point
	 * 			-'comment_id' _id_: The id of the comment associated with this point
	 * 			-'app_id' _id_: The id of the application associated this point
	 * 			-'point_value' _double_: The value of this point. Can be a decimal or int.
	 * 			-'point_type' _string_: The type of point this is
	 * 			-'point_date' _date/time_: The date and time of this point
	 *
	 * @return id $point_id The id of this point if the point is unique, otherwise return false
	 * @access public
	 */
	public static function addUniquePoint($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::_getPointsDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		if (empty($point_date)) {
			$point_date = date("Y-m-d H:i:s", time());
		}

		$user_ip = $_SERVER['REMOTE_ADDR'];
		$search_args = array('user_ip' => $user_ip);

		if (!empty($content_id)) {
			$search_args['content_id'] = $content_id;
		}

		if (!empty($comment_id)) {
			$search_args['comment_id'] = $comment_id;
		}

		if (!empty($app_id)) {
			$search_args['app_id'] = $app_id;
		}

		if (!empty($point_type)) {
			$search_args['point_type'] = $point_type;
		}

		if (!empty($user_id)) {
			$search_args['user_id'] = $user_id;
		}

		$point_list = self::getPointsList($search_args);

		if (empty($point_list)) {
			$allow_insert = true;
		}

		if ($allow_insert == true) {
			$query = "INSERT INTO " . PVDatabase::getPointsTableName() . "(user_id, content_id, comment_id, app_id, point_value, point_type, user_ip, point_date)  VALUES( '$user_id', '$content_id', '$comment_id', '$app_id', '$point_value', '$point_type', '$user_ip', '$point_date' ) ";
			$point_id = PVDatabase::return_last_insert_query($query, 'point_id', PVDatabase::getPointsTableName());

			$point_id = self::_applyFilter(get_class(), __FUNCTION__, $point_id, array('event' => 'return'));
			self::_notify(get_class() . '::' . __FUNCTION__, $point_id, $args);

			return $point_id;
		}
		return FALSE;
	}//end addUserPoster

	/**
	 * Searches for points in the database based upon passed arguements. The method uses the PV Standars Query
	 * as parameters passed.
	 *
	 * @param array $args Arguements that can be used when searching for a point
	 * 			-'user_id' _id_: The id of the user associated with this point
	 * 			-'content_id' _id_: The id of the content associated with this point
	 * 			-'comment_id' _id_: The id of the comment associated with this point
	 * 			-'app_id' _id_: The id of the application associated this point
	 * 			-'point_value' _double_: The value of this point. Can be a decimal or int.
	 * 			-'point_type' _string_: The type of point this is
	 * 			-'point_date' _date/time_: The date and time of this point
	 * 			-'point_id' _id_: The id of the point
	 * 			-'user_ip' _string_: The ip that was assigned when point was created
	 * 			-'join_content' _boolean_: Join content related to this point on the content_id
	 * 			-'join_comments' _boolean_: Join comments related to this point by the comment_id
	 * 			-'join_users' _boolean_: Join users related to this point by the user_id
	 * 			-'join_apps' _boolean_: Joins apps related to this point by the app_id
	 *
	 * @return array $points An array of arrays that contain point data
	 * @access public
	 */
	public static function getPointsList($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::_getPointsDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));

		$custom_where = $args['custom_where'];
		$custom_join = $args['custom_join'];
		$custom_select = $args['custom_select'];
		$args = PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);

		$table_name = PVDatabase::getPointsTableName();
		$db_type = PVDatabase::getDatabaseType();

		$first = 1;
		$content_array = array();

		$WHERE_CLAUSE = '';

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

		if (!empty($point_value)) {

			$point_value = trim($point_value);

			if ($first == 0 && ($point_value[0] != '+' && $point_value[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($app_id[0] == '+' || $app_id[0] == ',') && $first == 1) {
				$point_value[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($point_value, 'point_value');

			$first = 0;
		}//end not empty app_id

		if (!empty($point_type)) {

			$point_value = trim($point_type);

			if ($first == 0 && ($point_type[0] != '+' && $point_type[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($point_type[0] == '+' || $point_type[0] == ',') && $first == 1) {
				$point_type[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($point_type, 'point_type');

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

		if (!empty($comment_id)) {

			$comment_id = trim($comment_id);

			if ($first == 0 && ($comment_id[0] != '+' && $comment_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($comment_id[0] == '+' || $comment_id[0] == ',') && $first == 1) {
				$comment_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($comment_id, 'comment_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($user_ip)) {

			$user_ip = trim($user_ip);

			if ($first == 0 && ($user_ip[0] != '+' && $user_ip[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($user_ip[0] == '+' || $user_ip[0] == ',') && $first == 1) {
				$user_ip[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($user_ip, 'user_ip');

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

		if (!empty($point_id)) {

			$point_id = trim($point_id);

			if ($first == 0 && ($point_id[0] != '+' && $point_id[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($point_id[0] == '+' || $point_id[0] == ',') && $first == 1) {
				$point_id[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($point_id, 'point_id');

			$first = 0;
		}//end not empty app_id

		if (!empty($point_date)) {

			$point_date = trim($point_date);

			if ($first == 0 && ($point_date[0] != '+' && $point_date[0] != ',')) {
				$WHERE_CLAUSE .= " AND ";
			} else if (($point_date[0] == '+' || $point_date[0] == ',') && $first == 1) {
				$point_date[0] = '';
			}

			$WHERE_CLAUSE .= ' ' . PVTools::parseSQLOperators($point_date, 'point_date');

			$first = 0;
		}//end not empty app_id

		$JOINS = '';

		if (!empty($custom_join)) {
			$JOINS .= " $custom_join ";
		}

		if ($join_users == true) {
			$JOINS .= " JOIN " . PVDatabase::getUsersTableName() . " ON " . PVDatabase::getUsersTableName() . ".user_id=" . PVDatabase::getPointsTableName() . ".user_id ";
		}

		if ($join_content == true) {
			$JOINS .= " JOIN " . PVDatabase::getContentTableName() . " ON " . PVDatabase::getContentTableName() . ".content_id=" . PVDatabase::getPointsTableName() . ".content_id ";
		}

		if ($join_comments == true) {
			$JOINS .= " JOIN " . PVDatabase::getContentCommentsTableName() . " ON " . PVDatabase::getContentCommentsTableName() . ".comment_id=" . PVDatabase::getPointsTableName() . ".comment_id ";
		}

		if ($join_applications == true) {
			$JOINS .= " JOIN " . PVDatabase::getApplicationsTableName() . " ON " . PVDatabase::getApplicationsTableName() . ".app_id=" . PVDatabase::getPointsTableName() . ".app_id ";
		}

		if (!empty($custom_where)) {
			$WHERE_CLAUSE .= " $custom_where ";
		}

		if (!empty($WHERE_CLAUSE)) {
			$WHERE_CLAUSE = ' WHERE ' . $WHERE_CLAUSE;
		}

		if (!empty($distinct)) {
			$prefix_args .= " DISTINCT $distinct, ";
		}

		if (!empty($limit) && $db_type == 'mssql' && !$paged) {
			$prefix_args .= " TOP $limit ";
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
	}//end getUserPointsList

	/**
	 * Retrieve a point's data by the point's id.
	 *
	 * @param id $point_id The id of the points who data to retrieve
	 *
	 * @return array $point Data about a point
	 * @access public
	 */
	public static function getPoint($point_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $point_id);

		$point_id = self::_applyFilter(get_class(), __FUNCTION__, $point_id, array('event' => 'args'));

		if (!empty($point_id)) {

			$point_id = PVDatabase::makeSafe($point_id);
			$query = "SELECT * FROM " . PVDatabase::getPointsTableName() . " WHERE point_id='$point_id'";
			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);

			self::_notify(get_class() . '::' . __FUNCTION__, $row);
			$row = self::_applyFilter(get_class(), __FUNCTION__, $row, array('event' => 'return'));

			return $row;
		}

	}//end getUserPoint

	/**
	 * Updates a point based on the point's id.
	 *
	 * @param arrays $args Contains the data to be updated. The data that will be updated is as follows:
	 * 			-'user_id' _id_: The id of the user associated with this point
	 * 			-'content_id' _id_: The id of the content associated with this point
	 * 			-'comment_id' _id_: The id of the comment associated with this point
	 * 			-'app_id' _id_: The id of the application associated this point
	 * 			-'point_value' _double_: The value of this point. Can be a decimal or int.
	 * 			-'point_type' _string_: The type of point this is
	 * 			-'point_date' _date/time_: The date and time of this point
	 * 			-'point_id' _id_: This field will be used in deciding which point to update
	 *
	 * @return void
	 * @access public
	 */
	public static function updatePoint($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$args += self::_getPointsDefaults();
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		if (empty($point_date)) {
			$point_date = date("Y-m-d H:i:s", time());
		}

		$query = "UPDATE " . PVDatabase::getPointsTableName() . " SET point_value='$point_value', content_id='$content_id', comment_id='$comment_id', app_id='$app_id', point_type='$point_type', user_id='$user_id', point_date='$point_date' WHERE point_id='$point_id'";
		PVDatabase::query($query);
		self::_notify(get_class() . '::' . __FUNCTION__, $args);
	}//end updateUserPoint

	/**
	 * The point to be deleted. Uses the ID of the point in deciding which point to delete.
	 *
	 * @param id $point_id The id of the point to be deleted
	 *
	 * @return void
	 * @access public
	 */
	public static function deletePoint($point_id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $point_id);

		$point_id = self::_applyFilter(get_class(), __FUNCTION__, $point_id, array('event' => 'args'));

		if (!empty($point_id)) {
			$point_id = PVDatabase::makeSafe($point_id);
			$query = "DELETE FROM " . PVDatabase::getPointsTableName() . " WHERE point_id='$point_id' ";

			PVDatabase::query($query);
			self::_notify(get_class() . '::' . __FUNCTION__, $point_id);
		}
	}//end deleteUserPoint

	protected static function _getPointsDefaults() {
			
		$defaults = array(
			'point_id' => 0, 
			'user_id' => 0, 
			'content_id' => 0, 
			'comment_id' => 0, 
			'app_id' => 0, 
			'point_value' => 0, 
			'point_type' => '', 
			'user_ip' => 0, 
			'point_date' => ''
		);

		return $defaults;
	}

}//end class
