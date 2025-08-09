@extends('layouts.landing.main')

@section('content')


<div class="breadcrumb" style="border-bottom: 5px solid #ffc107; margin-top:150px"></div>


                 <!-- ==== donate us section start ==== -->
      <div class="cm-details donate-us community checkout faq">
        <div class="container">
           <div class="row gutter-60">

            <div class="col-lg-7">
               <a href="#donateFormContainer" class="mt-0 mb-3 btn btn--primary d-block d-sm-none">Donate Now</a>
                <div class="cm-details__content">
                  <div class="cm-details__poster" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                     <img src="{{asset('assets/images/donate_1.jpg')}}" alt="Image">
                  </div>
                   <div class="donate-inner" style="padding:40px 30px" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                      <div class="cm-group">
                         <h3 class="title-animation text-center fs-2">Empower The Next Generation of Innovators</h3>
                         <p class="text-dark">Your donation breaks barriers in STEAM education, creates mentorship opportunities, and builds pathways to success for marginalized youth across Ghana and Africa.
                         </p>
                      </div>
                </div>
             </div>

             
             <div class="detail__content" >
               <div class="detail__content_inner">
                  <h2 class="mb-2 fs-2 text-animation text-dark">Bank Details</h5>
             <table class="table table-bordered">  
            
                 <tbody>
                   <tr>
                       <td>Bank</td>
                       <td>Guaranty Trust Bank (GT Bank)</td>
                   </tr>
                   <tr>
                       <td>Account Name</td>
                       <td>Thrive & Shine LGB</td>
                   </tr>
                   <tr>
                       <td>Account Number</td>
                       <td>3219001000037</td>
                   </tr>
                   <tr>
                       <td>Bank Branch</td>
                       <td>Ring Road Central</td>
                   </tr>
                   <tr>
                       <td>SWIFT Code</td>
                       <td> GTBIGHAC</td>
                   </tr>
                   <tr>
                       <td>Sort Code</td>
                       <td>230119</td>
                   </tr>
               </tbody>
             </table>
             </div>
             </div>
        
            </div>

            <style>
                .community .community-donation {
                    bottom:-50px;
                }

                .detail__content_inner {
                  /* box-shadow: 0px 4px 28px 0px rgba(0, 0, 0, 0.05); */
                  padding: 20px 20px 10px;
                  margin-top: 60px;
                  margin-inline: 8px;
                  border-radius: 12px;
                  background: #fff;
                  -webkit-box-shadow: 0px 4px 28px 0px rgba(0, 0, 0, 0.05);
                  box-shadow: 0px 4px 28px 0px rgba(0, 0, 0, 0.05);
                  /* top: -60px;
                  margin-bottom: -60px;
                  position: relative;
                  z-index: 3; */
                }
            </style>
            <div class="col-lg-5" id="donateFormContainer">
                 <div class="cm-details__content" >
                    <div class="donate-inner" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                      
                       <div class="cta">
                          <div class="community-donation">
                             <div class="community-donation__inner">
                                <h5>Support Where It Counts.</h5>
                                <form id="donateForm" action="{{ route('initialize-payment') }}" method="POST">
                                @csrf

                                @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @foreach($errors->all() as $error)
                            <div class="alert alert-danger">{{ $error }}</div>
                            @endforeach

                            <div class="donation-form__single mt-3">
                                <h5>Donation Frequency</h5>
                                <div class="radio-wrapper">
                                   <div class="radio-single">
                                      <input type="radio" id="oneTimeDonation" name="donation-frequency" value="one_time">
                                      <label for="oneTimeDonation">One Time</label>
                                   </div>
                                   <div class="radio-single">
                                      <input type="radio" id="monthlyDonation" name="donation-frequency" value="monthly" checked>
                                      <label for="monthlyDonation">Monthly</label>
                                   </div>
                                </div>
                             </div>

                                <div class="donation-form" data-aos-delay="300">
                                   <div class="donation-form__single">
                                      <h5>Your Donation:</h5>
                                      <div class="input-group-icon">
                                         <div class="thumb">
                                            <span class="fw-bold text-white">GHS</span>
                                         </div>
                                         <input type="text" name="donation-amount" id="donationAmount">
                                      </div>
                                      <div class="made-amount">
                                         <span class="donation-amount">100</span>
                                         <span class="donation-amount active">200</span>
                                         <span class="donation-amount">250</span>
                                         <span class="donation-amount">300</span>
                                         <span class="donation-amount custom-amount">Custom</span>
                                      </div>
                                   </div>
                                  
                                   
                                </div>
                             </div>
                             <hr>
                             <div class="checkout__form">
                                <div class="intro">
                                   <h5>Details Information</h5>
                                </div>
                                <form action="index.html" method="post">
                                   <div class="input-group">
                                      <div class="input-single">
                                         <input type="text" name="c-firstname" id="cFirstName" placeholder="First Name">
                                         <i class="fa-solid fa-user"></i>
                                      </div>
                                      <div class="input-single">
                                         <input type="text" name="c-lastname" id="cLastName" placeholder="Last Name">
                                         <i class="fa-solid fa-user"></i>
                                      </div>
                                   </div>
                                   <div class="input-group">
                                      <div class="input-single">
                                         <input type="email" name="c-email" id="cEmail" placeholder="Your Email *"
                                            required>
                                         <i class="fa-solid fa-envelope"></i>
                                      </div>
                                      <div class="input-single">
                                         <input type="text" name="c-phone" id="cPhone" placeholder="Your Number"
                                            >
                                         <i class="fa-solid fa-phone"></i>
                                      </div>
                                   </div>
                                   <div class="form-cta">
                                      <button type="submit" aria-label="submit message" title="submit message"
                                         class="btn--primary" id="donateButton">Donate <i
                                            class="fa-solid fa-arrow-right"></i></button>
                                   </div>
                                </form>
                             </div>
                          </div>
                       </div>
                    </div>
                 </div>
                 @push('scripts')
                 <script>
                  $('#donateForm').on('submit', function(e) {
                    $('#donateButton').prop('disabled', true);
                    $('#donateButton').html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');
                  });

                  // $('#donationAmount').on('blur', function() {
                  //   var amount = $(this).val();
                  //   $('#donationAmount').val(amount);
                  // });
                 </script>
                 @endpush
            
                
        </div>
    </div>
</div>
@endsection