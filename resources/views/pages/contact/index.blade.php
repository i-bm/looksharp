@extends('layouts.landing.main')

@section('content')
@include('pages.partials.breadcrumbs', ['bannerBg' => 'assets/images/contact.jpg'])

 <!-- ==== contact section start ==== -->
 <section class="contact-main volunteer">
    <div class="container">
       <div class="row gutter-40">
          <div class="col-12 col-xl-6">
             <div class="contact__content">
                <div class="section__content" data-aos="fade-up" data-aos-duration="1000">
                   <span class="sub-title"><i class="icon-donation"></i> Get In Touch</span>
                   <h2 class="title-animation">Contact Us</h2>
                   {{-- <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium
                      doloremque laudantium, totam rem aperiam, eaque inventore
                   </p> --}}
                </div>
                <div class="contact-main__inner cta">
                   {{-- <div class="contact-main__single">
                      <div class="thumb">
                         <i class="fa-solid fa-location-dot"></i>
                      </div>
                      <div class="content">
                         <h6>Location</h6>
                         <p><a href="{{ config('misc.contact.google_maps') }}" target="_blank">
                               {{ config('misc.contact.address') }}
                            </a>
                         </p>
                      </div>
                   </div> --}}
                   {{-- <div class="contact-main__single">
                      <div class="thumb">
                         <i class="fa-solid fa-phone"></i>
                      </div>
                      <div class="content">
                         <h6>Phone</h6>
                         <p><a href="tel:{{ config('misc.contact.phone') }}">{{ config('misc.contact.phone') }}</a></p>
                      </div>
                   </div> --}}
                   <div class="contact-main__single">
                      <div class="thumb">
                         <i class="fa-solid fa-envelope"></i>
                      </div>
                      <div class="content">
                         <h6>Email</h6>
                         <p><a href="mailto:{{ config('misc.contact.email') }}">{{ config('misc.contact.email') }}</a></p>
                      </div>
                   </div>
                   <div class="contact-main__single">
                      <div class="thumb">
                         <i class="fa-solid fa-share-nodes"></i>
                      </div>
                      <div class="content">
                         <h6>Social</h6>
                         <div class="social">
                            <a href="{{ config('misc.social.facebook') }}" target="_blank" aria-label="share us on facebook"
                               title="facebook">
                               <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            
                            <a href="{{ config('misc.social.twitter') }}" target="_blank" aria-label="share us on twitter"
                               title="twitter">
                               <i class="fa-brands fa-twitter"></i>
                            </a>
                            <a href="{{ config('misc.social.linkedin') }}" target="_blank" aria-label="share us on linkedin"
                               title="linkedin">
                               <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                         </div>
                      </div>
                   </div>
                </div>
                <div class="contact-main__thumb cta">
                   <img src="{{asset('assets/images/send_form.jpg')}}" class="img-fluid rounded" alt="Image">
                </div>
             </div>
          </div>
          @include('pages.contact.form')
       </div>
    </div>
 </section>
 <!-- ==== / contact section end ==== -->
@endsection
