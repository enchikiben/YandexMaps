<?php
/**
 * Description of YandexMap
 *
 * @author EnChikiben
 */
class YandexMap extends CWidget
{	
	public $protocol = "http://";
	public $lang = "ru-RU";
	public $load = 'package.full';
	public $key = null;

	public $id = 'yandexmap';
	public $width = 600;
	public $height = 400;
	public $zoom = 7;
	public $center = array("ymaps.geolocation.latitude", "ymaps.geolocation.longitude");
	
	public $zoomControl = true;
	public $typeSelector = true;
	public $mapTools = true;
	public $smallZoomControl = true;
	public $miniMap = true;


	public $placemark = array();
	public $polyline = array();
	
	protected function registerClientScript()
    {
		$cs=Yii::app()->clientScript;

		$url = array();
		$url[] = "load=".$this->load;
		$url[] = "lang=".$this->lang;		

		$cs->registerScriptFile($this->protocol."api-maps.yandex.ru/2.0-stable/?".  implode("&", $url) );

		$jsOptions = array();

		if (is_array($this->center) && !empty($this->center) ) 
			$jsOptions[] = "center:[{$this->center[0]},{$this->center[1]}]";

		if ( $this->zoom ) 
			$jsOptions[] = 'zoom:'.$this->zoom;

		$controls = array();

		if ( $this->zoomControl ) $controls[] = "add('zoomControl')";		
		if ( $this->typeSelector ) $controls[] = "add('typeSelector')";		
		if ( $this->smallZoomControl ) $controls[] = "add('mapTools')";		
		if ( $this->miniMap ) $controls[] = "add('miniMap')";		

		$controls = "map.controls.".implode('.', $controls);

		$placemark = $this->placemarks();		
		$polyline = $this->polylines();		

		$options = implode(",",$jsOptions);
		$js = <<<EQF

ymaps.ready(init);
var map;

function init(){     
	map = new ymaps.Map ("{$this->id}",{
		$options
	});

	$controls
	$placemark
	$polyline
}

EQF;
		
		$cs->registerScript($this->id,$js,CClientScript::POS_HEAD);
    }
	
	protected function is_array(&$array){
		list($key) = array_keys($array);
		return is_array($array[$key]);
	}

	protected function placemarks(){
				
		$placemark = '';
		if (is_array($this->placemark) && !empty($this->placemark) ){
			if ( $this->is_array($this->placemark) ){
				foreach ($this->placemark as $key => $value) {					
					$placemark .= $this->placemark('placemark_'.$key, $value);
				}				
			} else {
				$placemark .= $this->placemark('placemark', $this->placemark);
			}
		}
		
		return $placemark;
	}

	protected function placemark($name,$value){
		
		$placemark = '';
		
		$options = array();
		if ( isset($value['options']) && !empty($value['options']) )
			foreach ($value['options'] as $k=>$v) {
				$options[] = $k.":'".$v."'";
			}
		$placemark .= "var {$name} = new ymaps.Placemark([{$value['lat']},{$value['lon']}],{".implode(',', $options)."},{});";
		$placemark .= "map.geoObjects.add({$name});";

		return $placemark;
	}	
	
	protected function polylines(){
		$polylines = '';
		if ( is_array($this->polyline) && !empty($this->polyline) ){
			
			list($key) = array_keys($this->polyline);
			if ( $this->is_array($this->polyline) && !isset($this->polyline[$key]['lat']) ){
				foreach ($this->polyline as $key => $value)				
					$polylines .= $this->polyline('polyline_'.$key,$value);
			} else {
				$polylines .= $this->polyline('polyline',$this->polyline);
			}
			
		}
		return $polylines;
	}
	
	protected function polyline($name,$value){
		
		$polyline = '';
		$coordinates = array();
		
		foreach ($value as $coordinate) {
			if ( isset($coordinate['lat'],$coordinate['lon']) )
				$coordinates[] = "[".$coordinate['lat'].",".$coordinate['lon']."]";
		}		
		
		$options = array();
		if ( isset($value['options']) && !empty($value['options']) )
			foreach ($value['options'] as $k=>$v) {
				$options[] = $k.":'".$v."'";
			}
			
		$polyline .= "var $name = new ymaps.Polyline([".implode(',', $coordinates)."], {}, {".implode(',', $options)."});";
		$polyline .= "map.geoObjects.add($name);";

		return $polyline;
	}	
	
	public function init()
	{
		$this->registerClientScript();
	}
 
	public function run()
	{
		echo CHtml::tag('div',array(
				'id' => $this->id,
				'style' => "width:{$this->width}px;height:{$this->height}px;"
			));		
	}	
}
?>
