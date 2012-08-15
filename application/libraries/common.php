<?php

class Common{

    static public function generateRandomKey($length=8) {
        $CI =& get_instance();

        $CI->load->helper('string');
        $curTime = time();
        $ret = "";
        $ret .= $curTime;
        $ret .= random_string("alnum", $length);
        return $ret;
    }

	public static function array_sort($array, $on, $isSortASC=true)
	{
	    $new_array = array();
	    $sortable_array = array();
	    if (count($array) > 0) {
	        foreach ($array as $k => $v) {
	            if (is_array($v)) {
	                foreach ($v as $k2 => $v2) {
	                    if ($k2 == $on) {
	                        $sortable_array[$k] = $v2;
	                    }
	                }
	            } else {
	                $sortable_array[$k] = $v;
	            }
	        }

	        if($isSortASC)
	        {
	        	asort($sortable_array);
	        }else
	        {
	            arsort($sortable_array);
	        }

	        foreach ($sortable_array as $k => $v) {
	            $new_array[$k] = $array[$k];
	        }
	    }

	    return $new_array;
	}

    static public function uploadPrecropImage($srcFile)
    {
        $imageTempPath = IMAGE_PATH;

        $destFile = false;
        $retry=0;
        do
        {
            $tempFileName = Common::generateRandomKey().date('YmdHis').'.jpg';

            if(!file_exists($imageTempPath.$tempFileName))
            {
                $destFile = $imageTempPath.$tempFileName;
               break;
            }
        }while(++$retry < 50);

        if(!$destFile)
        {
            _e("File name generation failed!!");
            return false;
        }

        $result = move_uploaded_file($srcFile, BASEPATH."../".$destFile);

        if(!$result)
        {
            _e("File Upload failed! $srcFile -> $destFile");
            return false;
        }
        return $destFile;
    }

    static function cropImage($origImage, $destFile, $x, $y, $w, $h, $targ_w, $targ_h)
    {
    	$x = $x?$x:0;
    	$y = $y?$y:0;
    	$w = $w?$w:$targ_w;
    	$h = $h?$h:$targ_h;

        _d("CropImage for $origImage, $destFile, $x, $y, $w, $h, $targ_h, $targ_w");

        $img_r = imagecreatefromjpeg($origImage);
        $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

        imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$targ_w,$targ_h,$w,$h);
//        header('Content-type: image/jpeg');

//        imagejpeg($dst_r, null, $jpeg_quality);

        imagejpeg($dst_r, $destFile, 85);
        chmod($destFile, 0744);

        return true;
    }

    static public function generateTempFile($tempPath)
    {
        $destFile = false;
        $retry=0;
        do
        {
            $tempFileName = Common::generateRandomKey().date('YmdHis');

            if(!file_exists($tempPath.$tempFileName))
            {
                $destFile = $tempPath.$tempFileName;
               break;
            }
        }while(++$retry < 30);

        if(!$destFile)
        {
            _e("File name generation failed!!");
            return false;
        }

        return BASEPATH."../".$destFile;
    }

    static function startBenchmark($functionName){
        $CI =& get_instance();
        $CI->benchmark->mark($functionName.'_start');
    }

    static function endBenchmark($functionName, $isCache=false, $ref=false, $minIgnoredElapsed=50, $isEcho=false){
        $CI =& get_instance();
        $CI->benchmark->mark($functionName.'_end');

        $elapsedTime = $CI->benchmark->elapsed_time($functionName.'_start', $functionName.'_end')*1000;
        $benchMarkName = false;
        if($isCache){
            $benchMarkName = $functionName.'-CACHE';
        }else{
            $benchMarkName = $functionName;
        }
        if ($elapsedTime > $minIgnoredElapsed) {
            _d('BENCHMARK[' . $benchMarkName . ']:' . $elapsedTime . "ms");
            if($isEcho)
                echo ('BENCHMARK[' . $benchMarkName . ']:' . $elapsedTime . "ms");
        }
    }
}

class SimpleImage {
    var $image;
    var $image_type;

    function load($filename)
    {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if( $this->image_type == IMAGETYPE_JPEG ) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif( $this->image_type == IMAGETYPE_GIF ) {
            $this->image = imagecreatefromgif($filename);
        } elseif( $this->image_type == IMAGETYPE_PNG ) {
            $this->image = imagecreatefrompng($filename);
        }
    }

    function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null)
    {
        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image,$filename,$compression);
        } elseif( $image_type == IMAGETYPE_GIF ) {
            imagegif($this->image,$filename);
        } elseif( $image_type == IMAGETYPE_PNG ) {
            imagepng($this->image,$filename);
        }
        if( $permissions != null) {
            chmod($filename,$permissions);
        }
    }
    function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }
    }
    function getWidth() {
      return imagesx($this->image);
    }
    function getHeight() {
      return imagesy($this->image);
    }
    function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
    }
    function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
    }
    function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
    }
    function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
    }
    function resizeKeepRatio($maxWidth, $maxHeight)
    {
    	$imgWidth = $this->getWidth();
    	$imgHeight = $this->getHeight();

		if($imgWidth > $maxWidth && $imgHeight > $maxHeight)
		{
		    if(($imgWidth/$imgHeight) > $maxWidth/$maxHeight)
		    {
			    $this->resizeToWidth($maxWidth);
		    }else{
			    $this->resizeToHeight($maxHeight);
		    }
		}elseif($imgWidth > $maxWidth)
		{
		    $this->resizeToWidth($maxWidth);
		}
        elseif($imgHeight > $maxHeight)
		{
		    $this->resizeToHeight($maxHeight);
		}
    }

};


