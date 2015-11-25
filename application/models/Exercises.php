<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exercises extends CI_model {

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct() {

		$this->load->helper('url');
		define('RESOURCES_URL', base_url('resources/exercises'));
	}

	/**
	 * Check answer
	 *
	 * @param  array $data Answer data
	 * @return void
	 */
	public function CheckAnswer($data) {

		$answerdata = json_decode($data, TRUE);
		$answer = '';

		foreach ($answerdata as $item) {
			switch ($item['name']) {
				case 'type':
					$type = $item['value'];
					break;
				case 'answer':
					$answer[] = $item['value'];
					break;
				case 'correct':
					$correct = json_decode($item['value'], TRUE);
					break;
				case 'solution':
					$solution = $item['value'];
					break;
			}
		}

		$submessages = [];

		switch ($type) {
			case 'int':
				if ($answer == '') {
					$status = 'NOT_DONE';
					$message = 'Hiányzik a válasz!';
				} elseif ($answer[0] == $correct) {
					$status = 'CORRECT';
					$message = 'Helyes válasz!';
				} else {
					$status = 'WRONG';
					$message = 'A helyes válasz: '.$solution;
				}
				break;
			case 'multi':
				if (!isset($answer)) {
					$status = 'NOT_DONE';
					$message = 'Jelölj be legalább egy választ!';
				} else {
					$status = 'CORRECT';
					$message = 'Helyes válasz!';
					foreach ($correct as $key => $value) {
						$submessages[$key] = 'WRONG';
						if ($value == 1) {
							if (in_array($key, $answer)) {
								$submessages[$key] = 'CORRECT';
							} else {
								$status = 'WRONG';
								$message = 'Hibás válasz!';
							}
						} else {
							if (in_array($key, $answer)) {
								$status = 'WRONG';
								$message = 'Hibás válasz!';
							} else {
								$submessages[$key] = 'CORRECT';
							}
						}
					}
				}
				break;
		}

		$output = array(
			'status' 		=> $status,
			'message' 		=> $message,
			'submessages'	=> $submessages
		);

		return $output;
	}

	/**
	 * Random number generator
	 *
	 * Generates number of $len digits in $numSys numeral system (e.g. value is 10 for
	 * decimal system).
	 *
	 * @param int $len    No. of digits.
	 * @param int $numSys Numeral system.
	 *
	 * @return int $num Random number.
	 */
	private function numGen($len, $numSys)
	{
		if ($len > 1) {
			// first digit non-0
			$num = rand(1, $numSys-1);
		} else {
			$num = rand(0, $numSys-1);
		}
		for ($i=0; $i<$len-1; $i++) {

			$digit = rand(0, $numSys-1);

			// for small numbers, last two digit differs
			while ($len < 4 && $i == 0 && $digit == $num) {
				$digit = rand(0, $numSys-1);
			}

			$num .= $digit;
		}
		return $num;
	}

	/* Count apples */
	public function count_apples($level=1) {

		if ($level == 1) {
			$num = rand(0,4);
		} elseif ($level == 2) {
			$num = rand(5,9);
		} elseif ($level == 3) {
			$num = rand(10,20);
		}

		$question = 'Hány darab alma van a fán?<div class="text-center"><img class="img-question" width="50%" src="'.RESOURCES_URL.'/count_apples/tree'.$num.'.png"></div>';
		$correct = $num;
		$options = '';
		$solution = '$'.$correct.'$';
		$type = 'int';

		return array(
			'question' => $question,
			'options' => $options,
			'correct' => $correct,
			'solution' => $solution,
			'type' => $type
		);
	}

	/* Define parity of numbers */
	public function parity($level=1) {

		if ($level == 1) {
			$num = rand(0,9); 
		} elseif ($level == 2) {
			$len = 3;
		} elseif ($level == 3) {
			$len = 5;
		}

		if ($level == 1) {

			$question = 'Páros vagy páratlan az alábbi szám?$$'.$num.'$$';
			$type = 'int';
			$options = array('páros', 'páratlan');
			$correct = $num%2;
			$solution = $options[$correct];

		} else {

			for ($i=0; $i < $len; $i++) { 
				$num[$i] = $this->numGen(rand(round($len/2),$len), 10);
			}

			$correct = [];

			while (array_sum($correct) == 0) {
				$parity = array('párosak', 'páratlanok');
				$par = rand(0,1);

				foreach ($num as $key => $value) {
					$correct[$key] = ($value%2 == $par ? 1 : 0);
					if ($value > 9999) {
						$value = number_format($value,0,',','\,');
					}
					$options[$key] = '$'.$value.'$';
				}
			}

			$question = 'Mely számok '.$parity[$par].' az alábbi számok közül?';
			$type = 'multi';
			$solution = '';

		}

		return array(
			'question' => $question,
			'options' => $options,
			'correct' => $correct,
			'solution' => $solution,
			'type' => $type
		);
	}

	/* Count even/odd numbers */
	public function count_parity($level=1) {

		if ($level == 1) {
			$no = rand(2,3); 
			$len = 1;
		} elseif ($level == 2) {
			$no = rand(5,10);
			$len = 3;
		} elseif ($level == 3) {
			$no = rand(10,20);
			$len = 5;
		}

		for ($i=0; $i < $no; $i++) { 
			$num[$i] = $this->numGen(rand(ceil($len/2),$len), 10);
		}

		$parity = array('páros', 'páratlan');
		$par = rand(0,1);

		$question = 'Hány szám '.$parity[$par].' az alábbiak közül?<br />';
		$correct = 0;

		foreach ($num as $key => $value) {
			$correct = ($value%2 == $par ? ++$correct : $correct);
			if ($value > 9999) {
				$value = number_format($value,0,',','\,');
			}
			$question .= '$'.$value.'$, ';
		}

		$question = rtrim($question, ', ');
		$type = 'int';
		$solution = '$'.$correct.'$';
		$options = '';

		return array(
			'question' => $question,
			'options' => $options,
			'correct' => $correct,
			'solution' => $solution,
			'type' => $type
		);
	}

}

?>