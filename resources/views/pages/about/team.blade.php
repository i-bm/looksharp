<!-- ==== team section start ==== -->
<section class="team">
    <div class="container">
       <div class="row justify-content-center">
          <div class="col-12 col-lg-10 col-xl-6">
             <div class="section__header text-center" data-aos="fade-up" data-aos-duration="1000">
                <h2 class="title-animation">Meet Our Volunteer
                   <span>Team</span> members
                </h2>
             </div>
          </div>
       </div>
       <div class="row gutter-40">
          @foreach (teamMembers() as $member)
          <div class="col-12 col-sm-6 col-xl-3">
             <div class="team__single-wrapper" data-aos="fade-up" data-aos-duration="500">
                <div class="team__single van-tilt">
                   <div class="team__single-thumb">
                      <a href="{{ $member['linkedin'] }}" target="_blank" title="linkedin">
                         <img src="{{asset('assets/images/team/'.$member['image'])}}" alt="{{ $member['name'] }}">
                      </a>
                      <div class="team__icons">
                         <div class="team__single-content__icon">
                            <i class="fa-solid fa-plus"></i>
                         </div>
                         <div class="team__single__thumb-social">
                            <ul>
                               <li>
                                  <a href="{{ $member['linkedin'] }}" target="_blank" title="linkedin">
                                     <i class="fa-brands fa-linkedin-in"></i>
                                  </a>
                               </li>
                               
                            </ul>
                         </div>
                      </div>
                   </div>
                   <div class="team__single-content">
                      <h6><a href="{{ $member['linkedin'] }}" target="_blank" title="linkedin">{{$member['name']}}</a></h6>
                      <p>{{$member['position']}}</p>
                   </div>
                </div>
             </div>
          </div>
          @endforeach
          {{-- <div class="col-12 col-sm-6 col-xl-3">
             <div class="team__single-wrapper" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                <div class="team__single van-tilt">
                   <div class="team__single-thumb">
                      <a href="team-details.html">
                         <img src="{{asset('assets/images/team/two.png')}}" alt="Image">
                      </a>
                      <div class="team__icons">
                         <div class="team__single-content__icon">
                            <i class="fa-solid fa-plus"></i>
                         </div>
                         <div class="team__single__thumb-social">
                            <ul>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-facebook-f"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-twitter"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-instagram"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-behance"></i>
                                  </a>
                               </li>
                            </ul>
                         </div>
                      </div>
                   </div>
                   <div class="team__single-content">
                      <h6><a href="team-details.html">Arian Drobloas</a></h6>
                      <p>Volunteer</p>
                   </div>
                </div>
             </div>
          </div>
          <div class="col-12 col-sm-6 col-xl-3">
             <div class="team__single-wrapper" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
                <div class="team__single van-tilt">
                   <div class="team__single-thumb">
                      <a href="team-details.html">
                         <img src="{{asset('assets/images/team/three.png')}}" alt="Image">
                      </a>
                      <div class="team__icons">
                         <div class="team__single-content__icon">
                            <i class="fa-solid fa-plus"></i>
                         </div>
                         <div class="team__single__thumb-social">
                            <ul>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-facebook-f"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-twitter"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-instagram"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-behance"></i>
                                  </a>
                               </li>
                            </ul>
                         </div>
                      </div>
                   </div>
                   <div class="team__single-content">
                      <h6><a href="team-details.html">Jara Klintof</a></h6>
                      <p>Volunteer</p>
                   </div>
                </div>
             </div>
          </div>
          <div class="col-12 col-sm-6 col-xl-3">
             <div class="team__single-wrapper" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="900">
                <div class="team__single van-tilt">
                   <div class="team__single-thumb">
                      <a href="team-details.html">
                         <img src="{{asset('assets/images/team/four.png')}}" alt="Image">
                      </a>
                      <div class="team__icons">
                         <div class="team__single-content__icon">
                            <i class="fa-solid fa-plus"></i>
                         </div>
                         <div class="team__single__thumb-social">
                            <ul>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-facebook-f"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-twitter"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-instagram"></i>
                                  </a>
                               </li>
                               <li>
                                  <a href="index.html">
                                     <i class="fa-brands fa-behance"></i>
                                  </a>
                               </li>
                            </ul>
                         </div>
                      </div>
                   </div>
                   <div class="team__single-content">
                      <h6><a href="team-details.html">Aiden Markram</a></h6>
                      <p>Volunteer</p>
                   </div>
                </div>
             </div>
          </div> --}}
       </div>
       {{-- <div class="row">
          <div class="col-12">
             <div class="section__cta cta text-center">
                <a href="our-team.html" aria-label="our team" title="our team" class="btn--primary">View All
                   <i class="fa-solid fa-arrow-right"></i></a>
             </div>
          </div>
       </div> --}}
    </div>
    <div class="spade">
       <img src="{{asset('assets/images/sprade-green.png')}}" alt="Image">
    </div>
 </section>
 <!-- ==== / team section end ==== -->