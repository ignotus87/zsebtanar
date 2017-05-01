<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Session extends CI_model {

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct() {

		parent::__construct();

		$this->load->helper('url');
		$this->load->helper('file');
		$this->load->model('Database');

		$this->DeleteOldSessions();

		return;
	}

	/**
	* Delete old sessions
	*
	* Deletes sessions older than given days from cache directory
	*
	* @return void
	**/
	public function DeleteOldSessions() {

		$path = $this->config->item('sess_save_path');
		$day = 1;
		$files = scandir($path);

		foreach ($files as $file) {
			if ($file != '.' && $file != '..') {
				$lastmodified = filemtime($path . $file);
				if((time() - $lastmodified) > $day * 24 * 3600) {
					unlink($path . $file);
				}
			}
		}
	}

	/**
	 * Unset user data
	 *
	 * Unsets used defined session variables.
	 *
	 * @return void
	 */
	public function UnsetUserData() {

		$user_data = $this->session->all_userdata();

		foreach ($user_data as $key => $value) {
			if ($key != 'session_id' &&
				$key != 'ip_address' &&
				$key != 'user_agent' &&
				$key != 'last_activity' &&
				$key != 'Logged_in') {

				$this->session->unset_userdata($key);
			}
		}

		return;
	}

	/**
	 * Update results
	 *
	 * Points will be calculated based on how many hints used.
	 * Level will only increase if no hints were used.
	 *
	 * @param int    $id         Exercise ID
	 * @param int 	 $hints_used Number of hints used
	 * @param int 	 $hints_all  Number of all hints
	 * @param string $message    Message for user
	 *
	 * @return string $message Message for user (updated)
	 */
	public function UpdateResults($id, $hints_used, $hints_all, $message) {

		$level_user = $this->getUserLevel($id);
		$level_max = $this->Database->getMaxLevel($id);

		// Update levels
		if ($hints_used == 0 && $level_user < $level_max) {
			$levels = $this->session->userdata('levels');
			$levels[$id] = $level_user + 1;
			$this->session->set_userdata('levels', $levels);
		}

		// Add points
		if ($hints_all > 0) {
			$prize = round(100*($hints_all-$hints_used)/$hints_all);
		} else {
			$prize = 100;
		}
		if ($prize > 0) {
			$this->Points($prize);
			$message .= '<br />+'.$prize.'&nbsp;<img src="'.
				base_url().'assets/images/coin.png" alt="coin" width="30">';
		}

		// Calculate progress
		$progress_old = $level_user/$level_max;
		if ($hints_used == 0) {
			$progress_new = ($level_user+1)/$level_max;

			if (($progress_old < 1/3 && $progress_new >= 1/3) ||
				($progress_old < 2/3 && $progress_new >= 2/3) ||
				($progress_old < 3/3 && $progress_new >= 3/3)) {

				// Level completed
				$prize = 200;
				$this->Points($prize);
				$this->Shields(1);
				$message .= '<br /><br />Szintet léptél!<br />'.
					'+1&nbsp;<img src="'.base_url().
					'assets/images/shield.png" alt="coin" width="30">&nbsp;&nbsp;'.
					'+'.$prize.
					'&nbsp;<img src="'.base_url().
					'assets/images/coin.png" alt="coin" width="30">';

				// Execise completed
				if ($progress_new == 1) {
					$prize = 1000;
					$this->Points($prize);
					$this->Trophies(1);
					$message .= '<br /><br />Elvégeztél egy feladatot!<br />'.
						'+1&nbsp;<img src="'.base_url().
						'assets/images/trophy.png" alt="coin" width="30">&nbsp;&nbsp;'.
						'+'.$prize.
						'&nbsp;<img src="'.base_url().
						'assets/images/coin.png" alt="coin" width="30">';
				}

				$message .= '</div><div class="small">Továbblépés <span id="time">5</span> másodperc múlva.<br />';
			}
		} else {

			$message .= '</div><div class="alert alert-info text-center"><span class="fa fa-info-circle"></span>&nbsp;<b>Szeretnél szintet ugrani?</b><br />Próbáld meg megoldani a feladatot segítség nélkül!</div><div class="small">Továbblépés <span id="time">10</span> másodperc múlva.<br />';

		}

		return $message;
	}

	/**
	 * Points
	 *
	 * @param int $amount Amount of points to add
	 *
	 * @return int $points Current amount of points
	 */
	public function Points($amount=0) {

		if (NULL !== $this->session->userdata('points')) {
			$points = $this->session->userdata('points');
		} else {
			$points = 0;
		}
		$points += $amount;
		$this->session->set_userdata('points', $points);

		return $points;
	}

	/**
	 * Shields
	 *
	 * @param int $amount Amount of shields to add
	 *
	 * @return int $shields Current amount of shields
	 */
	public function Shields($amount=0) {

		if (NULL !== $this->session->userdata('shields')) {
			$shields = $this->session->userdata('shields');
		} else {
			$shields = 0;
		}
		$shields += $amount;
		$this->session->set_userdata('shields', $shields);

		return $shields;
	}

	/**
	 * Trophies
	 *
	 * @param int $amount Amount of trophies to add
	 *
	 * @return int $shields Current amount of trophies
	 */
	public function Trophies($amount=0) {

		if (NULL !== $this->session->userdata('trophies')) {
			$trophies = $this->session->userdata('trophies');
		} else {
			$trophies = 0;
		}
		$trophies += $amount;
		$this->session->set_userdata('trophies', $trophies);

		return $trophies;
	}

	/**
	 * Get results
	 *
	 * @return array $data Results
	 */
	public function GetResults() {

		$data['points'] = $this->Points();
		$data['shields'] = $this->Shields();
		$data['trophies'] = $this->Trophies();

		return $data;
	}

	/**
	 * Get user progress for exercise
	 *
	 * @param int $id Exercise ID
	 *
	 * @return array $data Exercise progress (value + style) 
	 */
	public function UserProgress($id) {

		$level_max = $this->Database->getMaxLevel($id);
		$level_user = $this->getUserLevel($id);
		
		$progress = $level_user/$level_max;
		
		if ($progress < 1/3) {
			$style = 'info';
		} elseif ($progress < 2/3) {
			$style = 'warning';
		} else {
			$style = 'danger';
		}

		$stars[0] = (1/3 <= $progress ? 1 : 0);
		$stars[1] = (2/3 <= $progress ? 1 : 0);
		$stars[2] = (1 <= $progress ? 1 : 0);

		$progress = min(100, round($progress*100));

		$data = ['value' 	=> $progress,
				'style' 	=> $style,
				'stars' 	=> $stars];

		return $data;
	}

	/**
	 * Get user level for exercise
	 *
	 * @param int $id Exercise ID
	 *
	 * @return int $level_user User level
	 */
	public function getUserLevel($id) {

		$results = $this->session->userdata('levels');
		$level_user = (isset($results[$id]) ? $results[$id] : 0);

 		return $level_user;
	}

	/**
	 * Save exercise data to session
	 *
	 * @param int    $id    Exercise id
	 * @param int    $level Exercise level
	 * @param array  $data  Exercise data
	 * @param string $hash  Random string
	 *
	 * @return void
	 */
	public function SaveExerciseData($id, $level, $data, $hash) {

		$sessiondata = $this->session->userdata('exercise');

		$sessiondata[$hash] = array(
			'id'			=> $id,
			'level' 		=> $level,
			'correct' 		=> $data['correct'],
			'type' 			=> $data['type'],
			'solution'		=> $data['solution'],
			'hints_all' 	=> $data['hints_all'],
			'hints_used' 	=> $data['hints_used'],
			'hints' 		=> $data['hints'],
			'separator'		=> (array_key_exists('separator', $data) ? $data['separator'] : NULL)
		);

		$this->session->set_userdata('exercise', $sessiondata);

		return;
	}

	/**
	 * Get exercise data from session
	 *
	 * @param string $hash Random string
	 *
	 * @return array $data Exercise data
	 */
	public function GetExerciseData($hash) {

		$exercise = $this->session->userdata('exercise');

		$correct 	= $exercise[$hash]['correct'] ; 
		$solution  	= $exercise[$hash]['solution'];  
		$level 		= $exercise[$hash]['level']; 
		$type 		= $exercise[$hash]['type']; 
		$id 		= $exercise[$hash]['id'];
		$hints_used	= $exercise[$hash]['hints_used'];
		$hints_all	= $exercise[$hash]['hints_all'];
		$hints 		= $exercise[$hash]['hints'];
		$separator	= $exercise[$hash]['separator'];

		return array($correct,
			$hints,
			$hints_used,
			$hints_all,
			$solution,
			$separator,
			$level, $type, $id);
	}

	/**
	 * Get exercise hint from session
	 *
	 * @param string $hash Exercise hash
	 * @param int    $id   Id of hint
	 * @param string $type Request type (prev/next)
	 *
	 * @return array $data Exercise data
	 */
	public function GetExerciseHint($hash, $id, $type) {

		$exercise = $this->session->userdata('exercise');

		$hints_all 	= $exercise[$hash]['hints_all'];
		$hints_used = $exercise[$hash]['hints_used'];

		// Choose id of current hint
		if ($id == NULL) {
			if ($hints_all == $hints_used) { // no hints left
				return NULL;
			}
			$hint_current = $hints_used + 1;
		} elseif ($type == 'prev') {
			$hint_current = --$id;
			if ($hint_current == 0) { // no previous hint
				return NULL;
			}
		} elseif ($type == 'next') {
			$hint_current = ++$id;
			if ($hint_current > $hints_all) { // no next hint
				return NULL;
			}
		} else {
			$hint_current = $id;
		}

		$hints = $exercise[$hash]['hints'][$hint_current-1];

		// Update number of used hints
		if ($hint_current > $hints_used && $hints_used <= $hints_all) {
			$exercise[$hash]['hints_used'] = ++$hints_used;
			$this->session->set_userdata('exercise', $exercise);
		}

		return array('hints' 		=> $hints,
					'hints_all' 	=> $hints_all,
					'hints_used' 	=> $hints_used,
					'hint_current' 	=> $hint_current);
	}

	/**
	 * Delete exercise data from session
	 *
	 * @param string $hash Random string
	 *
	 * @return void
	 */
	public function DeleteExerciseData($hash) {

		$exercise = $this->session->userdata('exercise');
		unset($exercise[$hash]);
		$this->session->set_userdata('exercise', $exercise);

		return;
	}
}

?>