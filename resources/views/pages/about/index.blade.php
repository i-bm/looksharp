@extends('layouts.landing.main')

@section('content')
@include('pages.partials.breadcrumbs', ['bannerBg' => 'assets/images/about.jpg'])

<!-- ==== help section start ==== -->
<section class="help">
    <div class="container">
       <div class="row align-items-center gutter-40">
          <div class="col-12 col-lg-5 col-xxl-6 d-none d-lg-block">
             <div class="help__thumb">
                <div class="help__thumb-inner">
                   <div class="thumb-top thumb">
                      <img src="{{asset('assets/images/tech3.jpg')}}" alt="Image">
                   </div>
                   <div class="thumb-lg thumb" data-aos="fade-left" data-aos-duration="1000">
                      <img src="{{asset('assets/images/tech1.jpg')}}" alt="Image">
                      {{-- <div class="video-btn-wrapper">
                         <a href="https://www.youtube.com/watch?v=RvreULjnzFo" target="_blank" title="video Player"
                            class="open-video-popup">
                            <i class="icon-play"></i>
                         </a>
                      </div> --}}
                   </div>
                   <div class="thumb thumb-bottom">
                      <img src="{{asset('assets/images/tech2.jpg')}}" alt="Image">
                   </div>
                   <div class="line">
                      <img src="{{asset('assets/images/help/line.png')}}" alt="Image">
                   </div>
                   <div class="grid-line">
                      <img src="{{asset('assets/images/help/grid.png')}}" alt="Image" class="base-img">
                   </div>
                   {{-- <div class="vertical-text">
                    <h5>ddd</h5>
                      <h5>We Give <span>Donations</span> to Poor People </h5>
                   </div> --}}
                </div>
             </div>
          </div>
          <div class="col-12 col-lg-7 col-xxl-6">
             <div class="help__content">
                {{-- <span class="sub-title"><i class="icon-donation"></i>Start donating poor
                   people</span> --}}
                <h2 class="title-animation">About Thrive & Shine
                </h2>
                <p class="text-dark">{{ config('misc.foundation.name') }} is dedicated to fostering the next generation of african innovators. Our initiative, "Igniting STE(A)M Potential: Empowering Africa's Future," aims to address educational disparities and empower underprivileged, yet brilliant students to excel in Science, Technology, Engineering, and Mathematics STE(A)M fields.</p>
                
                
                
                <div class="help__content-icon-group d-flex flex-column align-items-start">
                   <div class="help__content-icon">
                      {{-- <div class="thumb">
                         <i class="icon-make-donation"></i>
                      </div> --}}
                      <div class="content help__content-list">
                         <h6>Our Mission</h6>
                         <p class="text-dark mb-2">Thrive & Shine empowers young Africans to reach their full potential by:</p>
                         <ol>
                           <li><i class="fa-solid fa-circle-check"></i>Providing financial access to STE(A)M education.</li>
                           <li><i class="fa-solid fa-circle-check"></i>Bridging the mentorship gap for high school and college students pursuing programs in STE(A)M.</li>
                           <li><i class="fa-solid fa-circle-check"></i>Building community and advancing humanitarian efforts.</li>
                         </ol>
                      </div>
                   </div>
                   {{-- <br /> --}}
                   <div class="help__content-icon">
                      <div class="thumb">
                         <i class="icon-support-heart"></i>
                      </div>
                      <div class="content">
                         <h6>Our Vision</h6>
                         <p class="text-dark">To empower young Africans to thrive as  leaders through access to STE(A)M education, mentorship, and a strong, purpose-driven community.</p>
                      </div>
                   </div>
                </div>
                <div class="help__content-list">
                   <ul>
                      <li><i class="fa-solid fa-circle-check"></i> 8% to 15% growth shows progress in STE(A)M, but access gaps remain. </li>
                      <li><i class="fa-solid fa-circle-check"></i> Women remain underrepresented in STE(A)M, with under 30% in jobs. </li>
                      <li><i class="fa-solid fa-circle-check"></i> Over 60% in the North have under four years of school, limiting STE(A)M.</li>
                   </ul>
                </div>
                {{-- <div class="help__content-cta cta">
                   <a href="about-us.html" aria-label="more about us" title="about us" class="btn--primary">More
                      About Us</a>
                   <div class="contact-btn">
                      <div class="contact-icon">
                         <i class="icon-phone"></i>
                      </div>
                      <div class="contact-content">
                         <p>Phone</p>
                         <a href="tel:01-793-7938">+236 (456) 896 22</a>
                      </div>
                   </div>
                </div> --}}
             </div>
          </div>
       </div>
    </div>
    <div class="hand">
       <img src="{{asset('assets/images/help/hand.png')}}" alt="Image">
    </div>
    {{-- <div class="parasuit">
       <img src="{{asset('assets/images/parasuit.png')}}" alt="Image">
    </div> --}}
    <div class="spade">
       <img src="{{asset('assets/images/help/spade.png')}}" alt="Image">
    </div>
 </section>
 <!-- ==== / help section end ==== -->

 @include('pages.about.team')
@endsection
