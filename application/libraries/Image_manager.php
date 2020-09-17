<?php
require APPPATH . 'libraries/Image.php';

class Image_manager 
{
	private $config;

	public function __construct() 
	{
		$CI =& get_instance();
		$CI->load->config('btkcommerce');
		$this->config = $CI->config->item('commerce');
	}

	public function get_image_uri($filename, $width = 40, $height = 40) {
		if ($filename == NULL || $filename === '') {
			log_message("info", "Image is empty, return default image");
			return $this->resize('small-logo.png', $width, $height);
		}

		$image_uri = NULL;
		if (file_exists($this->config['base_image_path'] .'/'. $filename)) {
			log_message("info", "Attempting to resize image to {$width} and {$height}");
			$image_uri = $this->resize($filename, 40, 40);
		} else if (!file_exists($this->config['base_image_path'] .'/'. $filename)){
			log_message("info", "Failed to find file with name {$filename}, create default image");
			$image_uri = $this->resize('small-logo.png', 40, 40);
		} 

		if ($image_uri == NULL) {
			log_message("info", "Image uri still null, return default image");
			return $this->resize('small-logo.png', 40, 40);
		}

		return $image_uri;
	}

	public function get_default_image_uri()
	{
		return $this->resize('small-logo.png', 40, 40);
	}

	public function resize($filename, $width, $height) {
		/** 
		var_dump(substr(str_replace('\\', '/', realpath($this->config['base_image_path'] . $filename)), 0, strlen($this->config['base_image_path'])));
		var_dump(str_replace('\\', '/', $this->config['base_image_path']));
		var_dump(realpath($this->config['base_image_path'] . $filename));
		
		if (!is_file($this->config['base_image_path'] . $filename) || 
			substr(str_replace('\\', '/', realpath($this->config['base_image_path'] . $filename)), 0, strlen($this->config['base_image_path'])) != str_replace('\\', '/', $this->config['base_image_path'])) {
			return;
		}**/ 

		if (!is_file($this->config['base_image_path'] .DIRECTORY_SEPARATOR. $filename)) {
			log_message("info", "Failed to open file with name {$filename}, its not file");
			return NULL;
		}

		if (!file_exists($this->config['base_image_path'] . DIRECTORY_SEPARATOR. $filename)) {
			log_message("info", "Failed to open file with name {$filename}, the file doest exists");
			return NULL;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

		try {
			if (!is_file($this->config['base_image_path'] . DIRECTORY_SEPARATOR. $image_new) || 
			(filemtime($this->config['base_image_path'] . DIRECTORY_SEPARATOR. $image_old) > filemtime($this->config['base_image_path'] . DIRECTORY_SEPARATOR. $image_new))) {
				list($width_orig, $height_orig, $image_type) = getimagesize($this->config['base_image_path'] . DIRECTORY_SEPARATOR. $image_old);
					
				if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) { 
					return $this->config['base_image_path'] . DIRECTORY_SEPARATOR . $image_old;
				}
	
				$path = '';

				$directories = explode('/', dirname($image_new));

				foreach ($directories as $directory) {
					$path = $path . '/' . $directory;

					if (!is_dir($this->config['base_cache_image_path'] .DIRECTORY_SEPARATOR. $path)) {
						@mkdir($this->config['base_cache_image_path'] .DIRECTORY_SEPARATOR. $path, 0777);
					}
				}

				if ($width_orig != $width || $height_orig != $height) {
					$image = new Image($this->config['base_image_path'] .DIRECTORY_SEPARATOR. $image_old);
					$image->resize($width, $height);
					$image->save($this->config['base_cache_image_path'] .DIRECTORY_SEPARATOR. $image_new);
				} else {
					copy($this->config['base_image_path'] .DIRECTORY_SEPARATOR. $image_old, $this->config['base_cache_image_path'] .DIRECTORY_SEPARATOR. $image_new);
				}
			}

			return $this->config['base_cache_image_uri'] .'/'. $image_new;
		} catch (Exception $ex) {
			return NULL;
		}

	}
}
