# Laravel Fieldables 
This is a packages that makes it possible to add custom fields to a model, by adding a trait to it.

## Install 
To install this package you need to do a few steps: 
- `composer require maben-dev/laravel-Fieldable`
- `php artisan vendor:publish --provider="MabenDev\Fieldable\FieldableProvider"`
- `php artisan migrate`

That's it your done!

## How to use
It's really easy, add some fields `myModel::addField('my_field', 'string');`.

Than you can use these fields like a normal variable on the class: \
set: `$myModel->my_field = 'my value';`.\
get: `$myModel->my_field;`.

## Important notes
You need to give a valid type when you create a field, valid types are:
- string
- integer
- boolean
- float
- file

These types make it easy for you to know how to handle the fields it self, in the future i plan to make them force the value to the type the field is.

NEVER change the database prefix value in the config after you have migrated, if you do the tables cannot be found anymore and the models won't work.

If you have a requests, please contact me at m.aben@live.nl

If you want to help or you have a great idea, please feel free to make pull requests. Much appreciated. 
