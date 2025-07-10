<?

//https://www.gaplus.net:452/bin/sub/menu6/test_test.php

$curl = curl_init();

curl_setopt_array($curl, array(
		CURLOPT_URL => 'www.ibss-b.co.kr/car/gas_car.php',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => 'caruse=caruse&jumin=jumin&idate=idate&idate_to=idate_to&carcode=carcode&cargrade=cargrade&baegicc=baegicc&caryear=caryear&car_kind=car_kind&people_numcc=people_numcc&ext_bupum=ext_bupum&add_bupum=add_bupum&add_bupumprice=add_bupumprice&carprice1=carprice1&carprice=carprice&carname=carname&guipcarrer=guipcarrer&traffic=traffic&halin=halin&special_code=special_code&special_code1=special_code1&ncr_code=ncr_code&ncr_code2=ncr_code2&ss_point=ss_point&ss_point3=ss_point3&car_guip=car_guip&car_own=car_own&buy_type=buy_type&child_halin=child_halin&eco_mileage=eco_mileage&tmap_halin=tmap_halin&car_own_halin=car_own_halin&careercode3=careercode3&lawcodecnt=lawcodecnt&lowestJumin=lowestJumin&jjumin=jjumin&c_jumin=c_jumin&ijumin=ijumin&fetus=fetus&divide_num=divide_num&dambo2=dambo2&dambo3=dambo3&dambo4=dambo4&dambo5=dambo5&dambo6=dambo6&goout1=goout1&carfamily=carfamily&carage=carage&muljuk=muljuk&MileGbn=MileGbn&MileKm=MileKm&fuel=fuel&hi_repair=hi_repair&religionchk=religionchk&otheracc=otheracc&agent=sinit_samsung&ret_url=www.hairinfo.kr%3A443%2Fhairinfo_sin_web_new%2Fsub%2Fmenu02%2Ftesttest2.php&j_kind=j_kind&cardate=cardate',
		CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded'
		),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>