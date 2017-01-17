<?php

class SiteTypeTableSeeder extends Seeder {

    public function run()
    {
        DB::table('site_types')->delete();

        \Web\Model\SiteType::create(array(
            'sort' => 10,
            'name' => 'business_services',
            'icon' => '<svg version="1.1"
	 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
	 x="0px" y="0px" width="22px" height="47px" viewBox="0 0 22 47" style="enable-background:new 0 0 22 47;" xml:space="preserve">
<style type="text/css">
	.st0{fill:none;stroke:#000000;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;}
</style>
<defs>
</defs>
<g>
	<line class="st0" x1="1" y1="46" x2="21" y2="46"/>
	<rect x="4" y="6" class="st0" width="14" height="40"/>
	<line class="st0" x1="8" y1="5" x2="8" y2="1"/>
	<line class="st0" x1="14" y1="5" x2="14" y2="3"/>
	<line class="st0" x1="7" y1="12" x2="9" y2="12"/>
	<line class="st0" x1="13" y1="12" x2="15" y2="12"/>
	<line class="st0" x1="7" y1="18" x2="9" y2="18"/>
	<line class="st0" x1="13" y1="18" x2="15" y2="18"/>
	<line class="st0" x1="7" y1="24" x2="9" y2="24"/>
	<line class="st0" x1="13" y1="24" x2="15" y2="24"/>
	<line class="st0" x1="7" y1="30" x2="9" y2="30"/>
	<line class="st0" x1="13" y1="30" x2="15" y2="30"/>
	<line class="st0" x1="7" y1="36" x2="9" y2="36"/>
	<line class="st0" x1="13" y1="36" x2="15" y2="36"/>
	<line class="st0" x1="7" y1="42" x2="9" y2="42"/>
	<line class="st0" x1="13" y1="42" x2="15" y2="42"/>
</g>
</svg>',
			'icon_width' => 22
        ));

        \Web\Model\SiteType::create(array(
            'sort' => 20,
            'name' => 'food_drinks',
            'icon' => '<svg version="1.1"
	 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
	 x="0px" y="0px" width="68px" height="46px" viewBox="0 0 68 46" style="enable-background:new 0 0 68 46;" xml:space="preserve">
<style type="text/css">
	.st0{fill:none;stroke:#000000;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;}
</style>
<defs>
</defs>
<g>
	<g>
		<g>
			<path class="st0 stroke-0" d="M32.4,2.6c11,0,20,9,20,20s-9,20-20,20s-20-9-20-20S21.4,2.6,32.4,2.6 M32.4,0.6c-12.2,0-22,9.9-22,22
				c0,12.2,9.9,22,22,22s22-9.8,22-22C54.4,10.4,44.5,0.6,32.4,0.6L32.4,0.6z"/>
		</g>
		<g>
			<path class="st0 stroke-0" d="M32.4,9.9c7,0,12.7,5.7,12.7,12.7c0,7-5.7,12.7-12.7,12.7s-12.7-5.7-12.7-12.7C19.7,15.6,25.4,9.9,32.4,9.9 M32.4,7.9
				c-8.1,0-14.7,6.6-14.7,14.7s6.6,14.7,14.7,14.7s14.7-6.6,14.7-14.7S40.5,7.9,32.4,7.9L32.4,7.9z"/>
		</g>
	</g>
	<g>
		<line class="st0" x1="7" y1="45" x2="7" y2="5"/>
		<path class="st0" d="M7,9V3.6C7,2.1,5.5,1,4,1C2.5,1,1,2.3,1,3.8V21"/>
		<path class="st0" d="M7,21.8c0,1.5-1.4,2.8-2.9,2.8c-1.5,0-3.1-1.4-3.1-2.9V19"/>
	</g>
	<g>
		<line class="st0" x1="61" y1="45" x2="61" y2="1"/>
		<path class="st0" d="M67,1v7.4c0,3.8-2.2,6.8-6,6.8c-3.8,0-6-3.1-6-6.8V1"/>
	</g>
</g>
</svg>',
			'icon_width' => 68
        ));

        \Web\Model\SiteType::create(array(
            'sort' => 30,
            'name' => 'digital_tech',
            'icon' => '<svg version="1.1"
	 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
	 x="0px" y="0px" width="52px" height="42px" viewBox="0 0 52 42" style="enable-background:new 0 0 52 42;" xml:space="preserve">
<style type="text/css">
	.st0{fill:none;stroke:#000000;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;}
</style>
<defs>
</defs>
<g>
	<g>
		<g>
			<path class="st0" d="M51,39.4c0,0.9-0.7,1.6-1.6,1.6h-8.9c-0.9,0-1.6-0.7-1.6-1.6V18.6c0-0.9,0.7-1.6,1.6-1.6h8.9
				c0.9,0,1.6,0.7,1.6,1.6V39.4z"/>
			<line class="st0" x1="40" y1="21" x2="50" y2="21"/>
			<line class="st0" x1="40" y1="37" x2="50" y2="37"/>
		</g>
		<path class="st0" d="M38,33H3.9C2.4,33,1,31.5,1,30.1V3.2C1,1.8,2.4,1,3.9,1h40.9C46.2,1,47,1.8,47,3.2V16"/>
		<line class="st0" x1="2" y1="5" x2="46" y2="5"/>
		<line class="st0" x1="2" y1="27" x2="38" y2="27"/>
		<polygon class="st0" points="30.1,41 18.2,41 19.9,33 28.4,33 		"/>
	</g>
	<line class="st0" x1="16" y1="41" x2="32" y2="41"/>
</g>
</svg>',
			'icon_width' => 52
        ));

        \Web\Model\SiteType::create(array(
            'sort' => 40,
            'name' => 'hotel_travel',
            'icon' => '<svg version="1.1"
	 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
	 x="0px" y="0px" width="46px" height="49px" viewBox="0 0 46 49" style="enable-background:new 0 0 46 49;" xml:space="preserve">
<style type="text/css">
	.st0{fill:none;stroke:#000000;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;}
</style>
<defs>
</defs>
<g>
	<path class="st0" d="M45,30v-4l-18-9.9V5c0-2.2-1.8-4-4-4c-2.2,0-4,1.8-4,4v11.1L1,26v4l18-4v12l-8,8.1V48l12-4l12,4v-1.9L27,38V26
		L45,30z"/>
</g>
</svg>',
			'icon_width' => 46
        ));

        \Web\Model\SiteType::create(array(
            'sort' => 50,
            'name' => 'personal',
            'icon' => '<svg version="1.1"
	 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
	 x="0px" y="0px" width="45.8px" height="48px" viewBox="0 0 45.8 48" style="enable-background:new 0 0 45.8 48;"
	 xml:space="preserve">
<style type="text/css">
	.st0{fill:none;stroke:#000000;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;}
</style>
<defs>
</defs>
<g>
	<path class="st0" d="M5.7,9.6C9.7,4.4,16,1,22.9,1c12,0,21.7,9.6,21.7,21.3l0,0.6c0.1,2.9,0.3,7.8-0.9,13.8"/>
	<path class="st0" d="M1,26.5v-4c0-3.4,0.9-6.8,2.4-9.7"/>
	<path class="st0" d="M29.7,6.6c-2.1-0.9-4.6-1.3-7-1.3C13.2,5.3,5,13.2,5,22.5v4.2c0,1.8-1.2,3.2-3,3.2"/>
	<path class="st0" d="M37.7,42.8c3.1-8.4,2.8-16,2.7-19.7c0-0.3,0-0.6,0-0.8c0-5.4-2.6-10.2-6.6-13.4"/>
	<path class="st0" d="M17.2,10.9c1.8-0.9,3.7-1.4,5.8-1.4c7.2,0,13.1,5.7,13.1,12.8c0,0.3,0,0.6,0,0.9c0.1,4,0.5,12.8-4.2,22"/>
	<path class="st0" d="M9.8,22.5c0-3.7,1.7-7.2,4.4-9.5"/>
	<path class="st0" d="M2.3,34.4l1.9-0.5c4.2-1.2,5.6-3,5.6-7.1"/>
	<path class="st0" d="M31.9,25.9c0,4.9-0.6,13.1-6,21.1"/>
	<path class="st0" d="M4.8,38c6-1.8,8.2-5.2,8.2-11.2v-4.2c0-4.7,4.6-8.6,9.4-8.6c4.9,0,8.9,3.8,8.9,8.5"/>
	<path class="st0" d="M26.5,35.5c-1,3.6-2.7,7.6-5.8,11.4"/>
	<path class="st0" d="M8.4,41.6c8.2-3,10.6-9,10.6-14.8c0-1.1,0-4.2,0-4.2c0-2.4,1.9-4.3,4.2-4.3c2.4,0,4.4,1.8,4.4,4.2
		c0,1.1,0.2,3.1,0.1,5.6"/>
	<path class="st0" d="M20.8,37.9c1.6-3,2.2-6.5,2.2-10.4v-3.1"/>
	<path class="st0" d="M13.7,45c1.8-1.1,3.4-2.4,4.6-3.8"/>
</g>
</svg>',
			'icon_width' => 46
        ));
    }
}