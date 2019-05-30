```js
$.ajax({
    url: 'https://www.domain.com' +
             '/{{ ltrim($route['boundUri'], '/') }}',
@if(count($route['bodyParameters']))
{!! json_encode($route['cleanBodyParameters'], JSON_PRETTY_PRINT) !!}
   ,
@endif
    success: function(response) {},
    error: function(response) {}
});
```