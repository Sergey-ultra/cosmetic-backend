@extends('layouts.master')

@section('content')

  {{--  <div class="container">
--}}
{{--        <div class="review col-8 mt-4 mb-6 rounded-3">--}}
{{--            <div class="review__title">--}}
{{--                <h2>Отзывы</h2>--}}
{{--                <h5>Количество отзывов </h5>--}}
{{--            </div>--}}
{{--            <hr>--}}
{{--            <div class="review__body">--}}
{{--                <div id="reviewForm"></div>--}}

{{--                @if(isset($reviews))--}}
{{--                    @foreach($reviews as $reviewIndex => $review)--}}
{{--                        <div class="d-flex justify-content-between">--}}
{{--                            <div class="d-flex justify-content-between">--}}
{{--                                <h4>{{ $review->conclusion }}</h4>--}}
{{--                                <img class="ms-2"--}}
{{--                                     @switch($review->rating)--}}
{{--                                     @case(1)--}}
{{--                                         src="{{Storage::url('/icons/icon1.svg')}}"--}}
{{--                                     @break--}}
{{--                                     @case(2)--}}
{{--                                         src="{{Storage::url('/icons/icon2.svg')}}"--}}
{{--                                     @break--}}
{{--                                     @case(3)--}}
{{--                                         src="{{Storage::url('/icons/icon3.svg')}}"--}}
{{--                                     @break--}}
{{--                                     @case(4)--}}
{{--                                        src="{{Storage::url('/icons/icon4.svg')}}"--}}
{{--                                     @break--}}
{{--                                     @default--}}

{{--                                     @endswitch--}}

{{--                                />--}}

{{--                            </div>--}}
{{--                            <div class="review__date">{{  $review->created_at->format('d.m.Y')  }}</div>--}}
{{--                        </div>--}}

{{--                        <div>{{ $review->comment }}</div>--}}
{{--                        <div class="review__plus d-flex">--}}
{{--                            <img src="{{ Storage::url('/icons/review-p.gif') }}" alt="">--}}
{{--                            <div>{{ $review->plus }}</div>--}}
{{--                        </div>--}}
{{--                        <div class="review__minus d-flex">--}}
{{--                            <img src="{{ Storage::url('/icons/review-m.gif') }}" alt="">--}}
{{--                            <div>{{ $review->minus }}</div>--}}
{{--                        </div>--}}


{{--                        <div  class="row d-flex justify-content-start"  data-bs-toggle="modal" data-bs-target="#staticBackdrop{{ $reviewIndex }}">--}}
{{--                            @foreach(json_decode($review->imageUrl) as $index => $image)--}}
{{--                                <div  class="review__img d-flex align-items-center   col-md-4 col-lg-3">--}}
{{--                                    <img  class="img-fluid w-100"  src="{{ Storage::url($image) }}" alt="{{ $image }}">--}}
{{--                                </div>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}


{{--                        <!-- Modal -->--}}
{{--                        <div class="modal fade" id="staticBackdrop{{ $reviewIndex }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">--}}
{{--                            <div class="modal-dialog   modal-xl">--}}
{{--                                <div class="modal-content">--}}
{{--                                    <div class="modal-header">--}}
{{--                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--                                    </div>--}}
{{--                                    <div class="modal-body">--}}
{{--                                        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">--}}
{{--                                            <ol class="carousel-indicators">--}}
{{--                                                @foreach(json_decode($review->imageUrl) as $index => $image)--}}
{{--                                                    <li data-bs-target="#carouselExampleIndicators"--}}
{{--                                                        data-bs-slide-to="{{ $index }}" @if($index == 0) class="active" @endif></li>--}}
{{--                                                @endforeach--}}
{{--                                            </ol>--}}
{{--                                            <div class="carousel-inner">--}}
{{--                                                @foreach(json_decode($review->imageUrl) as $index => $image)--}}
{{--                                                    <div class="carousel-item @if($index == 0) active @endif ">--}}
{{--                                                        <img  class="img-fluid w-100"  src="{{ Storage::url($image) }}" alt="{{ $image }}">--}}
{{--                                                    </div>--}}
{{--                                                @endforeach--}}


{{--                                            </div>--}}
{{--                                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">--}}
{{--                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>--}}
{{--                                                <span class="visually-hidden">Previous</span>--}}
{{--                                            </a>--}}
{{--                                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">--}}
{{--                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>--}}
{{--                                                <span class="visually-hidden">Next</span>--}}
{{--                                            </a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="modal-footer">--}}

{{--                                    </div>--}}

{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div>Автор: {{ $review->user->name }}</div>--}}

{{--                        <div id="addComment" class="container-sm   justify-content-end">--}}
{{--                            <v-add-comment></v-add-comment>--}}
{{--                        </div>--}}

{{--                        <hr>--}}
{{--                    @endforeach--}}
{{--                @endif--}}

{{--            </div>--}}
{{--            </div>--}}


   {{-- </div>--}}

@endsection





