function countdown(time,id)
{
	var jetzt;
	var rest;
	var tage, stunden, minuten, sekunden;
	jetzt = new Date();
	rest = Math.floor(time - jetzt/1000);
	var verbleibende_zeit;

	if (rest < -900) {
		verbleibende_zeit = 'Angriff schon vorbei';
	} else if (rest <= 0) {
		rest = 900 + rest;
		minuten = Math.floor(rest/60);
		if (minuten < 10) minuten = '0' + minuten;
		rest %= 60;
		
		sekunden = rest;
		if (sekunden < 10) sekunden = '0' + sekunden;
		verbleibende_zeit = 'Angriff läuft (noch ' + minuten + ':' + sekunden + ')';
	} else if (rest > 0) {
		if (rest >= 86400) {
			tage = Math.floor(rest/86400);
			rest %= 86400;
		}
		
		stunden = Math.floor(rest/3600);
		if (stunden < 10) stunden = '0' + stunden;
		rest %= 3600;
		
		minuten = Math.floor(rest/60);
		if (minuten < 10) minuten = '0' + minuten;
		rest %= 60;
		
		sekunden = rest;
		if (sekunden < 10) sekunden = '0' + sekunden;
		
		if (typeof tage != "undefined") {
			if (tage == 1)
				verbleibende_zeit = tage + ' Tag ' + stunden + ':' + minuten + ':' + sekunden;
			else
				verbleibende_zeit = tage + ' Tage ' + stunden + ':' + minuten + ':' + sekunden;
		} else {
			verbleibende_zeit = stunden + ':' + minuten + ':' + sekunden;
		}
	}
	
	if (document.getElementById) 
		{document.getElementById('countdown' + id).firstChild.data = verbleibende_zeit;}
	else if (document.all) {document.all('countdown' + id).firstChild.data = verbleibende_zeit;}
	else if (document.layers) {document.layers('countdown' + id).firstChild.data = verbleibende_zeit;}
	
	setTimeout("countdown(" + time + ", " + id + ")", 1000);
}