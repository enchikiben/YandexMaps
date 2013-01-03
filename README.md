Yandex Maps
==========

Yii Yandex Maps addition to allowing sozlavat map with labels and lines.

Installation and Setup
---------------------

Place the extension files in a directory 'extensions' of your application. For example, in a folder yandexmap

~~~
'ext.yandexmap.YandexMap'
~~~

Use
-----

~~~
[php]
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
				'strokeWidth'=> 5
			)
		),
	));

~~~

Use Placemark
---

Placemark (labels) - can be passed as a single item or a group of elements.

~~~
[php]
...
		'placemark' => array(
				'lat'=>55.8,
				'lon'=>37.8,
				'options'=>array(
					'balloonContentHeader'=>'header',
					'balloonContentBody'=>'body',
					'balloonContentFooter'=>'footer',
				)
			
		),
...

~~~

group

~~~
[php]
...
		'placemark' => array(
			array(
				'lat'=>55.8,
				'lon'=>37.8,
				'options'=>array(
					'balloonContentHeader'=>'header',
					'balloonContentBody'=>'body',
					'balloonContentFooter'=>'footer',
				)
			),
			array(
				'lat'=>55.8,
				'lon'=>37.8,
				'options'=>array(
					'balloonContentHeader'=>'header',
					'balloonContentBody'=>'body',
					'balloonContentFooter'=>'footer',
				)
			),
		),
...

~~~

Use Polyline
---

Polyline (broken lines) - you can peredevat as one item or as Grumm elements. Tocher coordinates given arrays, and can be infinite.

~~~
[php]
...
	'polyline' => array(		
		array('lat'=>55.80,'lon'=>37.30),
		array('lat'=>55.80,'lon'=>37.40),
		array('lat'=>55.70,'lon'=>37.30),
		array('lat'=>55.70,'lon'=>37.40),
		'options'=>array(
			'strokeWidth'=> 5
		)
	)
...

~~~

group

~~~
[php]
...
	'polyline' => array(
		array(
			array('lat'=>55.80,'lon'=>37.30),
			array('lat'=>55.80,'lon'=>37.40),
			array('lat'=>55.70,'lon'=>37.30),
			array('lat'=>55.70,'lon'=>37.40),
			'options'=>array(
				'strokeWidth'=> 5
			)
		),
		array(
			array('lat'=>55.80,'lon'=>37.30),
			array('lat'=>55.80,'lon'=>37.40),
			array('lat'=>55.70,'lon'=>37.30),
			array('lat'=>55.70,'lon'=>37.40),
			'options'=>array(
				'strokeWidth'=> 5
			)
		),
	),
...

~~~