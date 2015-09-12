<?php
$url = 'http://api.openweathermap.org/data/2.5/forecast?q=cyberjaya,my&mode=json&units=metric&cnt=7';
$json = file_get_contents($url);
?>
<!DOCTYPE>
<html>
<head>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/knockout/3.3.0/knockout-min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js'></script>
	<script>
	var data = <?=$json?>;
	</script>
</head>
<body><table>
		<tbody data-bind='foreach:forecasts'>
			<tr>
				<td bgcolor="#f4faff" width="273" valign="top">
					<font color="#008000" size="2"><strong data-bind='text:dt'></strong></font>
				</td>
			</tr>
			<tr>
				<td width="501" bgcolor="#f4fbff">Pagi : <span data-bind='text:m'></span> </td>
			</tr>
			<tr>
				<td  bgcolor="#f4fbff"> Petang : <span data-bind='text:e'></span>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f4fbff"> Malam : <span data-bind='text:n'></span></td>
			</tr>
			<tr>
				<td bgcolor="#f4fbff"> Minimum : <span data-bind='text:min'></span></td>
			</tr>
			<tr>
				<td bgcolor="#f4fbff"> Maksimum : <span data-bind='text:max'></span></td>
			</tr>
			<tr>
				<td bgcolor="#f4fbff">&nbsp;</td>
			</tr>
			</tbody>
	</table>

	<script>
	var getDateTime = function (x) {
			var g = null; //return g
			var m = moment(x);
			if(!m || !m.isValid()) { return; } //if we can't find a valid or filled moment, we return.

			var malam = 18, petang = 12, pagi = 4;
			var cur = parseFloat(m.format("HH"));
			if (cur === 0){
				cur = 24;
			}
			if(cur >= pagi && cur < petang) {
				g = 0; // "pagi";
			}else if(cur >= petang && cur <= malam) {
				g = 1; // "petang";
			} else {
				g = 2//"malam";
			}
			return g;
	}, ViewModel = new function(){
		var self = this;
		self.forecasts = ko.observableArray();
	};
	ko.applyBindings(ViewModel);
	$(document).ready(function(){
		var viewData  = [];
		for(var k=0;k<data.list.length;k++){
			var v = data.list[k],s =v.dt_txt.split(" "),d = s[0],vdk = '';
			switch (getDateTime(v.dt_txt)){
				case 1:
					vdk = 'e';
				break;
				case 2:
					vdk = 'n';
				break;
				default:
					vdk = 'm';
				break;
			}
			if(typeof(viewData[d]) === 'undefined'){
				viewData[d] = [];
			}
			if(typeof(viewData[d].max) === 'undefined' || typeof(viewData[d].min) === 'undefined'){
				viewData[d].min = viewData[d].max = 0;
			}
			if(viewData[d].max < v.main.temp_max){
				viewData[d].max = v.main.temp_max;
			}
			if(viewData[d].min > v.main.temp_min || viewData[d].min == 0){
				viewData[d].min = v.main.temp_min;
			}
			viewData[d][vdk] = v.weather[0].main;
		}

		for (var i in viewData) {
			var v  = viewData[i];
			v.m = v.m || '-';
			v.e = v.e || '-';
			v.n = v.n || '-';
			v.dt = i;
			v.max+=" °C";
			v.min+=" °C";
			ViewModel.forecasts.push(v);
		}
	});

	</script>
</body>
</html>
