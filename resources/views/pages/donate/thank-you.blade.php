@extends('layouts.landing.main')

@section('content')

                 <!-- ==== donate us section start ==== -->
      <div class="cm-details donate-us community checkout faq" style="margin-top: 100px;">
        <div class="container">
           <div class="row gutter-60">
            <div class="col-lg-8 mx-auto">
                <div class="cm-details__content">
                    <div class="cm-details__poster" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <img src="{{asset('assets/images/thankyou.jpg')}}" alt="Image">
                     </div>
                    <div class="donate-inner" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <div class="cm-group">
                            <h3 class="title-animation text-center fs-2">Thank You {{ $payment->first_name }} for Your Donation</h3>
                            <p class="text-dark">Your <strong>{{ $payment->amount }} {{ $payment->currency }}</strong> donation has been successfully processed. We are grateful for your support and look forward to continuing our mission to empower the next generation of innovators.</p>
                            <div class="text-center mt-5">
                                <a href="{{route('donate')}}" class="btn--secondary">Donate Again</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection