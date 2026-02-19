<?php

class SideMenu{

	private $CI;



	private $menuList;


	public function __construct(
		$menuList = array()){
		$this->CI =& get_instance(); 
		$this->menuList = $menuList;

		return $this->generate();

	}



	public function generate($active=''){
		$admin = $this->CI->session->userdata('admin');
		$user = $this->CI->session->userdata('user');
		$menu['id_role'] = ($admin ? $admin['id_role'] : $user['id_role']);
		
		$html = '<ul class="sidebar-wrapper">';

		foreach ($this->menuList as $keyGroup => $valueGroup) {
			$icon = (isset($valueGroup['icon'])) ? '<i class="fa fa-'.$valueGroup['icon'].' icon"></i>' : '';

			if(in_array($menu['id_role'], $valueGroup['role'])){
				if(isset($valueGroup['title'])){
					$arrow = (isset($valueGroup['list'])) ? '<span class="pull-right plus-sign-up plus-sign-bottom">
									
								</span>' : '';
					$has_child = (isset($valueGroup['list']) && count($valueGroup['list'])>0) ? 'has-child has-dropdown' : ''; 
					$html .= '<li class="sidebar-list '.$has_child.'">
								<a href="'.(($valueGroup['url']!='')?$valueGroup['url']:'').'" class="sidebar-heading">
									'.$icon.'
									<span>'.$valueGroup['title'].'</span>
									'.$arrow.'
								</a>';
				}	
				if(isset($valueGroup['list'])){
					$html .= '<ul class="sidebar-menu is-dropdown">';
					foreach ($valueGroup['list'] as $key => $value) {
						if(in_array($menu['id_role'], $value['role'])){
							$module = (isset($value['module'])) ? 'id="'.$value['module'].'"' : '';

							$url = ($this->CI->session->userdata('admin')['id_division'] == 5) ? base_url('/pemaketan/') : $value['url'] ;
							$html .= '	<li class="sidebar-menu-item" '.$module.'>
											<a href="'.$url.'" class="sidebar-menu-button">
												<i class="fa fa-angle-right"></i>
												<span>'.$value['title'].'</span>
											</a>
										</li>';
						}
					}
					$html .= '</ul>';
				}
				$html .='</li>';
			}
		}

		$html .= '</ul>';

		return $html;

	}



}