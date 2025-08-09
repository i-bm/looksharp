@extends('layouts.landing.main')

@section('content')
@include('pages.partials.breadcrumbs', ['bannerBg' => 'assets/images/events.jpg'])


<!-- ==== event section start ==== -->
<section class="event event-alt">
   <div class="container">
      {{-- <div class="row justify-content-center">
         <div class="col-12 col-md-8 col-xl-7">
            <div class="section__header text-center" data-aos="fade-up" data-aos-duration="1000">
               <span class="sub-title"><i class="icon-donation"></i>Start donating poor
                  people</span>
               <h2 class="title-animation">Checkout our upcoming full <span>event</span> list</h2>
            </div>
         </div>
      </div> --}}
      <div class="row gutter-30">
         <div class="col-12 col-lg-6 col-xl-7">
            @foreach ($events as $event)
            <div class="event__single-wrapper" data-aos="fade-up" data-aos-duration="1000">
               <div class="event__single van-tilt">
                  <a href="{{ route('event.show', $event['slug']) }}"> 
                     <div class="event__single-thumb">
                        <img src="{{ asset($event['image']) }}" alt="Image"> 
                     </div>
                  </a>
                  <div class="event__content">
                     <span>{{ $event['date'] }}</span>
                     <h4><a href="{{ route('event.show', $event['slug']) }}">{{ $event['title'] }}</a>
                     </h4>
                     <p><i class="fa-solid fa-location-dot"></i> {{ $event['location'] }}</p>
                  </div>
               </div>
            </div>
            @endforeach
         </div>
         {{-- <div class="col-12 col-lg-6 col-xl-5">
            <div class="event__single-wrapper" data-aos="fade-left" data-aos-duration="1000">
               <div class="event__single event-single-alt van-tilt">
                  <div class="event__single-thumb">
                     <img src="assets/images/event/two.png" alt="Image">
                  </div>
                  <div class="event__content">
                     <span>October 19, 2025</span>
                     <h4><a href="event-details.html">Unity in Giving Community
                           Charity Event</a>
                     </h4>
                     <p><i class="fa-solid fa-location-dot"></i> 135 W, 46nd Street, New York</p>
                  </div>
               </div>
            </div>
            <div class="event__single-wrapper" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="300">
               <div class="event__single  event-single-alt van-tilt">
                  <div class="event__single-thumb">
                     <img src="assets/images/event/three.png" alt="Image">
                  </div>
                  <div class="event__content">
                     <span>October 19, 2025</span>
                     <h4><a href="event-details.html">Unity in Giving Community
                           Charity Event</a>
                     </h4>
                     <p><i class="fa-solid fa-location-dot"></i> 135 W, 46nd Street, New York</p>
                  </div>
               </div>
            </div>
         </div> --}}
      </div>
      {{-- <div class="row">
         <div class="col-12">
            <div class="pagination-wrapper" data-aos="fade-up" data-aos-duration="1000">
               <ul class="pagination main-pagination">
                  <li>
                     <button>
                        <i class="fa-solid fa-angles-left"></i>
                     </button>
                  </li>
                  <li>
                     <a href="blog-list.html">1</a>
                  </li>
                  <li>
                     <a href="blog-list.html" class="active">2</a>
                  </li>
                  <li>
                     <a href="blog-list.html">3</a>
                  </li>
                  <li>
                     <button>
                        <i class="fa-solid fa-angles-right"></i>
                     </button>
                  </li>
               </ul>
            </div>
         </div>
      </div> --}}
   </div>
   <div class="spade">
      <img src="{{asset('assets/images/blog/spade-base.png')}}" alt="Image" class="base-img">
   </div>
</section>
<!-- ==== / event section end ==== -->


@endsection
