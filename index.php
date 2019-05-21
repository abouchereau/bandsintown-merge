<?php 
//bandsintown API KEY
//please ask your own at Bandsintown Support <support@bandsintown.com>
define('BIT_APP_ID','xxxxxxx');

include_once __DIR__.'/BandsInTownMerge.php';

$bitm = new BandsInTownMerge(BIT_APP_ID);
$bitm->setCacheLength(60);//minutes
$bitm->addBand('Duo Fines Lames','Duo Fines Lames');
$bitm->addBand('Quatuor Megamix', 'Quatuor Megamix');
$bitm->addBand('Bande Originale - Collectif La Saugrenue','Bande Originale');
$bitm->addBand('LE BALLUCHE DE LA SAUGRENUE','Le Balluche de la Saugrenue');
$bitm->addBand('CHORO DE AKSAK', 'Choro de Aksak');
$bitm->addBand('Kif Kif','Kif Kif');
$bitm->addBand('LA FANFARE SAUGRENUE','La Fanfare Saugrenue');
$bitm->addBand('YGRANKA','Ygranka');
if (isset($_GET['forceCache'])) {
	$bitm->reloadCache();
}
$dates = $bitm->getDates();

?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.6.4/leaflet.min.css"  />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.6.4/leaflet.js"></script>
<style type="text/css">
body {
	color:rgb(50, 85, 98);
	}
td, th {
    padding: 5px;
}
#map {
	width:500px;
	height:450px;	
}
#dates {
	width:500px;
	height:450px;
	overflow-y:scroll;
	background-color:#FFF;
}
.pointer {
	cursor:pointer;
	}
	.mois {
		background-color:rgb(50, 85, 98);
		color:#FFF;}
</style>
</head>
<body>
<div id="dates">
<?php 
$prevMonth = ''; 
$prevYear = ''; 
?>
<table class="highlight">
	<tbody>
		<?php foreach($dates as $d) { 
			if ($d[9] != $prevMonth) {
				$prevMonth = $d[9];
				?>
				<tr>
					<td class="center-align mois" colspan="3"><?php echo $d[9]; 
						if ($d[10] != $prevYear) {
							$prevYear = $d[10];		
							echo " ".$d[10]; 
						}					
					?></td>
				</tr>
			<?php } ?>
				
			<tr class="pointer" onclick="window.open('<?php echo $d[6]; ?>');" title="Afficher les détails">
				<td><nobr><?php echo $d[7]; ?> <?php echo $d[8]; ?></nobr></td>
				<td><?php echo $d[1]; ?></td>
				<td><?php echo $d[3]; ?></td>
			</tr>
		<?php } ?>
	</tbody>
</table>
</div>
<div id="map"></div>
</body>
<script>
var icone = new L.icon();
var latmin = Number.POSITIVE_INFINITY;
var longmin = Number.POSITIVE_INFINITY;
var latmax = Number.NEGATIVE_INFINITY;
var longmax = Number.NEGATIVE_INFINITY;	
var map = L.map('map');

var Esri_WorldStreetMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
	attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012'
});

Esri_WorldStreetMap.addTo(map);
var layer = new L.LayerGroup();
var data = <?php echo json_encode($dates); ?>;
for (var i in data)
{	
	if (data[i].length == 11)
	{
		var lat = data[i][4];
		var lng = data[i][5];
		var town = data[i][3];
		var date = data[i][7]+" "+data[i][8]+" "+data[i][9]+" "+data[i][10];
		var okaz = data[i][2];
		var groupe = data[i][1];	
		var url = data[i][6];	
		latmin = Math.min(latmin, lat);
		latmax = Math.max(latmax, lat);
		longmin = Math.min(longmin, lng);
		longmax = Math.max(longmax, lng);
		
		var latlng = new L.LatLng(lat, lng);		
		var marker = new L.marker(latlng);

		var html = '<div style="text-align:center"><b>'+date+'</b><br />'+groupe+'<br /><b>'+town+'</b><br /><a href="'+url+'" target="_blank">'+okaz+'</i></div>';			
		var popup = new L.Popup({closeButton: false});
		popup.setContent(html);
		marker.bindPopup(popup);
		marker.addTo(layer);		
	}
}
layer.addTo(map);
var bounds = new L.LatLngBounds([latmin, longmin],[latmax, longmax]);
map.fitBounds(bounds, {padding: [40,40]});

</script>
</html>



