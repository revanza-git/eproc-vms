<?php
class Breadcrumb{
	private $prefix = '<ol class="breadcrumb pull-in">';

	private $suffix = '</ol>';

	private $list;

	public function __construct($config){
		$this->list[0] = array(
			'url'	=> site_url(),
			'title'	=> 'Dashboard'
		);
	}

	public function generate(){
		$html = $this->prefix;
		$i = 1;
		ksort($this->list);
		foreach ($this->list as $key => $value) {
			$_class = '';
			$_value = '<a href="'.$value['url'].'">'.$value['title'].'</a>';

			if($i == count($this->list)) {
				$_class .= 'active';
				$_value = $value['title'];
			}

			$html 	.= '<li class="'.$_class.'">'.$_value.'</li>';

			$i++;
		}

		return $html.=$this->suffix;
	}
	public function addlevel($key, $value){

		$this->list[$key] = $value;
	}
}