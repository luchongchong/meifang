<?php
class earth{	
	//获取距离
	public function getDistance($lng1, $lat1, $lng2, $lat2)
	{
		
		$earthRadius = 6367000; //approximate radius of earth in meters
		
		$lat1 = ($lat1 * pi() ) / 180;
		$lng1 = ($lng1 * pi() ) / 180;
		$lat2 = ($lat2 * pi() ) / 180;
		$lng2 = ($lng2 * pi() ) / 180;
		
		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
		$calculatedDistance = $earthRadius * $stepTwo;
		return round($calculatedDistance);
	}
	
	//计算最大/最小经纬度
	public function getMNlnByDis($myLng, $myLat, $distance){
		$range = 180 / pi() * ($distance/1000) / 6372.797; 
		$lngR = $range / cos($myLat * pi() / 180);
		$result['maxLat'] = $myLat + $range;//最大纬度
		$result['minLat'] = $myLat - $range;//最小纬度
		$result['maxLng'] = $myLng + $lngR;//最大经度
		$result['minLng'] = $myLng - $lngR;//最小经度
		
		return $result;
	}
}