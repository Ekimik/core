<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v3.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\View\Input;

use Cake\View\Input\InputInterface;
use Cake\View\StringTemplate;
use Cake\Utility\Time;

/**
 * Input widget class for generating a date time input widget.
 *
 * This class is intended as an internal implementation detail
 * of Cake\View\Helper\FormHelper and is not intended for direct use.
 */
class DateTime implements InputInterface {

/**
 * Select box widget.
 *
 * @var Cake\View\Input\Select;
 */
	protected $_select;

/**
 * List of inputs that can be rendered
 *
 * @var array
 */
	protected $_selects = [
		'year',
		'month',
		'day',
		'hour',
		'minute',
		'second',
		'meridian',
	];

/**
 * Template instance.
 *
 * @var \Cake\View\StringTemplate
 */
	protected $_templates;

/**
 * Constructor
 *
 * @param Cake\View\StringTemplate $templates
 * @param Cake\View\Input\SelectBox $selectBox
 */
	public function __construct($templates, $selectBox) {
		$this->_select = $selectBox;
		$this->_templates = $templates;
	}

/**
 * Renders a date time widget
 *
 * - `name` - Set the input name.
 * - `disabled` - Either true or an array of options to disable.
 * - `val` - A date time string, integer or DateTime object
 * - `empty` - Set to true to add an empty option at the top of the
 *   option elements. Set to a string to define the display value of the
 *   empty option.
 *
 * In addtion to the above options, the following options allow you to control
 * which input elements are generated. By setting any option to false you can disable
 * that input picker. In addition each picker allows you to set additional options
 * that are set as HTML properties on the picker.
 *
 * - `year` - Array of options for the year select box.
 * - `month` - Array of options for the month select box.
 * - `day` - Array of options for the day select box.
 * - `hour` - Array of options for the hour select box.
 * - `minute` - Array of options for the minute select box.
 * - `second` - Set to true to enable the seconds input. Defaults to false.
 * - `meridian` - Set to true to enable the meridian input. Defaults to false.
 *   The meridian will be enabled automatically if you chose a 12 hour format.
 *
 * The `year` option accepts the `start` and `end` options. These let you control
 * the year range that is generated. It defaults to +-5 years from today.
 *
 * The `month` option accepts the `name` option which allows you to get month
 * names instead of month numbers.
 *
 * The `hour` option allows you to set the `format` option which accepts
 * 12 or 24, allowing you to indicate which hour format you want.
 *
 * The `minute` option allows you to define the following options:
 *
 * - `interval` The interval to round options to.
 * - `round` Accepts `up` or `down`. Defines which direction the current value
 *   should be rounded to match the select options.
 *
 * @param array $data Data to render with.
 * @return string A generated select box.
 * @throws \RuntimeException When option data is invalid.
 */
	public function render(array $data) {
		$data += [
			'name' => '',
			'empty' => false,
			'disabled' => null,
			'val' => null,
			'year' => [],
			'month' => [],
			'day' => [],
			'hour' => [],
			'minute' => [],
			'second' => [],
			'meridian' => null,
		];

		$selected = $this->_deconstuctDate($data['val'], $data);

		if (!isset($data['meridian']) &&
			isset($data['hour']['format']) &&
			$data['hour']['format'] == 12
		) {
			$data['meridian'] = [];
		}

		$templateOptions = [];
		foreach ($this->_selects as $select) {
			if ($data[$select] === false || $data[$select] === null) {
				$templateOptions[$select] = '';
				unset($data[$select]);
				continue;
			}
			if (!is_array($data[$select])) {
				throw \RuntimeException(sprintf(
					'Options for "%s" must be an array|false|null',
					$select
				));
			}
			$method = $select . 'Select';
			$data[$select]['name'] = $data['name'] . "[" . $select . "]";
			$data[$select]['val'] = $selected[$select];

			if (!isset($data[$select]['empty'])) {
				$data[$select]['empty'] = $data['empty'];
			}
			if (!isset($data[$select]['disabled'])) {
				$data[$select]['disabled'] = $data['disabled'];
			}
			$templateOptions[$select] = $this->{$method}($data[$select]);
			unset($data[$select]);
		}
		unset($data['name'], $data['empty'], $data['disabled'], $data['val']);
		$templateOptions['attrs'] = $this->_templates->formatAttributes($data);
		return $this->_templates->format('dateWidget', $templateOptions);
	}

/**
 * Deconstructs the passed date value into all time units
 *
 * @param string|integer|array|DateTime $value
 * @param array $options Options for conversion.
 * @return array
 */
	protected function _deconstuctDate($value, $options) {
		if (empty($value)) {
			return [
				'year' => '', 'month' => '', 'day' => '',
				'hour' => '', 'minute' => '', 'second' => ''
			];
		}
		if (is_string($value)) {
			$date = new \DateTime($value);
		} elseif (is_int($value)) {
			$date = new \DateTime('@' . $value);
		} elseif (is_array($value)) {
			$date = new \DateTime();
			if (isset($value['year'], $value['month'], $value['day'])) {
				$date->setDate($value['year'], $value['month'], $value['day']);
			}
			if (isset($value['hour'], $value['minute'], $value['second'])) {
				$date->setTime($value['hour'], $value['minute'], $value['second']);
			}
		} else {
			$date = clone $value;
		}

		if (isset($options['minute']['interval'])) {
			$change = $this->_adjustValue($date->format('i'), $options['minute']);
			$date->modify($change > 0 ? "+$change minutes" : "$change minutes");
		}

		return [
			'year' => $date->format('Y'),
			'month' => $date->format('m'),
			'day' => $date->format('d'),
			'hour' => $date->format('H'),
			'minute' => $date->format('i'),
			'second' => $date->format('s'),
			'meridian' => $date->format('a'),
		];
	}

/**
 * Adjust $value based on rounding settings.
 *
 * @param int $value The value to adjust.
 * @param array The options containing interval and possibly round.
 * @return integer The amount to adjust $value by.
 */
	protected function _adjustValue($value, $options) {
		$options += ['interval' => 1, 'round' => null];
		$changeValue = $value * (1 / $options['interval']);
		switch ($options['round']) {
			case 'up':
				$changeValue = ceil($changeValue);
				break;
			case 'down':
				$changeValue = floor($changeValue);
				break;
			default:
				$changeValue = round($changeValue);
		}
		return ($changeValue * $options['interval']) - $value;
	}

/**
 * Generates a year select
 *
 * @param array $options
 * @return string
 */
	public function yearSelect($options = []) {
		$options += [
			'name' => '',
			'val' => null,
			'start' => date('Y', strtotime('-5 years')),
			'end' => date('Y', strtotime('+5 years')),
			'order' => 'desc',
			'options' => []
		];

		$options['start'] = min($options['val'], $options['start']);
		$options['end'] = max($options['val'], $options['end']);

		if (empty($options['options'])) {
			$options['options'] = $this->_generateNumbers($options['start'], $options['end']);
		}
		if ($options['order'] === 'asc') {
			$options['options'] = array_reverse($options['options'], true);
		}
		unset($options['start'], $options['end'], $options['order']);
		return $this->_select->render($options);
	}

/**
 * Generates a month select
 *
 * @param array $options
 * @return string
 */
	public function monthSelect($options = []) {
		$options += [
			'name' => '',
			'names' => false,
			'val' => null,
			'leadingZeroKey' => true,
			'leadingZeroValue' => false
		];

		if (empty($options['options'])) {
			if ($options['names'] === true) {
				$options['options'] = $this->_getMonthNames($options['leadingZeroKey']);
			} else {
				$options['options'] = $this->_generateNumbers(1, 12, $options);
			}
		}

		unset($options['leadingZeroKey'], $options['leadingZeroValue'], $options['names']);
		return $this->_select->render($options);
	}

/**
 * Generates a day select
 *
 * @param array $options
 * @return string
 */
	public function daySelect($options = []) {
		$options += [
			'name' => '',
			'val' => null,
			'leadingZeroKey' => true,
			'leadingZeroValue' => false,
		];
		$options['options'] = $this->_generateNumbers(1, 31, $options);

		unset($options['names'], $options['leadingZeroKey'], $options['leadingZeroValue']);
		return $this->_select->render($options);
	}

/**
 * Generates a hour select
 *
 * @param array $options
 * @return string
 */
	public function hourSelect($options = []) {
		$options += [
			'name' => '',
			'val' => null,
			'format' => 24,
			'leadingZeroKey' => true,
			'leadingZeroValue' => false,
		];
		$is24 = $options['format'] == 24;

		if (!$is24 && $options['val'] > 12) {
			$options['val'] = sprintf('%02d', $options['val'] - 12);
		}

		if (empty($options['options'])) {
			$end = $is24 ? 24 : 12;
			$options['options'] = $this->_generateNumbers(1, $end, $options);
		}

		unset($options['format'], $options['leadingZeroKey'], $options['leadingZeroValue']);
		return $this->_select->render($options);
	}

/**
 * Generates a minute select
 *
 * @param array $options
 * @return string
 */
	public function minuteSelect($options = []) {
		$options += [
			'name' => '',
			'val' => null,
			'interval' => 1,
			'round' => 'up',
			'leadingZeroKey' => true,
			'leadingZeroValue' => true,
		];
		if (empty($options['options'])) {
			$options['options'] = $this->_generateNumbers(0, 59, $options);
		}

		unset(
			$options['leadingZeroKey'],
			$options['leadingZeroValue'],
			$options['interval'],
			$options['round']
		);
		return $this->_select->render($options);
	}

/**
 * Generates a second select
 *
 * @param array $options
 * @return string
 */
	public function secondSelect($options = []) {
		$options += [
			'name' => '',
			'val' => null,
			'leadingZeroKey' => true,
			'leadingZeroValue' => true,
			'options' => $this->_generateNumbers(1, 60)
		];

		unset($options['leadingZeroKey'], $options['leadingZeroValue']);
		return $this->_select->render($options);
	}

/**
 * Generates a meridian select
 *
 * @param array $options
 * @return string
 */
	public function meridianSelect($options = []) {
		$options += [
			'name' => '',
			'val' => null,
			'options' => ['am' => 'am', 'pm' => 'pm']
		];
		return $this->_select->render($options);
	}

/**
 * Returns a translated list of month names
 *
 * @param boolean $leadingZero
 * @return array
 */
	protected function _getMonthNames($leadingZero = false) {
		$months = [
			'01' => __d('cake', 'January'),
			'02' => __d('cake', 'February'),
			'03' => __d('cake', 'March'),
			'04' => __d('cake', 'April'),
			'05' => __d('cake', 'May'),
			'06' => __d('cake', 'June'),
			'07' => __d('cake', 'July'),
			'08' => __d('cake', 'August'),
			'09' => __d('cake', 'September'),
			'10' => __d('cake', 'October'),
			'11' => __d('cake', 'November'),
			'12' => __d('cake', 'December'),
		];

		if ($leadingZero === false) {
			$i = 1;
			foreach ($months as $key => $name) {
				$months[$i++] = $name;
				unset($months[$key]);
			}
		}

		return $months;
	}

/**
 * Generates a range of numbers
 *
 * ### Options
 *
 * - leadingZeroKey - Set to true to add a leading 0 to single digit keys.
 * - leadingZeroValue - Set to true to add a leading 0 to single digit values.
 * - interval - The interval to generate numbers for. Defaults to 1.
 *
 * @param integer $start Start of the range of numbers to generate
 * @param integer $end End of the range of numbers to generate
 * @param array $options
 * @return array
 */
	protected function _generateNumbers($start, $end, $options = []) {
		$options += [
			'leadingZeroKey' => true,
			'leadingZeroValue' => true,
			'interval' => 1
		];

		$numbers = [];
		$i = $start;
		while ($i <= $end) {
			$key = (string)$i;
			$value = (string)$i;
			if ($options['leadingZeroKey'] === true) {
				$key = sprintf('%02d', $key);
			}
			if ($options['leadingZeroValue'] === true) {
				$value = sprintf('%02d', $value);
			}
			$numbers[$key] = $value;
			$i += $options['interval'];
		}
		return $numbers;
	}

}
