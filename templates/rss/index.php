<?
header("Content-type: text/xml");
header("Content-Disposition: inline;");
?><? echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" ?>
<rss xmlns:dc="http://purl.org/dc/elements/1.1/" version="2.0">
  <channel>
<?=$navigation;?>
<?=$main;?>
    </channel>
</rss>
