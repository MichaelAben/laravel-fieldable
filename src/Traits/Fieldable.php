<?php


namespace MabenDev\Fieldable\Traits;

use MabenDev\Fieldable\Models\Field;
use MabenDev\Fieldable\Models\Value;
use function foo\func;

trait Fieldable
{
    protected $fieldOriginalValues = [];
    protected $fieldValues = [];
    protected $fieldIdList = [];

    public static function addField(string $name, string $type = 'string')
    {
        return Field::findOrCreate(
            __CLASS__,
            $name,
            $type);
    }

    public static function deleteField(string $name)
    {
        $field = Field::where([
            ['fieldable_model', __CLASS__],
            ['name', $name],
        ]);
        if (empty($field)) return true;
        return $field->delete();
    }

    public static function deleteAllFields()
    {
        foreach(Field::all() as $field) {
            if(!$field->delete()) return false;
        }
        return true;
    }

    public function deleteAllValues()
    {
        foreach(Field::where('fieldable_model', __CLASS__)->get() as $field) {
            $field->values()->where('fieldable_id', $this->id)->delete();
        }
    }

    public function setValueReadOnly(string $name)
    {
        $field = Field::find($this->fieldIdList[$name]);
        if(!empty($value = $field->values()->where('fieldable_id', $this->id)->first())) {
            $value->read_only = true;
            $value->save();
        }
    }

    public function loadFields()
    {
        foreach(Field::where('fieldable_model', __CLASS__)->get() as $field) {
            if(!empty($value = $field->values()->where('fieldable_id', $this->id)->first())) {
                $value = $value->value;
            } else {
                $value = null;
            }
            $this->fieldOriginalValues[$field->name] = $value;
            $this->fieldValues[$field->name] = $value;
            $this->fieldIdList[$field->name] = $field->id;
        }
    }

    public function __get($name)
    {
        if(array_key_exists($name, $this->fieldValues)) {
            return $this->fieldValues[$name];
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if(array_key_exists($name, $this->fieldValues)) {
            return $this->fieldValues[$name] = $value;
        }
        return parent::__set($name, $value);
    }

    public function hasAttribute(string $name)
    {
        return array_key_exists($name, $this->attributes);
    }

    protected function saveFields()
    {
        foreach($this->fieldValues as $name => $fieldValue) {
            if($this->fieldOriginalValues[$name] == $fieldValue) continue;

            Value::updateOrCreate([
                'fieldable_id' => $this->id,
                'field_id' => $this->fieldIdList[$name],
            ], [
                'fieldable_id' => $this->id,
                'field_id' => $this->fieldIdList[$name],
                'value' => $fieldValue,
            ]);
        }
    }

    public static function bootFieldable()
    {
        static::saved(function ($model) {
            $model->saveFields();
        });

        static::retrieved(function ($model) {
            $model->loadFields();
        });

        static::deleting(function ($model) {
            $model->deleteAllValues();
        });
    }
}
