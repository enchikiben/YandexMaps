yandexmaps
==========

Yii Yandex Maps extension

[code]
	$this->widget('ext.yandexmap.YandexMap',array(
		'id'=>'map',
		'width'=>600,
		'height'=>400,
		'center'=>array(55.76, 37.64),
		'placemark' => array(
			array(
				'lat'=>55.8,
				'lon'=>37.8,
				'options'=>array(
					'balloonContentHeader'=>'header',
					'balloonContentBody'=>'body',
					'balloonContentFooter'=>'footer',
				)
			)
		),
		'polyline' => array(
			array('lat'=>55.80,'lon'=>37.30),
			array('lat'=>55.80,'lon'=>37.40),
            array('lat'=>55.70,'lon'=>37.30),
            array('lat'=>55.70,'lon'=>37.40),
			'options'=>array(
				'strokeWidth'=> 5 // ширина линии'
			)
		),
	));
[/code]