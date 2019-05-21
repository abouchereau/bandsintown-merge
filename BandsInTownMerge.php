<?php



class BandsInTownMerge {
	
	const URL_API = 'https://rest.bandsintown.com/artists/__BAND__/events?app_id=__APP_ID__';
	private $cacheLength = 60;//nb minutes
	private $cacheFile = "dates.json";
	private $appId;
	private $bands = [];
	
	public function __construct($appId=null) {
		$this->appId = $appId;
	}
	
	public function setCacheFile($cacheFile) {
		$this->cacheFile = $cacheFile;
	}
	
	public function addBand($id,$displayName) {
		$this->bands[$id] = $displayName;
	}
	
	public function getDates() {
		$lastUpdate = filemtime(__DIR__.'/'.$this->cacheFile);
		$mustReloadCache = (time()-$lastUpdate) > ($this->cacheLength*60);		
		if($mustReloadCache) {
			$this->reloadCache();
		}
		return json_decode(file_get_contents($this->cacheFile),1);		
	}
	
	public function reloadCache() {		
		$taball = [];
		foreach($this->bands as $key => $band) {		
			$url = str_replace('__BAND__',$key, self::URL_API);
			$url = str_replace('__APP_ID__',$this->appId,$url);
			$t = file_get_contents($url);
			$tab = json_decode($t,1);
			$taball = array_merge($taball,json_decode($t,1));			
		}

		$dates = [];
		foreach($taball as $tab) {
			$dc = new DateCustom($tab['datetime']);
			$dates[] = [
				$tab['datetime'],
				$bands[$tab['lineup'][0]],
				$tab['venue']['name'],
				$tab['venue']['city'],
				$tab['venue']['latitude'],
				$tab['venue']['longitude'],
				$tab['url'],
				$dc->getJourSem(),
				$dc->getJour(),
				$dc->getMois(),
				$dc->getAnnee()
				];
		}
		usort($dates,function($a,$b) {return $a[0] > $b[0];});
		file_put_contents(__DIR__.'/'.$this->cacheFile,json_encode($dates));
	}
}




class DateCustom {
	
	private $jours = ['Dim.','Lun.','Mar.','Mer.','Jeu.','Ven.','Sam.'];
	private $mois = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
	private $dt = null;
	
	public function __construct($txt) {
		$this->dt = \DateTime::createFromFormat('Y-m-d',substr($txt,0,10));
	}
	
	public function getJour() {
		return $this->dt->format('d');
	}
	
	public function getJourSem() {
		return $this->jours[$this->dt->format('w')*1];
	}
	
	public function getMois() {
		return $this->mois[$this->dt->format('m')*1];
	}
	
	public function getAnnee() {
		return $this->dt->format('Y');
	}
	
}
