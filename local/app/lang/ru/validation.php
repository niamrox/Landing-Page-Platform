<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => "Атрибут :attribute должен быть принят.",
	"active_url"           => "Атрибут :attribute не является валидным URL.",
	"after"                => "Атрибут :attribute должен быть датой после :date.",
	"alpha"                => "Атрибут :attribute должен содержать только буквы.",
	"alpha_dash"           => "Атрибут :attribute может содержать только буквы, цифры и знаки тире.",
	"alpha_num"            => "Атрибут :attribute может содержать только буквы и цифры.",
	"array"                => "Атрибут :attribute должен быть массивом.",
	"before"               => "Атрибут :attribute должен быть датой до :date.",
	"between"              => array(
		"numeric" => "Атрибут :attribute должен быть между :min и :max.",
		"file"    => "Атрибут :attribute должен быть между :min и :max килобайт.",
		"string"  => "Атрибут :attribute должен быть между :min и :max символов.",
		"array"   => "Атрибут :attribute должен содержать между :min и :max элементов.",
	),
	"boolean"              => "Поле :attribute должно быть истиным или ложным",
	"confirmed"            => "Подтверждение :attribute не совпадает.",
	"date"                 => "Атрибут :attribute не является корректной датой.",
	"date_format"          => "Атрибут :attribute не соответствует формату :format.",
	"different"            => "Атрибуты :attribute и :other должны различаться.",
	"digits"               => "Атрибут :attribute должен составлять :digits цифр.",
	"digits_between"       => "Атрибут :attribute должен быть между :min и :max цифр.",
	"email"                => ":attribute должен быть валидным адресом электронной почты.",
	"exists"               => "Выбранный :attribute невалидный.",
	"image"                => "Атрибут :attribute должен быть изображением.",
	"in"                   => "Выбранный атрибут :attribute невалиден.",
	"integer"              => ":attribute должен быть целым числом.",
	"ip"                   => ":attribute должен быть валидным IP адресом.",
	"max"                  => array(
		"numeric" => ":attribute не может быть больше чем :max.",
		"file"    => ":attribute не может быть больше :max килобайт.",
		"string"  => ":attribute не может быть больше чем :max символов.",
		"array"   => ":attribute не может содержать более :max элементов.",
	),
	"mimes"                => ":attribute должен быть файлом типа: :values.",
	"min"                  => array(
		"numeric" => ":attribute должен быть как минимум :min.",
		"file"    => ":attribute должен быть как минимум :min килобайт.",
		"string"  => ":attribute должен быть как минимум :min символов.",
		"array"   => ":attribute должен содержать как минимум :min элементов.",
	),
	"not_in"               => "Выбранный атрибут :attribute невалиден.",
	"numeric"              => ":attribute должен быть числом.",
	"regex"                => "Формат :attribute невалиден.",
	"required"             => "Поле :attribute является обязательным.",
	"required_if"          => "Поле :attribute является обязательным когда :other равен :value.",
	"required_with"        => "Поле :attribute является обязательным когда имеются :values.",
	"required_with_all"    => "Поле :attribute является обязательным когда имеются :values.",
	"required_without"     => "Поле :attribute является обязательным когда нет :values.",
	"required_without_all" => "Поле :attribute является обязательным когда нет ни одного из :values.",
	"same"                 => "Атрибуты :attribute и :other должны совпадать.",
	"size"                 => array(
		"numeric" => "Атрибут :attribute должен быть :size.",
		"file"    => "Атрибут :attribute должен быть :size килобайт.",
		"string"  => "Атрибут :attribute должен быть :size символов.",
		"array"   => "Атрибут :attribute должен содержать :size элементов.",
	),
	"unique"               => "Атрибут :attribute уже занят.",
	"url"                  => "Атрибут :attribute имеет невалидный формат.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(
		'attribute-name' => array(
			'rule-name' => 'пользовательское сообщение',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
