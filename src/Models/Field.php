<?php

namespace MabenDev\Fieldable\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        'fieldable_model',
        'name',
        'type',
    ];

    protected $allowedTypes = [
        'string',
        'integer',
        'boolean',
        'float',
        'file',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('MabenDevFieldable.database.prefix') . 'fields');
        parent::__construct($attributes);

        self::creating(function (Field $field) {
            $field->checkType();
        });
        self::updating(function (Field $field) {
            $field->checkType();
        });
        self::deleting(function (Field $field) {
            $this->values()->delete();
        });
    }

    public function values()
    {
        return $this->hasMany(
            Value::class,
            'field_id');
    }

    public function checkType()
    {
        if(!in_array($this->type, $this->getAllowedTypes())) {
            throw new \Exception('Field type not allowed');
        }
    }

    /**
     * @return array
     */
    public function getAllowedTypes(): array
    {
        return $this->allowedTypes;
    }

    /**
     * @param array $allowedTypes
     */
    public function setAllowedTypes(array $allowedTypes): void
    {
        $this->allowedTypes = $allowedTypes;
    }

    /**
     * @param  string  $model
     * @param  string  $name
     * @param  string  $type
     *
     * @return mixed
     */
    public static function findOrCreate(string $model, string $name, string $type = 'string')
    {
        $field = Field::where([
            'fieldable_model' => $model,
            'name' => $name,
            ])->first();
        if(!empty($field)) return $field;

        $field = Field::create([
            'fieldable_model' => $model,
            'name' => $name,
            'type' => $type,
        ]);

        return $field;
    }
}
