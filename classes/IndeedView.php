<?php
namespace Indeed\Uap;
class IndeedView
{

	private $_template = '';
	private $_content_data = array();
	private $_do_extract = FALSE;


	/*
	 * @param none
	 * @return none
	 */
	public function __construct(){
			return $this;
	}


	/*
	 * @param string
	 * @return none
	 */
	public function setTemplate($full_path=''){
			$this->_template = $full_path;
			return $this;
	}


	/*
	 * @param array
	 * @return none
	 */
	public function setContentData($data, $extract=FALSE){
			$this->_content_data = $data;
			$this->_do_extract = $extract;
			return $this;
	}


	/*
	 * @param none
	 * @return string
	 */
	public function getOutput(){
			ob_start();
			if ($this->_do_extract){
					extract($this->_content_data);
			} else {
					$data = $this->_content_data;
			}

			require $this->_template;
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
	}

}
