<?php namespace LukeTowers\APSS\Models;

use Model;
use October\Rain\Network\Http;
use RainLab\Location\Models\Setting;

/**
 * NeedleReport Model
 */
class NeedleReport extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'luketowers_apss_needle_reports';

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = ['data'];

    /**
     * @var array Relations
     */
    public $attachOne = [
        'photo' => [\System\Models\File::class],
    ];

    public function getLatitudeAttribute()
    {
        return @$this->data['location']['lat'];
    }

    public function getLongitudeAttribute()
    {
        return @$this->data['location']['lng'];
    }

    public function getDirectionsUrl()
    {
        $lat = $this->latitude;
        $lng = $this->longitude;

        return "https://www.google.com/maps/dir/?api=1&destination=$lat,$lng";
    }

    public function beforeCreate()
    {
        $request = request();
        $this->ip_address = $request->ip();
        $this->data = array_merge($this->data, ['user_agent' => $request->header('User-Agent')]);

        $lat = @$this->data['location']['lat'];
        $lng = @$this->data['location']['lng'];
        $city = '';
        $province = '';

        try {
            $key = Setting::get('google_maps_key');
            $request = Http::get("https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=$key");
            $result = json_decode($request->body);

            if (!empty($result->results)) {
                foreach ($result->results as $result) {
                    foreach ($result->address_components as $component) {
                        if (in_array('locality', $component->types)) {
                            $city = $component->long_name;
                        }
                        if (in_array('administrative_area_level_1', $component->types)) {
                            $province = $component->long_name;
                        }

                        if (!empty($city) && !empty($province)) {
                            break 2;
                        }
                    }
                }
            }
        } catch (\Exception $ex) {
            throw $ex;
        }

        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->city = $city;
        $this->province = $province;
    }

    public function getCityOptions()
    {
        $options = static::lists('city');
        return array_combine($options, $options);
    }

    public function getProvinceOptions()
    {
        $options = static::lists('province');
        return array_combine($options, $options);
    }
}
