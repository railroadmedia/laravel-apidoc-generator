# {!! $groupName !!}

# JSON Endpoints


@foreach($parsedRoutes as $group => $parsedRoute)
@if($writeCompareFile === true)
{!! $parsedRoute['output'] !!}
@else
{!! isset($parsedRoute['modified_output']) ? $parsedRoute['modified_output'] : $parsedRoute['output'] !!}
@endif
@endforeach
