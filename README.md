Cache and Release
=================

Uses Google Closure API to compress javascript file.  The class also caches the result locally.


## Example Usage

````
$compressor = new compressor('main.js');
print '<script type="text/javascript" src="'.$compressor->cacheName.'"></script>';
````