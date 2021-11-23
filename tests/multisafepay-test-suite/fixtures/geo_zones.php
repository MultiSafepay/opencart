<?php

class GeoZones {

    public function getGeoZones() {
        $geo_zones = array(
            'name' => 'Netherlands',
            'description' => 'Netherlands',
            'zone_to_geo_zone' => array(
                array(
                    'country_id' => '150',
                    'zone_id' => '0'
                )
            )
        );
        return $geo_zones;
    }

}