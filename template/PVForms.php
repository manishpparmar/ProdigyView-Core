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
class PVForms extends PVStaticObject {

	/**
	 * Creates an input that would correspond to any field that is an <input>.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param sring $type The type of input being generated
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function input($name, $type, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $type, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'type' => $type, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$type = $filtered['type'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$input = '<input name="' . $name . '" type="' . $type . '" ';

		$input .= PVHtml::getStandardAttributes($options);
		$input .= PVHtml::getEventAttributes($options);
		$input .= self::getFormAttributes($options);

		$input .= '/>';

		if (!isset($css_options['disable_css'])) {
			return PVHtml::div($input, $css_options);
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $type, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a button input element with options passed to.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function button($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-button');
		$css_options += $css_defaults;

		$input = self::input($name, 'button', $options, $css_options);
		;
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a checkbox input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function checkbox($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-checkbox');
		$css_options += $css_defaults;

		$input = self::input($name, 'checkbox', $options, $css_options);
		;
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a text input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function text($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-text');
		$css_options += $css_defaults;

		$input = self::input($name, 'text', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a file input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function file($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-file');
		$css_options += $css_defaults;

		$input = self::input($name, 'file', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a date input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function date($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-date');
		$css_options += $css_defaults;

		$input = self::input($name, 'date', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a hidden input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function hidden($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-hidden');
		$css_options += $css_defaults;

		$input = self::input($name, 'hidden', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a image input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function image($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-image');
		$css_options += $css_defaults;

		$input = self::input($name, 'image', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a search input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function search($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-search');
		$css_options += $css_defaults;

		$input = self::input($name, 'search', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a submit input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function submit($name, $value = 'Submit', $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-submit');
		$css_options += $css_defaults;
		$options['value'] = $value;

		$input = self::input($name, 'submit', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a textfield input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function textfield($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-textfield');
		$css_options += $css_defaults;

		$input = self::input($name, 'text', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a radio button input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function radiobutton($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-radio');
		$css_options += $css_defaults;

		$input = self::input($name, 'radio', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a time input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function time($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-time');
		$css_options += $css_defaults;

		$input = self::input($name, 'time', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a url input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function url($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-url');
		$css_options += $css_defaults;

		$input = self::input($name, 'url', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a range input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function range($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-range');
		$css_options += $css_defaults;

		$input = self::input($name, 'range', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a reset input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function reset($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-reset');
		$css_options += $css_defaults;

		$input = self::input($name, 'reset', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a color input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function color($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-color');
		$css_options += $css_defaults;

		$input = self::input($name, 'color', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a password input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function password($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-password');
		$css_options += $css_defaults;

		$input = self::input($name, 'password', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a number input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function number($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-number');
		$css_options += $css_defaults;

		$input = self::input($name, 'number', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a number input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function email($name, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-email');
		$css_options += $css_defaults;

		$input = self::input($name, 'email', $options, $css_options);
		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options, $css_options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	/**
	 * Creates a label input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $text The text to appear in the label
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function label($text, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('text' => $text, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$text = $filtered['text'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-label');
		$css_options += $css_defaults;

		$label = '<label ';

		if (!empty($options['for'])) {
			$label .= 'for="' . $options['for'] . '" ';
		}

		$label .= PVHtml::getStandardAttributes($options);
		$label .= PVHtml::getEventAttributes($options);
		$label .= self::getFormAttributes($options);

		$label .= '>' . $text . '</label>';

		if (!isset($css_options['disable_css'])) {
			return PVHtml::div($label, $css_options);
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $label, $name, $options, $css_options);
		$label = self::_applyFilter(get_class(), __FUNCTION__, $label, array('event' => 'return'));

		return $label;
	}

	/**
	 * Creates a checkbox input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the select fields name.
	 * @param array $data The data that will create the options. The key in the array will be the options value and the value
	 * 				in the array will be the options display.
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function select($name, $data, $options = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $data, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'data' => $data, 'options' => $options, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$data = $filtered['data'];
		$options = $filtered['options'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('name' => 'form-select');
		$css_options += $css_defaults;

		$tag = '<select name="' . $name . '" ';
		$tag .= PVHtml::getStandardAttributes($options);
		$tag .= PVHtml::getEventAttributes($options);
		$tag .= self::getFormAttributes($options);
		$tag .= '>';

		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$tag .= '<option ';
				$tag .= PVHtml::getStandardAttributes($value);
				$tag .= PVHtml::getEventAttributes($value);
				$tag .= self::getFormAttributes($value);
				$tag .= '>' . $value['option'] . '</option>';
			} else {
				if (isset($options['value'])) {
					if (is_array($options['value'])) {
						if (in_array($key, $options['value'])) {
							$tag .= '<option value="' . $key . '" selected >' . $value . '</option>';
						} else {
							$tag .= '<option value="' . $key . '" >' . $value . '</option>';
						}
					} else {
						if ($key == $options['value']) {

							$tag .= '<option value="' . $key . '" selected >' . $value . '</option>';
						} else {
							$tag .= '<option value="' . $key . '" >' . $value . '</option>';
						}
					}//end iset value
				} else {
					$tag .= '<option value="' . $key . '" >' . $value . '</option>';
				}
			}
		}

		$tag .= '</select>';

		if (!isset($css_options['disable_css'])) {
			return PVHtml::div($tag, $css_options);
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $name, $data, $options, $css_options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Form attributes that are present in many form elements. This functionisused for assigning those attribute by passing
	 * them in as an array and returning them as a string. Contains both html and html5 elements
	 *
	 * @param array $attributes Attribues that will be assigned if they match
	 * 			-'accept' _string_: The class attribute
	 * 			-'autocomplete' _string_: The class attribute
	 * 			-'autofocus' _string_: The class attribute
	 * 			-'chcked' _string_: The class attribute
	 *  		-'disabled' _string_: The class attribute
	 *  		-'form' _string_: The class attribute
	 *  		-'formaction' _string_: The class attribute
	 *  		-'formenctype' _string_: The class attribute
	 *  		-'formmethod' _string_: The class attribute
	 *  		-'formnovalidation' _string_: The class attribute
	 *  		-'formtarget' _string_: The class attribute
	 *  		-'height' _string_: The class attribute
	 *  		-'list' _string_: The class attribute
	 *  		-'max' _string_: The class attribute
	 *  		-'maxlength' _string_: The class attribute
	 * 			-'min' _string_: The class attribute
	 *
	 * @return string $attributes Returns the matched attributes as a string
	 * @access public
	 * @todo complete documentation
	 */
	public static function getFormAttributes($attributes = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $attributes);

