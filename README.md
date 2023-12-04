
<div align="center">
<h1>Horizom DTO</h1>

Data Transfer Objects for all PHP applications.
</div>

<p align="center">
<a href="https://packagist.org/packages/horizom/dto"><img src="https://poser.pugx.org/horizom/dto/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/horizom/dto"><img src="https://poser.pugx.org/horizom/dto/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/horizom/dto"><img src="https://poser.pugx.org/horizom/dto/license.svg" alt="License"></a>
</p>

Data Transfer Objects (DTOs) are objects that are used to transfer data between systems. DTOs are typically used in applications to provide a simple, consistent format for transferring data between different parts of the application, such as between the user interface and the business logic.

## Installation

```bash
composer require horizom/dto
```

## Usage

### Defining DTO Properties

```php
use Horizom\DTO\DTO;

class UserDTO extends DTO
{
    public string $name;

    public string $email;

    public string $password;
}
```

### Creating DTO Instances

You can create a `DTO` instance on many ways:

#### From array

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);
```

You can also use the `fromArray` static method:

```php
$dto = UserDTO::fromArray([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);
```

#### From JSON strings

```php
$dto = UserDTO::fromJson('{"name": "John Doe", "email": "john.doe@example.com", "password": "s3CreT!@1a2B"}');
```

### Accessing DTO Data

After you create your `DTO` instance, you can access any properties like an `object`:

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);

$dto->name; // 'John Doe'
$dto->email; // 'john.doe@example.com'
$dto->password; // 's3CreT!@1a2B'
```

### Casting DTO Properties

You can cast your `DTO` properties to some types:

```php
use Carbon\Carbon;
use Horizom\DTO\DTO;
use DateTimeImmutable;

class UserDTO extends DTO
{
    public string $id;

    public string $name;

    public string $email;

    public string $password;

    public Carbon $created_at;

    public DateTimeImmutable $updated_at;

    public array $roles;

    protected function casts()
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'password' => 'string',
            'created_at' => Carbon::class,
            'updated_at' => DateTimeImmutable::class,
            'roles' => 'array',
        ];
    }
}
```

### Defining Default Values

Sometimes we can have properties that are optional and that can have default values. You can define the default values for your `DTO` properties in the `defaults` function:

```php
use Horizom\DTO\DTO;
use Illuminate\Support\Str;

class UserDTO extends DTO
{
    // ...

    protected function defaults()
    {
        return [
            'username' => Str::slug($this->name),
        ];
    }
}
```

With the `DTO` definition above you could run:

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);

$dto->username; // 'john_doe'
```

### Transforming DTO Data

You can convert your DTO to some formats:

#### To array

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
]);

$dto->toArray();
// [
//     "name" => "John Doe",
//     "email" => "john.doe@example.com",
//     "password" => "s3CreT!@1a2B",
// ]
```

#### To JSON string

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
]);

$dto->toJson();
// '{"name":"John Doe","email":"john.doe@example.com","password":"s3CreT!@1a2B"}'
```

### Create Your Own Type Cast

#### Castable classes

You can easily create new `Castable` types for your project by implementing the `Horizom\DTO\Casting\Castable` interface. This interface has a single method that must be implemented:

```php
public function cast(string $property, mixed $value): mixed;
```

Let's say that you have a `URLWrapper` class in your project, and you want that when passing a URL into your `DTO` it will always return a `URLWrapper` instance instead of a simple string:

```php
use Horizom\DTO\Casting\Castable;

class URLCast implements Castable
{
    public function cast(string $property, mixed $value): URLWrapper
    {
        return new URLWrapper($value);
    }
}
```

Then you could apply this to your DTO:

```php
use Horizom\DTO\DTO;

class CustomDTO extends DTO
{
    protected function casts()
    {
        return [
            'url' => new URLCast(),
        ];
    }

    protected function defaults()
    {
        return [];
    }
}
```

#### Callable casts

You can also create new Castable types for your project by using a callable/callback:

```php
use Horizom\DTO\DTO;

class CustomDTO extends DTO
{
    protected function casts(): array
    {
        return [
            'url' => function (string $property, mixed $value) {
                return new URLWrapper($value);
            },
        ];
    }

    protected function defaults(): array
    {
        return [];
    }
}
```

Or you can use a static method:

```php
use Horizom\DTO\Casting\Cast;
use Horizom\DTO\DTO;

class CustomDTO extends DTO
{
    protected function casts()
    {
        return [
            'url' => Cast::make(
                function (string $property, mixed $value) {
                    return new URLWrapper($value);
                },
                function (string $property, URLWrapper $value) {
                    return $value->toString();
                }
            )
        ];
    }

    protected function defaults()
    {
        return [];
    }
}
```

### Case of possibility of extending with Laravel

You can extend the `DTO` class to create your own `DTO` class with some custom methods:

```php
use App\Http\Resources\UserResource;
use Horizom\DTO\DTO;
use Illuminate\Database\Eloquent\Model;

class UserDTO extends DTO
{
    public int $id;

    public string $name;

    public string $email;

    public string $password;

    public Carbon $created_at;

    public CarbonImmutable $updated_at;

    public DateTimeImmutable $verified_at;

    public static function fromModel(Model $model) {
        return new self($model->toArray());
    }

    public function toModel() {
        return new Model($this->toArray());
    }

    public function toResource() {
        return new UserResource($this->toArray());
    }

    protected function casts()
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'password' => 'string',
            'created_at' => Carbon::class,
            'updated_at' => CarbonImmutable::class,
            'verified_at' => DateTimeImmutable::class,
        ];
    }
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
