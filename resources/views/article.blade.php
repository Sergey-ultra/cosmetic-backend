@extends('layouts.master')

@section('content')
    <div style="width: 300px;height:200px;">
        <img
            style="height: 100%;width: 100%;"
            src="{{ $article->image }}" alt="{{ $article->image }}">
    </div>
    <div>{{ $article->title }}</div>
    <div>{{ $article->preview }}</div>
    <div>{!! $article->body !!}</div>
@endsection


