<?php
/**
 * @author EnChikiben
 */
class YandexMap extends CWidget
{
	private $cs = null;

	public $htmlOptions = array();

	public $protocol = "http://";
	public $lang = "ru-RU";
	public $load = 'package.full';
	public $key = null;
	public $coordorder = 'latlong';
	public $mode = 'release';


	public $id = 'yandexmap';
	public $width = 600;
	public $height = 400;
	public $zoom = 10;
	public $center = array("ymaps.geolocation.latitude", "ymaps.geolocation.longitude");
        public $behaviors = array('default', 'scrollZoom');
	public $options = array();
        public $clustering = true;
	public $controls = array(
		'zoomControl' => true,
		'typeSelector' => true,
		'mapTools' => true,
		'smallZoomControl' => false,
		'miniMap' => true,
		'scaleLine' => true,
		'searchControl' => true,
		'trafficControl' => true
	);


	public $placemark = array();
	public $polyline = array();
        public $route = array();

	protected function connectYandexJsFile(){
		$url = array();
		$url[] = "load=".$this->load;
		$url[] = "lang=".$this->lang;

		$this->cs->registerScriptFile($this->protocol."api-maps.yandex.ru/2.0-stable/?".  implode("&", $url) );
	}

	protected function initMapJsObject(){
		$state = array();

		if (is_array($this->center) && !empty($this->center) )
			$state[] = "center:[{$this->center[0]},{$this->center[1]}]";
		else
			throw new Exception('Error center map coordinat.');

		if ( $this->zoom )
			$state[] = 'zoom:'.$this->zoom;
                foreach ($this->behaviors as $key => $value) {
                    $this->behaviors[$key]="'".$value."'";
                }
                $beh=implode(",",$this->behaviors);
                $state[]='behaviors:['.$beh.']';

		$state = implode(",",$state);

		$options = $this->generateOptions($this->options);
		return "map = new ymaps.Map ('{$this->id}',{".$state."},{".$options."});";
	}

	protected function initMapControl(){
		$controls = array();

		if ( is_array($this->controls) && !empty($this->controls) )
			foreach ($this->controls as $key => $value ) {
				if ( $value )
					$controls[] = "add(".CJavaScript::encode($key).")";
			}

		return "map.controls.".implode('.', $controls).';';
	}


	protected function registerClientScript(){

		$this->connectYandexJsFile();

		$map = $this->initMapJsObject();
		$control = $this->initMapControl();
                
		$placemark = $this->placemarks();
		$polyline = $this->polylines();
                $route = $this->route();
		$js = <<<EQF

ymaps.ready(function(){\n 
	$map\n 
	$control\n 
        $route\n 
	$placemark
	$polyline\n         
});\n 

EQF;

		$this->cs->registerScript($this->id,$js,CClientScript::POS_END);
	}

	protected function is_array(&$array){
		list($key) = array_keys($array);
		return is_array($array[$key]);
	}

	protected function generateOptions(&$array){
		$options = array();

		if ( !empty($array) && is_array($array) )
			foreach ($array as $key => $value ) {
				$options[] = CJavaScript::encode($key).":".CJavaScript::encode($value)."";
			}
		else
			return null;

		return implode(',', $options);
	}


	protected function placemarks(){
            if ($this->clustering){
		$placemark = 'var myGeoObjects = [];';
		if (is_array($this->placemark) && !empty($this->placemark) ){
			if ( $this->is_array($this->placemark) ){
				foreach ($this->placemark as $key => $value) {
					$placemark .= $this->placemark($key, $value);
				}
			} else {
				$placemark .= $this->placemark('placemark', $this->placemark);
			}
		}
                $placemark.="clusterer = new ymaps.Clusterer({clusterDisableClickZoom: true, gridSize:96});\n
                            clusterer.add(myGeoObjects);\n
                            map.geoObjects.add(clusterer);\n";
            }
            else{
                $placemark = '';
                if (is_array($this->placemark) && !empty($this->placemark) ){
			if ( $this->is_array($this->placemark) ){
				foreach ($this->placemark as $key => $value) {
					$placemark .= $this->placemark("placemark_".$key, $value);
				}
			} else {
				$placemark .= $this->placemark('placemark', $this->placemark);
			}
		}
            }
		return $placemark;
	}

	protected function placemark($name,&$value){

		if ( !isset($value['lat'],$value['lon']) ) return;

		$placemark = '';

		$properties = '';
		if ( isset($value['properties']) ){
			$properties = $this->generateOptions($value['properties']);
		}

		$options = '';
		if ( isset($value['options']) ){
			$options = $this->generateOptions($value['options']);
		}
                if ($this->clustering){
                    $placemark .= "myGeoObjects[{$name}] = new ymaps.Placemark([{$value['lat']},{$value['lon']}],{".$properties."},{".$options."}); ";
                }
                else{
                    $placemark .= "var {$name} = new ymaps.Placemark([{$value['lat']},{$value['lon']}],{".$properties."},{".$options."}); ";
                    $placemark .= "map.geoObjects.add({$name}); ";
                }

		return $placemark;
	}
        protected function route(){

		if ($this->route["from"]=="") return;

		$route = '';
                $address2 = CJavaScript::encode($this->route["from"]);
		$address1 = CJavaScript::encode($this->route["to"]);
                $success=CJavaScript::encode($this->route["success"]);
		$route.= "\n ymaps.route([".$address2.",".$address1."]).then(function (route) {\n 
                            map.geoObjects.add(route);\n 
                            var points = route.getWayPoints();\n 
                            lastPoint = points.getLength() - 1;\n 
                            $success
                            points.options.set('preset', 'twirl#redStretchyIcon');
                            // Задаем контент меток в начальной и конечной точках.
                            points.get(0).properties.set('iconContent', 'Точка отправления');
                            points.get(lastPoint).properties.set('iconContent', 'Точка прибытия');
                            map.panTo(points.get(lastPoint).geometry.getCoordinates());\n 
                         }, function (error) {\n 
                            alert('Призошла ошибка: ' + error.message);\n 
                        });\n ";

		return $route;
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

	protected function polyline($name,&$value){

		$polyline = '';

		$coordinates = array();
		foreach ($value as $coordinate) {
			if ( isset($coordinate['lat'],$coordinate['lon']) )
				$coordinates[] = "[".$coordinate['lat'].",".$coordinate['lon']."]";
		}

		if ( empty($coordinates) ) return;

		$properties = '';
		if ( isset($value['properties']) ){
			$properties = $this->generateOptions($value['properties']);
		}

		$options = '';
		if ( isset($value['options']) ){
			$options = $this->generateOptions($value['options']);
		}

		$polyline .= "var $name = new ymaps.Polyline([".implode(',', $coordinates)."], {".$properties."}, {".$options."});\n";
		$polyline .= "map.geoObjects.add($name);\n";

		return $polyline;
	}


	public function init(){
		$this->cs = Yii::app()->clientScript;

		$this->registerClientScript();
	}

	public function run(){

		$this->htmlOptions['id'] = $this->id;

		if ( !isset($this->htmlOptions['style']) )
			$this->htmlOptions['style'] = "width:{$this->width}px;height:{$this->height}px;";

		echo CHtml::tag('div',$this->htmlOptions,'');
	}
}
?>
