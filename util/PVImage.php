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
class PVImage extends PVStaticObject {
	
	protected static $_converter = 'imagick';
	protected static $_write_image = true;
	protected static $_write_image_location = PV_IMAGE;
	protected static $_display_image = false;
	protected static $_add_extension = true;
	protected static $_return = 'image_location';
	
	/**
	 *  Initalize the PVImage class with default variables for image conversion
	 * 
	 * @param array @config An array of options to set as the default
	 * 			-'converter' _string_: The tool to be used for conversion. Default is Imagick
	 * 			-'write_image' _boolean_: Specifies to write the image to file. Default is true
	 * 			-'write_image_location' _string_: The default location to write the image to. Default is PV_ROOT.PV_IMAGE defines.
	 * 			-'display_image' _boolean_: The default boolean to display the image in a header after processing of method is complete. Default is false.
	 * 			-'add_extension' _boolean_: Will add an extension to then of the file if it is written. The extension will be the image type
	 * 			-'return' _string_: The default return. The default set is image_location to return the location of the written image file. The
	 * 			other option is 'image_object' which return the object to manipuate the image or 'image_bytes' which will return the image in a string of bytes
	 * 
	 * @return void
	 * @access public
	 */
	public static function init($config = array()) {
		
		$defaults = array(
			'converter' => 'imagick',
			'write_image' => true,
			'write_image_location' => PV_ROOT.PV_IMAGE,
			'display_image' => false,
			'add_extension' => true,
			'return' => 'image_location'
		);
		
		$config += $defaults;
		
		self::$_converter = $config['converter'];
		self::$_write_image = $config['write_image'];
		self::$_write_image_location  = $config['write_image_location'];
		self::$_display_image = $config['display_image'];
		self::$_add_extension = $config['add_extension'];
		self::$_return = $config['return'];
		
	}

