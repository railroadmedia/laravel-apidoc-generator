## Laravel API Documentation Generator

Automatically generate your API documentation from your existing Laravel/Lumen/[Dingo](https://github.com/dingo/api) routes. [Here's what the output looks like](http://marcelpociot.de/whiteboard/).

`php artisan apidoc:generate`

[![Latest Stable Version](https://poser.pugx.org/mpociot/laravel-apidoc-generator/v/stable)](https://packagist.org/packages/mpociot/laravel-apidoc-generator)[![Total Downloads](https://poser.pugx.org/mpociot/laravel-apidoc-generator/downloads)](https://packagist.org/packages/mpociot/laravel-apidoc-generator)
[![License](https://poser.pugx.org/mpociot/laravel-apidoc-generator/license)](https://packagist.org/packages/mpociot/laravel-apidoc-generator)
[![codecov.io](https://codecov.io/github/mpociot/laravel-apidoc-generator/coverage.svg?branch=master)](https://codecov.io/github/mpociot/laravel-apidoc-generator?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpociot/laravel-apidoc-generator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mpociot/laravel-apidoc-generator/?branch=master)
[![Build Status](https://travis-ci.org/mpociot/laravel-apidoc-generator.svg?branch=master)](https://travis-ci.org/mpociot/laravel-apidoc-generator)
[![StyleCI](https://styleci.io/repos/57999295/shield?style=flat)](https://styleci.io/repos/57999295)

## Installation
PHP 7 and Laravel 5.5 or higher are required.

```sh
composer require mpociot/laravel-apidoc-generator
```

### Laravel
Publish the config file by running:

```bash
php artisan vendor:publish --provider="Mpociot\ApiDoc\ApiDocGeneratorServiceProvider" --tag=apidoc-config
```

This will create an `apidoc.php` file in your `config` folder.

### Lumen
- Register the service provider in your `bootstrap/app.php`:

```php
$app->register(\Mpociot\ApiDoc\ApiDocGeneratorServiceProvider::class);
```

- Copy the config file from `vendor/mpociot/laravel-apidoc-generator/config/apidoc.php` to your project as `config/apidoc.php`. Then add to your `bootstrap/app.php`:

```php
$app->configure('apidoc');
```


## Steps to be taken

### Group the endpoints

In order to split the documentation in more .md files we should group the endpoints.

Using `@group` in a controller doc block creates a Group within the API documentation. For each group a new .md file with the group name will be generated. 

The documentation for all routes handled by that group will be included in the .md file.
We can also specify an `@group` on a single method to override the group defined at the controller level.

```php
/**
 * Class ContentJsonController
 *
 * @group Content API
 *
 * @package Railroad\Railcontent\Controllers
 */
```
### Define the Permissions

In order to extract the permissions required to each route we should define them in method doc block comments with `@permission` annotations. 

Can define many `@permission` tags, each string defined after the tag will be displayed in the documentation

```php
    /** Pull contents based on content ids.
     * @param Request $request
     *
     *
     * @return Fractal
     * @throws NotAllowedException
     *
     * @permission Must be logged in
     * @permission Must have the pull.contents permission
     *
     * @queryParam ids required Example:2,1
     */
```
### Define the Request parameters

To specify a list of valid parameters the API route accepts, we should use the `@bodyParam` and `@queryParam` annotations.

The `@bodyParam` annotation takes the name of the parameter, its type, an optional “required” label, and then its description.
The `@queryParam` annotation takes the name of the parameter, an optional “required” label, and then its description.

```php
    /** List linked comments, the current page it's the page with the comment
     *
     * @param $commentId
     * @param Request $request
     * @return Fractal
     * @throws NonUniqueResultException
     *
     * @queryParam comment_id integer required Example:1
     * @bodyParam limit integer How many to load per page. By default:10 Example:10
     */
```

They will be included in the generated documentation text and example requests.

A random value will be used as the value of each parameter in the example requests. If we would like to specify an example value, we can do so by adding Example: the-example to the end of the description. 

We can also add the @queryParam and @bodyParam annotations to a \Illuminate\Foundation\Http\FormRequest subclass instead, if we are using one in the controller method. 

```php
/**
 * Class ContentDatumCreateRequest
 *
 * @package Railroad\Railcontent\Requests
 *
 * @bodyParam data.type string required  Must be 'contentData'. Example: contentData
 * @bodyParam data.attributes.key string required  The data key. Example: description
 * @bodyParam data.attributes.value string  required Data value.  Example: indsf fdgg  gfg
 * @bodyParam data.attributes.position integer The position of this datum relative to other datum with the same key under the same content id.
 * @bodyParam data.relationships.content.data.type string required  Must be 'content'. Example: content
 * @bodyParam data.relationships.content.data.id integer required  Must exists in contents. Example: 1
 */
```

### Provide the example response

We can provide an example response for a route. This will be displayed in the examples section. There are several ways of doing this:

`@response`

We can provide an example response for a route by using the @response annotation with valid JSON. Moreover, we can define multiple @response tags as well as the HTTP status code related to a particular response (if no status code set, 200 will be assumed).

`@transformer, @transformerCollection, and @transformerModel`

A new instance will be created  and it will be passed to the transformer and display the result of that as the example response.

`@responseFile`

We can use any data of an actual response. We can put this response in a file (as a JSON string) within the Laravel storage directory and link to it. 

The package will parse this response and display in the examples for this route.

Similarly to `@response` tag, we can provide multiple `@responseFile` tags along with the HTTP status code of the response

`generate the response automatically`

If we don’t specify an example response using any of the above means, the package will attempt to get a sample response by making a request to the route (a `response call`).

For this solution we should populate the database with seek Doctrine entities. 

In the config file we can define the entityManager and the requiredEntities that should be generated, the number of seek entities and some custom data that should be used.

```php
    'entityManager'=>'Railroad\Railcontent\Managers\RailcontentEntityManager',
    'requiredEntities' => [
        'Railroad\Railcontent\Entities\User' => [
            'nr' => 1,
        ],
        'Railroad\Railcontent\Entities\Content' => [
            'nr' => 3,
            'data' => [
                'brand' => 'brand',
                'type' => 'course',
                'status' => 'published',
                'video' => null,
                'user' => null,
                'userProgress' => null
            ]
        ],
        'Railroad\Railcontent\Entities\ContentData' => [
            'nr' => 1,
        ],
         'Railroad\Railcontent\Entities\ContentHierarchy' => [
            'nr' => 1,
        ],

        'Railroad\Railcontent\Entities\Permission' => [
            'nr' => 10,
            'data' => [
                'brand' =>'brand',
            ]
        ],
        'Railroad\Railcontent\Entities\Comment' => [
            'nr' => 2,
            'data' => [
                'deletedAt' => null,
            ]
        ],
        'Railroad\Railcontent\Entities\ContentPermission' => [
            'nr' => 1
        ],

        'Railroad\Railcontent\Entities\UserContentProgress' => [
            'nr' => 1
        ]
    ],
```
The package will create automatically the seek Doctrine entities, before the response calls are executed.
 
## laravel-apidoc-generator documentation
Check out the documentation at [ReadTheDocs](http://laravel-apidoc-generator.readthedocs.io).


### License

The Laravel API Documentation Generator is free software licensed under the MIT license.
