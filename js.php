<?php 

	header("Content-Type: application/javascript");

	# echo "var debug = " . $GLOBALS['DEBUG'] . ";\n";
	# echo "var language = '" . $GLOBALS['LANGUAGE'] . "';\n";
	# echo "\n\n";
	
/********************************************************************************************************/

	function JScompression( $content ){
		$pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/';

		$content = trim( $content );
		$content = preg_replace( $pattern, '', $content );
		$content =  str_replace( ' }', '}', $content );
		$content =  str_replace( '{ ', '{', $content );
		$content =  str_replace( ' =', '=', $content );
		$content =  str_replace( '= ', '=', $content );
		$content =  str_replace( ' ;', ';', $content );
		$content =  str_replace( '; ', ';', $content );
		$content =  str_replace( ' )', ')', $content );
		$content =  str_replace( '( ', '(', $content );
		$content = preg_replace( '#/\*.*?\*/#', ' ', $content );
		$content = preg_replace( '/ +/', ' ', $content );
		$content = preg_replace( '/[[:blank:]]+/', ' ', $content );
		$content = preg_replace( '/^\s+/', ' ', $content );
		$content = preg_replace( '/[[:blank:]]+/', ' ', $content );
		$content =  str_replace( '// ', "\n// ", $content );
		$content =  str_replace( '}//', "}\n// ", $content );
		$content =  str_replace( ';//', ";\n// ", $content );
		$content =  str_replace( ',//', ",\n// ", $content );
		$content = preg_replace( '/ +/', ' ', $content );
		$content = preg_replace( '#/\*.*?\*/#', ' ', $content );
		$content = preg_replace( '/^\s+/', ' ', $content );
		$content = preg_replace( "/([\r\n])(\/\*)[\s\S]*?(\*\/)/", " ", $content );

		$content = preg_replace( '/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $content )); 
		$content = preg_replace( "/([\r\n])(\/\*)[\s\S]*?(\*\/)/", " ", $content );

		$string = $content;
		$array = explode( "\n" , $string );
		
			foreach( $array as $arr ) {
				if( substr( $arr, 0, 2) != '//' ){
					$output[] = trim( $arr );
				}
			}

		$code = implode( "\n" , $output );
		$code = preg_replace( "/ +/", " ", $code );
		$code = preg_replace( "/[\r\n]+/", "\n", $code );
		$code = str_replace( "http://","http:\/\/",$code );

		return $code;
	}

/********************************************************************************************************/

	function print_js( $path , $comp ){

		$iter = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($_SERVER['DOCUMENT_ROOT'] . $path, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::SELF_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
		);

		$paths = array($path);

		foreach ($iter as $path => $dir) {

			$valfin = $path;
			$exp = explode('.', $valfin);
			$valfin_end = end($exp);

			if($dir->isDir()) {
				$valfin = $valfin;
			}
			$paths[] = $valfin;
			
		}

		if($paths > ''){
			sort($paths);
		}

		for($i=0; $i < count($paths);++$i){

		$check = explode('.', $paths[$i]);
		$file_extension = end($check);

			if (strpos($paths[$i], "#OLD") === false && strpos($paths[$i], ".DAV") === false){
				if($file_extension == "js" ){
					$code .= file_get_contents( $paths[$i] );
				}
			}

		}

		if( $comp === true ){
			return JScompression( $code );
		}else{
			return $code;
		}

	}

echo <<< EOT
/*
	CREATED BY DARIO PASSARIELLO 
	copyright (c) 2020

	The MIT License (MIT)
	Copyright (c) 2020 Dario Passariello
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/


EOT;

echo print_js( "/script/" , true );
	
?>