		$attributes = self::_applyFilter(get_class(), __FUNCTION__, $attributes, array('event' => 'args'));

		$return_attributes = '';
		$accepted_attributes = array('accept', 'autocomplete', 'autofocus', 'checked', 'disabled', 'form', 'formaction', 'formenctype', 'formmethod', 'formnovalidate', 'formtarget', 'height', 'list', 'max', 'maxlength', 'min', 'multiple', 'pattern', 'placeholder', 'readonly', 'required', 'size', 'step', 'type', 'value', 'width', 'novalidate', 'dirname');

		foreach ($attributes as $key => $attribute) {
			if (in_array($key, $accepted_attributes) && !PVValidator::isInteger($key)) {
				$return_attributes .= $key . '="' . $attribute . '" ';
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $return_attributes, $attributes);
		$return_attributes = self::_applyFilter(get_class(), __FUNCTION__, $return_attributes, array('event' => 'return'));

		return $return_attributes;
	}

	/**
	 * Creates a checkbox input element with options passed too it.
	 *
	 * @see PVHTML::getStandardAttributes()
	 * @see PVHTML::getEventAttributes()
	 * @see PVHTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $name The name of the input being generated. Will be the input field's name
	 * @param string $value The value in the textarea
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through PVHTML::getStandardAttributes, PVHTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 * @param array $css_options Options than can define how the CSS is styled around the form the div around the element.
	 * 				Options will be passed to PVHTML::getStandardAttributes() and PVHTML::getEventAttributes(). Have the option
	 * 				'disable_css' will disable the div surrouding the element.
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function textarea($name, $value, $attributes = array(), $css_options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $value, $attributes, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'value' => $value, 'attributes' => $attributes, 'css_options' => $css_options), array('event' => 'args'));
		$name = $filtered['name'];
		$value = $filtered['value'];
		$attributes = $filtered['attributes'];
		$css_options = $filtered['css_options'];

		$css_defaults = array('class' => 'form-textarea');
		$css_options += $css_defaults;

		$textarea = '<textarea ';

		$textarea .= 'name="' . $name . '" ';

		$textarea .= PVHtml::getStandardAttributes($attributes);
		$textarea .= PVHtml::getEventAttributes($attributes);
		$textarea .= self::getFormAttributes($attributes);

		$textarea .= '>' . $value . '</textarea>';

		if (!isset($css_options['disable_css'])) {
			return PVHtml::div($textarea, $css_options);
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $textarea, $name, $value, $attributes, $css_options);
		$textarea = self::_applyFilter(get_class(), __FUNCTION__, $textarea, array('event' => 'return'));

		return $textarea;
	}//end getTextArea

	/**
	 * Gets the tags for display s form with the data inside of it.
	 */
	public static function form($name, $data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options, 'data' => $data), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];
		$data = $filtered['data'];

		$tag = self::formBegin($name, $options);
		$tag .= $data;
		$tag .= self::formClose();

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $name, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	public static function formBegin($name, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'options' => $options), array('event' => 'args'));
		$name = $filtered['name'];
		$options = $filtered['options'];

		$defaults = array('method' => 'POST');
		$options += $defaults;

		$input = '<form ';

		if (!empty($options['method'])) {
			$input .= 'method="' . $options['method'] . '" ';
		}

		if (!empty($options['name'])) {
			$input .= 'name="' . $options['name'] . '" ';
		}

		if (!empty($options['accept-charset'])) {
			$input .= 'accept-charset="' . $options['accept-charset'] . '" ';
		}

		if (!empty($options['action'])) {
			$input .= 'action="' . $options['action'] . '" ';
		}

		if (!empty($options['enctype'])) {
			$input .= 'enctype="' . $options['enctype'] . '" ';
		}

		$input .= PVHtml::getStandardAttributes($options);
		$input .= PVHtml::getEventAttributes($options);
		$input .= self::getFormAttributes($options);

		$input .= '>';

		self::_notify(get_class() . '::' . __FUNCTION__, $input, $name, $options);
		$input = self::_applyFilter(get_class(), __FUNCTION__, $input, array('event' => 'return'));

		return $input;
	}

	public static function formEnd($options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));

		$input = '</form>';

		return $input;
	}

}//end class
