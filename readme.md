# Restricted

Restricted allows you to restrict your users from signing up with reserved words.

Reserved words can be:

1. Your route segments - Example, you have this route: www.mywebsite.com/login
and your application allows to view user profile like this: www.mywebsite.com/username
This package can crawl all your routes and return a validation message when a user tries to register with such words like "login"
2. Words you just want to reserve - Example: cart, products, admin etc. These words can  be added manually to the reserved.txt file. after running the "restricted:index" command.

## Installation

To install Restricted use composer

### Download

```
composer require nawrasbukhari/restricted
```

### Publish the config

```
php artisan vendor:publish --provider="Nawras\Restricted\RestrictedServiceProvider" --tag="config"
```

## Usage

First, we need to crawl and index the application routes by running the command:

```
php artisan restricted:index
```
Now, you can simply add restricted to your validations like so:

```php
    $this->validate($request, [
        'name' => 'required|string|min:5',
        'username' => 'required|restricted'
    ]);
```
You can also add a new validation message

```php
    $this->validate($request, [
        'name' => 'required|string|min:5',
        'username' => 'required|restricted'
    ],[
    	'username.restricted' => 'A user exists with that username. Please try another or add more characters'
    ]);
```
## Settings

* file_path: (string) File name and path to save the indexed words
* index_level: (int) How deep do u want us to crawl your routes? ExAMPLE => www.mywebsite.com/segment1/segment2/segment3. setting this value to '2', will allow indexing of segment1 and segment2 and exclude segment3
* merge: (bool) should we to merge the new results with the old ones

## License

MIT license - free to use and abuse!