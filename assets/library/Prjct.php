<?php
/*
 * prjct v1.0.0 - 2016-01-18
 * A PHP library that brings your folder based projects to HTML
 * https://github.com/jonaszielke/prjct
 *
 * Copyright (c) 2016 Jonas Zielke <jones@sayjones.de>
 *
 * Released under the MIT license
 *
 * For the full license information, view the LICENSE file that was distributed
 * with this source code.
*/

class Prjct{
	const version = '1.0.0';

	private $_path;
	private $_single = 0;
	//private $_prjctPerPage = -1;
	private $_projectData = [];
	private $_infoData = [];
	private $_error = [];

	public function set_path($val){
		if(substr($val, -1) !== '/'){
			$val .= '/';
		}
		$this->_path = $val;
	}
	public function get_path(){
		return $this->_path;
	}
	private function set_single($val){
		$this->_single = $val;
	}
	public function get_single(){
		return $this->_single;
	}
	private function set_prjctPerPage($val){
		$this->_prjctPerPage = $val < -1 ? -1 : $val;
	}
	public function get_prjctPerPage(){
		return $this->_prjctPerPage;
	}
	private function set_projectData($val){
		$this->_projectData = $val;
	}
	public function get_projectData(){
		return $this->_projectData;
	}
	private function set_infoData($val){
		$this->_infoData = $val;
	}
	public function get_infoData(){
		return $this->_infoData;
	}

	public function __construct($path = 'project/'){
		$this->set_path($path);

		if(PHP_OS === "SunOS"){
			exit;
		}

		if(!is_dir($this->get_path())){
			exit;
		}

		$info = $this->getInfo();
		if(null === $info){
			exit;
		}
		$this->set_infoData($info);

		// get folder
		$folders = explode("/", $_SERVER['REQUEST_URI']);

		$way = 0;
		if(count($folders) === 3){
			$way = 1;

			//if($folders[1] === 'page'){
			//	$way = 2;
			//}
		}

		// single project
		if(1 === $way){
			$this->set_single(1);

			$folder = $folders[count($folders) - 2];
			// search for specific url in folder
			$projectname = glob($this->get_path()."*{".implode(",", explode("-", $folder))."}", GLOB_BRACE);

			// found one project? Get its data
			if(count($projectname) === 1){
				$project = $this->getProject($projectname[0]);
				$x[$project['id']] = $project;

				$this->set_projectData($x);
			}
		}
		// project overview
		else if(0 === $way ){ // || 2 === $way
			$this->set_single(0);

			$projects = [];

			// get all projects in folder
			$dirs = glob($path."[0-9]*", GLOB_ONLYDIR);

			// pagenation
			//if(2 === $way){
			//
			//}

			foreach($dirs as &$dir){
				$project = $this->getProject($dir);

				if(null !== $project){

					$projects[$project['id']] = $project;
				}
			}

			$this->set_projectData($this->sortFolders($projects));
		}
	}

	private function getProject($path){
		$project = [];

		// basic info
		$project['id'] = $this->getProjectNumber(basename($path));
		$project['name'] = $this->getProjectName(basename($path));
		$project['url'] = $this->decodeString($project['name']);
		$project['path'] = $path;
		$project['parts'] = array();

		$subprojects = $this->sortFolders($this->getProjectSubFolders($path));

		// Two options:
		// 1. There aren't any sub projects
		// 2. In single view AND there are some sub projects
		if(null === $subprojects || (null !== $subprojects && $this->get_single())){
			$rootProjectImages = $this->getProjectImages($path);
			$rootProjectDescritption = $this->getProjectDescription($path);

			// found data in root project folder
			$mainfolder = $this->getProjectData($path);
			if(null !== $mainfolder){
				$project['parts']['main'] = $mainfolder;
			}

			// add sub projects when in single view
			if(null !== $subprojects && $this->get_single()){
				$project['parts'] = array_merge($project['parts'], $subprojects);
			}
		}
		// There are subprojects for the overview view
		else if(!$this->get_single()){
			// get latest (newest) subproject
			$project['parts']['main'] = array_values($subprojects)[0];
		}

		if(isset($project['parts']) && null !== $project['name']){
			return $project;
		}

		return null;
	}

	private function getProjectSubFolders($path){
		$subfolders = [];

		$dirs = glob($path."/[0-9]*", GLOB_ONLYDIR);
		foreach($dirs as &$dir){
			$subproject = $this->getProjectData($dir);
			if(null !== $subproject){
				$subfolders[$subproject['id']] = $subproject;
			}
		}

		return count($subfolders) >= 1 ? $subfolders : null;
	}

