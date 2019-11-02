<?php namespace LukeTowers\APSS\Models;

use Model;

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
        return $this->data['use_map'] ? @$this->data['map_location']['lat'] : @$this->data['location']['lat'];
    }

    public function getLongitudeAttribute()
    {
        return $this->data['use_map'] ? @$this->data['map_location']['lng'] : @$this->data['location']['lng'];
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
    }
}
