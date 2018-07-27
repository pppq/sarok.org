<?if(isset($xml))
{
   //ob_clean();
   header("Content-type: application/octet-stream");
   header("Content-disposition: attachment; filename=\"sarok-".now2().".xml\"");
	echo "<?xml version=\"1.0\"?>\n";
	echo $xml;
}
else
{
	echo "Siker!";
}
?>