	private function getProjectData($path){
		$projectImages = $this->getProjectImages($path);
		$projectDescritption = $this->getProjectDescription($path);

		if(count($projectImages) >= 1 && count($projectDescritption) >= 1){
			$projectNumber = $this->getProjectNumber(basename($path));

			$data['id'] = $this->getProjectNumber(basename($path));
			$data['name'] = $this->getProjectName(basename($path));
			$data['img'] = $this->get_single() === 0 ? array_slice($projectImages, 0, 1) : $projectImages;
			$data['desc'] = file_get_contents($projectDescritption[0]); //'Hi';//
			$data['attach'] = $this->getProjectAttachments($path);

			return $data;
		}

		return null;
	}

	private function getProjectNumber($projectname){
		preg_match('/^[0-9]*/', $projectname, $output);

		if(1 === count($output)){
			return $output[0];
		}

		return null;
	}

	private function getProjectName($projectname){
		preg_match('/^[0-9]*\s(.*)/', $projectname, $output);

		if(2 === count($output)){
			return trim($output[1]);
		}

		return null;
	}

	private function sortFolders($array){

		if(null === $array){
			return $array;
		}

		// http://stackoverflow.com/a/6618787/1932412
		$k = array_keys($array);
		$v = array_values($array);
		array_multisort($k, SORT_ASC, $v, SORT_DESC);
		$array = array_combine($k, $v);

		return array_reverse($array);
	}

	private function getProjectImages($path){
		return preg_grep('/\.(jpg|gif|png|svg|jpeg)$/i', glob($path."/*.*", GLOB_BRACE));
	}
	private function getProjectDescription($path){
		return glob($path."/*.{md,txt,rtf}", GLOB_BRACE);
	}
	private function getProjectAttachments($path){
		return glob($path."/attach/*", GLOB_BRACE);
	}

	private function decodeString($string){
		return urlencode(preg_replace(
			$patterns = array('/ /', '/\//', '/---/', '/--/'),
			$replace  = array('-',   '-',    '-',     '-'),
			$string));
	}

	private function getInfo(){
		if(file_exists($this->get_path().'info.md')){
			$output = [];

			$info = file_get_contents($this->get_path().'info.md');

			// http://stackoverflow.com/a/34600061/1932412
			// Thank you!
			preg_match_all("#^- ([^:]*):\s(.*(?>\R.*)*?(?=\R- |\R*\z))#m", $info, $matches);
			//unset($matches[0]);

			//print_r($matches);

			// website title
			$string = array_search('website title', array_map('strtolower', $matches[1]));
			$output['websitetitle'] = $string === false ? 'Work Samples' : str_replace("\n", '', trim($matches[2][$string]));

			// contact header button
			$string = array_search('available', array_map('strtolower', $matches[1]));
			$output['headerButton'] = $string === false ? 'Contact' : str_replace("\n", '', trim($matches[2][$string]));

			// name
			$string = array_search('name', array_map('strtolower', $matches[1]));
			$output['name'] = $string === false ? 'Your name' : str_replace("\n", '', trim($matches[2][$string]));

			// email
			$string = array_search('e-mail', array_map('strtolower', $matches[1]));
			$output['email'] = $string === false ? 'hallo@email.de' : str_replace("\n", '', trim($matches[2][$string]));

			// title
			$string = array_search('title', array_map('strtolower', $matches[1]));
			$output['title'] = $string === false ? "Alle Menschen werden Brüder,\nWo dein sanfter Flügel weilt." : trim($matches[2][$string]);

			// description
			$string = array_search('description', array_map('strtolower', $matches[1]));
			$output['description'] = $string === false ? 'Donec ullamcorper nulla non metus auctor fringilla. Sed posuere consectetur est at lobortis. Aenean lacinia bibendum nulla sed consectetur. Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Donec id elit non mi porta gravida at eget metus.' : trim($matches[2][$string]);

			// links
			$string = array_search('links', array_map('strtolower', $matches[1]));
			$output['links'] = $string === false ? '' : str_replace("\n", '', trim($matches[2][$string]));

			// footer
			$string = array_search('footer', array_map('strtolower', $matches[1]));
			$output['footer'] = $string === false ? 'Hello again' : trim($matches[2][$string]);

			//print_r($output);

			return $output;
		}

		return null;
	}
}

?>
