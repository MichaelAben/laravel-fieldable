<?php


namespace MabenDev\Fieldable\Models;


use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    protected $fillable = [
        'field_id',
        'fieldable_id',
        'value',
        'read_only',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('MabenDevFieldable.database.prefix') . 'field_values');
        parent::__construct($attributes);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
