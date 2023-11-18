
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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
