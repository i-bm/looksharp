<div class="col-lg-6">
    <div class="bg-light rounded-1 p-60 relative">
        <form name="contactForm" id="contact_form" method="post" action="#">
            <div class="row g-4">
                <div class="col-lg-12">
                    <h3>Get In Touch</h3>
                    <p>Have a question, suggestion, or just want to say hi? Fill out the form below and
                        we’ll get back to you soon.</p>

                    <div class="field-set">
                        <input type="text" name="name" id="name" class="form-control mb-4" placeholder="Your Name"
                            required>
                    </div>

                    <div class="field-set">
                        <input type="text" name="email" id="email" class="form-control mb-4" placeholder="Your Email"
                            required>
                    </div>

                    <div class="field-set">
                        <input type="text" name="phone" id="phone" class="form-control mb-4" placeholder="Your Phone"
                            required>
                    </div>

                    <div class="field-set">
                        <textarea name="message" id="message" class="form-control mb-4 h-100px"
                            placeholder="Your Message" required></textarea>
                    </div>
                </div>
            </div>

            <div id='submit' class="mt-3">
                <input type='submit' id='send_message' value='Send Message' class="btn-main">
            </div>

            <div id="success_message" class='success'>
                Your message has been sent successfully. Refresh this page if you want to send more
                messages.
            </div>
            <div id="error_message" class='error'>
                Sorry there was an error sending your form.
            </div>
        </form>
    </div>
</div>
