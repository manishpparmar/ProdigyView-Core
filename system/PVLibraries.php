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
 *THIS SOFTWARE IS PROVIDED BY My-Lan AS IS'' AND ANY EXPRESS OR IMPLIED
 *WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 *FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL My-Lan OR
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

class PVLibraries extends PVStaticObject {

	private static $javascript_libraries_array;
	private static $jquery_libraries_array;
	private static $prototype_libraries_array;
	private static $motools_libraries_array;
	private static $css_files_array;
	private static $open_javascript;
	private static $libraries;

	public static function init($config = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);

		$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));

		self::$javascript_libraries_array = array();
		self::$jquery_libraries_array = array();
		self::$prototype_libraries_array = array();
		self::$motools_libraries_array = array();
		self::$css_files_array = array();
		self::$libraries = array();

		self::_notify(get_class() . '::' . __FUNCTION__, $config);
	}

	/**
	 * Adds javascript files to a queue of javascript files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 *
	 * @param string $script The name of script to be added. The name of script acts as key for accessing the script and the location of the script.
	 *
	 * @return void
	 * @access public
	 */
	public static function enqueueJavascript($script) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $script);

		$script = self::_applyFilter(get_class(), __FUNCTION__, $script, array('event' => 'args'));
		self::$javascript_libraries_array[$script] = $script;
		self::_notify(get_class() . '::' . __FUNCTION__, $script);
	}

	/**
	 * Adds jquery files to a queue of jquery files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 *
	 * @param string $script The name of script to be added. The name of script acts as key for accessing the script and the location of the script.
	 *
	 * @return void
	 * @access public
	 */
	public static function enqueueJquery($script) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $script);

		$script = self::_applyFilter(get_class(), __FUNCTION__, $script, array('event' => 'args'));
		self::$jquery_libraries_array[$script] = $script;
		self::_notify(get_class() . '::' . __FUNCTION__, $script);
	}

	/**
	 * Adds prototype files to a queue of prototype files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 *
	 * @param string $script The name of script to be added. The name of script acts as key for accessing the script and the location of the script.
	 * 
	 * @return void
	 * @access public
	 */
	public static function enqueuePrototype($script) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $script);

		$script = self::_applyFilter(get_class(), __FUNCTION__, $script, array('event' => 'args'));
		self::$prototype_libraries_array[$script] = $script;
		self::_notify(get_class() . '::' . __FUNCTION__, $script);
	}

	/**
	 * Adds mootools files to a queue of mootools files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 *
	 * @param string $script The name of script to be added. The name of script acts as key for accessing the script and the location of the script.
	 * 
	 * @return void
	 * @access public
	 */
	public static function enqueueMootools($script) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $script);

		$script = self::_applyFilter(get_class(), __FUNCTION__, $script, array('event' => 'args'));
		self::$motools_libraries_array[$script] = $script;
		self::_notify(get_class() . '::' . __FUNCTION__, $script);
	}

	/**
	 * Adds css files to a queue of css files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 *
	 * @param string $script The name of script to be added. The name of script acts as key for accessing the script and the location of the script.
	 * 
	 * @return void
	 * @access public
	 */
	public static function enqueueCss($script) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $script);

		$script = self::_applyFilter(get_class(), __FUNCTION__, $script, array('event' => 'args'));
		self::$css_files_array[$script] = $script;
		self::_notify(get_class() . '::' . __FUNCTION__, $script);
	}

	/**
	 * Adds a script directly into a queue to be outputted later.The script should be inputted with opening
	 * and closing tags as it would appear when the output occurs
	 * 
	 *
	 * @param string $script The string to be added to a queue. The string does not have a key and cannot be removed once added.
	 * 
	 * @return void
	 * @access public
	 */
	public static function enqueueOpenscript($script) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $script);

		$script = self::_applyFilter(get_class(), __FUNCTION__, $script, array('event' => 'args'));
		self::$open_javascript .= $script;
		self::_notify(get_class() . '::' . __FUNCTION__, $script);
	}

	/**
	 * Returns javascript file locations that have been inserted
	 * into the queue.
	 *
	 * @return array $script Returns an array of scripts. The key => value are the same and should present the location of the script
	 * @access public
	 */
	public static function getJavascriptQueue() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$javascript_libraries_array);
		$script = self::_applyFilter(get_class(), __FUNCTION__, self::$javascript_libraries_array, array('event' => 'return'));

		return $script;
	}

	/**
	 * Returns JQuery file locations that have been inserted
	 * into the queue.
	 *
	 * @return array $script Returns an array of scripts. The key => value are the same and should present the location of the script
	 * @access public
	 */
	public static function getJqueryQueue() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$jquery_libraries_array);
		$script = self::_applyFilter(get_class(), __FUNCTION__, self::$jquery_libraries_array, array('event' => 'return'));

		return $script;
	}

	/**
	 * Returns Prototype file locations that have been inserted
	 * into the queue.
	 *
	 * @return array $script Returns an array of scripts. The key => value are the same and should present the location of the script
	 * @access public
	 */
	public static function getPrototypeQueue() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$prototype_libraries_array);
		$script = self::_applyFilter(get_class(), __FUNCTION__, self::$prototype_libraries_array, array('event' => 'return'));

		return $script;
	}

	/**
	 * Returns mootools file locations that have been inserted
	 * into the queue.
	 *
	 * @return array $script Returns an array of scripts. The key => value are the same and should present the location of the script
	 * @access public
	 */
	public static function getMootoolsQueue() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$motools_libraries_array);
		$script = self::_applyFilter(get_class(), __FUNCTION__, self::$motools_libraries_array, array('event' => 'return'));

		return $script;
	}

	/**
	 * Returns css file locations that have been inserted
	 * into the queue.
	 *
	 * @return array $script Returns an array of scripts. The key => value are the same and should present the location of the script
	 * @access public
	 */
	public static function getCssQueue() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$css_files_array);
		$script = self::_applyFilter(get_class(), __FUNCTION__, self::$css_files_array, array('event' => 'return'));

		return $script;
	}

	/**
	 * Returns the open scripts that were previously added to the open script queue.
	 * 
	 * @return string $script The scripts added will be returned in one unified string
	 * @access public
	 */
	public static function getOpenscriptQueue() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$open_javascript);
		$script = self::_applyFilter(get_class(), __FUNCTION__, self::$open_javascript, array('event' => 'return'));

		return $script;
	}

	/**
	 * Add a library that will be auto loaded when loadLibraries is called. The libraries
	 * added will be available through the class.
	 *
	 * @param folder_name The name of folder that contains the library. By default the folder should be in the PV_Libraries
	 * 		  DEFINE location. Also acts as the library name when being referenced
	 * @param array $options Options than can be used to configure the library that will be loaded
	 * 			-'path' _string_: The path to the library. The default path is PV_LIBRARIES.$folder_name.DS
	 * 			-'autoload' _boolean_: When true, library will be automatically loaded when loadLibraries is called. Default is true.
	 * 			-'extensions' _array_: An array of allowed file extensions that will be included when the library loads. Default is .php
	 *
	 * @return void
	 * @access public
	 */
	public static function addLibrary($folder_name, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $folder_name, $options);

		$defaults = array('path' => PV_LIBRARIES . $folder_name . DS, 'autoload' => true, 'extensions' => array('.php'));

		$options += $defaults;
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('folder_name' => $folder_name, 'options' => $options), array('event' => 'args'));
		$folder_name = $filtered['folder_name'];
		$options = $filtered['options'];

		self::$libraries[$folder_name] = $options;
		self::_notify(get_class() . '::' . __FUNCTION__, $folder_name, $options);
	}

	/**
	 * Looks through any libraries that have been added through addLibrary function. If there ae libraries
	 * and their autoload is set to true, the library's file and folders will be included and made accessible.
	 *
	 * @return void
	 * @access public
	 */
	public static function loadLibraries() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		if (!empty(self::$libraries)) {

			foreach (self::$libraries as $key => $value) {
				if ($value['autoload']) {
					$library = PVFileManager::getFilesInDirectory($value['path'], array('verbose' => true));
					self::_loadLibrary($library, $value['extensions']);
				}
			}//end foreach
		}

		self::_notify(get_class() . '::' . __FUNCTION__);
	}//end loadLibraries

	/**
	 * Explicity loads a specfic library, even if autoload is set to false. If the library is already loaded, the files that have already
	 * been included WILL NOT be re-included.
	 *
	 * @param string $library_name The name of the library to be load. Will be the same name passed when addLibrary was used.
	 *
	 * @return void
	 * @access public
	 */
	public static function loadLibrary($library_name) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $library_name);

		$library_name = self::_applyFilter(get_class(), __FUNCTION__, $library_name, array('event' => 'args'));

		if (isset(self::$libraries[$library_name])) {
			$library = PVFileManager::getFilesInDirectory(self::$libraries[$library_name]['path'], array('verbose' => true));
			self::_loadLibrary($library, self::$libraries[$library_name]['extensions']);
			self::_notify(get_class() . '::' . __FUNCTION__, $library_name);
		}//end loadLibrary

	}//end

	/**
	 * Loads the library that is passed through. Uses include_once when including a file.
	 *
	 * @param array $library An array of the library that contains directores, files, and file information
	 * @param array $allowed_extensions The allowed extensions
	 *
	 * @return void
	 * @access protected
	 */
	protected static function _loadLibrary($library, $allow_extensions) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $library, $allow_extensions);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('library' => $library, 'allow_extensions' => $allow_extensions), array('event' => 'args'));
		$library = $filtered['library'];
		$allow_extensions = $filtered['allow_extensions'];

		foreach ($library as $key => $value) {
			if ($value['type'] == 'folder') {
				self::_loadLibrary($value['files'], $allow_extensions);
			} else {
				if (empty($allow_extensions)) {
					include_once ($key);
				} else {
					$extensions_allowed = implode($allow_extensions, '|');
					if (preg_match('/' . $extensions_allowed . '/', $value['basename'], $matches))
						include_once ($key);
				}
			}//end else
		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $library, $allow_extensions);
	}//end _loadLibrary

}//end class
