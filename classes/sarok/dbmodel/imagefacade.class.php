<?php


/**
 *  imageFacade
 */

class imageFacade {
	/**
	 *  Initializes db connection
	 */
	private $dbcon;
	private $log;
	private $db;
	public function imageFacade() {

		$this->log = singletonloader :: getInstance("log");
		$this->db = singletonloader :: getInstance("mysql");
		$this->log->info("imageFacade initialized");

	}//0613528669  -- Ez egy olasz etterem! Nagyon finom pizza van benne es olasz a szakacs.

	public function uploadImage($filename,$location,$userID)
	{
		global $imageMaxWidth, $imageMaxHeight,$imageQuality,$thumbWidth;
		$filename=date("Y-m-d")."_".$filename;
		$this->log->debug("uploadImage($filename,$location,$userID)");
		$dir=$_SERVER["DOCUMENT_ROOT"]."/userimages/$userID";
		if(!file_exists($dir)) mkdir($dir);
		$dir_thumb=$_SERVER["DOCUMENT_ROOT"]."/userimages/$userID/thumbs";
		if(!file_exists($dir_thumb)) mkdir($dir_thumb);
		$destination="$dir/$filename";

		$destination_thumb="$dir_thumb/$filename";
		$this->log->debug("destination is: $destination");
		list($width, $height, $type, $attr) = getimagesize($location);
		$this->log->debug("Image $filename: Width: $width, Height: $height, Type: $type, Attr: $attr");
		$this->log->debug("creating thumbnail of the image to $destination_thumb with width $thumbWidth");
		$this->resizeImage($location,$destination_thumb,$thumbWidth,$thumbWidth,$imageQuality);
		if($width>$imageMaxWidth or $height>$imageMaxHeight)
		{
			$this->log->debug("Image is too large, first resizing it.");
			$this->resizeImage($location,$destination,$imageMaxWidth,$imageMaxHeight,$imageQuality);
			unlink($location);
		}
		else
		{
			$this->log->debug("Image's size is OK, moving it to $destination");
			move_uploaded_file($location,$destination);
		}

	}

	public function resizeImage($src,$dest,$maxWidth,$maxHeight,$quality=100) {
        $this->log->debug("resizeImage($src,$dest,$maxWidth,$maxHeight,$quality)");
        if (file_exists($src)  && isset($dest)) {
        	$this->log->debug("$src exists");
            // path info
            $destInfo  = pathinfo($dest);

            // image src size
            $srcSize   = getimagesize($src);

            // image dest size $destSize[0] = width, $destSize[1] = height
            $srcRatio  = $srcSize[0]/$srcSize[1]; // width/height ratio
            $destRatio = $maxWidth/$maxHeight;
            if ($destRatio > $srcRatio) {
                    $destSize[1] = $maxHeight;
                    $destSize[0] = $maxHeight*$srcRatio;
            }
            else {
                    $destSize[0] = $maxWidth;
                    $destSize[1] = $maxWidth/$srcRatio;
            }

            // path rectification
            if ($destInfo['extension'] == "gif") {
                  $dest = substr_replace($dest, 'jpg', -3);
                  $this->log->debug("creating .jpg instead of .gif");
            }

        // true color image, with anti-aliasing
        //phpinfo();
        $destImage = imagecreatetruecolor($destSize[0],$destSize[1]);
    //    imageantialias($destImage,true);

        // src image
        switch ($srcSize[2]) {
            case 1: //GIF
            $srcImage = imagecreatefromgif($src);
            break;

            case 2: //JPEG
            $srcImage = imagecreatefromjpeg($src);
            break;

            case 3: //PNG
            $srcImage = imagecreatefrompng($src);
            break;

            default:
            return false;

        }


        // resampling
		$this->log->debug("resampling image");
        imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0,$destSize[0],$destSize[1],$srcSize[0],$srcSize[1]);
        // generating image
        switch ($srcSize[2]) {
            case 1:
            case 2:
            imagejpeg($destImage,$dest,$quality);
            break;

            case 3:
            imagepng($destImage,$dest);
            break;
        }
        return true;
    }
    else {
        return false;
    }
}

	public function listDir($dirname)
{
         $this->log->debug("listDir($dirname)");
 if(!file_exists($dirname)) return array();
        if($dirname[strlen($dirname)-1]!='/')
                $dirname.='/';
        $result_array=array();
        $handle=opendir($dirname);
        while ($file = readdir($handle))
        {
        	$this->log->debug("listDir: listed $file");
                if($file=='.'||$file=='..')
                        continue;
/*                if(is_dir($dirname.$file))
                      list_dir($dirname.$file.'\\');
                else                                   */
			if(eregi("jpg",$file) || eregi("gif",$file) || eregi("png",$file)  ){

                        $result_array[]=$file;
                        $this->log->debug("listDir: added $file to list");
                 //       $result_array["dir"][]=$dirname;
                        }
        }
        closedir($handle);
        //$this->log->debug("listDir($dirname)");
        return $result_array;

}

public function delImage($filename,$userID)
{
	$this->log->debug("deleting ".$_SERVER["DOCUMENT_ROOT"]."/userimages/$userID/$filename");
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/userimages/$userID/$filename"))
	{
		unlink($_SERVER["DOCUMENT_ROOT"]."/userimages/$userID/$filename");
		unlink($_SERVER["DOCUMENT_ROOT"]."/userimages/$userID/thumbs/$filename");
		return true;
	}
	else
	{
		return false;
	}
}
	/**
	 * END
	 */
}
?>