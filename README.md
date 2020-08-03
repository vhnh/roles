![VHNH](https://avatars3.githubusercontent.com/u/66573047?s=200)

# vhnh/roles
Add inheritance to Eloquent models by using the same table.

![tests](https://github.com/vhnh/roles/workflows/tests/badge.svg)

## Database Setup

Our child models referencing the same table as its parent, so we must store the type.

```php
Schema::create('users', function (Blueprint $table) {
    $table->string('type', 16);
    // ...
});
```

## The parent

The base model provides an `$availableRoles` property. Here we'll register all available child models. Laravel guesses the referencing table from the class name – but our model class depends on its type so we have to be explicit with the `$table`.

The `Vhnh\Roles\HasRoles` trait uses mass assignment, so we must allow for that by adding the `type` attribute to our `$fillable` fields.
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Vhnh\Roles\HasRoles;

class User extends Model
{
    use HasRoles;

    protected $availableRoles = [
        'admin' => Admin::class,
    ];

    protected $fillable = [
        'type',
    ];

    protected $table = 'users';

    // ...
}
```

## The child

The `Vhnh\Roles\HasRoles` trait does not rely on `ReflectionClass` instead we add a `static $role` property to resolve the correct model instance. 

```php
<?php

namespace App;

class Admin extends User
{
    protected static $role = 'admin';

    // ...
}
```

## License
The Vhnh Role package is open-sourced software licensed under the [MIT](http://opensource.org/licenses/MIT) license.