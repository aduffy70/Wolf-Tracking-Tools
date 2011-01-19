<?php
if ($_FILES['uploadedfile']['type'] == "text/php")
{
    die("***Error: Only .csv files can be converted***");
}
$csvFileName = $_FILES['uploadedfile']['tmp_name'] or die("***Error: File does not exist***");
$csvFile = fopen($csvFileName, "r");
$kmlFileName = "kmlfiles/file_" . uniqid() . ".kml";
$kmlFile = fopen($kmlFileName, "w") or die("***Error: can't open file***");
fgets($csvFile);    #Throw out the header row
$kmlString = '<?xml version="1.0" encoding="UTF-8"?><kml xmlns="http://www.opengis.net/kml/2.2"><Folder>';
#$format = "\n\t<Placemark>\n\t\t<name>%s</name>\n\t\t<Point>\n\t\t\t<coordinates>%s,%s,0</coordinates>\n\t\t</Point>\n\t</Placemark>";
$format = "\n\t<Placemark>\n\t\t<name>%s</name>";
$format = $format . "\n\t\t<Style>\n\t\t\t<IconStyle><Icon><href>%s</href></Icon></IconStyle>\n\t\t</Style>";
$format = $format . "\n\t\t<Point>\n\t\t\t<coordinates>%s,%s,0</coordinates>\n\t\t</Point>\n\t</Placemark>";
$recordCount = 0;
while(!feof($csvFile))
{
    list($title, $latitude, $longitude, $marker) = explode(",", fgets($csvFile)) or die("***Error: Invalid file***");
    $title = trim($title);
    $latitude = trim($latitude);
    $longitude = trim($longitude);
    if ($marker == 1)
    {
        $marker = 'http://www.google.com/intl/en_us/mapfiles/ms/icons/green-dot.png';
    }
    else if ($marker == 2)
    {
        $marker = 'http://www.google.com/intl/en_us/mapfiles/ms/icons/red-dot.png';
    }
    else if ($marker == 3)
    {
        $marker = 'http://www.google.com/intl/en_us/mapfiles/ms/icons/purple-dot.png';
    }
    else if ($marker == 4)
    {
        $marker = 'http://www.google.com/intl/en_us/mapfiles/ms/icons/yellow-dot.png';
    }
    else $marker = 'http://www.google.com/intl/en_us/mapfiles/ms/icons/blue-dot.png';    
    if (($latitude != "") and ($longitude != ""))
    {
        $kmlString = $kmlString . sprintf($format, $title, $marker, $latitude, $longitude);
        $recordCount = $recordCount + 1;    
    }
}
$kmlString = $kmlString . "</Folder>\n</kml>";
fwrite($kmlFile, $kmlString);
fclose($kmlFile);
fclose($csvFile);
echo "Generated kml file with " . $recordCount . " records: " . $kmlFileName;
$googleMapsLink = "http://maps.google.com/maps?q=http://fernseed.usu.edu/" . $kmlFileName; 
echo '<br><br> <a href="http://fernseed.usu.edu/';
echo $kmlFileName;
echo '">Download file</a> or <a href="';
echo $googleMapsLink;
echo '">view in Google Maps</a>';
?>