	public static function uploadImage($file_name, $tmp_name, $file_size, $file_type, $image_width = 300, $image_height = 300, $thumbnailwidth = 150, $thumbnailheight = 150) {

		$image_folder_url = PV_IMAGE;
		$save_name = "";

		$image_types = array("image/bmp", "image/jpeg", "image/pjpeg", "image/gif", "image/x-png", "image/png", "image/pjpeg");

		if (pv_isImageFile($file_type)) {

			$image_exist = true;

			while ($image_exist) {
				$randomFileName = pv_generateRandomString(20, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');

				if (!file_exists(PV_ROOT . "$image_folder_url$randomFileName.jpg") || !file_exists(PV_ROOT . "$image_folder_url$randomFileName.png") || !file_exists(PV_ROOT . "$image_folder_url$randomFileName.gif")) {
					$image_exist = false;
				}
			}//end while

			if ($file_type == 'image/bmp') {
				$save_name = "$image_folder_url$randomFileName.bmp";
			} else if (pv_isJpegFile($file_type)) {
				$save_name = "$image_folder_url$randomFileName.jpg";
			} else if ($file_type == 'image/gif') {
				$save_name = "$image_folder_url$randomFileName.gif";
			} else if (pv_isPngFile($file_type)) {
				$save_name = "$image_folder_url$randomFileName.png";
			}

			$file_name = PVDatabase::makeSafe($file_name);
			$file_type = PVDatabase::makeSafe($file_type);
			$file_size = PVDatabase::makeSafe($file_size);
			$save_name = PVDatabase::makeSafe($save_name);

			if (empty($app_id)) {
				$app_id = 0;
			}

			if (empty($file_size)) {
				$file_size = 0;
			}
			
			if (move_uploaded_file($tmp_name, PV_ROOT . $save_name)) {

				$thumb_url = "";

				if ($file_type == 'image/bmp') {
					$thumb_url = "$image_folder_url$randomFileName-tn.bmp";
				} else if (pv_isJpegFile($file_type)) {
					$thumb_url = "$image_folder_url$randomFileName-tn.jpg";
				} else if ($file_type == 'image/gif') {
					$thumb_url = "$image_folder_url$randomFileName-tn.gif";
				} else if (pv_isPngFile($file_type)) {
					$thumb_url = "$image_folder_url$randomFileName-tn.png";
				}

				self::resizeImageGD(PV_ROOT . $save_name, PV_ROOT . DS . $thumb_url, $thumbnailwidth, $thumbnailheight);

				$images_array = array('image_url' => $save_name, 'thumb_url' => $thumb_url);

				return $images_array;
			} else {
				return FALSE;
			}

		} else {
			return false;
		}
	}// end upload Image

	public static function updateImageFromContent($content_id, $content_type, $file_name, $tmp_name, $file_size, $file_type, $image_width = 300, $image_height = 300, $thumbnailwidth = 150, $thumbnailheight = 150, $image_src = '') {

		$image_folder_url = PV_IMAGE;
		$save_name = "";

		$query = "SELECT image_url FROM " . pv_getImageContentTableName() . " WHERE image_id='$content_id'";
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$current_file_name = $row['image_url'];

		$basename = basename($current_file_name);
		$randomFileName = substr($basename, 0, strrpos($basename, '.'));

		$image_types = array("image/bmp", "image/jpeg", "image/pjpeg", "image/gif", "image/x-png", "image/pjpeg");
		
		if (pv_isImageFile($file_type)) {
			
			if (empty($randomFileName)) {
				
				if (pv_isImageFile($file_type)) {
					$randomFileName = pv_generateRandomString(20, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');
					$image_exist = true;

					while ($image_exist) {
						if (!file_exists(PV_ROOT . "$image_folder_url$randomFileName.jpg") || !file_exists(PV_ROOT . "$image_folder_url$randomFileName.png") || !file_exists(PV_ROOT . "$image_folder_url$randomFileName.gif")) {
							$image_exist = false;
						}//end
					}//end while
				}

			}
			
			if ($file_type == 'image/bmp') {
				$save_name = "$image_folder_url$randomFileName.bmp";
				$save_location = $randomFileName . '.bmp';
			} else if (pv_isJpegFile($file_type)) {
				$save_name = "$image_folder_url$randomFileName.jpg";
				$save_location = $randomFileName . '.jpg';
			} else if ($file_type == 'image/gif') {
				$save_name = "$image_folder_url$randomFileName.gif";
				$save_location = $randomFileName . '.gif';
			} else if (pv_isPngFile($file_type)) {
				$save_name = "$image_folder_url$randomFileName.png";
				$save_location = $randomFileName . '.png';
			}

			$file_name = PVDatabase::makeSafe($file_name);
			$file_type = PVDatabase::makeSafe($file_type);
			$file_size = PVDatabase::makeSafe($file_size);
			$save_name = PVDatabase::makeSafe($save_name);

			if (empty($app_id)) {
				$app_id = 0;
			}

			if (empty($file_size)) {
				$file_size = 0;
			}

			if (move_uploaded_file($tmp_name, PV_ROOT . $save_name) || PVFileManager::copyFile($tmp_name, PV_ROOT . $save_name)) {

				$thumb_url = "";

				if ($file_type == 'image/bmp') {
					$thumb_url = "$image_folder_url$randomFileName-tn.bmp";
					$thumb_name = $randomFileName . '-tn.bmp';
				} else if (pv_isJpegFile($file_type)) {
					$thumb_url = "$image_folder_url$randomFileName-tn.jpg";
					$thumb_name = $randomFileName . '-tn.jpg';
				} else if ($file_type == 'image/gif') {
					$thumb_url = "$image_folder_url$randomFileName-tn.gif";
					$thumb_name = $randomFileName . '-tn.gif';
				} else if (pv_isPngFile($file_type)) {
					$thumb_url = "$image_folder_url$randomFileName-tn.png";
					$thumb_name = $randomFileName . '-tn.png';
				}

				self::resizeImageGD(PV_ROOT . $save_name, PV_ROOT . DS . $thumb_url, $thumbnailwidth, $thumbnailheight);

				if (empty($image_width)) {
					$image_width = 300;
				}

				if (empty($image_height)) {
					$image_height = 300;
				}

				if (empty($thumbnailwidth)) {
					$thumbnailwidth = 150;
				}

				if (empty($thumbnailheight)) {
					$thumbnailheight = 150;
				}

				$query = "UPDATE " . pv_getImageContentTableName() . " SET image_type='$file_type', image_size='$file_size', image_url='$save_location', thumb_url='$thumb_name', image_width='$image_width', image_height='$image_height', thumb_width='$thumbnailwidth', thumb_height='$thumbnailheight', image_src='$image_src' WHERE  image_id='$content_id' ";
				PVDatabase::query($query);
				
				return 1;
			} else {
				echo "Not able to move file";
				return $this -> UPLOAD_FAILED;
			}

		} else {
			return $this -> INVALID_TYPE;
		}
	}// end upload Image

	public static function updateImage($current_file_name, $file_name, $tmp_name, $file_size, $file_type, $image_width = 300, $image_height = 300, $thumbnailwidth = 150, $thumbnailheight = 150) {

		$image_folder_url = PV_IMAGE ;
		$save_name = "";

		$basename = basename($current_file_name);
		$randomFileName = substr($basename, 0, strrpos($basename, '.'));

		$image_types = array("image/bmp", "image/jpeg", "image/pjpeg", "image/gif", "image/x-png", "image/pjpeg");

		if (pv_isImageFile($file_type)) {

			if (empty($randomFileName)) {

				if (in_array(strtolower($file_type), $image_types)) {
					$randomFileName = pv_generateRandomString(20, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');
					$image_exist = true;

					while ($image_exist) {
						if (!file_exists(PV_ROOT . "$image_folder_url$randomFileName.jpg") || !file_exists(PV_ROOT . "$image_folder_url$randomFileName.png") || !file_exists(PV_ROOT . "$image_folder_url$randomFileName.gif")) {
							$image_exist = false;
						}//end
					}//end while
				}

			}

			if ($file_type == 'image/bmp') {
				$save_name = "$image_folder_url$randomFileName.bmp";
			} else if (pv_isJpegFile($file_type)) {
				$save_name = "$image_folder_url$randomFileName.jpg";
			} else if ($file_type == 'image/gif') {
				$save_name = "$image_folder_url$randomFileName.gif";
			} else if (pv_isPngFile($file_type)) {
				$save_name = "$image_folder_url$randomFileName.png";
			}

			$file_name = PVDatabase::makeSafe($file_name);
			$file_type = PVDatabase::makeSafe($file_type);
			$file_size = PVDatabase::makeSafe($file_size);
			$save_name = PVDatabase::makeSafe($save_name);

			if (empty($app_id)) {
				$app_id = 0;
			}

			if (empty($file_size)) {
				$file_size = 0;
			}

			if (move_uploaded_file($tmp_name, PV_ROOT . $save_name)) {

				$thumb_url = "";

				if ($file_type == 'image/bmp') {
					$thumb_url = "$image_folder_url$randomFileName-tn.bmp";
					$thumb_name = $randomFileName . '-tn.bmp';
				} else if (pv_isJpegFile($file_type)) {
					$thumb_url = "$image_folder_url$randomFileName-tn.jpg";
					$thumb_name = $randomFileName . '-tn.jpg';
				} else if ($file_type == 'image/gif') {
					$thumb_url = "$image_folder_url$randomFileName-tn.gif";
					$thumb_name = $randomFileName . '-tn.gif';
				} else if (pv_isPngFile($file_type)) {
					$thumb_url = "$image_folder_url$randomFileName-tn.png";
					$thumb_name = $randomFileName . '-tn.png';
				}

				self::resizeImageGD(PV_ROOT . $save_name, PV_ROOT . DS . $thumb_url, $thumbnailwidth, $thumbnailheight);

				return 1;
			} else {
				echo "Not able to move file";
				return $this -> UPLOAD_FAILED;
			}

		} else {
			return $this -> INVALID_TYPE;
		}
	}// end upload Image

	/**
	 * R
	 */
	public static function resizeImageGD($name, $filename, $new_w = 150, $new_h = 150) {

		if ($new_w == 0) {
			$new_w = 150;
		}
		if ($new_h == 0) {
			$new_h = 150;
		}

		$system = explode('.', $name);

		if (pv_checkFileMimeType($name, '/jpg|jpeg/', array('search_method' => 'PREG_MATCH'))) {
			$src_img = imagecreatefromjpeg($name);
		} else if (pv_checkFileMimeType($name, '/png/', array('search_method' => 'PREG_MATCH'))) {
			$src_img = imagecreatefrompng($name);
		} else {
			$src_img = imagecreatefromjpeg($name);
		}

		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);

		if ($old_x > $old_y) {
			$thumb_w = $new_w;
			$thumb_h = $old_y * ($new_h / $old_x);
		}
		if ($old_x < $old_y) {
			$thumb_w = $old_x * ($new_w / $old_y);
			$thumb_h = $new_h;
		}
		if ($old_x == $old_y) {
			$thumb_w = $new_w;
			$thumb_h = $new_h;
		}

		$dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);

		if (pv_checkFileMimeType($name, '/png/', array('search_method' => 'PREG_MATCH'))) {
			imagepng($dst_img, $filename);
		} else {
			imagejpeg($dst_img, $filename);
		}

		imagedestroy($dst_img);
		imagedestroy($src_img);
	}

	
	public static function cropImage($src, $ouput, $width, $height) {

		$file_type = PVFileManager::getFileMimeTypee($src);

		if (PVValidator::isJpegFile($file_type)) {
			$original_image_gd = imagecreatefromjpeg($file_name);
		} else if (PVValidator::isGifFile($file_type)) {
			$original_image_gd = imagecreatefromgif($file_name);
		} else if (PVValidator::isPngFile($file_type)) {
			$original_image_gd = imagecreatefrompng($file_name);
		}

	}
	
	/**
	 * Add's a watermark to an image. By default use imagick but if that is not installed,
	 * the default will be gd.
	 * 
	 * @param mixed $image The value passed in can either be the location on a file system or the images in bytes. If
	 * 			the image is bytes, the options 'type' = blob my be set.
	 * @param array $options Options that can be used for further configuring the dropshadow
	 * 			-'converter' _string_: The default converter set by the init function.
	 * 			-'write_image' _boolean_: Write the image out to file. Default is true.
	 * 			-'write_image_location' _string_: The location to save the image. Default is the save location set in the init
	 * 			-'write_image_name' _string_: A name to save the image as. Default is a random string.
	 * 			-'display_image' _boolean_: Determines if the image is to be displayed automatically in a header. Default is false.
	 * 			-'add_extension' _boolean_: Add a file extension to the write_image_name. Default is true.
	 * 			-'return' _string_: Specifiy to return an object or file location. Default is set in the init()
	 * 			-'format' _string_: The format to save the image in. Default is png.
	 * 			-'type' _string_: The format the image is in before manipulation. If a blob fo byties is being passed set type to 'blob', otherwise type will be file.
	 * 			
	 * 		
	 * 
	 * @return mixed $return The data to be returned. The type of data that is returned is set in the options
	 * @access public
	 * @todo add GD support
	 */
	public static function watermarkImageWithText($image, $watermark, $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $image, $watermark, $options);
			
		$defaults = array(
			'font' => 'Helvetica',
			'font_size' => 30,
			'font_color' => 'black',
			'font_style' => Imagick::STYLE_NORMAL,
			'fill_alpha' => 0.4,
			'position_x' => 0,
			'position_y' => 0,
			'rotation' => 0,
			'type' => 'file',
			'gravity' => Imagick::GRAVITY_CENTER,
			'converter' => self::$_converter,
			'write_image' => self::$_write_image,
			'write_image_location' => self::$_write_image_location,
			'write_image_name' => PVTools::generateRandomString(),
			'display_image' => self::$_display_image,
			'add_extension' => self::$_add_extension,
			'return' => self::$_return
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('image' => $image, 'watermark' => $watermark, 'options' => $options ), array('event' => 'args'));
		$image = $filtered['image'];
		$watermark = $filtered['watermark'];
		$options = $filtered['options'];
		
		$return = null;
		
		if($options['converter'] == 'imagick') {
			
			if($options['type'] == 'blob') {
				$im = new Imagick();
				$im -> readImageBlob($image);
			} else {
				$im = new Imagick($image);
			}
	 
			$draw = new ImagickDraw();
			$draw->setFont( $options['font'] );
			$draw->setFontSize( $options['font_size'] );
			$draw->setFontStyle( $options['font_size'] );
			$draw->setFillAlpha( $options['fill_alpha'] );
			$draw->setFillColor($options['font_color']);
			$draw->setGravity( $options['gravity'] );
			$im->annotateImage( $draw, $options['position_x'], $options['position_y'], $options['rotation'], $watermark );
			
			
			if($options['write_image']) {
				$output_file = $options['write_image_location'].$options['write_image_name'] ;
				
				if($options['add_extension'])
					$output_file .= '.'.strtolower($im->getImageFormat());
					
				if($options['return'] == 'image_location')
					$return = $output_file;
				
				$im->writeImage( $output_file );
			}
			
			if($options['display_image']) {
				header( "Content-Type: image/{$im->getImageFormat()}" );
				echo $im;
			}
			
			if($options['return'] == 'image_object')
					$return = $im;
			else
				$im -> destroy();
			
		} else {
			
			$file_type = PVFileManager::getFileMimeType($image);

			if (PVValidator::isGifFile($file_type)) {
				$original_image = imagecreatefromgif($image);
			} else if (PVValidator::isPngFile($file_type)) {
				$original_image = imagecreatefrompng($image);
			} else {
				$original_image = imagecreatefromjpeg($image);
			}
			
		}//end else
		
		self::_notify(get_class() . '::' . __FUNCTION__, $return, $image, $watermark, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));
		
