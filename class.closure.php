<?php

/**
 * The MIT License (MIT)
 * Copyright (c) 2013 Andrew Champ
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction, 
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, 
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial 
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT 
 * LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
 * NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
 
 
/**
 * EXAMPLE USAGE
 * 
 * $compressor = new compressor('main.js');
 * print '<script async type="text/javascript" src="'.$compressor->cacheName.'"></script>';
 */
 
 
	class compressor{
	
	
		private $file;
		private $raw;
		private $compressTime;
		
		public $cacheName;
		public $compressed = array();
		public $compiledCode;
		
	
		public function __construct($_file=null, $_cacheName='cached.js', $_compressTime=3600){
			if($_file == null)
				exit('You need to specify the file to compress in '.__CLASS__.' class.');
			
			$this->file = $_file;
			$this->cacheName = $_cacheName;
			$this->compressTime = $_compressTime;
			
			if(file_exists($this->file) && $this->checkTime() > $this->compressTime):
				$this->raw = file_get_contents($this->file);
				if(!empty($this->raw)):
					$this->initiate();
					$this->save();
				endif;
			endif;
		}
		
		
		private function initiate(){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'Content-type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
				'output_format=json'
				.'&output_info=compiled_code'
				//.'&output_info=warnings'
				.'&output_info=errors'
				.'&compilation_level=SIMPLE_OPTIMIZATIONS' // WHITESPACE_ONLY, SIMPLE_OPTIMIZATIONS, ADVANCED_OPTIMIZATIONS (WARNING: ADVANCED_OPTIMIZATIONS can be unstable.)
				.'&warning_level=VERBOSE'
				//.'&code_url='
				.'&js_code=' . urlencode($this->raw)
			);
			curl_setopt($ch, CURLOPT_URL, 'http://closure-compiler.appspot.com/compile');
			$response = curl_exec($ch);
			
			$this->compressed = json_decode($response, true);
			$this->compiledCode = $this->compressed['compiledCode'];
		}
		
		
		private function save(){
			file_put_contents($this->cacheName, $this->compiledCode);
		}
		
		
		private function checkTime(){
			return time() - filemtime($this->cacheName);
		}
	
	
	}


?>