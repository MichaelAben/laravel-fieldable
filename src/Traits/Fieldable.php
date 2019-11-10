<?php


namespace MabenDev\Fieldable\Traits;

use MabenDev\Fieldable\Models\Field;
use MabenDev\Fieldable\Models\Value;

trait Fieldable
{
    protected $fieldOriginalValues = [];
    protected $fieldValues = [];
    protected $fields = [];

    public static function addField(string $name, string $type)
    {
        $field = Field::create([
            'fielable_model' => __class__,
            'name' => $name,
            'type' => $type,
        ]);
        return $field;
    }

    public static function removeField(string $name)
    {
        $field = Field::where([
            ['fieldable_model', __class__],
            ['name', $name],
        ]);
        if (empty($field)) return true;
        return $field->delete();
    }

    public function deleteAllValues()
    {
        foreach(Field::where('fieldable_model', __CLASS__)->get() as $field) {
            $field->values()->where('fieldable_id', $this->id)->delete();
        }
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->fields)) {
            $field = Field::where([
                ['fieldable_model', __CLASS__],
                ['name', $name],
            ])->first();

            if(empty($field)) return $this->getAttribute($name);
            $this->fields[$name] = $field;
        } else {
            $field = $this->fields[$name];
        }
        $value = $field->values()->where('fieldable_id', $this->id)->first();
        if(!empty($value)) {
            $value = $value->value;
        }

        $this->fieldValues[$field->name] = $value;
        $this->fieldOriginalValues[$field->name] = $value;
        return $this->fieldValues[$field->name];
    }

    public function __set($name, $value)
    {
        if(array_key_exists($name, $this->attributes)) $this->$name = $value;

        if (!array_key_exists($name, $this->fields)) {
            $field = Field::where([
                ['fieldable_model', __CLASS__],
                ['name', $name],
            ])->first();

            if(empty($field)) return $this->$name = $value;
            $this->fields[$field->name] = $field;
        } else {
            $field = $this->fields[$name];
        }

        $val = Value::where([
            ['field_id', $field->id],
            ['fieldable_id', $this->id],
        ])->first();

        if(!empty($val)) {
            $this->fieldOriginalValues[$field->name] = $val->value;
        } else {
            $this->fieldOriginalValues[$field->name] = null;
        }
        $this->fieldValues[$field->name] = $value;
    }

    protected function saveFields()
    {
        foreach($this->fields as $field) {
            if(!array_key_exists($field->name, $this->fieldValues)) continue;

            if(is_null($this->fieldValues[$field->name])) {
                $value = Value::where([
                    ['fieldable_id', $this->id],
                    ['field_id', $field->id],
                ])->first();
                if(!empty($value)) $value->delete();
                continue;
            }

            if($this->fieldValues[$field->name] == $this->fieldOriginalValues[$field->name]) continue;

            Value::updateOrCreate([
                'fieldable_id' => $this->id,
                'field_id' => $field->id,
            ], [
                'fieldable_id' => $this->id,
                'field_id' => $field->id,
                'value' => $this->fieldValues[$field->name],
            ]);
        }
    }

    public static function bootFieldable()
    {
        static::saving(function ($model) {
            $model->saveFields();
        });

        static::deleting(function ($model) {
            $model->deleteAllValues();
        });
    }
}