		return $return;
	}//end watermarkImage
	
	/**
	 * Creates a watermark over an image with another image
	 * 
	 * @param mixed $image The value passed in can either be the location on a file system or the images in bytes. If
	 * 			the image is bytes, the options 'type' = blob my be set.
	 * @param array $options Options that can be used for further configuring the dropshadow
	 * 			-'converter' _string_: The default converter set by the init function.
	 * 			-'write_image' _boolean_: Write the image out to file. Default is true.
	 * 			-'write_image_location' _string_: The location to save the image. Default is the save location set in the init
	 * 			-'write_image_name' _string_: A name to save the image as. Default is a random string.
	 * 			-'display_image' _boolean_: Determines if the image is to be displayed automatically in a header. Default is false.
	 * 			-'add_extension' _boolean_: Add a file extension to the write_image_name. Default is true.
	 * 			-'return' _string_: Specifiy to return an object or file location. Default is set in the init()
	 * 			-'format' _string_: The format to save the image in. Default is png.
	 * 			-'type' _string_: The format the image is in before manipulation. If a blob fo byties is being passed set type to 'blob', otherwise type will be file.
	 * 			-'offest_x' _int_: The offset on the x_coordinate when placing the drop shadow. Default is 0
	 * 			-'offeset_y' _int_: The offset on the y_coordinate when placing the drop shadow. Default is 0.
	 * 		
	 * 
	 * @return mixed $return The data to be returned. The type of data that is returned is set in the options
	 * @access public
	 * @todo add GD support
	 */
	public static function watermarkImageWithImage($image, $watermark, $options = array()){
			
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $image, $watermark, $options);
			
		$defaults = array(
			'offset_y' => 0,
			'offset_x' => 0,
			'composite' => Imagick::COMPOSITE_OVER,
			'type' => 'file',
			'watermark_type' => 'file',
			'watermark_opactiy' => 1,
			'gravity' => Imagick::GRAVITY_CENTER,
			'converter' => self::$_converter,
			'write_image' => self::$_write_image,
			'write_image_location' => self::$_write_image_location,
			'write_image_name' => PVTools::generateRandomString(),
			'display_image' => self::$_display_image,
			'add_extension' => self::$_add_extension,
			'return' => self::$_return
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('image' => $image, 'watermark' => $watermark, 'options' => $options ), array('event' => 'args'));
		$image = $filtered['image'];
		$watermark = $filtered['watermark'];
		$options = $filtered['options'];
		
		$return = null;
		
		if($options['converter'] == 'imagick') {
			
			if($options['type'] == 'blob') {
				$im = new Imagick();
				$im -> readImageBlob($image);
			} else {
				$im = new Imagick($image);
			}
			
			if($options['watermark_type'] == 'blob') {
				$watermark_im = new Imagick();
				$watermark_im -> readImageBlob($watermark);
			} else {
				$watermark_im = new Imagick($watermark);
			}

			$im -> compositeImage($watermark_im, $options['composite'], $options['offset_x'], $options['offset_y']);
			$watermark_im -> destroy();
			
			if($options['write_image']) {
				$output_file = $options['write_image_location'].$options['write_image_name'] ;
				
				if($options['add_extension'])
					$output_file .= '.'.strtolower($im->getImageFormat());
					
				if($options['return'] == 'image_location')
					$return = $output_file;
				
				$im->writeImage( $output_file );
			}
			
			if($options['display_image']) {
				header( "Content-Type: image/{$im->getImageFormat()}" );
				echo $im;
			}
			
			if($options['return'] == 'image_object')
					$return = $im;
			else
				$im -> destroy();
			
		} else {
			
			$file_type = PVFileManager::getFileMimeType($image);

			if (PVValidator::isGifFile($file_type)) {
				$original_image = imagecreatefromgif($image);
			} else if (PVValidator::isPngFile($file_type)) {
				$original_image = imagecreatefrompng($image);
			} else {
				$original_image = imagecreatefromjpeg($image);
			}
			
		}//end else
		
		self::_notify(get_class() . '::' . __FUNCTION__, $return, $image, $watermark, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));
		
		return $return;
	}
	
	/**
	 * Converts an image to another format.
	 * 
	 * @param mixed $image The image can either be the location of an image on a server or a bytes of an image. If the passed variable is
	 * 			bytes, remember to set the option type to 'blob'
	 * @param string $format The format to change the image into. The most common are png, gif and jpeg.
	 * @param array $options Options that can alter the conversion process
	 * 			-'converter' _string_: The default converter set by the init function
	 * 			-'write_image' _boolean_: Write the image out to file. Default is true.
	 * 			-'write_image_location' _string_: The location to save the image. Default is the save location set in the init
	 * 			-'write_image_name' _string_: A name to save the image as. Default is a random string.
	 * 			-'add_extension' _boolean_: Add a file extension to the write_image_name. Default is true.
	 * 			-'return' _string_: Specifiy to return an object or file location. Default is set in the init()
	 * 			-'format' _string_: The format to save the image in. Default is gif.
	 * 
	 * @return $mixed $value The value return is set by the option. Either the location to the file is return or the object for creating the image.
	 * @access public
	 * @todo add GD support
	 */	
	public static function convertImageFormat($image, $format, $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $image, $format, $options);
		
		$defaults = array(
			'type' => 'file',
			'converter' => self::$_converter,
			'write_image' => self::$_write_image,
			'write_image_location' => self::$_write_image_location,
			'write_image_name' => PVTools::generateRandomString(),
			'display_image' => self::$_display_image,
			'add_extension' => self::$_add_extension,
			'return' => self::$_return
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('image' => $image, 'format' => $format, 'options' => $options ), array('event' => 'args'));
		$image = $filtered['image'];
		$format = $filtered['format'];
		$options = $filtered['options'];
		
        $im = new Imagick();
       
		if($options['type'] == 'blob') {
			$im = new Imagick();
			$im -> readImageBlob($image);
		} else {
			$im = new Imagick($image);
			$im -> pingImage($image);
			$im -> readImage( $image );
		}
       
        $im->setImageFormat( $format );

        if($options['write_image']) {
			$output_file = $options['write_image_location'].$options['write_image_name'] ;
				
			if($options['add_extension'])
				$output_file .= '.'.strtolower($im->getImageFormat());
				
			$im->writeImage( $output_file );
				
			if($options['return'] == 'image_location')
				$return = $output_file;
		}
			
		if($options['display_image']) {
			header( "Content-Type: image/{$im->getImageFormat()}" );
			echo $im;
		}
			
		if($options['return'] == 'image_object') {
			$return = $im;
		} else {
			if($options['return'] == 'image_bytes')
				$return = $im -> getImageBlob();
			$im -> destroy();
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $image, $format, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));
		
		return $return;
	}
	
	/**
	 * Scale an image to a new size based on the passed width and height.
	 * 
	 * @param mixed $image Either pass the location of the file on a server or the image as bytes. If the image is passed as bytes,
	 * 			set the options type to 'blob'
	 * @param array $options Options that control the animating of images
	 * 			-'converter' _string_: The default converter set by the init function
	 * 			-'write_image' _boolean_: Write the image out to file. Default is true.
	 * 			-'write_image_location' _string_: The location to save the image. Default is the save location set in the init
	 * 			-'write_image_name' _string_: A name to save the image as. Default is a random string.
	 * 			-'add_extension' _boolean_: Add a file extension to the write_image_name. Default is true.
	 * 			-'return' _string_: Specifiy to return an object or file location. Default is set in the init()
	 * 			-'format' _string_: The format to save the image in. Default is gif.
	 * 			-'bestfit' _boolean_: Scales image to the best fit. Default is false.
	 * 
	 * @return $mixed $value The value return is set by the option. Either the location to the file is return or the object for creating the image.
	 * @access public
	 * @todo add GD support
	 */
	public static function scaleImage($image, $width, $height, $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $image, $width, $height, $options);
		
		$defaults = array(
			'bestfit' => false,
			'type' => 'file',
			'converter' => self::$_converter,
			'write_image' => self::$_write_image,
			'write_image_location' => self::$_write_image_location,
			'write_image_name' => PVTools::generateRandomString(),
			'display_image' => self::$_display_image,
			'add_extension' => self::$_add_extension,
			'return' => self::$_return
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('image' => $image, 'width' => $width, 'height' => $height, 'options' => $options ), array('event' => 'args'));
		$image = $filtered['image'];
		$width = $filtered['width'];
		$height = $filtered['height'];
		$options = $filtered['options'];
		
		$return = '';
		
		if($options['converter'] == 'imagick') {
			if($options['type'] == 'blob') {
				$im = new Imagick();
				$im -> readImageBlob($image);
			} else {
				$im = new Imagick($image);
			}
			
			$im->scaleImage($width, $height, $options['bestfit']);
			
			if($options['write_image']) {
				$output_file = $options['write_image_location'].$options['write_image_name'] ;
				
				if($options['add_extension'])
					$output_file .= '.'.strtolower($im->getImageFormat());
				
				$im->writeImage( $output_file );
				
				if($options['return'] == 'image_location')
					$return = $output_file;
			}
			
			if($options['display_image']) {
				header( "Content-Type: image/{$im->getImageFormat()}" );
				echo $im;
			}
			
			if($options['return'] == 'image_object')
					$return = $im;
		} else {
			
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $return, $image, $width, $height, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));
		
		return $return;
	}//end scale image
	
	/**
	 * Returns the width image of the image. By default, Imagick will be used if it installed,
	 * otherwise the GD libraries will be used.
	 * 
	 * @param string $image The location of the image
	 * @param array $options
	 * 
	 * @return int $width The width of the image
	 * @access public
	 * @todo implement with GD functions
	 */
	public static function getImageWidth($image, $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $image, $options);
		
		$defaults = array(
			'converter' => self::$_converter,
			'type' => 'file',
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('image' => $image, 'options' => $options ), array('event' => 'args'));
		$image = $filtered['image'];
		$options = $filtered['options'];
		
		if($options['converter'] == 'imagick') {
			
			if($options['type'] == 'blob') {
				$im = new Imagick();
				$im -> readImageBlob($image);
			} else {
				$im = new Imagick($image);
			}
			
			$width = $im -> getImageWidth();
			
		} else {
			
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $width, $image, $options);
		$width = self::_applyFilter(get_class(), __FUNCTION__, $width, array('event' => 'return'));
		
		return $width;
	}//end getImageWidth
	
	/**
	 * Returns the height image of the image. By default, Imagick will be used if it installed,
	 * otherwise the GD libraries will be used.
	 * 
	 * @param string $image The location of the image
	 * @param array $options
	 * 
	 * @return int $height The height of the image
	 * @access public
	 * @todo add GD support
	 */
	public static function getImageHeight($image, $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $image, $options);
		
		$defaults = array(
			'converter' => self::$_converter,
			'type' => 'file',
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('image' => $image, 'options' => $options ), array('event' => 'args'));
		$image = $filtered['image'];
		$options = $filtered['options'];
		
		if($options['converter'] == 'imagick') {
			if($options['type'] == 'blob') {
				$im = new Imagick();
				$im -> readImageBlob($image);
			} else {
				$im = new Imagick($image);
			}
			
			$height = $im -> getImageHeight();
			
		} else {
			
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $height, $image, $options);
		$height = self::_applyFilter(get_class(), __FUNCTION__, $height, array('event' => 'return'));
		
		return $height;
	}//end getImageWidth
	
	/**
	 * Takes an array of images and put them into an animated gif. The method accepts images as blobs/bytes,
	 * locations to a file, or an imagick object that is holding an image.
	 * 
	 * @param array $data The day is the array of images to be passed in. The data at each index can either be
	 * 				a blog/bytes, location to a file, or an imagick object with animage
	 * @param array $options Options that control the animating of images
	 * 			-'converter' _string_: The default converter set by the init function
	 * 			-'write_image' _boolean_: Write the image out to file. Default is true.
	 * 			-'write_image_location' _string_: The location to save the image. Default is the save location set in the init
	 * 			-'write_image_name' _string_: A name to save the image as. Default is a random string.
	 * 			-'add_extension' _boolean_: Add a file extension to the write_image_name. Default is true.
	 * 			-'return' _string_: Specifiy to return an object or file location. Default is set in the init()
	 * 			-'format' _string_: The format to save the image in. Default is gif.
	 * 			-'image_delay' _integer_: The delay between the images, default is 30
	 * 			-'image_width' _integer_: The width of the images
	 * 			-'image_height' _integer_: The height of the images
	 * 
	 * @return $mixed $value The value return is set by the option. Either the location to the file is return or the object for creating the image.
	 * @access public
	 * @todo add GD support
	 */
	public static function animateImage($data , array $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);
		
		$defaults = array(
			'converter' => self::$_converter,
			'write_image' => self::$_write_image,
			'write_image_location' => self::$_write_image_location,
			'write_image_name' => PVTools::generateRandomString(),
			'display_image' => self::$_display_image,
			'add_extension' => self::$_add_extension,
			'return' => self::$_return,
			'format' => 'gif',
			'image_delay' => 30,
			'image_width' => 100,
			'image_height' => 100,
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('data' => $data, 'options' => $options ), array('event' => 'args'));
		$data = $filtered['data'];
		$options = $filtered['options'];
		
		$im = new Imagick();
		$im -> setFormat( $options['format'] );
	 
	    foreach($data as $key => $value ) {
	   		
			if(is_string($value) && !file_exists($value)) {
				$tmp = new Imagick();
				$tmp -> readImageBlob($value);
				
				$im -> addImage($tmp);
				$tmp -> destroy();
			} else if(is_string($value) && file_exists($value)) {
				$tmp = new Imagick();
				$tmp -> readImage($value);
				$im -> addImage($tmp);
				$tmp -> destroy();
			} else if(is_object($value)) {
				$im -> addImage($value);
			}
	    }
		
		$im -> setImageFormat( $options['format'] );
		if($options['write_image']) {
			$output_file = $options['write_image_location'].$options['write_image_name'] ;
				
			if($options['add_extension'])
				$output_file .= '.'.strtolower($im->getImageFormat());
				
			$im->writeImages( $output_file, true );
				
			if($options['return'] == 'image_location')
				$return = $output_file;
		}
		
		if($options['display_image']) {
			header( "Content-Type: image/{$im->getImageFormat()}" );
			echo $im;
		}
			
		if($options['return'] == 'image_object') {
			$return = $im;
		} else {
			if($options['return'] == 'image_bytes')
				$return = $im -> getImageBlob();
			$im -> destroy();
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $return, $data, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));
		
		return $return;
	}

	/**
	 * Combines an array of strings together to create an image of animated text.
	 * 
	 * @param array $data The array of strings. Each index in the array should be a different string
	 * @param array $options An array of options that can determine how the string will be animated
	 * 			-'converter' _string_: The default converter set by the init function.
	 * 			-'write_image' _boolean_: Write the image out to file. Default is true.
	 * 			-'write_image_location' _string_: The location to save the image. Default is the save location set in the init
	 * 			-'write_image_name' _string_: A name to save the image as. Default is a random string.
	 * 			-'display_image' _boolean_: Determines if the image is to be displayed automatically in a header. Default is false.
	 * 			-'add_extension' _boolean_: Add a file extension to the write_image_name. Default is true.
	 * 			-'return' _string_: Specifiy to return an object or file location. Default is set in the init()
	 * 			-'format' _string_: The format to save the image in. Default is gif.
	 * 			-'image_delay' _integer_: The delay between the images, default is 30
	 * 			-'image_width' _integer_: The width of the images
	 * 			-'image_height' _integer_: The height of the images
	 * 			-'font' _string_: The font to be used when writing text. Defaultis Helvetica
	 * 			-'font_size' _int_: The size of the font to be used. Default is 20
	 * 			-'font_color' _string_: The color of the font. Default is black.
	 * 			-'font_style' _int_: The style of the font. Default is Imagick::Style_Normal
	 * 			-'fill_alpha' _double: The number between 0 and 1 on how transparent the text is. Default is 1.
	 * 			-'position_x' _int_: The starting position of the text on the x-coordinate. Default is 0.
	 * 			-'position_y' _int_: The starting position of the text on the y-coordinate. Default is 0.
	 * 			-'rotation' _int_: The number of degress to rotate the text. Default is 0.
	 * 			-'gravity' _int_: The imagick defined constant on the gravity of the text. Default is Imagick::GRAVITY_CENTER
	 * 			-'pixel_color' _string_: Serves as the background color the text will be placed on. Default is white
	 * 
	 * @return mixed $return The data to be return. Return is set in the options
	 * @access public
	 * @todo add GD support
	 */
	public static function animateText($data , array $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);
		
		$defaults = array(
			'font' => 'Helvetica',
			'font_size' => 20,
			'font_color' => 'black',
			'font_style' => Imagick::STYLE_NORMAL,
			'fill_alpha' => 1,
			'position_x' => 0,
			'position_y' => 0,
			'rotation' => 0,
			'gravity' => Imagick::GRAVITY_CENTER,
			'converter' => self::$_converter,
			'write_image' => self::$_write_image,
			'write_image_location' => self::$_write_image_location,
			'write_image_name' => PVTools::generateRandomString(),
			'display_image' => self::$_display_image,
			'add_extension' => self::$_add_extension,
			'return' => self::$_return,
			'format' => 'gif',
			'image_delay' => 30,
			'image_width' => 100,
			'image_height' => 100,
			'pixel_color' => 'white',
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('data' => $data, 'options' => $options ), array('event' => 'args'));
		$data = $filtered['data'];
		$options = $filtered['options'];
		
		$im = new Imagick();
		$im -> setFormat( $options['format'] );
		 
		$color = new ImagickPixel( $options['pixel_color'] );
		$color->setColor( $options['pixel_color'] );
		
    	$draw = new ImagickDraw();
	    $draw->setFont( $options['font'] );
		$draw->setFontSize( $options['font_size'] );
		$draw->setFontStyle( $options['font_size'] );
		$draw->setFillAlpha( $options['fill_alpha'] );
		$draw->setFillColor($options['font_color']);
		$draw->setGravity( $options['gravity'] );
	 
	    foreach($data as $key => $value ) {
		        $im -> newImage( $options['image_width'] , $options['image_height'], $color );
				$im -> annotateImage( $draw, $options['position_x'], $options['position_y'], $options['rotation'], $value);
		        $im -> setImageDelay( $options['image_delay'] );
	    }
		
		$im -> setImageFormat( $options['format'] );
		
		if($options['write_image']) {
			$output_file = $options['write_image_location'].$options['write_image_name'] ;
				
			if($options['add_extension'])
				$output_file .= '.'.strtolower($im->getImageFormat());
				
			$im->writeImages( $output_file, true );
				
			if($options['return'] == 'image_location')
				$return = $output_file;
		}
		
		if($options['display_image']) {
			header( "Content-Type: image/{$im->getImageFormat()}" );
			echo $im;
		}
			
		if($options['return'] == 'image_object') {
			$return = $im;
		} else {
			if($options['return'] == 'image_bytes')
				$return = $im -> getImageBlob();
			$im -> destroy();
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $return, $data, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));
		
		return $return;
	}

	/**
	 * Converts a string of text into an image
	 * 
	 * @param array $string The string to be converted into an image
	 * @param array $options An array of options that can determine how the string will be animated
	 * 			-'converter' _string_: The default converter set by the init function.
	 * 			-'write_image' _boolean_: Write the image out to file. Default is true.
	 * 			-'write_image_location' _string_: The location to save the image. Default is the save location set in the init
	 * 			-'write_image_name' _string_: A name to save the image as. Default is a random string.
	 * 			-'display_image' _boolean_: Determines if the image is to be displayed automatically in a header. Default is false.
	 * 			-'add_extension' _boolean_: Add a file extension to the write_image_name. Default is true.
	 * 			-'return' _string_: Specifiy to return an object or file location. Default is set in the init()
	 * 			-'format' _string_: The format to save the image in. Default is gif.
	 * 			-'image_delay' _integer_: The delay between the images, default is 30
	 * 			-'image_width' _integer_: The width of the images
	 * 			-'image_height' _integer_: The height of the images
	 * 			-'font' _string_: The font to be used when writing text. Defaultis Helvetica
	 * 			-'font_size' _int_: The size of the font to be used. Default is 20
	 * 			-'font_color' _string_: The color of the font. Default is black.
	 * 			-'font_style' _int_: The style of the font. Default is Imagick::Style_Normal
	 * 			-'fill_alpha' _double: The number between 0 and 1 on how transparent the text is. Default is 1.
	 * 			-'position_x' _int_: The starting position of the text on the x-coordinate. Default is 0.
	 * 			-'position_y' _int_: The starting position of the text on the y-coordinate. Default is 0.
	 * 			-'rotation' _int_: The number of degress to rotate the text. Default is 0.
	 * 			-'gravity' _int_: The imagick defined constant on the gravity of the text. Default is Imagick::GRAVITY_CENTER
	 * 			-'pixel_color' _string_: Serves as the background color the text will be placed on. Default is white
	 * 
	 * @return mixed $return The data to be returned. The type of data that is returned is set in the options
	 * @access public
	 * @todo add GD support
	 */
	public static function textToImage($string, $options) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string, $options);
		
		$defaults = array(
			'font' => 'Helvetica',
			'font_size' => 30,
			'font_color' => 'black',
			'font_style' => Imagick::STYLE_NORMAL,
			'fill_alpha' => 0,
			'position_x' => 10,
			'position_y' => 10,
			'rotation' => 0,
			'type' => 'file',
			'gravity' => Imagick::GRAVITY_CENTER,
			'converter' => self::$_converter,
			'write_image' => self::$_write_image,
			'write_image_location' => self::$_write_image_location,
			'write_image_name' => PVTools::generateRandomString(),
			'display_image' => self::$_display_image,
			'add_extension' => self::$_add_extension,
			'return' => self::$_return,
			'format' => 'gif',
			'image_delay' => 30,
			'image_width' => 100,
			'image_height' => 100,
			'pixel_color' => 'white',
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('string' => $string,'options' => $options ), array('event' => 'args'));
		$string = $filtered['string'];
		$options = $filtered['options'];
			
	    $im = new Imagick();
	    $draw = new ImagickDraw();
	
		$draw->setFont( $options['font'] );
		$draw->setFontSize( $options['font_size'] );
		$draw->setFontStyle( $options['font_size'] );
		$draw->setFillAlpha( $options['fill_alpha'] );
		$draw->setFillColor($options['font_color']);
	
	    $pixel = new ImagickPixel( $options['pixel_color'] );
	    $font_info = $im->queryFontMetrics($draw, $string);
	
	    $width = $font_info['textWidth'];
	    $height = $font_info['textHeight'];
	
	    $im->newImage($width, $height, $pixel);
	    $im->annotateImage( $draw, $options['position_x'], $options['position_y'], $options['rotation'],$string);
	    $im->setImageFormat($options['format']);
		
		if($options['write_image']) {
			$output_file = $options['write_image_location'].$options['write_image_name'] ;
				
			if($options['add_extension'])
				$output_file .= '.'.strtolower($im->getImageFormat());
				
			$im->writeImages( $output_file , true );
				
			if($options['return'] == 'image_location')
				$return = $output_file;
		}
			
		if($options['display_image']) {
			header( "Content-Type: image/{$im->getImageFormat()}" );
			echo $im;
		}
			
		if($options['return'] == 'image_object') {
			$return = $im;
		} else {
			if($options['return'] == 'image_bytes')
				$return = $im -> getImageBlob();
			$im -> destroy();
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $return, $string, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));
		
		return $return;
	}

	/**
	 * Creates an rectangular image.
	 * 
	 * @param int $width The width of the image
	 * @param int $height The height of the image
	 * @param string $color The color of the image
	 * @param array $options Options that can be used to configure the drawing of the shape
	 * 			-'converter' _string_: The default converter set by the init function.
	 * 			-'write_image' _boolean_: Write the image out to file. Default is true.
	 * 			-'write_image_location' _string_: The location to save the image. Default is the save location set in the init
	 * 			-'write_image_name' _string_: A name to save the image as. Default is a random string.
	 * 			-'display_image' _boolean_: Determines if the image is to be displayed automatically in a header. Default is false.
	 * 			-'add_extension' _boolean_: Add a file extension to the write_image_name. Default is true.
	 * 			-'return' _string_: Specifiy to return an object or file location. Default is set in the init()
	 * 			-'format' _string_: The format to save the image in. Default is png.
	 * 
	 * @return mixed $return The data to be returned. The type of data that is returned is set in the options
	 * @access public 
	 * @todo add GD support
	 */
	public static function drawImage($width, $height, $color, $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $width, $height, $color, $options);
		
		$defaults = array(
			'converter' => self::$_converter,
			'write_image' => self::$_write_image,
			'write_image_location' => self::$_write_image_location,
			'write_image_name' => PVTools::generateRandomString(),
			'display_image' => self::$_display_image,
			'add_extension' => self::$_add_extension,
			'return' => self::$_return,
			'format' => 'png',
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('color' => $color, 'width' => $width, 'height' => $height, 'options' => $options ), array('event' => 'args'));
		$color = $filtered['color'];
		$width = $filtered['width'];
		$height = $filtered['height'];
		$options = $filtered['options'];
        
        $im = new Imagick();

        $im->newImage( $width , $height , $color, $options['format'] );

        if($options['write_image']) {
			$output_file = $options['write_image_location'].$options['write_image_name'] ;
				
			if($options['add_extension'])
				$output_file .= '.'.strtolower($im->getImageFormat());
				
			$im->writeImage( $output_file );
				
			if($options['return'] == 'image_location')
				$return = $output_file;
		}
			
		if($options['display_image']) {
			header( "Content-Type: image/{$im->getImageFormat()}" );
			echo $im;
		}
			
		if($options['return'] == 'image_object') {
			$return = $im;
		} else {
			if($options['return'] == 'image_bytes')
				$return = $im -> getImageBlob();
			$im -> destroy();
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $return, $width, $height, $color, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));
		
		return $return;
	}
	
	/**
	 * Draws an ellipse shape based on the passed values.
	 * 
	 * @param double $width The width of the ellipse
	 * @param double $height The height of the ellipse
	 * @param string $color The color of the ellipse
	 * @param array $options Options that can be used for further configuring the ellipse
	 * 			-'converter' _string_: The default converter set by the init function.
	 * 			-'write_image' _boolean_: Write the image out to file. Default is true.
	 * 			-'write_image_location' _string_: The location to save the image. Default is the save location set in the init
	 * 			-'write_image_name' _string_: A name to save the image as. Default is a random string.
	 * 			-'display_image' _boolean_: Determines if the image is to be displayed automatically in a header. Default is false.
	 * 			-'add_extension' _boolean_: Add a file extension to the write_image_name. Default is true.
	 * 			-'return' _string_: Specifiy to return an object or file location. Default is set in the init()
	 * 			-'format' _string_: The format to save the image in. Default is png.
	 * 			-'offest_x' _int_: The offset on the x_coordinate when drawing the ellipse. Default is the width/2
	 * 			-'offeset_y' _int_: The offset on the y_coordinate when drawign the ellipse. Default is height/2
	 * 			-'radius_x' _int_: Default is width/2
	 * 			-'radius_y' _int_: Default height /2
	 * 
	 * @return mixed $return The data to be returned. The type of data that is returned is set in the options
	 * @access public 
	 * @todo add GD support			
	 */
	public static function drawEllipse($width, $height, $color, $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $width, $height, $color, $options);
		
		$defaults = array(
			'converter' => self::$_converter,
			'write_image' => self::$_write_image,
			'write_image_location' => self::$_write_image_location,
			'write_image_name' => PVTools::generateRandomString(),
			'display_image' => self::$_display_image,
			'add_extension' => self::$_add_extension,
			'return' => self::$_return,
			'format' => 'png',
			'offset_x' => $width/2,
			'offset_y' => $height/2,
			'radius_x' => ($width/2)-2,
			'radius_y' => ($height/2)-2,
			'start_angle' => 0,
			'end_angle' => 360
		);
		
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('color' => $color, 'width' => $width, 'height' => $height, 'options' => $options ), array('event' => 'args'));
		$color = $filtered['color'];
		$width = $filtered['width'];
		$height = $filtered['height'];
		$options = $filtered['options'];
		
		$im = new Imagick();
        $im->newImage( $width, $height, new ImagickPixel( 'transparent' ) );
        
        $draw = new ImagickDraw();   
        $draw->setFillColor( new ImagickPixel( $color ) );
        $draw->ellipse( $options['offset_x'], $options['offset_x'], $options['radius_x'], $options['radius_y'], $options['start_angle'], $options['end_angle'] );

        $im->drawImage( $draw );
        $im->setImageFormat( $options['format'] );

        if($options['write_image']) {
			$output_file = $options['write_image_location'].$options['write_image_name'] ;
				
			if($options['add_extension'])
				$output_file .= '.'.strtolower($im->getImageFormat());
				
			$im->writeImage( $output_file );
				
			if($options['return'] == 'image_location')
				$return = $output_file;
		}
			
		if($options['display_image']) {
			header( "Content-Type: image/{$im->getImageFormat()}" );
			echo $im;
		}
			
		if($options['return'] == 'image_object') {
			$return = $im;
		} else {
			if($options['return'] == 'image_bytes')
				$return = $im -> getImageBlob();
			$im -> destroy();
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $return, $width, $height, $color, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));
		
		return $return;
	}
	
	/**
	 * Creates a drop shadown behind an image.
	 * 
	 * @param mixed $image The value passed in can either be the location on a file system or the images in bytes. If
	 * 			the image is bytes, the options 'type' = blob my be set.
	 * @param array $options Options that can be used for further configuring the dropshadow
	 * 			-'converter' _string_: The default converter set by the init function.
	 * 			-'write_image' _boolean_: Write the image out to file. Default is true.
	 * 			-'write_image_location' _string_: The location to save the image. Default is the save location set in the init
	 * 			-'write_image_name' _string_: A name to save the image as. Default is a random string.
	 * 			-'display_image' _boolean_: Determines if the image is to be displayed automatically in a header. Default is false.
	 * 			-'add_extension' _boolean_: Add a file extension to the write_image_name. Default is true.
	 * 			-'return' _string_: Specifiy to return an object or file location. Default is set in the init()
	 * 			-'format' _string_: The format to save the image in. Default is png.
	 * 			-'offest_x' _int_: The offset on the x_coordinate when placing the drop shadow. Default is 0
	 * 			-'offeset_y' _int_: The offset on the y_coordinate when placing the drop shadow. Default is 0.
	 * 			-'radius_x' _int_: Default is width/2
	 * 			-'radius_y' _int_: Default height /2
	 * 
	 * @return mixed $return The data to be returned. The type of data that is returned is set in the options
	 * @access public
	 * @todo add GD support
	 */
	public static function drawDropShadow( $image, $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $image, $options);
		
		$defaults = array(
			'offset_y' => 0,
			'offset_x' => 0,
			'composite' => Imagick::COMPOSITE_OVER,
			'opacity' => 50,
			'position_x' => 0,
			'position_y' => 0,
			'float' => 2,
			'type' => 'file',
			'gravity' => Imagick::GRAVITY_CENTER,
			'converter' => self::$_converter,
			'write_image' => self::$_write_image,
			'write_image_location' => self::$_write_image_location,
			'write_image_name' => PVTools::generateRandomString(),
			'display_image' => self::$_display_image,
			'add_extension' => self::$_add_extension,
			'return' => self::$_return,
			'format' => 'png',
			'pixel_color' => 'black',
		);
	    
		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('image' => $image,'options' => $options ), array('event' => 'args'));
		$image = $filtered['image'];
		$options = $filtered['options'];
		
	    if($options['type'] == 'blob') {
			$im = new Imagick();
			$im -> readImageBlob($image);
		} else {
			$im = new Imagick($image);
		}
	
	    $im->setImageFormat($options['format']);
	    $shadow = $im->clone();
	    $shadow->setImageBackgroundColor( new ImagickPixel( $options['pixel_color'] ) );
	    $shadow->shadowImage( $options['opacity'], $options['float'], $options['position_x'], $options['position_y'] );
	    $shadow->compositeImage( $im, $options['composite'], $options['offset_x'], $options['offset_y'] );
		$im -> destroy();
		
	     if($options['write_image']) {
			$output_file = $options['write_image_location'].$options['write_image_name'] ;
				
			if($options['add_extension'])
				$output_file .= '.'.strtolower($shadow->getImageFormat());
				
			$shadow -> writeImage( $output_file );
				
			if($options['return'] == 'image_location')
				$return = $output_file;
		}
			
		if($options['display_image']) {
			header( "Content-Type: image/{$im->getImageFormat()}" );
			echo $shadow;
		}
			
		if($options['return'] == 'image_object') {
			$return = $im;
		} else {
			if($options['return'] == 'image_bytes')
				$return = $im -> getImageBlob();
			$im -> destroy();
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $return, $image, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));
		
		return $return;
	}

}//end class
