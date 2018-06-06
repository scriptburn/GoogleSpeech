

{{-- regular object attribute --}}
@php
	$value = $entry->{$column['name']};
@endphp

<a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/url/create?url='.$value) }}">{{ $value }}</a>