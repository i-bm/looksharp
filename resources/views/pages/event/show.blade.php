@extends('layouts.landing.main')

@section('content')
@include('pages.partials.breadcrumbs', ['bannerBg' => 'assets/images/events.jpg'])

<style>
   .cta p{
      color: #000;
   }
</style>
      <!-- ==== event details section start ==== -->
      <div class="cm-details">
         <div class="container">
            <div class="row gutter-60">
               <div class="col-12 col-xl-8">
                  <div class="cm-details__content">
                     <div class="cm-details__poster" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <img src="{{ asset($event['image']) }}" alt="Image">
                     </div>
                     <div class="cm-details-meta">
                        <p><i class="fa-solid fa-calendar-days"></i>{{ $event['date'] }}</p>
                        <p><i class="fa-solid fa-location-dot"></i>{{ $event['location'] }}</p>
                     </div>
                     <div class="cm-group cta">
                        <h3 class="title-animation">{{ $event['title'] }}</h3>
                        <p>In a move to inspire and empower the next generation of African innovators, Thrive and Shine LBG made its presence felt at the Day of Scientific Renaissance of Africa (DSRA) 2025. The event, held on Friday, June 27th, at the CEDI Conference Centre, University of Ghana, Legon, was themed “Climate Sustainability: Innovate, Safeguard, Prosper,” providing a fertile ground for the organization to advance its core mission.</p>
                        <p>Thrive and Shine LBG is dedicated to identifying and nurturing brilliant but under-resourced African students with a passion for Science, Technology, Engineering, and Mathematics (STEM). The organization firmly believes that the continent's sustainable future and prosperity are intrinsically linked to the cultivation of its youth in these critical fields.</p>
                        <p>At the DSRA, the Thrive and Shine LBG team actively engaged with a vibrant audience of students and the general public. Their exhibition served as a beacon for aspiring young scientists and engineers, offering a gateway to opportunities that might otherwise seem unattainable. The team provided comprehensive information about their scholarship programs, which are designed to alleviate financial barriers and provide robust mentorship to promising students pursuing STEM-related disciplines.</p>
                        <p>By participating in this significant event, Thrive and Shine LBG aimed to directly connect with the bright minds that will shape Africa's tomorrow. The organization's representatives shared insights into their ongoing initiatives and passionately articulated the pivotal role of youth in driving innovation and ensuring a sustainable and prosperous Africa.</p>
                        <p>The enthusiastic response and intellectual curiosity of the students at the conference have further galvanized Thrive and Shine LBG's commitment. The organization remains steadfast in its dedication to not only helping young Africans thrive in their academic pursuits but also to empowering them to shine as the future leaders and problem-solvers of the continent.</p>
                     </div>

                     <div class="cm-img-group cta">
                        @foreach ($event['images'] as $image)
                        <div class="cm-img-single">
                           <img src="{{asset($image)}}" alt="Image">
                        </div>
                        @endforeach
                     </div>
                  </div>
               </div>
               <div class="col-12 col-xl-4">
                  <div class="cm-details__sidebar">
                     <div class="cm-sidebar-widget" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <div class="intro">
                           <h5>search here</h5>
                        </div>
                        <form action="#" method="post">
                           <input type="text" name="search-product" id="searchProduct" placeholder="Search Here..."
                              required>
                           <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                     </div>
                     {{-- <div class="cm-sidebar-widget" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <div class="intro">
                           <h5>Recent Posts</h5>
                        </div>
                        <div class="cm-sidebar-post">
                           <div class="single-item">
                              <div class="thumb">
                                 <a href="blog-details.html">
                                    <img src="assets/images/blog/ph-one.png" alt="Image">
                                 </a>
                              </div>
                              <div class="content">
                                 <p><i class="fa-solid fa-calendar-days"></i> <span>November 19, 2024</span>
                                 </p>
                                 <p><a href="blog-details.html">Where Innovation Meets Foundation</a>
                                 </p>
                              </div>
                           </div>
                           <div class="single-item">
                              <div class="thumb">
                                 <a href="blog-details.html">
                                    <img src="assets/images/blog/ph-two.png" alt="Image">
                                 </a>
                              </div>
                              <div class="content">
                                 <p><i class="fa-solid fa-calendar-days"></i> <span>November 19, 2024</span>
                                 </p>
                                 <p><a href="blog-details.html">Where Innovation Meets Foundation</a>
                                 </p>
                              </div>
                           </div>
                           <div class="single-item">
                              <div class="thumb">
                                 <a href="blog-details.html">
                                    <img src="assets/images/blog/three.png" alt="Image">
                                 </a>
                              </div>
                              <div class="content">
                                 <p><i class="fa-solid fa-calendar-days"></i> <span>November 22, 2024</span>
                                 </p>
                                 <p><a href="blog-details.html">Structures That Stand,
                                       Dreams That Soar</a>
                                 </p>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="cm-sidebar-widget" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <div class="intro">
                           <h5>Tags</h5>
                        </div>
                        <div class="tag-wrapper">
                           <a href="shop.html">t-shirt</a>
                           <a href="shop.html">Banner Design</a>
                           <a href="shop.html">Brochures</a>
                           <a href="shop.html">Landing</a>
                           <a href="shop.html">Print</a>
                           <a href="shop.html">Business Card</a>
                        </div>
                     </div> --}}
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- ==== / event details section end ==== -->

@endsection