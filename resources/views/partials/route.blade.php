<!-- START_{{$route['id']}} -->
@if($route['title'] != '')## {{ $route['title']}}
@else## {{$route['uri']}}@endif
@if($route['authenticated'])

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>@endif
@if($route['description'])

{!! $route['description'] !!}
@endif


### HTTP Request
@foreach($route['methods'] as $method)
    `{{$method}} {{$route['uri']}}`

@endforeach

### Permissions
@if(count($route['permissions']))
    @foreach($route['permissions'] as $permission)
- {{$permission}}
    @endforeach
@endif

### Request Parameters


|Type|Key|Required|Notes|
|----|---|--------|-----|
@foreach($route['queryParameters'] as $attribute => $parameter)
|query|{{$attribute}}| @if($parameter['required']) yes @else  @endif |{!! $parameter['description'] !!}|
@endforeach
@foreach($route['bodyParameters'] as $attribute => $parameter)
|body|{{$attribute}}| @if($parameter['required']) yes @else  @endif |{!! $parameter['description'] !!}|
@endforeach

@if(count($route['validationRules']))
### Validation Rules
```php
{!! json_encode($route['validationRules'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
```
@endif

### Request Example:

@foreach($settings['languages'] as $language)
@include("apidoc::partials.example-requests.$language")

@endforeach

@if(in_array('GET',$route['methods']) || (isset($route['showresponse']) && $route['showresponse']))
@if(is_array($route['response']))
@foreach($route['response'] as $response)
### Response Example ({{$response['status']}}):

```json
@if(is_object($response['content']) || is_array($response['content']))
{!! json_encode($response['content'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
@else
{!! json_encode(json_decode($response['content']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
@endif
```
@endforeach
@else

### Response Example:

```json
@if(is_object($route['response']) || is_array($route['response']))
{!! json_encode($route['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
@else
{!! json_encode(json_decode($route['response']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
@endif
```
@endif
@endif




<!-- END_{{$route['id']}} -->
